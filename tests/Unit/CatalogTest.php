<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\Icons;
use Typo3CmsMcp\Catalog\Labels;
use Typo3CmsMcp\Catalog\Meta;
use Typo3CmsMcp\Catalog\TranslationDomain;
use Typo3CmsMcp\Tools;

final class CatalogTest extends TestCase
{
    #[Test]
    public function aComponentQueryIsAnsweredByTheComponentItself(): void
    {
        $components = Components::find('badge');

        self::assertSame('badge', $components[0]['name']);
        self::assertContains('name', $components[0]['matchedIn']);
        self::assertNotSame('', $components[0]['markup']);
    }

    #[Test]
    public function aComponentMatchedByItsOwnNameOutranksOneMatchedDeepInAClassList(): void
    {
        $components = Components::find('card');

        self::assertSame('card', $components[0]['name']);
        foreach ($components as $component) {
            self::assertNotSame(['sub-component classes'], $component['matchedIn']);
        }
    }

    #[Test]
    public function anEmptyQueryListsTheWholeCatalogAlphabetically(): void
    {
        $names = array_column(Components::find(null), 'name');

        self::assertSame(count(Components::load()), count($names));
        $sorted = $names;
        sort($sorted);
        self::assertSame($sorted, $names);
    }

    #[Test]
    public function anUnknownComponentMatchesNothing(): void
    {
        self::assertSame([], Components::find('quantumflux'));
    }

    #[Test]
    public function everyComponentCarriesItsSassSource(): void
    {
        foreach (Components::load() as $component) {
            self::assertNotSame('', $component['rootClass'], $component['name'] . ' has no root class');

            if ($component['sassPath'] === null) {
                // Only a web component may have no Sass source: its styles live
                // in the element itself.
                self::assertStringStartsWith('typo3-', $component['rootClass'], $component['name'] . ' has no Sass source');
                continue;
            }
            self::assertStringEndsWith('.scss', $component['sassPath'], $component['name'] . ' has no Sass source');
        }
    }

    #[Test]
    public function anExactIconIdentifierWins(): void
    {
        $icons = Icons::find('actions-open');

        self::assertSame('actions-open', $icons[0]['identifier']);
        self::assertContains('exact identifier', $icons[0]['why']);
    }

    #[Test]
    public function aConceptFindsTheIconThatSpellsTheShape(): void
    {
        $icons = Icons::find('warning');
        $identifiers = array_column($icons, 'identifier');

        self::assertNotSame([], $icons);
        self::assertContains('actions-exclamation-triangle', $identifiers, 'the warning concept must reach the triangle icon');

        $why = implode(' ', array_merge(...array_column($icons, 'why')));
        self::assertStringContainsString('concept "warning"', $why);
    }

    #[Test]
    public function aCategoryNameAloneDoesNotMatchEveryIconInIt(): void
    {
        $icons = Icons::find('actions');

        foreach ($icons as $icon) {
            self::assertGreaterThan(0, $icon['matched'], $icon['identifier'] . ' matched on its category only');
        }
    }

    #[Test]
    public function anUnknownIconMatchesNothing(): void
    {
        self::assertSame([], Icons::find('quantumflux'));
    }

    #[Test]
    public function anIdentifierShapedQueryIsToldApartFromASearchPhrase(): void
    {
        self::assertTrue(Icons::looksLikeIdentifier('actions-open'));
        // Registered by an extension or lazily, so outside this catalog but
        // still an identifier — the shape is what decides, not the coverage.
        self::assertTrue(Icons::looksLikeIdentifier('status-reference-hard'));
        self::assertTrue(Icons::looksLikeIdentifier('flags-multiple'));

        self::assertFalse(Icons::looksLikeIdentifier('move record up'));
        self::assertFalse(Icons::looksLikeIdentifier('warning'));
        self::assertFalse(Icons::looksLikeIdentifier('quantum-flux'));
    }

    #[Test]
    public function anIdentifierThatIsNotInTheSnapshotIsReportedAsAMiss(): void
    {
        // The dangerous answer: 21 icons that share a name part, led by an
        // unrelated one, each with a plausible "why" — read as a confirmation
        // that the queried identifier resolves to something.
        $result = Tools::call('typo3_icon_lookup', ['query' => 'status-reference-hard']);

        self::assertFalse($result->data['exactMatch']);
        self::assertStringContainsString('is not in this snapshot', $result->text);
        self::assertStringContainsString('suggestions, not the answer', $result->text);
    }

    #[Test]
    public function aRegisteredIdentifierIsConfirmedAsOne(): void
    {
        $result = Tools::call('typo3_icon_lookup', ['query' => 'actions-open']);

        self::assertTrue($result->data['exactMatch']);
        self::assertSame('actions-open', $result->data['icons'][0]['identifier']);
    }

    #[Test]
    public function everyIconAnswerSaysHowToRenderTheIdentifier(): void
    {
        foreach ([['query' => 'actions-open'], ['query' => 'quantumflux'], []] as $arguments) {
            $data = Tools::call('typo3_icon_lookup', $arguments)->data;
            self::assertNotSame([], $data['usage'], json_encode($arguments) . ' carries no usage');
        }
    }

