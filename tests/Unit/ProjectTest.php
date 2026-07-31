<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Project;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;

/**
 * What the repository around the installation consists of.
 *
 * A recommendation is worth as much as its fit: a check that this project does
 * not declare does not exist here, whatever the core does with the same name.
 */
final class ProjectTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function theProjectIsDescribedFromItsFilesAlone(): void
    {
        $root = $this->composerProject('vendor', '13.4.33');
        $this->manifest($root, [
            'require' => ['php' => '^8.4', 'typo3/cms-core' => '^13.4'],
            'scripts' => ['t3g:cgl' => 'php-cs-fixer fix', 't3g:phpstan' => 'phpstan analyse'],
        ]);
        $this->site($root, 'events-site', [
            'base' => 'https://events.example/',
            'rootPageId' => 1,
            'dependencies' => ['acme/events-set'],
            'languages' => [['title' => 'Deutsch', 'languageId' => 0]],
        ]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('13.4.33', $project['typo3Version']);
        self::assertSame('^8.4', $project['phpConstraint']);
        self::assertSame('^13.4', $project['coreConstraint']);

        // The system extension is TYPO3's; the sitepackage is what this
        // repository is working on.
        self::assertSame(
            [['key' => 'my_sitepackage', 'path' => 'packages/my_sitepackage', 'origin' => Project::ORIGIN_PROJECT]],
            $project['extensions'],
        );

        self::assertSame('events-site', $project['sites'][0]['identifier']);
        self::assertSame(['acme/events-set'], $project['sites'][0]['sets']);
        self::assertSame(['Deutsch'], $project['sites'][0]['languages']);

        self::assertSame(
            ['composer t3g:cgl', 'composer t3g:phpstan'],
            array_column($project['commands'], 'command'),
        );
    }

    #[Test]
    public function aSiteConfigurationThatCannotBeParsedCostsThatSiteAndNoOther(): void
    {
        // Mid-edit, or with a placeholder a parser rejects. The other sites are
        // still the answer.
        $root = $this->composerProject();
        $this->site($root, 'good', ['base' => 'https://good.example/']);
        mkdir($root . '/config/sites/broken', 0o777, true);
        file_put_contents($root . '/config/sites/broken/config.yaml', "base: [unclosed\n\tmixed: tabs");
        Instance::discoverFrom($root);

        $identifiers = array_column(Project::describe()['sites'], 'identifier');
        self::assertContains('good', $identifiers);
        self::assertContains('broken', $identifiers, 'the site exists even when its configuration does not parse');
    }

    #[Test]
    public function withoutAnInstallationThereIsNoProjectToDescribe(): void
    {
        Instance::discoverFrom(null);

        self::assertNull(Project::describe());

        $result = Tools::call('typo3_project_scope', []);
        self::assertSame('nothing', $result->data['answeredBy']);
    }

    #[Test]
    public function theAnswerNamesTheCommandsThatExistHere(): void
    {
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => ['ci' => 'phpunit']]);
        Instance::discoverFrom($root);

        $text = Tools::call('typo3_project_scope', [])->text;

        self::assertStringContainsString('composer ci', $text);
        self::assertStringContainsString('runTests.sh suites do not', $text);
    }

    #[Test]
    public function aDeclaredCommandSaysWhetherRunningItChangesTheSources(): void
    {
        // Three recorded REVIEW-02 runs were told not to change files and ran
        // none of the fifteen commands they were offered — among them a
        // php-cs-fixer line and a phplint line that change nothing. A name
        // cannot carry that: cgl and cgl:ci are the same tool one flag apart,
        // so the body is what is read.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => [
            'cgl' => ['php-cs-fixer --diff -v fix'],
            'cgl:ci' => ['php-cs-fixer --diff -v --dry-run fix'],
            'phpstan' => ['phpstan analyze --configuration Build/phpstan.neon'],
            'phpstan:baseline' => ['phpstan analyze --generate-baseline Build/phpstan-baseline.neon'],
            'test:php:lint' => ['phplint'],
            'test:php:unit' => ['phpunit -c Build/phpunit-unit.xml'],
            'lint' => ['@test:php:lint'],
            'test' => ['@test:php:lint', '@test:php:unit'],
            'set-version' => ['extension-helper version:set'],
        ]]);
        file_put_contents($root . '/package.json', json_encode([
            'scripts' => ['lint:js' => 'eslint Resources/Private', 'build' => 'vite build'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $runs = array_column(Project::describe()['commands'], 'runs', 'command');

        self::assertSame([
            'composer cgl' => Project::RUNS_AS_CHANGE,
            'composer cgl:ci' => Project::RUNS_AS_CHECK,
            'composer phpstan' => Project::RUNS_AS_CHECK,
            'composer phpstan:baseline' => Project::RUNS_AS_CHANGE,
            'composer test:php:lint' => Project::RUNS_AS_CHECK,
            // It runs the project's own code, and no declaration says what that
            // writes. Undeclared is not a quiet no.
            'composer test:php:unit' => Project::RUNS_UNDECLARED,
            // A reference is followed, so a wrapper is worth what it wraps —
            // and a script that reaches one undeclared line is undeclared.
            'composer lint' => Project::RUNS_AS_CHECK,
            'composer test' => Project::RUNS_UNDECLARED,
            'composer set-version' => Project::RUNS_AS_CHANGE,
            'npm run lint:js' => Project::RUNS_AS_CHECK,
            'npm run build' => Project::RUNS_AS_CHANGE,
        ], $runs);

        $text = Tools::call('typo3_project_scope', [])->text;
        self::assertStringContainsString('composer cgl:ci (composer.json) — check: php-cs-fixer --diff -v --dry-run fix', $text);
        self::assertStringContainsString('A task told not to change files can run the checks and nothing else', $text);
    }

    #[Test]
    public function aCommandThatDeclaresNothingReadableIsNotCalledSafe(): void
    {
        // The failure that matters is the other direction: a body nobody can
        // read reported as a check would send a review into a script that
        // rewrites the checkout it was told to leave alone.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => [
            'shell' => ["find src -name '*.php' -print0 | xargs -0 -n1 php -l"],
            'handler' => 'Acme\\Composer\\Scripts::install',
            'console' => ['@php vendor/bin/typo3 extension:setup'],
            'itself' => ['@itself'],
            'linted' => ['@php vendor/bin/phplint'],
        ]]);
        Instance::discoverFrom($root);

        self::assertSame(
            [
                'composer shell' => Project::RUNS_UNDECLARED,
                'composer handler' => Project::RUNS_UNDECLARED,
                'composer console' => Project::RUNS_UNDECLARED,
                // A script that references itself ends, and ends undeclared.
                'composer itself' => Project::RUNS_UNDECLARED,
                // @php is which PHP, not what is run: the tool behind it is
                // still read.
                'composer linted' => Project::RUNS_AS_CHECK,
            ],
            array_column(Project::describe()['commands'], 'runs', 'command'),
        );
    }

    #[Test]
    public function aPatchedDependencyIsPartOfWhatThisProjectIs(): void
    {
        // A patched package does not behave as its version says, and the next
        // composer update either reapplies the patch or fails on it. Nothing
        // else about this project matters more to an upgrade.
        $root = $this->composerProject();
        $this->manifest($root, ['extra' => ['patches' => [
            'typo3/cms-core' => ['Keep the old redirect behaviour' => 'patches/core-redirects.patch'],
        ]]]);
        Instance::discoverFrom($root);

        self::assertSame(
            [['package' => 'typo3/cms-core', 'description' => 'Keep the old redirect behaviour', 'file' => 'patches/core-redirects.patch']],
            Project::describe()['patches'],
        );
        self::assertStringContainsString('Patched dependencies', Tools::call('typo3_project_scope', [])->text);
    }

    #[Test]
    public function whatAnExtensionRegistersIsReadFromItsOwnFiles(): void
    {
        // The project scope names an extension and its path. A maintenance
        // question is about what is inside it, and all of it is declarative.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Configuration/TCA/tx_acme_event.php', "<?php\nreturn ['ctrl' => []];\n");
        // Numbered, because that is what fixes the order overrides load in —
        // so the file name says nothing and the table has to be read.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/102_tt_content.php',
            "<?php\n\$GLOBALS['TCA']['tt_content']['columns']['header']['label'] = 'x';\n"
        );
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/900_pages.php',
            "<?php\nExtensionManagementUtility::addToAllTCAtypes(\n 'pages',\n '--div--;Acme',\n);\n"
        );
        $this->declare($extension . '/Configuration/Icons.php', "<?php\nreturn [\n 'acme-event' => ['provider' => 'x'],\n];\n");
        $this->declare(
            $extension . '/Configuration/Backend/Modules.php',
            "<?php\nreturn [\n 'acme_events' => ['parent' => 'web', 'labels' => 'acme.modules.events'],\n];\n"
        );
        $this->declare(
            $extension . '/Configuration/RequestMiddlewares.php',
            "<?php\nreturn [\n 'frontend' => [\n  'acme/tracking' => ['target' => 'X'],\n ],\n];\n"
        );
        $this->declare(
            $extension . '/Configuration/Services.yaml',
            "services:\n  Acme\\\\SitePackage\\\\Processor:\n    tags:\n      - name: 'data.processor'\n        identifier: 'acme-events'\n"
        );
        $this->declare($extension . '/Configuration/Sets/AcmeEvents/config.yaml', "name: acme/events-set\n");
        $this->declare($extension . '/Resources/Private/Partials/Event.fluid.html', '');
        $this->declare($extension . '/Classes/DataProcessing/EventProcessor.php', "<?php\n");
        $this->declare($extension . '/ext_localconf.php', "<?php\n");
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'my_sitepackage']);

        self::assertSame(['tx_acme_event'], $result->data['tcaTables']);
        self::assertSame(
            ['pages', 'tt_content'],
            $result->data['tcaOverrides'],
            'the table is read from what the file does, not from what it is called',
        );
        self::assertSame(['acme-event'], $result->data['icons']);
        self::assertSame(['acme_events'], $result->data['backendModules']);
        self::assertSame(['acme/tracking'], $result->data['middlewares']);
        self::assertSame(['data.processor'], $result->data['serviceTags']);
        self::assertSame([['name' => 'acme/events-set', 'path' => 'Configuration/Sets/AcmeEvents/']], $result->data['siteSets']);
        self::assertSame(['Resources/Private/Partials/'], $result->data['fluidRoots']);
        self::assertSame([['kind' => 'DataProcessing', 'files' => 1]], $result->data['classes']);
        self::assertContains('ext_localconf.php', $result->data['files']);
        self::assertSame(Project::ORIGIN_PROJECT, $result->data['origin']);

        // What is declared is here; what ext_localconf.php does at runtime is
        // not, and the answer says so rather than letting it be assumed.
        self::assertStringContainsString('not what it does at runtime', $result->text);
    }

    #[Test]
    public function theContentElementsAnExtensionAddsAreNamedRatherThanPointedAt(): void
    {
        // "It extends tt_content" says where they are registered. What a
        // sitepackage question is about is which ones — and both item shapes
        // are in use, because an extension is written for the line it supports.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/102_tt_content.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:teaser',
                    'value' => 'acme_teaser',
                    'icon' => 'acme-teaser',
                ], 'header', 'after');
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:slider',
                    'acme_slider',
                    'acme-slider',
                ]);
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'label' => 'Built somewhere else',
                    'value' => self::CTYPE,
                ]);
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'header_layout', [
                    'label' => 'Quiet',
                    'value' => 'acme_quiet',
                ]);
                PHP
        );
        // Which template one renders through is the next question after which
        // ones there are, and both TypoScript shapes are in use.
        $this->declare(
            $extension . '/Configuration/Sets/AcmeSite/setup.typoscript',
            <<<'TYPOSCRIPT'
                tt_content.acme_teaser =< lib.contentElement
                tt_content.acme_teaser {
                    templateName = Teaser
                }
                tt_content {
                    acme_quiet =< lib.contentElement
                    acme_quiet.templateName = Quiet
                }
                TYPOSCRIPT
        );
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                ['identifier' => 'acme_slider', 'templateName' => null, 'source' => null],
                ['identifier' => 'acme_teaser', 'templateName' => 'Teaser', 'source' => 'Configuration/Sets/AcmeSite/setup.typoscript'],
            ],
            $result->data['contentElements'],
            'both item shapes are read, and a value that is no literal is left out rather than guessed',
        );
        self::assertSame(['tt_content'], $result->data['tcaOverrides']);
        // An item of another field is a value in that field, not a content
        // element — not even when the TypoScript renders one under that name.
        self::assertNotContains('acme_quiet', array_column($result->data['contentElements'], 'identifier'));
        self::assertStringContainsString('acme_teaser — renders through Teaser', $result->text);
        self::assertStringContainsString('at runtime, takes from a constant', $result->text);
    }

    #[Test]
    public function aContentElementRegisteredWithAddRecordTypeIsFoundAsWell(): void
    {
        // The call that carries no table in front of it: since 13.4 the
        // registration is one addRecordType() whose table argument is the fifth
        // and defaults to tt_content — and it is written in a file per element,
        // so the file name is the one thing that must not be believed.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_hero_carousel.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addRecordType(
                    [
                        'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:hero_carousel',
                        'value' => 'acme_hero_carousel',
                        'icon' => 'acme-hero-carousel',
                        'group' => 'default',
                    ],
                    '--div--;General,header,acme_slides',
                );
                PHP
        );
        // The same call registers record types of other tables, and those are
        // page types rather than content elements.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/pages_landing.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addRecordType(
                    ['label' => 'Landing page', 'value' => '117', 'icon' => 'acme-landing'],
                    '--div--;General,title',
                    [],
                    '',
                    'pages',
                );
                PHP
        );
        $this->declare(
            $extension . '/Configuration/Sets/AcmeSite/setup.typoscript',
            "tt_content.acme_hero_carousel =< lib.contentElement\ntt_content.acme_hero_carousel.templateName = HeroCarousel\n"
        );
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [['identifier' => 'acme_hero_carousel', 'templateName' => 'HeroCarousel', 'source' => 'Configuration/Sets/AcmeSite/setup.typoscript']],
            $result->data['contentElements'],
        );
        self::assertSame(
            ['pages', 'tt_content'],
            $result->data['tcaOverrides'],
            'the table comes from the call, so the per-element file name is never mistaken for one',
        );
    }

    #[Test]
    public function anIdentifierThatTookADetourThroughAVariableIsStillRead(): void
    {
        // A forward review of a real sitepackage on 2026-07-31 was told the
        // extension had three content elements. It had four: the fourth wrote
        // `$contentType = '…'` at the top of its override and used the variable
        // in the item, and the parser only saw literals. A tool that answers
        // three when there are four is worse than one that declines — the
        // session that trusts it concludes the template is dead code.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_hero_carousel.php',
            <<<'PHP'
                <?php
                $contentType = 'acme_hero_carousel';
                ExtensionManagementUtility::addTCAcolumns('tt_content', ['acme_slides' => []]);
                ExtensionManagementUtility::addRecordType(
                    [
                        'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:carousel',
                        'value' => $contentType,
                        'icon' => 'acme-hero-carousel',
                    ],
                    '--div--;General,header,acme_slides',
                );
                PHP
        );
        // Reassigned, so what it holds at the call depends on the order the file
        // runs in — which is the one thing reading cannot establish.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_reused.php',
            <<<'PHP'
                <?php
                $type = 'acme_first';
                ExtensionManagementUtility::addRecordType(['label' => 'First', 'value' => $type], 'header');
                $type = 'acme_second';
                ExtensionManagementUtility::addRecordType(['label' => 'Second', 'value' => $type], 'header');
                PHP
        );
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'my_sitepackage']);

        self::assertSame(
            ['acme_hero_carousel'],
            array_column($result->data['contentElements'], 'identifier'),
            'a single-assignment string variable resolves, and a reassigned one is still declined',
        );
    }

    #[Test]
    public function whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut(): void
    {
        // Three forward reviews of the same site package missed that it has no
        // manual, because there is no file to trip over: `find` cannot list a
        // document nobody wrote. The same three read its XLF headers and none
        // reported the German source language. Both are facts about the files,
        // both are cheap, and neither is discoverable by reading further — so
        // they are told rather than left to be found.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Tests/Unit/SomeTest.php', "<?php\n");
        $this->declare(
            $extension . '/Resources/Private/Language/locallang.xlf',
            '<?xml version="1.0"?><xliff version="1.0"><file source-language="de" datatype="plaintext"></file></xliff>',
        );
        $this->declare(
            $extension . '/Resources/Private/Language/de.locallang.xlf',
            '<?xml version="1.0"?><xliff version="1.0"><file source-language="en" target-language="de"></file></xliff>',
        );
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                'manual' => null,
                'readme' => null,
                'tests' => ['Unit'],
                'languageFiles' => [[
                    'path' => 'Resources/Private/Language/locallang.xlf',
                    'sourceLanguage' => 'de',
                    // The prefixed file is this one's translation, not a file of
                    // its own — which is what makes a missing one visible.
                    'translations' => ['de'],
                ]],
            ],
            $result->data['artifacts'],
        );
        self::assertStringContainsString('Ships: manual none, readme none, tests Unit', $result->text);
        self::assertStringContainsString('source-language de, translated into de', $result->text);
        // The fact is here; whether it is allowed to be German is not.
        self::assertStringContainsString('not what it should declare', $result->text);
    }

    #[Test]
    public function anExtensionTheInstallationDoesNotHaveIsAMissWithTheKeysItDoes(): void
    {
        $root = $this->composerProject();
        Instance::discoverFrom($root);

        $result = Tools::call('typo3_extension_scope', ['extension' => 'news']);

        self::assertNull($result->data['path']);
        self::assertContains('my_sitepackage', $result->data['installed']);
        self::assertStringContainsString('my_sitepackage', $result->text);
    }

    private function declare(string $file, string $content): void
    {
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0o777, true);
        }
        file_put_contents($file, $content);
    }

    /** @param array<string, mixed> $manifest */
    private function manifest(string $root, array $manifest): void
    {
        $existing = is_file($root . '/composer.json')
            ? (array) json_decode((string) file_get_contents($root . '/composer.json'), true)
            : [];
        file_put_contents(
            $root . '/composer.json',
            json_encode($manifest + $existing + ['name' => 'acme/site'], JSON_THROW_ON_ERROR),
        );
    }

    /** @param array<string, mixed> $configuration */
    private function site(string $root, string $identifier, array $configuration): void
    {
        $path = $root . '/config/sites/' . $identifier;
        mkdir($path, 0o777, true);
        file_put_contents($path . '/config.yaml', \Symfony\Component\Yaml\Yaml::dump($configuration, 4));
    }
}
