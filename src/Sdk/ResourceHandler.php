<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Sdk;

use Mcp\Server\ClientGateway;
use Mcp\Server\Handler\ResourceHandlerInterface;
use Typo3CmsMcp\Knowledge\Coverage;
use Typo3CmsMcp\Knowledge\Documents;

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
    public const INDEX_URI = 'typo3://core';
    public const DOCUMENT_PREFIX = 'typo3://core/';

    public function read(string $uri, ClientGateway $gateway): string
    {
        if ($uri === self::INDEX_URI) {
            return self::index();
        }

        if (str_starts_with($uri, self::DOCUMENT_PREFIX)) {
            $id = substr($uri, strlen(self::DOCUMENT_PREFIX));

            return Documents::read($id);
        }

        throw new \RuntimeException(sprintf('Unknown resource: %s', $uri));
    }

    /**
     * The index as it is served, which is also what its declared size counts:
     * the definition tells a client how many bytes reading it costs, and the
     * only honest source for that number is the string handed over.
     */
    public static function index(): string
    {
        // The profile's scope, not the stored one: the index is read by the
        // same client that gets the tool list, and a topic it cannot reach is
        // no more useful here than in typo3_server_scope.
        $scope = Coverage::offered();

        $index = [
            'purpose' => $scope['purpose'],
            'covers' => $scope['covers'],
            'routing' => $scope['routing'],
            'documents' => array_map(static fn(array $document): array => [
                'id' => $document['id'],
                'title' => $document['title'],
                'uri' => self::DOCUMENT_PREFIX . $document['id'],
            ], Documents::documents()),
        ];

        return (string) json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
