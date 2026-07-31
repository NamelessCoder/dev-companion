<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;

/**
 * Drives the real entrypoint the way a client does: a subprocess speaking
 * JSON-RPC over stdin and stdout. Everything in between — SDK wiring, schema
 * validation, error mapping — is only exercised here.
 */
final class StdioServerTest extends TestCase
{
    /** The newest revision the bundled SDK speaks. */
    private const PROTOCOL_VERSION = '2025-11-25';

    private ?string $temporaryRoot = null;

    protected function tearDown(): void
    {
        if ($this->temporaryRoot !== null && is_dir($this->temporaryRoot)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->temporaryRoot, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir($this->temporaryRoot);
        }
        $this->temporaryRoot = null;
    }

    #[Test]
    public function theServerAnnouncesItselfWithItsBoundary(): void
    {
        $result = $this->call([$this->request(1, 'initialize', [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
        ])])[1];

        self::assertSame('typo3-cms-mcp', $result['result']['serverInfo']['name']);
        self::assertSame(self::PROTOCOL_VERSION, $result['result']['protocolVersion']);
        self::assertStringContainsString('checkout', $result['result']['instructions']);
    }

    #[Test]
    public function everyToolIsListedWithItsSchemasAndAnnotations(): void
    {
        $tools = $this->session([$this->request(2, 'tools/list')])[2]['result']['tools'];

        self::assertNotSame([], $tools);
        foreach ($tools as $tool) {
            self::assertArrayHasKey('inputSchema', $tool);
            self::assertArrayHasKey('outputSchema', $tool, $tool['name'] . ' has no output schema');
            self::assertArrayHasKey('annotations', $tool, $tool['name'] . ' has no annotations');
        }

        self::assertContains('typo3_server_scope', array_column($tools, 'name'));
    }

    #[Test]
    public function theCommitMessageGuideIsAvailableAsAPrompt(): void
    {
        $prompts = $this->session([$this->request(2, 'prompts/list')])[2]['result']['prompts'];
        self::assertContains('commit_message', array_column($prompts, 'name'));

        $result = $this->session([$this->request(2, 'prompts/get', [
            'name' => 'commit_message',
            'arguments' => [
                'summary' => 'Explain the prompt primitive',
                'workflow' => 'project',
            ],
        ])])[2]['result'];

        self::assertSame('user', $result['messages'][0]['role']);
        self::assertStringContainsString(
            '[TASK] Explain the prompt primitive',
            $result['messages'][0]['content']['text']
        );
    }

