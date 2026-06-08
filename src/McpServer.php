<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Stateless MCP dispatcher. Maps a single decoded JSON-RPC message to a
 * JSON-RPC response, or null for notifications (the caller answers 202).
 */
final class McpServer
{
    private const SERVER_NAME = 'typo3-cms-mcp';
    private const SERVER_VERSION = '0.2.0';
    private const DEFAULT_PROTOCOL_VERSION = '2025-03-26';

    /**
     * @param array<string, mixed> $message
     * @return array<string, mixed>|null Null for notifications.
     */
    public function handle(array $message): ?array
    {
        $method = isset($message['method']) ? (string) $message['method'] : '';
        $id = $message['id'] ?? null;
        $params = is_array($message['params'] ?? null) ? $message['params'] : [];

        // Notifications (no id) get no response.
        if ($id === null) {
            return null;
        }

        try {
            return match ($method) {
                'initialize' => $this->success($id, $this->initialize($params)),
                'ping' => $this->success($id, (object) []),
                'tools/list' => $this->success($id, ['tools' => Tools::definitions()]),
                'tools/call' => $this->success($id, $this->callTool($params)),
                'resources/list' => $this->success($id, ['resources' => $this->listResources()]),
                'resources/templates/list' => $this->success($id, ['resourceTemplates' => $this->listResourceTemplates()]),
                'resources/read' => $this->success($id, $this->readResource($params)),
                default => $this->error($id, -32601, sprintf('Method not found: %s', $method)),
            };
        } catch (ResourceNotFoundException $exception) {
            return $this->error($id, -32602, $exception->getMessage());
        } catch (\Throwable $exception) {
            return $this->error($id, -32603, $exception->getMessage());
        }
    }

    /** @param array<string, mixed> $params */
    private function initialize(array $params): array
    {
        $requested = isset($params['protocolVersion']) ? (string) $params['protocolVersion'] : self::DEFAULT_PROTOCOL_VERSION;

        return [
            'protocolVersion' => $requested !== '' ? $requested : self::DEFAULT_PROTOCOL_VERSION,
            'capabilities' => [
                'tools' => (object) [],
                'resources' => (object) [],
            ],
            'serverInfo' => [
                'name' => self::SERVER_NAME,
                'version' => self::SERVER_VERSION,
            ],
        ];
    }

    /** @param array<string, mixed> $params */
    private function callTool(array $params): array
    {
        $name = (string) ($params['name'] ?? '');
        $arguments = is_array($params['arguments'] ?? null) ? $params['arguments'] : [];

        try {
            $text = Tools::call($name, $arguments);
        } catch (\Throwable $exception) {
            return [
                'content' => [['type' => 'text', 'text' => $exception->getMessage()]],
                'isError' => true,
            ];
        }

        return [
            'content' => [['type' => 'text', 'text' => $text]],
        ];
    }

    /** @return array<int, array<string, string>> */
    private function listResources(): array
    {
        $resources = [[
            'uri' => 'typo3://core',
            'name' => 'TYPO3 core knowledge index',
            'mimeType' => 'application/json',
        ]];

        foreach (Knowledge::documents() as $document) {
            $resources[] = [
                'uri' => 'typo3://core/' . $document['id'],
                'name' => $document['title'],
                'mimeType' => 'text/markdown',
            ];
        }

        return $resources;
    }

    /** @return array<int, array<string, string>> */
    private function listResourceTemplates(): array
    {
        return [[
            'uriTemplate' => 'typo3://core/{documentId}',
            'name' => 'TYPO3 core knowledge document',
            'mimeType' => 'text/markdown',
        ]];
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function readResource(array $params): array
    {
        $uri = (string) ($params['uri'] ?? '');

        if ($uri === 'typo3://core') {
            $index = [
                'documents' => array_map(static fn(array $document): array => [
                    'id' => $document['id'],
                    'title' => $document['title'],
                    'uri' => 'typo3://core/' . $document['id'],
                ], Knowledge::documents()),
            ];

            return ['contents' => [[
                'uri' => $uri,
                'mimeType' => 'application/json',
                'text' => (string) json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            ]]];
        }

        if (str_starts_with($uri, 'typo3://core/')) {
            $id = substr($uri, strlen('typo3://core/'));
            try {
                $text = Knowledge::read($id);
            } catch (\RuntimeException $exception) {
                throw new ResourceNotFoundException($exception->getMessage());
            }

            return ['contents' => [[
                'uri' => $uri,
                'mimeType' => 'text/markdown',
                'text' => $text,
            ]]];
        }

        throw new ResourceNotFoundException(sprintf('Unknown resource: %s', $uri));
    }

    private function success(mixed $id, mixed $result): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    private function error(mixed $id, int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }
}
