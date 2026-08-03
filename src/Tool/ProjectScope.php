<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Project;
use Typo3CmsMcp\Installation\Typo3Cli;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Result\Unsupported;

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
        return 'Describe the project around the TYPO3 installation this server was started in: its TYPO3 and PHP constraints, the extensions that are its own rather than TYPO3\'s, the sites it configures with the site sets each depends on, and the commands it declares in composer.json and package.json — each marked a check that hands the code back as it was, a change that rewrites something, or unknown where the declared body does not say. Read from files only, no console and no database, so it answers on a fresh clone. It also names the environment the repository configures to run itself in: a DDEV project states the PHP its container runs, which is a different interpreter from the caller\'s shell and where the commands below are run. Call it before recommending or running a check — these are the commands that exist in this repository, and the ones marked check are what a task told not to change files may run.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'root' => Schema::nullableString('Absolute path of the project. Null when there is no installation to describe.'),
            'kind' => Schema::string('core-checkout or composer-project.'),
            'typo3Version' => Schema::nullableString('The TYPO3 version installed here, read from the core package.'),
            'phpConstraint' => Schema::nullableString('What composer.json requires of PHP. What the project declares, not what runs it — see environment.'),
            'coreConstraint' => Schema::nullableString('What it requires of typo3/cms-core.'),
            'environment' => [
                'type' => ['object', 'null'],
                'description' => 'The environment this repository configures to run itself in, read from that environment\'s own files. Null means nothing here configures one that this server reads — .ddev/config.yaml and TYPO3_MCP_CONSOLE are what it reads — so the commands below run wherever the caller runs them.',
                'properties' => [
                    'via' => ['type' => 'string', 'enum' => ['ddev', 'override'], 'description' => 'ddev: the repository carries a .ddev/config.yaml. override: nothing in the files says so, and TYPO3_MCP_CONSOLE names a command that reaches this installation somewhere other than the caller\'s own shell.'],
                    'php' => Schema::nullableString('The PHP that environment runs, where its files state it. Null is not "none": a DDEV project that states no php_version gets the default of the installed DDEV, and an environment named by TYPO3_MCP_CONSOLE states its version nowhere this server can read. typo3_server_scope reports the version the console actually answers on.'),
                    'source' => Schema::string('Where this was read: the .ddev config file that states the version last, or TYPO3_MCP_CONSOLE.'),
                    'entered' => ['type' => 'boolean', 'description' => 'True when this server is already running inside that environment, so its shell is that environment and a declared command needs nothing in front of it.'],
                ],
                'required' => ['via', 'php', 'source', 'entered'],
            ],
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
                'command' => Schema::string('As this repository declares it. Where environment is not null, it is run inside that environment rather than in the caller\'s shell.'),
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
        ], ['root', 'environment', 'extensions', 'sites', 'commands', 'patches', 'answeredBy'], []);
    }

    public static function answer(array $args): ToolResult
    {
        $project = Project::describe();
        if ($project === null) {
            return Unsupported::because(
                'no TYPO3 installation was found to describe',
            );
        }

        $lines = [sprintf(
            '%s — %s, TYPO3 %s, PHP %s%s',
            $project['root'],
            $project['kind'],
            $project['typo3Version'] ?? 'version unknown',
            $project['phpConstraint'] ?? 'unconstrained',
            self::runtime($project['environment']),
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
            : 'Commands this repository declares — these exist here, the core\'s testing suites do not. '
                . 'What each one does to the sources is read off its body, never by running it: a check reports '
                . 'and leaves them as they are, a change rewrites something, and unknown is a body that does not '
                . 'say — a test suite runs the project\'s own code, and no declaration covers that. A task told '
                . 'not to change files can run the checks and nothing else. A check may still write a cache of '
                . 'its own; what it does not do is hand the code back different.';
        if ($project['commands'] !== []) {
            $lines[] = self::whereTheyRun($project['environment']);
        }
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

    /**
     * What the opening line says about the second PHP, beside the declared one.
     *
     * Beside rather than instead: the constraint is what the project accepts
     * and the environment is what it gets, and a review that holds the first
     * against the interpreter its own shell happens to have has compared two
     * machines. Empty where there is one machine, so the line an ordinary
     * project answers with does not change.
     *
     * @param array{via: string, php: ?string, source: string, entered: bool}|null $environment
     */
    private static function runtime(?array $environment): string
    {
        if ($environment === null) {
            return '';
        }
        if ($environment['via'] === Typo3Cli::VIA_OVERRIDE) {
            return sprintf(' declared, and run in the environment %s names, whose PHP nothing here states', Typo3Cli::CONSOLE_VARIABLE);
        }
        if ($environment['php'] === null) {
            return sprintf(
                ' declared, and run in DDEV, which %s states no php_version for — so the installed DDEV\'s own default'
                    . ' applies and typo3_server_scope is what reports the version its console answers on',
                $environment['source'],
            );
        }

        return sprintf(
            ' declared and %s in DDEV%s',
            $environment['php'],
            $environment['entered'] ? ', which this server is already inside' : '',
        );
    }

    /**
     * Where the commands just listed are run, which the list itself never said.
     *
     * `skills/base.md` sends every task to run the checks this list holds, and
     * a declared `composer test:unit` put on the caller's own shell in a
     * containerised project is a different interpreter from the one the project
     * is built for — which is the finding `feedback/2026-07-31-193611` reported
     * as a version mismatch that blocked nothing.
     *
     * @param array{via: string, php: ?string, source: string, entered: bool}|null $environment
     */
    private static function whereTheyRun(?array $environment): string
    {
        if ($environment === null) {
            // Said rather than left out. An answer that names no environment
            // reads as "there is none" whether this looked or not, so it says
            // what it looked at and the reader can tell the two apart.
            return 'Nothing in this repository configures an environment of its own — .ddev/config.yaml and '
                . Typo3Cli::CONSOLE_VARIABLE . ' are what this reads — so these run wherever you run them.';
        }
        if ($environment['via'] === Typo3Cli::VIA_OVERRIDE) {
            return sprintf(
                'They are run in the environment %s names for this installation, not in the shell you have, and '
                    . 'nothing readable here says which PHP that is — typo3_server_scope names the command it was given.',
                Typo3Cli::CONSOLE_VARIABLE,
            );
        }
        if ($environment['entered']) {
            return 'They are run in the DDEV project this repository configures, and this server is already inside '
                . 'it, so they are run as they are written here.';
        }

        return sprintf(
            'They are run in the DDEV project this repository configures, not in the shell you have: "ddev composer '
                . '<name>" for a composer script, "ddev exec <command>" for the rest. Run one directly and it runs on '
                . 'whatever PHP this machine carries%s, which is not what the project is built for.',
            $environment['php'] === null ? '' : ' rather than on ' . $environment['php'],
        );
    }
}
