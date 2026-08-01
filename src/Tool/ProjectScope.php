<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Project;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\Unanswered;
use Typo3CmsMcp\ToolResult;

/**
 * What the repository around the installation consists of.
 *
 * The knowledge base describes TYPO3; this describes the project, because a
 * recommendation is only worth as much as its fit: a check that is not declared
 * here does not exist here, whatever the core does with the same name.
 */
final class ProjectScope extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_project_scope';
    }

    public static function description(): string
    {
        return 'Describe the project around the TYPO3 installation this server was started in: its TYPO3 and PHP constraints, the extensions that are its own rather than TYPO3\'s, the sites it configures with the site sets each depends on, and the commands it declares in composer.json and package.json, each with what running it does to the sources: a check that hands the code back as it was, a change that rewrites something, or unknown where the declared body does not say. Read from files only — no console, no database — so it answers on a fresh clone. Call it before recommending or running a check: the commands listed here are the ones that exist in this repository, and the ones marked check are the ones a task told not to change files can run.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'root' => Schema::nullableString('Absolute path of the project. Null when there is no installation to describe.'),
            'kind' => Schema::string('core-checkout or composer-project.'),
            'typo3Version' => Schema::nullableString('The TYPO3 version installed here, read from the core package.'),
            'phpConstraint' => Schema::nullableString('What composer.json requires of PHP.'),
            'coreConstraint' => Schema::nullableString('What it requires of typo3/cms-core.'),
            'extensions' => Schema::listOf(Schema::object([
                'key' => Schema::string(),
                'path' => Schema::string('Relative to the project root.'),
                'origin' => ['type' => 'string', 'enum' => ['project', 'third-party', 'fixture'], 'description' => 'project: inside the repository, so what it is working on. third-party: installed as a dependency. fixture: shipped by the repository\'s test setup, below a Tests/ directory, so it exists to be loaded by a suite rather than developed.'],
            ], ['key', 'path', 'origin']), 'Extensions that are not TYPO3 system extensions.'),
            'sites' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'base' => Schema::string(),
                'rootPageId' => ['type' => ['integer', 'null']],
                'sets' => Schema::listOf(Schema::string(), 'The site sets this site depends on, by their composer-style name.'),
                'languages' => Schema::listOf(Schema::string()),
            ], ['identifier', 'base', 'rootPageId', 'sets', 'languages'])),
            'commands' => Schema::listOf(Schema::object([
                'command' => Schema::string('Ready to run in this repository.'),
                'source' => Schema::string('composer.json or package.json.'),
                'declares' => Schema::string('The body the manifest declares for it, lines joined with &&.'),
                'runs' => ['type' => 'string', 'enum' => ['check', 'change', 'unknown'], 'description' => 'What running it does to the sources, read off the body rather than by running it. check: it reports and hands the code back as it was, so a task told not to change files can run it — it may still write a cache of its own. change: it rewrites something. unknown: the body does not say, which is what a test suite is, because it runs the project\'s own code.'],
            ], ['command', 'source', 'declares', 'runs']), 'What this repository declares. A check that is not here does not exist here.'),
            'patches' => Schema::listOf(Schema::object([
                'package' => Schema::string('The dependency being patched.'),
                'description' => Schema::string('What the patch is for, where composer.json says.'),
                'file' => Schema::string('The patch file, relative to the project root.'),
            ], ['package', 'description', 'file']), 'Patches from extra.patches. A patched package does not behave as its version says.'),
            'answeredBy' => Schema::answeredBy(),
            'unavailable' => Schema::unavailable(),
        ], ['root', 'extensions', 'sites', 'commands', 'patches', 'answeredBy']);
    }

    public static function answer(array $args): ToolResult
    {
        $project = Project::describe();
        if ($project === null) {
            return Unanswered::because(
                'no TYPO3 installation was found to describe',
                ['root' => null, 'extensions' => [], 'sites' => [], 'commands' => [], 'patches' => []],
            );
        }

        $lines = [sprintf(
            '%s — %s, TYPO3 %s, PHP %s',
            $project['root'],
            $project['kind'],
            $project['typo3Version'] ?? 'version unknown',
            $project['phpConstraint'] ?? 'unconstrained',
        )];

        $lines[] = '';
        $lines[] = $project['extensions'] === []
            ? 'Extensions: none beyond TYPO3\'s own.'
            : 'Extensions that are not TYPO3\'s own:';
        foreach ($project['extensions'] as $extension) {
            $lines[] = sprintf('- %s (%s) — %s', $extension['key'], $extension['origin'], $extension['path']);
        }

        $lines[] = '';
        $lines[] = $project['sites'] === []
            ? 'Sites: none configured below config/sites/.'
            : 'Sites, with the sets each one depends on:';
        foreach ($project['sites'] as $site) {
            $lines[] = sprintf(
                '- %s%s%s%s',
                $site['identifier'],
                $site['base'] === '' ? '' : ' at ' . $site['base'],
                $site['rootPageId'] === null ? '' : ', root page ' . $site['rootPageId'],
                $site['sets'] === [] ? ', no sets' : ', sets: ' . implode(', ', $site['sets']),
            );
        }

        $lines[] = '';
        $lines[] = $project['commands'] === []
            ? 'This repository declares no commands of its own in composer.json or package.json. What to run is '
                . 'then whatever its CI configuration does.'
            : 'Commands this repository declares — these exist here, the core\'s runTests.sh suites do not. '
                . 'What each one does to the sources is read off its body, never by running it: a check reports '
                . 'and leaves them as they are, a change rewrites something, and unknown is a body that does not '
                . 'say — a test suite runs the project\'s own code, and no declaration covers that. A task told '
                . 'not to change files can run the checks and nothing else. A check may still write a cache of '
                . 'its own; what it does not do is hand the code back different.';
        foreach ($project['commands'] as $command) {
            $lines[] = sprintf(
                '- %s (%s) — %s: %s',
                $command['command'],
                $command['source'],
                $command['runs'],
                $command['declares'],
            );
        }

        if ($project['patches'] !== []) {
            $lines[] = '';
            $lines[] = 'Patched dependencies — these packages do not behave as their version says, and the next '
                . 'composer update either reapplies the patch or fails on it:';
            foreach ($project['patches'] as $patch) {
                $lines[] = sprintf(
                    '- %s: %s (%s)',
                    $patch['package'],
                    $patch['description'] === '' ? 'no description given' : $patch['description'],
                    $patch['file'],
                );
            }
        }

        return ToolResult::create(implode("\n", $lines), $project + ['answeredBy' => 'packages']);
    }
}
