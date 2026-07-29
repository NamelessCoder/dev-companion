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
