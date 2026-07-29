<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\Meta;
use Typo3CmsMcp\Catalog\SystemExtensions;
use Typo3CmsMcp\Catalog\TranslationDomain;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Versions;

final class CatalogTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function theCatalogSaysHowItRelatesToTheInstallationBeingRead(): void
    {
        // Both numbers were known and never contrasted, so v15 markup and a
        // v15 custom-property contract were handed to a v13 backend as fact.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        $result = Tools::call('typo3_catalog_scope', []);
        self::assertSame('13.4.33', $result->data['catalog']['installedVersion']);
        self::assertStringContainsString('13.4.33', (string) $result->data['catalog']['skew']);
        self::assertStringContainsString('13.4.33', $result->text);

        // A component answer carries the pin, so it carries the gap too.
        self::assertStringContainsString('13.4.33', Tools::call('typo3_component_lookup', ['query' => 'badge'])->text);
    }

    #[Test]
    public function anInstallationWithoutTranslationDomainsIsGivenTheFileReference(): void
    {
        // The domain string is syntactically fine on a version that cannot
        // resolve it, and every label written with it renders empty. This is
        // the one answer that is withheld rather than qualified.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        $result = Tools::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertNull($result->data['domain']);
        self::assertSame('my_ext.db', $result->data['domainOnNewerVersions']);
        self::assertStringContainsString('LLL:EXT:my_ext/Resources/Private/Language/locallang_db.xlf', $result->text);
    }

    #[Test]
    public function anInstallationThatResolvesDomainsIsGivenTheDomain(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', '14.3.0'));

        $result = Tools::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertSame('my_ext.db', $result->data['domain']);
        self::assertNull($result->data['domainOnNewerVersions']);
    }

    #[Test]
    public function nothingIsSaidAboutASkewThatIsNotThere(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', Meta::read()['source']['version'] . '.0'));

        $result = Tools::call('typo3_catalog_scope', []);
        self::assertNull($result->data['catalog']['skew']);
    }

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
    public function aComponentNamedOutrightWinsOverOneThatMerelyMentionsIt(): void
    {
        // "status indicator" used to return Badge, "note" the Tree via its
        // node-note sub-component, and "dropzone" nothing at all.
        foreach (['dropzone', 'note', 'status indicator'] as $query) {
            self::assertSame(
                str_replace(' ', '-', $query),
                Components::find($query)[0]['name'] ?? null,
                $query . ' does not return itself first'
            );
        }
    }

    #[Test]
    public function aComponentNotVerifiedOnTheTargetIsDeclinedRatherThanHandedOver(): void
    {
        // The skew sentence named the difference without acting on it. Markup
        // taken from one revision either holds on the stated version or it does
        // not, and the answer for "does not" is to decline it.
        $result = Tools::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '13.4']);

        self::assertNotContains(
            'status-indicator',
            array_column($result->data['components'], 'name'),
            'a v14 custom-property contract is not handed to a 13.4 caller',
        );
        self::assertSame(['status-indicator'], array_column($result->data['withheld'], 'name'));
        self::assertSame(13, $result->data['targetVersion']);

        // Silently dropping it would read as "this component does not exist",
        // so the withholding names itself and what to check instead.
        self::assertStringContainsString('Withheld for TYPO3 v13', $result->text);
        self::assertStringContainsString('_status-indicator.scss', $result->text);
        self::assertStringContainsString('13.4 branch', $result->text);
    }

    #[Test]
    public function aComponentVerifiedOnTheTargetIsAnsweredWithTheRangeItHoldsFor(): void
    {
        $result = Tools::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '14.3']);

        $described = $result->data['components'][0];
        self::assertSame('status-indicator', $described['name']);
        self::assertSame(14, $described['since']);
        self::assertSame('TYPO3 v14 and newer', $described['verifiedOn']);
        self::assertSame([], $result->data['withheld']);
        self::assertStringContainsString('Verified on: TYPO3 v14 and newer', $result->text);
    }

    #[Test]
    public function withoutATargetTheWholeCatalogAnswersAndEachEntryCarriesItsRange(): void
    {
        // Nobody said which version this is for, so nothing is withheld and the
        // caller is told the range instead — the same rule the hints follow.
        $result = Tools::call('typo3_component_lookup', ['query' => 'status indicator']);

        self::assertNull($result->data['targetVersion']);
        self::assertSame('status-indicator', $result->data['components'][0]['name']);
        self::assertSame([], $result->data['withheld']);
    }

    #[Test]
    public function theCatalogSaysHowMuchOfItWasVerifiedOnAStatedVersion(): void
    {
        $result = Tools::call('typo3_catalog_scope', ['targetVersion' => '14']);

        self::assertSame(14, $result->data['targetVersion']);
        self::assertSame(count(Components::load()), $result->data['verifiedCount']);
        self::assertSame([], $result->data['withheld']);

        // The custom-property contract the catalog describes arrived after
        // 12.4, so most of it is not verified there and the scope says so.
        $onTwelve = Tools::call('typo3_catalog_scope', ['targetVersion' => '12.4']);
        self::assertLessThan(count(Components::load()), $onTwelve->data['verifiedCount']);
        self::assertStringContainsString('Withheld for TYPO3 v12', $onTwelve->text);
    }

    #[Test]
    public function aComponentCarriesEverySassFileItSpans(): void
    {
        $input = array_values(array_filter(Components::load(), static fn(array $c): bool => $c['name'] === 'input'))[0];

        // The form controls are one component split across four files; naming
        // only the first made the rest look like they were not part of it.
        self::assertContains('Build/Sources/Sass/component/forms/_form-text.scss', $input['sassPaths']);
        self::assertSame($input['sassPaths'][0], $input['sassPath'], 'sassPath stays the primary one');
    }

    #[Test]
    public function everyRecordedBindingNamesACoveredVersion(): void
    {
        // A binding outside the covered range withholds an entry from every
        // caller or from none, and both are silent — bin/verify-catalog is what
        // holds the numbers to the checkouts, this holds them to versions.json.
        $majors = Versions::majors();
        foreach (Components::load() as $component) {
            foreach (['since', 'until'] as $bound) {
                if ($component[$bound] !== null) {
                    self::assertContains($component[$bound], $majors, $component['name'] . ' is bound to a version this knowledge base does not cover');
                }
            }
        }
    }

    #[Test]
    public function whetherAnExtensionIsPartOfTheCoreIsAnswerable(): void
    {
        // It was answered from memory in both directions in one session: a
        // community package cited as evidence of what the core does, and a
        // system extension nobody knew was there.
        $camino = Tools::call('typo3_system_extension_lookup', ['query' => 'typo3/theme-camino']);
        self::assertSame(1, $camino->data['matchCount']);
        self::assertSame('theme_camino', $camino->data['extensions'][0]['key']);
        self::assertNotSame('', $camino->data['extensions'][0]['shippedOn'], 'it is not shipped on every covered line');

        $contentBlocks = Tools::call('typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']);
        self::assertSame(0, $contentBlocks->data['matchCount']);
        self::assertStringContainsString('third-party', $contentBlocks->text, 'a miss is about the core, not about the package');
    }

    #[Test]
    public function aTargetVersionDecidesWhichExtensionsAreShipped(): void
    {
        $onThirteen = Tools::call('typo3_system_extension_lookup', ['query' => 'theme_camino', 'targetVersion' => '13.4']);
        self::assertSame(0, $onThirteen->data['matchCount'], 'the theme is not part of that line');

        $everything = Tools::call('typo3_system_extension_lookup', []);
        self::assertGreaterThan($onThirteen->data['matchCount'], $everything->data['matchCount']);
        foreach ($everything->data['extensions'] as $extension) {
            self::assertStringStartsWith('typo3/', $extension['package'], $extension['key'] . ' has no package to require it by');
        }
    }

    #[Test]
    public function everyShippedRangeNamesACoveredVersion(): void
    {
        $majors = Versions::majors();
        foreach (SystemExtensions::load() as $extension) {
            self::assertNotSame('', $extension['description'], $extension['key'] . ' says nothing about itself');
            foreach (['since', 'until'] as $bound) {
                if ($extension[$bound] !== null) {
                    self::assertContains($extension[$bound], $majors, $extension['key'] . ' is bound to a version this knowledge base does not cover');
                }
            }
        }
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
    public function aDomainIsDerivedForAFileThatDoesNotExistYet(): void
    {
        // The point of computing rather than looking up: a file in any
        // extension, and one a patch is about to add, both get an answer —
        // which is exactly when it cannot be looked up anywhere.
        $result = Tools::call('typo3_translation_domain_lookup', [
            'path' => 'packages/my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame(null, $result['domain'], 'a project path is not an EXT: reference');

        $result = Tools::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame('my_extension.not_yet_written', $result['domain']);
    }

    #[Test]
    public function aPathThatNamesNoExtensionDerivesNoDomain(): void
    {
        $result = Tools::call('typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf'])->data;

        self::assertNull($result['domain']);
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
