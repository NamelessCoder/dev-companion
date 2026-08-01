<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Catalog\Components;
use Typo3CmsMcp\Knowledge\Catalog\DemoMarkup;
use Typo3CmsMcp\Knowledge\Catalog\Meta;
use Typo3CmsMcp\Knowledge\Catalog\References;
use Typo3CmsMcp\Knowledge\Catalog\SystemExtensions;
use Typo3CmsMcp\Knowledge\Catalog\TranslationDomain;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\Catalogs;

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

        $result = Registry::call('typo3_catalog_scope', []);
        self::assertSame('13.4.33', $result->data['catalog']['installedVersion']);
        self::assertStringContainsString('13.4.33', (string) $result->data['catalog']['skew']);
        self::assertStringContainsString('13.4.33', $result->text);

        // A component answer carries the pin, so it carries the gap too.
        self::assertStringContainsString('13.4.33', Registry::call('typo3_component_lookup', ['query' => 'badge'])->text);
    }

    #[Test]
    public function anInstallationWithoutTranslationDomainsIsGivenTheFileReference(): void
    {
        // The domain string is syntactically fine on a version that cannot
        // resolve it, and every label written with it renders empty. This is
        // the one answer that is withheld rather than qualified.
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        $result = Registry::call('typo3_translation_domain_lookup', [
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

        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_ext/Resources/Private/Language/locallang_db.xlf',
        ]);

        self::assertSame('my_ext.db', $result->data['domain']);
        self::assertNull($result->data['domainOnNewerVersions']);
    }

    #[Test]
    public function nothingIsSaidAboutASkewThatIsNotThere(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', Meta::read()['source']['version'] . '.0'));

        $result = Registry::call('typo3_catalog_scope', []);
        self::assertNull($result->data['catalog']['skew']);
    }

    #[Test]
    public function theInstalledComponentContractWinsOverTheBundledSnapshot(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backendCss = $root . '/typo3/sysext/backend/Resources/Public/Css';
        mkdir($backendCss, 0o777, true);
        file_put_contents(
            $backendCss . '/backend.css',
            '.badge { --typo3-badge-bg: green; } .badge-success {} .badge-installed {} .dropzone {}',
        );

        $styleguide = $root . '/typo3/sysext/styleguide';
        mkdir($styleguide . '/Resources/Private/Templates/Backend/Components', 0o777, true);
        file_put_contents($styleguide . '/composer.json', json_encode([
            'name' => 'typo3/cms-styleguide',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'styleguide']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents(
            $styleguide . '/Resources/Private/Templates/Backend/Components/Badges.fluid.html',
            '<sg:example><span class="badge badge-installed">Installed</span></sg:example>',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_component_lookup', ['query' => 'badge-installed']);
        $badge = $result->data['components'][0];

        self::assertSame('installation', $result->data['componentSource']);
        self::assertSame('14.3.5', $badge['contractVersion']);
        self::assertSame('14.3.5', $badge['describesVersion']);
        self::assertSame('installation', $badge['markupSource']);
        self::assertContains('badge-installed', $badge['classes']);
        self::assertSame(['badge-success'], $badge['variants']);
        self::assertSame(['--typo3-badge-bg'], $badge['customProperties']);
        self::assertSame('<span class="badge badge-installed">Installed</span>', $badge['markup']);
        self::assertContains(
            'EXT:styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html',
            $badge['sourceFiles'],
        );
        self::assertNull($result->data['catalog']['skew']);
        self::assertStringContainsString('Other installed classes: badge-installed', $result->text);
        self::assertStringContainsString('installed TYPO3 14.3.5 packages', $result->text);

        $fallback = Registry::call('typo3_component_lookup', ['query' => 'dropzone']);
        self::assertSame('installation', $fallback->data['componentSource']);
        self::assertSame('14.3.5', $fallback->data['components'][0]['contractVersion']);
        self::assertSame('catalog', $fallback->data['components'][0]['markupSource']);
        self::assertSame(Meta::read()['source']['version'], $fallback->data['components'][0]['describesVersion']);
        self::assertStringContainsString('bundled TYPO3 15.0 fallback', $fallback->text);

        $scope = Registry::call('typo3_catalog_scope', []);
        self::assertSame('installation', $scope->data['componentSource']);
        self::assertStringContainsString('Installed component contract', $scope->text);

        $broad = Registry::call('typo3_component_lookup', [
            'query' => 'backend component markup for the installed TYPO3 version',
        ]);
        self::assertGreaterThan(0, $broad->data['matchCount']);
        self::assertSame('installation', $broad->data['componentSource']);
    }

    #[Test]
    public function anInstalledContractDoesNotAnswerForAnotherTargetMajor(): void
    {
        $root = $this->coreCheckout('14.3.5');
        $backendCss = $root . '/typo3/sysext/backend/Resources/Public/Css';
        mkdir($backendCss, 0o777, true);
        file_put_contents($backendCss . '/backend.css', '.badge {} .badge-installed {}');
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_component_lookup', [
            'query' => 'badge',
            'targetVersion' => '13.4',
        ]);

        self::assertSame('catalog', $result->data['componentSource']);
        self::assertSame(Meta::read()['source']['version'], $result->data['components'][0]['contractVersion']);
        self::assertNotContains('badge-installed', $result->data['components'][0]['classes']);
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
    public function aQueryTheCatalogWasNotWrittenForIsAMissRatherThanItsNearestWords(): void
    {
        // Three components came back for this, each on one word out of five:
        // Dropdown and Form Inputs through a keyword, Card because its summary
        // is written in the words any question about content is written in.
        self::assertSame([], Components::find('content element preview heading text'));

        // What the query names is still the answer, however the rest of the
        // sentence reads — the coverage rule is for what nobody named.
        self::assertSame('badge', Components::find('add a badge to the module header')[0]['name']);
        // And a class is a way in of its own: it is what the miss suggests.
        self::assertNotSame([], Components::find('input-group'));
    }

    #[Test]
    public function aStatedVersionSaysWhatItDidToTheAnswerBesideTheSnapshotItWasReadFrom(): void
    {
        $result = Registry::call('typo3_component_lookup', ['query' => 'badge', 'targetVersion' => '14.3']);

        self::assertStringContainsString('Answered for TYPO3 v14', $result->text);
        self::assertStringContainsString('not which versions an entry holds on', $result->text);
        self::assertStringNotContainsString(
            'Answered for TYPO3',
            Registry::call('typo3_component_lookup', ['query' => 'badge'])->text,
            'nobody stated a version, so nothing is claimed about one',
        );
    }

    #[Test]
    public function aComponentNotVerifiedOnTheTargetIsDeclinedRatherThanHandedOver(): void
    {
        // The skew sentence named the difference without acting on it. Markup
        // taken from one revision either holds on the stated version or it does
        // not, and the answer for "does not" is to decline it.
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '13.4']);

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
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator', 'targetVersion' => '14.3']);

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
        $result = Registry::call('typo3_component_lookup', ['query' => 'status indicator']);

        self::assertNull($result->data['targetVersion']);
        self::assertSame('status-indicator', $result->data['components'][0]['name']);
        self::assertSame([], $result->data['withheld']);
    }

    #[Test]
    public function theCatalogSaysHowMuchOfItWasVerifiedOnAStatedVersion(): void
    {
        $result = Registry::call('typo3_catalog_scope', ['targetVersion' => '14']);

        self::assertSame(14, $result->data['targetVersion']);
        self::assertSame(count(Components::load()), $result->data['verifiedCount']);
        self::assertSame([], $result->data['withheld']);

        // The custom-property contract the catalog describes arrived after
        // 12.4, so most of it is not verified there and the scope says so.
        $onTwelve = Registry::call('typo3_catalog_scope', ['targetVersion' => '12.4']);
        self::assertLessThan(count(Components::load()), $onTwelve->data['verifiedCount']);
        self::assertStringContainsString('Withheld for TYPO3 v12', $onTwelve->text);
    }

    #[Test]
    public function theCatalogScopeSeparatesEntryValidityFromItsSourceCheckout(): void
    {
        $result = Registry::call('typo3_catalog_scope', ['targetVersion' => '14.3']);

        self::assertStringContainsString('For TYPO3 v14', $result->text);
        self::assertStringContainsString('Each component entry owns this validity range', $result->text);
        self::assertStringContainsString('Checkout branch: main', $result->text);
        self::assertLessThan(
            strpos($result->text, 'Checkout branch: main'),
            strpos($result->text, 'For TYPO3 v14'),
            'the requested version is stated before the provenance version',
        );
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
        // caller or from none, and both are silent — bin/cli catalog:check is what
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
    public function everyEntryWithADemoRecordsWhatItRead(): void
    {
        // The binding is derived from names, so a demo rewritten around the same
        // classes reads as unchanged — bin/cli catalog:check compares these
        // digests against the checkouts, this holds them to versions.json and to
        // the entries that have a demo to digest at all.
        $majors = Versions::majors();
        foreach (Catalogs::read('components') as $entry) {
            $digests = $entry['markupDigests'] ?? [];
            if (($entry['demoPath'] ?? '') === '') {
                self::assertSame([], $digests, $entry['name'] . ' digests a demo it does not name');
                continue;
            }

            self::assertNotSame([], $digests, $entry['name'] . ' names a demo and records nothing it read there');
            foreach ($digests as $major => $digest) {
                self::assertContains((int) $major, $majors, $entry['name'] . ' digests a version this knowledge base does not cover');
                self::assertMatchesRegularExpression('/^[0-9a-f]{12}$/', (string) $digest, $entry['name'] . ' records something no digest produced');
            }
        }
    }

    #[Test]
    public function aDemoIsDigestedByWhatItSaysAboutTheComponent(): void
    {
        // What the digest has to notice: the same class names, arranged
        // differently. The names alone say nothing about the second template.
        $one = '<sg:example><span class="badge badge-default">Badge</span></sg:example>';
        $two = '<sg:example><div><span class="badge badge-default">Badge</span></div></sg:example>';

        self::assertNotSame(DemoMarkup::examples($one, 'badge'), DemoMarkup::examples($two, 'badge'));

        // And what it may not notice: a demo that renders the component through
        // a ViewHelper names it nowhere, so nothing there holds its markup.
        $viewHelper = '<sg:example><f:flashMessages queueIdentifier="styleguide.default" /></sg:example>';
        self::assertFalse(DemoMarkup::carries(implode("\n", DemoMarkup::examples($viewHelper, 'alert')), 'alert'));
        self::assertTrue(DemoMarkup::carries(implode("\n", DemoMarkup::examples($one, 'badge')), 'badge'));
    }

    #[Test]
    public function whetherAnExtensionIsPartOfTheCoreIsAnswerable(): void
    {
        // It was answered from memory in both directions in one session: a
        // community package cited as evidence of what the core does, and a
        // system extension nobody knew was there.
        $camino = Registry::call('typo3_system_extension_lookup', ['query' => 'typo3/theme-camino']);
        self::assertSame(1, $camino->data['matchCount']);
        self::assertSame('theme_camino', $camino->data['extensions'][0]['key']);
        self::assertNotSame('', $camino->data['extensions'][0]['shippedOn'], 'it is not shipped on every covered line');

        $contentBlocks = Registry::call('typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']);
        self::assertSame(0, $contentBlocks->data['matchCount']);
        self::assertStringContainsString('third-party', $contentBlocks->text, 'a miss is about the core, not about the package');
    }

    #[Test]
    public function aTargetVersionDecidesWhichExtensionsAreShipped(): void
    {
        $onThirteen = Registry::call('typo3_system_extension_lookup', ['query' => 'theme_camino', 'targetVersion' => '13.4']);
        self::assertSame(0, $onThirteen->data['matchCount'], 'the theme is not part of that line');

        $everything = Registry::call('typo3_system_extension_lookup', []);
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
    public function theCoresOwnWorkedExamplesAreIndexed(): void
    {
        // Three times in one session the real answer was a directory inside the
        // core repository, and all three times it was reached by accident. A
        // hint per subject fixes the subject it was written for; the index is
        // for the next one.
        $everything = Registry::call('typo3_reference_list', []);
        self::assertGreaterThan(0, $everything->data['matchCount']);

        $paths = array_column($everything->data['references'], 'path');
        self::assertContains('typo3/sysext/theme_camino', $paths);
        self::assertContains('typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example', $paths);

        // And the version decides, because a path that is not on that branch
        // costs a read and answers nothing.
        $onThirteen = Registry::call('typo3_reference_list', ['targetVersion' => '13.4']);
        self::assertNotContains('typo3/sysext/theme_camino', array_column($onThirteen->data['references'], 'path'));
    }

    #[Test]
    public function aWorkedExampleIsNamedBesideTheHintItIsAnExampleOf(): void
    {
        // The layout hint and the theme it was written from were two answers
        // that never met: the hint was read, the extension was found later by
        // being told about it.
        $result = Registry::call('typo3_architecture_lookup', [
            'task' => 'directory structure of a sitepackage extension',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('typo3/sysext/theme_camino', $result->text);
        self::assertStringContainsString('typo3_reference_list', $result->text);
    }

    #[Test]
    public function everyIndexedExampleSaysWhatItIsAnExampleOfAndWhereItIs(): void
    {
        $majors = Versions::majors();
        $hintIds = array_column(ArchitectureHints::load(), 'id');
        foreach (References::load() as $entry) {
            self::assertNotSame('', $entry['reference'], $entry['id'] . ' says nothing about itself');
            self::assertStringNotContainsString('..', $entry['path'], $entry['id'] . ' does not name a path in a checkout');
            if ($entry['hint'] !== null) {
                self::assertContains($entry['hint'], $hintIds, $entry['id'] . ' points at a hint that does not exist');
            }
            foreach (['since', 'until'] as $bound) {
                if ($entry[$bound] !== null) {
                    self::assertContains($entry[$bound], $majors, $entry['id'] . ' is bound to a version this knowledge base does not cover');
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
        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'packages/my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame(null, $result['domain'], 'a project path is not an EXT: reference');

        $result = Registry::call('typo3_translation_domain_lookup', [
            'path' => 'EXT:my_extension/Resources/Private/Language/NotYetWritten.xlf',
        ])->data;

        self::assertSame('my_extension.not_yet_written', $result['domain']);
    }

    #[Test]
    public function aPathThatNamesNoExtensionDerivesNoDomain(): void
    {
        $result = Registry::call('typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf'])->data;

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
