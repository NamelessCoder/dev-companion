<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Catalog\Components;
use Typo3CmsMcp\Catalog\Meta;
use Typo3CmsMcp\Catalog\TranslationDomain;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;

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
    public function aComponentCarriesEverySassFileItSpans(): void
    {
        $input = array_values(array_filter(Components::load(), static fn(array $c): bool => $c['name'] === 'input'))[0];

        // The form controls are one component split across four files; naming
        // only the first made the rest look like they were not part of it.
        self::assertContains('Build/Sources/Sass/component/forms/_form-text.scss', $input['sassPaths']);
        self::assertSame($input['sassPaths'][0], $input['sassPath'], 'sassPath stays the primary one');
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