    #[Test]
    public function aLabelIsAnsweredWithItsTranslationDomainReference(): void
    {
        $labels = Labels::find('save close document');

        self::assertNotSame([], $labels);
        $label = $labels[0];
        self::assertMatchesRegularExpression('/^[a-z0-9_]+\.[a-z0-9_]+:/', $label['ref'], 'the primary reference is the domain form');
        self::assertStringStartsWith('EXT:', $label['legacyRef']);
        self::assertStringEndsWith(':' . $label['id'], $label['legacyRef']);
    }

    #[Test]
    public function aLabelMatchedInItsKeyOutranksOneMatchedInItsText(): void
    {
        $labels = Labels::find('saveAndCloseDoc');

        self::assertSame('saveAndCloseDoc', $labels[0]['id']);
        self::assertContains('key', $labels[0]['matchedIn']);
    }

    #[Test]
    public function requiringAllTermsIsWhatSeparatesAHitFromNoise(): void
    {
        self::assertSame([], Labels::find('save quantumflux'));
        self::assertNotSame([], Labels::find('save quantumflux', false));
    }

    #[Test]
    public function aRelaxedLabelAnswerSaysThatNothingMatchedClosely(): void
    {
        // Any-term matching over a long phrase used to report thousands of
        // labels as matches, with nothing saying no close match exists.
        $result = Tools::call('typo3_label_lookup', [
            'query' => 'Restore or permanently remove deleted records',
        ]);

        self::assertTrue($result->data['relaxed']);
        self::assertStringStartsWith('No label in the curated subset matches', $result->text);
        self::assertLessThan(50, $result->data['matchCount'], 'a relaxed answer must stay a suggestion list');
    }

    #[Test]
    public function anIdentifierThatCarriesMoreOfTheQueryOutranksAVaguerOne(): void
    {
        // A concept hit on a term the name already matched used to count twice,
        // which put actions-move ahead of actions-move-up for "move record up".
        self::assertSame('actions-move-up', Icons::find('move record up')[0]['identifier']);
    }

    #[Test]
    public function registeredDomainsAreListedWithTheirLabelCount(): void
    {
        $domains = Labels::domains('alt_doc');

        self::assertNotSame([], $domains);
        foreach ($domains as $domain) {
            self::assertSame(TranslationDomain::fromReference($domain['ref']), $domain['domain']);
            self::assertGreaterThan(0, $domain['count']);
        }
    }

    /**
     * The cases are the ones TranslationDomainMapperTest states in the core, so
     * this port is held to the same rules as the original.
     */
    #[Test]
    public function theTranslationDomainIsDerivedByTheCoreRules(): void
    {
        $expected = [
            'EXT:test_translation_domain/Resources/Private/Language/locallang.xlf' => 'test_translation_domain.messages',
            'EXT:test_translation_domain/Resources/Private/Language/locallang_toolbar.xlf' => 'test_translation_domain.toolbar',
            'EXT:test_translation_domain/Resources/Private/Language/locallang_sudo_mode.xlf' => 'test_translation_domain.sudo_mode',
            'EXT:test_translation_domain/Resources/Private/Language/Form/locallang_tabs.xlf' => 'test_translation_domain.form.tabs',
            'EXT:test_translation_domain/Resources/Private/Language/SudoMode/locallang.xlf' => 'test_translation_domain.sudo_mode.messages',
            'EXT:test_translation_domain/Resources/Private/Language/de.locallang.xlf' => 'test_translation_domain.messages',
            'EXT:core/Resources/Private/Language/locallang.xlf' => 'core.messages',
        ];

        foreach ($expected as $reference => $domain) {
            self::assertSame($domain, TranslationDomain::fromPath($reference), $reference);
        }
    }

    #[Test]
    public function aDomainIsDerivedForAFileTheCatalogDoesNotContain(): void
    {
        // The point of deriving rather than looking up: a file outside the
        // snapshot, and a file a patch is about to add, both get an answer.
        $result = Tools::call('typo3_label_lookup', [
            'mode' => 'derive',
            'query' => 'typo3/sysext/backend/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame('backend.not_yet_written', $result['domain']);
        self::assertFalse($result['inSnapshot']);
    }

    #[Test]
    public function aPathThatNamesNoExtensionDerivesNoDomain(): void
    {
        $result = Tools::call('typo3_label_lookup', ['mode' => 'derive', 'query' => 'somewhere/else.xlf'])->data;

        self::assertSame(0, $result['matchCount']);
        self::assertArrayNotHasKey('domain', $result);
    }

    #[Test]
    public function theCatalogsSayWhichCoreRevisionTheyDescribe(): void
    {
        $meta = Meta::read();

        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/', $meta['source']['commit']);
        self::assertNotSame('', $meta['source']['branch']);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $meta['verifiedAt']);
        self::assertSame(count(Components::load()), $meta['counts']['components'], 'the component count drifted from the catalog');
    }

    #[Test]
    public function theProvenanceLineNamesTheSnapshot(): void
    {
        $line = Meta::line();

        self::assertStringContainsString(Meta::read()['source']['version'], $line);
        self::assertStringContainsString('not in this snapshot', $line);
    }
}
