<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Mcp;

use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ResourceHandlerInterface;
use Typo3CmsMcp\Knowledge;
use Typo3CmsMcp\Scope;

/**
 * Serves the typo3://core resources from the knowledge base.
 *
 * One instance backs every registered resource: the typo3://core index (what
 * this server covers, plus a JSON listing of the available documents) and each
 * typo3://core/{id} markdown document. The SDK wraps the returned string with
 * the mime type declared on the matching resource definition.
 */
final class ResourceHandler implements ResourceHandlerInterface
{
    private const INDEX_URI = 'typo3://core';
    private const DOCUMENT_PREFIX = 'typo3://core/';

    public function read(string $uri, ClientGateway $gateway): string
    {
        if ($uri === self::INDEX_URI) {
            return $this->index();
        }

        if (str_starts_with($uri, self::DOCUMENT_PREFIX)) {
            $id = substr($uri, strlen(self::DOCUMENT_PREFIX));

            return Knowledge::read($id);
        }

        throw new \RuntimeException(sprintf('Unknown resource: %s', $uri));
    }

    private function index(): string
    {
        // The profile's scope, not the stored one: the index is read by the
        // same client that gets the tool list, and a topic it cannot reach is
        // no more useful here than in typo3_server_scope.
        $scope = Scope::offered();

        $index = [
            'purpose' => $scope['purpose'],
            'covers' => $scope['covers'],
            'routing' => $scope['routing'],
            'documents' => array_map(static fn(array $document): array => [
                'id' => $document['id'],
                'title' => $document['title'],
                'uri' => self::DOCUMENT_PREFIX . $document['id'],
            ], Knowledge::documents()),
        ];

        return (string) json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
