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
    public function aToolCallReturnsTextAndStructuredContent(): void
    {
        $result = $this->session([$this->request(2, 'tools/call', [
            'name' => 'typo3_icon_lookup',
            'arguments' => ['query' => 'warning', 'limit' => 3],
        ])])[2]['result'];

        self::assertFalse($result['isError']);
        self::assertSame('text', $result['content'][0]['type']);
        self::assertNotSame('', $result['content'][0]['text']);
        self::assertGreaterThan(0, $result['structuredContent']['matchCount']);
        self::assertNotSame([], $result['structuredContent']['icons']);
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
