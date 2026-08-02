<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Smoke;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tests\Support\Directory;

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
        if ($this->temporaryRoot !== null) {
            Directory::remove($this->temporaryRoot);
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
        // Held here as well as in ScopeTest, because this is the string a
        // client is actually handed: what the SDK puts on the wire is what a
        // client truncates, and it truncates without telling either side.
        self::assertLessThanOrEqual(
            Coverage::INSTRUCTIONS_BUDGET,
            mb_strlen($result['result']['instructions']),
        );
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

    /**
     * A question about an installation, asked where there is none, over the
     * wire a client actually reads. Nothing failed, so nothing is an error —
     * `D-ANS-005`. What comes back is the unsupported answer and the caller's
     * own query, with no count, no flag and no empty list to read as a result.
     */
    #[Test]
    public function aQuestionThatCannotBeAnsweredHereIsStillAnAnswer(): void
    {
        $this->temporaryRoot = sys_get_temp_dir() . '/typo3-cms-mcp-nothing-' . bin2hex(random_bytes(6));
        mkdir($this->temporaryRoot, 0o777, true);

        $result = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_icon_lookup',
            'arguments' => ['query' => 'publish'],
        ])], $this->temporaryRoot)[2]['result'];

        self::assertFalse($result['isError'], 'an unmet precondition is not a tool error');
        self::assertSame(['query', 'unsupported'], self::sorted($result['structuredContent']));
        self::assertSame('no-installation', $result['structuredContent']['unsupported']['cause']);
        self::assertContains(
            $this->temporaryRoot,
            $result['structuredContent']['unsupported']['searched'],
            'the answer does not name where it looked'
        );
        self::assertStringContainsString('not answerable here', $result['content'][0]['text']);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<int, string>
     */
    private static function sorted(array $data): array
    {
        $keys = array_keys($data);
        sort($keys);

        return $keys;
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

    /**
     * The one input-side alternative on the surface, over the wire that
     * produced the complaint. `typo3_documentation_lookup` takes `queries` or
     * `page`, `required` names neither, and a call carrying only
     * `targetVersion` is refused for both branches at once — one sentence per
     * branch, joined, because the SDK formats the leaves of a failed `oneOf`
     * separately. A session read the last half as "page is required", sent
     * `page: ""`, and reported the tool as unusable for search.
     *
     * The keyword stays and the rule is now stated where the call is composed
     * — both argument descriptions, on the wire in `tools/list` — so this holds
     * what a client still validates against. `D-ANS-012` says what would show
     * that the wrong half was fixed.
     */
    #[Test]
    public function aCallCarryingNeitherOfTwoAlternativeArgumentsNamesBoth(): void
    {
        $response = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_documentation_lookup',
            'arguments' => ['targetVersion' => '14.3'],
        ])])[2];

        self::assertSame(-32602, $response['error']['code']);
        self::assertStringContainsString('queries', $response['error']['message']);
        self::assertStringContainsString('page', $response['error']['message']);

        $documentation = null;
        foreach ($this->session([$this->request(2, 'tools/list')])[2]['result']['tools'] as $tool) {
            if ($tool['name'] === 'typo3_documentation_lookup') {
                $documentation = $tool;
            }
        }

        self::assertNotNull($documentation, 'the tool that carries the alternative is not offered');
        self::assertSame(['targetVersion'], $documentation['inputSchema']['required']);
        self::assertSame(
            [['required' => ['queries']], ['required' => ['page']]],
            $documentation['inputSchema']['oneOf'],
            'the alternative no longer reaches a client that reads oneOf',
        );
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
     * The one thing a client never tells this server and it has to work out for
     * itself: which installation the session is in.
     *
     * Thirteen of the twenty tools answer differently once one is found, and
     * three of them are not even offered — so a server that reads nothing
     * answers about TYPO3 in general where it was asked about a checkout, and
     * says so nowhere. The mechanism is covered a class at a time by every test
     * that hands `Instance` a directory itself. What only this can cover is that
     * something hands one in at all: the line doing it is in the entrypoint, and
     * with it deleted the rest of the suite stays green.
     */
    #[Test]
    public function theServerWorksOutWhichInstallationItWasStartedIn(): void
    {
        $root = $this->installation();

        $scope = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_server_scope',
            'arguments' => new \stdClass(),
        ])], $root)[2]['result']['structuredContent']['installation'];

        self::assertTrue($scope['found'], 'the server was started in an installation and did not find it');
        self::assertSame($root, $scope['root']);
        self::assertSame(Instance::KIND_COMPOSER_PROJECT, $scope['kind']);
        self::assertSame(Instance::VIA_DISCOVERY, $scope['via']);
    }

    /**
     * A client starts the server in the directory the session is in, and that is
     * rarely the project root — it is wherever the file being worked on lives.
     * Finding the installation from there is the walk-up, and it is the half
     * that makes the answer right in practice rather than in a fixture.
     */
    #[Test]
    public function itWalksUpToTheInstallationFromInsideIt(): void
    {
        $root = $this->installation();
        $inside = $root . '/packages/my_sitepackage/Classes/Controller';
        mkdir($inside, 0o777, true);

        $scope = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_server_scope',
            'arguments' => new \stdClass(),
        ])], $inside)[2]['result']['structuredContent']['installation'];

        self::assertTrue($scope['found'], 'started four directories in, the walk-up gave up');
        self::assertSame($root, $scope['root']);
        self::assertSame($inside, $scope['startedFrom']);
    }

    /** A Composer project with TYPO3 in it, which is what most callers stand in. */
    private function installation(): string
    {
        $root = sys_get_temp_dir() . '/typo3-cms-mcp-discovery-' . bin2hex(random_bytes(6));
        $this->temporaryRoot = $root;
        mkdir($root . '/vendor/composer', 0o777, true);
        mkdir($root . '/vendor/typo3/cms-core', 0o777, true);
        file_put_contents($root . '/composer.json', (string) json_encode([
            'name' => 'acme/site',
            'require' => ['typo3/cms-core' => '^13.4'],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/vendor/composer/installed.json', (string) json_encode([
            'packages' => [[
                'name' => 'typo3/cms-core',
                'version' => '13.4.33',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
            ]],
        ], JSON_THROW_ON_ERROR));

        return $root;
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
    private function session(array $requests, ?string $cwd = null): array
    {
        return $this->call(array_merge([
            $this->request(1, 'initialize', [
                'protocolVersion' => self::PROTOCOL_VERSION,
                'capabilities' => new \stdClass(),
                'clientInfo' => ['name' => 'phpunit', 'version' => '1'],
            ]),
            (string) json_encode(['jsonrpc' => '2.0', 'method' => 'notifications/initialized']),
        ], $requests), $cwd);
    }

    /**
     * @param array<int, string> $lines
     * @return array<int, array<string, mixed>>
     */
    private function call(array $lines, ?string $cwd = null): array
    {
        // The working directory is the whole of what a client tells this server
        // about where it is, so a test about discovery is a test about this
        // argument.
        $process = proc_open(
            [PHP_BINARY, Paths::root() . '/bin/typo3-cms-mcp'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $cwd,
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
