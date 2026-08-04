<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use Mcp\Schema\ResourceDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Sdk\ResourceHandler;
use Typo3CmsMcp\Server\Factory;

/**
 * The typo3:// resources as a host reads them, which is before anything of
 * this server has been called.
 *
 * A tool is called by the model mid-task and explains itself in its answer; a
 * resource is picked out of a list by the application or the user, who have the
 * list and nothing else. `R-ANS-022` is what follows from that, and this holds
 * the fields the choice is made on.
 */
final class ResourceSurfaceTest extends TestCase
{
    #[Test]
    public function everyResourceSaysWhatItIsAndHowMuchReadingItCosts(): void
    {
        foreach (Factory::resources() as $resource) {
            self::assertNotNull(
                $resource->description,
                $resource->uri . ' is offered as a name and a title, which is not enough to pick it by',
            );
            self::assertNotNull($resource->mimeType, $resource->uri . ' does not say what it is served as');
            self::assertSame(
                strlen(self::served($resource)),
                $resource->size,
                $resource->uri . ' declares a size that is not what a client reads',
            );
        }
    }

    /**
     * The order is the whole of what a priority says, so it is what is held:
     * the index above the documents it lists, and a document that holds
     * wherever the caller is working above one that stops at the core.
     */
    #[Test]
    public function whatADocumentIsWorthOutsideTheCoreDecidesWhereAPickerPutsIt(): void
    {
        $priorities = [];
        foreach (Factory::resources() as $resource) {
            $priority = $resource->annotations?->priority;
            self::assertNotNull($priority, $resource->uri . ' is offered with nothing to sort it by');
            $priorities[$resource->uri] = $priority;
        }

        $coreOnly = [];
        $transferable = [];
        foreach (Documents::documents() as $document) {
            $priority = $priorities[ResourceHandler::DOCUMENT_PREFIX . $document['id']];
            self::assertLessThan(
                $priorities[ResourceHandler::INDEX_URI],
                $priority,
                $document['id'] . ' is offered above the index that says what it is',
            );
            if (Documents::isCoreOnly($document['id'])) {
                $coreOnly[] = $priority;
            } else {
                $transferable[] = $priority;
            }
        }

        self::assertNotSame([], $coreOnly);
        self::assertNotSame([], $transferable);
        self::assertLessThan(
            min($transferable),
            max($coreOnly),
            'a document that stops at the core is offered as high as one that holds anywhere',
        );
    }

    #[Test]
    public function aDocumentThatStopsAtTheCoreSaysSoWhereItIsPicked(): void
    {
        foreach (Documents::documents() as $document) {
            self::assertStringContainsString(
                Documents::isCoreOnly($document['id']) ? 'does not transfer' : 'Holds for',
                (string) Documents::description($document['id']),
                $document['id'] . ' is offered without saying who its answers oblige',
            );
        }
    }

    /**
     * The protocol's `audience` is the user of the client or the model reading
     * the resource, and it is neither of the three audiences `R-AUD-001` names
     * — those are not values it takes. Everything served here is for both
     * roles, so the field says nothing and is left off.
     */
    #[Test]
    public function noResourceClaimsTheProtocolsAudienceForTheOneThisServerMeans(): void
    {
        foreach (Factory::resources() as $resource) {
            self::assertNull($resource->annotations?->audience, $resource->uri);
        }
    }

    /** What a client gets when it reads the resource, which is what its size counts. */
    private static function served(ResourceDefinition $resource): string
    {
        return $resource->uri === ResourceHandler::INDEX_URI
            ? ResourceHandler::index()
            : Documents::read(substr($resource->uri, strlen(ResourceHandler::DOCUMENT_PREFIX)));
    }
}