    #[Test]
    public function aToolCallReturnsTextAndStructuredContent(): void
    {
        $result = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_component_lookup',
            'arguments' => ['query' => 'badge'],
        ])])[2]['result'];

        self::assertFalse($result['isError']);
        self::assertSame('text', $result['content'][0]['type']);
        self::assertNotSame('', $result['content'][0]['text']);
        self::assertGreaterThan(0, $result['structuredContent']['matchCount']);
        self::assertNotSame([], $result['structuredContent']['components']);
    }

    #[Test]
    public function theKnowledgeIndexIsServedWithTheScope(): void
    {
        $result = $this->session([$this->request(2, 'resources/read', ['uri' => 'typo3://core'])])[2]['result'];

        $index = json_decode($result['contents'][0]['text'], true);
        self::assertIsArray($index);
        self::assertArrayHasKey('purpose', $index);
        self::assertArrayHasKey('documents', $index);
        self::assertContains('typo3-core-rules', array_column($index['documents'], 'id'));
    }

    #[Test]
    public function aKnowledgeDocumentIsServedAsMarkdown(): void
    {
        $result = $this->session([$this->request(2, 'resources/read', [
            'uri' => 'typo3://core/typo3-core-rules',
        ])])[2]['result'];

        self::assertStringContainsString('# TYPO3 Core Contribution Rules', $result['contents'][0]['text']);
    }

    #[Test]
    public function invalidArgumentsAreRejectedBeforeTheToolRuns(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_rule_lookup',
            'arguments' => new \stdClass(),
        ])])[2];

        self::assertSame(-32602, $response['error']['code']);
        self::assertStringContainsString('query', $response['error']['message']);
    }

    #[Test]
    public function anUnknownToolIsAnError(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_does_not_exist',
            'arguments' => new \stdClass(),
        ])])[2];

        self::assertArrayHasKey('error', $response);
    }

    #[Test]
    public function anUnknownResourceIsAnError(): void
    {
        $response = $this->session([$this->request(2, 'resources/read', ['uri' => 'typo3://core/nope'])])[2];

        self::assertArrayHasKey('error', $response);
    }

    /**
     * A client writes its next request while the server is still working on the
     * last one. Where that work is a console command, the command inherits the
     * server's stdin unless it is given one of its own — and `ddev exec` reads
     * stdin to the end, so the queued request is eaten and the session hangs on
     * an answer that can never come. Both runs of `REVIEW-02` in an extension
     * checkout died here, 24 minutes apart, with no error on either side.
     */
    #[Test]
    public function aRequestBehindOneThatRunsTheConsoleIsStillAnswered(): void
    {
        $root = $this->installationWithADrainingConsole();
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root,
            getenv() + ['TYPO3_MCP_CONSOLE' => PHP_BINARY . ' ' . $root . '/console.php']
        );
        self::assertIsResource($process);

        fwrite($pipes[0], implode("\n", [
            $this->request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
            $this->request(2, 'tools/call', ['name' => 'typo3_fluid_namespace_list', 'arguments' => new \stdClass()]),
        ]) . "\n");
        // Late enough that the console command is running, which is the only
        // moment this can go wrong: written earlier the line sits in the
        // server's own read buffer, where no child can reach it.
        usleep(200_000);
        fwrite($pipes[0], $this->request(3, 'tools/call', ['name' => 'typo3_server_scope', 'arguments' => new \stdClass()]) . "\n");
        fclose($pipes[0]);

        $stdout = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $answered = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            $decoded = json_decode(trim($line), true);
            if (is_array($decoded) && isset($decoded['id'])) {
                $answered[] = $decoded['id'];
            }
        }

        self::assertContains(2, $answered, 'the console command itself went unanswered');
        self::assertContains(3, $answered, 'the console command swallowed the request queued behind it');
    }

    /**
     * An installation whose console takes a moment and then reads its stdin to
     * the end, the way `ddev exec` does.
     */
    private function installationWithADrainingConsole(): string
    {
        $root = sys_get_temp_dir() . '/typo3-cms-mcp-stdio-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', (string) json_encode([
            'name' => 'typo3/cms',
            'type' => 'typo3-cms-core',
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/typo3/sysext/core/composer.json', (string) json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/console.php', "<?php\nusleep(400000);\nstream_get_contents(STDIN);\necho '[]';\n");

        return $root;
    }

    /**
     * @param array<int, string> $requests
     * @return array<int, array<string, mixed>> responses by request id
     */
    private function session(array $requests): array
    {
        return $this->call(array_merge([
            $this->request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
        ], $requests));
    }

    /**
     * @param array<int, string> $lines
     * @return array<int, array<string, mixed>>
     */
    private function call(array $lines): array
    {
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        self::assertIsResource($process);

        fwrite($pipes[0], implode("\n", $lines) . "\n");
        fclose($pipes[0]);

        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);

        self::assertSame(0, $status, 'the server exited with ' . $status . ': ' . $stderr);

        $responses = [];
        foreach (explode("\n", trim($stdout)) as $line) {
            if (trim($line) === '') {
                continue;
            }
            $decoded = json_decode($line, true);
            self::assertIsArray($decoded, 'the server wrote a non-JSON line: ' . $line);
            $responses[$decoded['id'] ?? 0] = $decoded;
        }

        return $responses;
    }

    /** @param array<string, mixed>|null $params */
    private function request(int $id, string $method, ?array $params = null): string
    {
        $request = ['jsonrpc' => '2.0', 'id' => $id, 'method' => $method];
        if ($params !== null) {
            $request['params'] = $params;
        }

        return (string) json_encode($request);
    }
}
