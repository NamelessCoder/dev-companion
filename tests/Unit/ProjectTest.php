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
