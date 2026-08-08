<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What the repository around the installation consists of.
 *
 * The knowledge base describes TYPO3; this describes the project, because a
 * recommendation is only worth as much as its fit: a check that is not declared
 * here does not exist here, whatever the core does with the same name.
 */
final class ProjectDescribe extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_project_describe';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages];
    }

    public static function description(): string
    {
        return 'Describe the project around the TYPO3 installation this server was started in: its TYPO3 and PHP constraints, including the PHP floor the installed core requires and not only the one this project declares, the extensions that are its own rather than TYPO3\'s, the sites it configures with the site sets each depends on, and the commands it declares in composer.json and package.json — each marked a check that hands the code back as it was, a change that rewrites something, or unknown where the declared body does not say. Read from files only, no console and no database, so it answers on a fresh clone. It also names the environment the repository configures to run itself in: a DDEV project states the PHP its container runs, which is a different interpreter from the caller\'s shell and where the commands below are run, plus what that environment runs without being asked — each hook as the stage it fires at and the command it runs, and the pull recipes its database and files come from. Call it before booting such a project or before recommending or running a check — these are the commands that exist in this repository, and the ones marked check are what a task told not to change files may run.';
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
            'corePhpConstraint' => Schema::nullableString('What the installed typo3/cms-core requires of PHP, out of that package\'s own composer.json — the lowest a package here may declare it supports. Neither of the other two PHP numbers: not what this project declares, and not what environment.php runs. Not derivable from the TYPO3 major either — v13.4 and v14.3 both require ^8.2, v12.4 requires ^8.1. Null where no core package was found to read.'),
            'environment' => [
                'type' => ['object', 'null'],
                'description' => 'The environment this repository configures to run itself in, read from that environment\'s own files. Null means nothing here configures one that this server reads — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what it reads — so the commands below run wherever the caller runs them.',
                'properties' => [
                    'via' => ['type' => 'string', 'enum' => ['ddev', 'override'], 'description' => 'ddev: the repository carries a .ddev/config.yaml. override: nothing in the files says so, and TYPO3_DEV_COMPANION_CONSOLE names a command that reaches this installation somewhere other than the caller\'s own shell.'],
                    'php' => Schema::nullableString('The PHP that environment runs, where its files state it. Null is not "none": a DDEV project that states no php_version gets the default of the installed DDEV, and an environment named by TYPO3_DEV_COMPANION_CONSOLE states its version nowhere this server can read. typo3_server_scope reports the version the console actually answers on.'),
                    'source' => Schema::string('Where this was read: the .ddev config file that states the version last, or TYPO3_DEV_COMPANION_CONSOLE.'),
                    'entered' => ['type' => 'boolean', 'description' => 'True when this server is already running inside that environment, so its shell is that environment and a declared command needs nothing in front of it.'],
                    'hooks' => Schema::listOf(Schema::object([
                        'stage' => Schema::string('The DDEV stage it fires at: post-start, post-import-db, pre-pull and the rest.'),
                        'command' => Schema::string('What that stage runs, as the file states it. A block of several lines is joined with ";", which is what the shell does with it.'),
                        'service' => Schema::nullableString('The container it runs in, "web" where the task names none. Null means it runs on the host instead, which is what an exec-host task is.'),
                    ], ['stage', 'command', 'service']), 'What this environment runs without being asked, from .ddev/config.yaml and every .ddev/config.*.yaml beside it. The commands list is what a caller may run; these fire on their own at the stage each names, so an environment that installs dependencies on start and updates the schema on import says so here. Empty means those files declare no hooks. Unmarked, unlike the commands: runs says whether a caller may run something, and a hook is not the caller\'s to run.'),
                    'providers' => Schema::listOf(Schema::object([
                        'name' => Schema::string('What to pass: "ddev pull <name>".'),
                        'source' => Schema::string('The recipe file, relative to the project root.'),
                        'operations' => Schema::listOf(Schema::string(), 'pull, push, or both — which of the two the recipe declares commands for. A recipe with no push commands is one you cannot push upstream with.'),
                    ], ['name', 'source', 'operations']), 'The pull and push recipes below .ddev/providers/ that this repository wrote, which is where its database and files come from. DDEV writes its own recipes into every project and marks them #ddev-generated; those are left out, because they say what DDEV puts everywhere rather than what this project decided.'),
                ],
                'required' => ['via', 'php', 'source', 'entered', 'hooks', 'providers'],
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
            'guides' => Schema::listOf(Schema::object([
                'id' => Schema::string('What typo3_rule_lookup takes as documentId to return the whole document.'),
                'title' => Schema::string(),
            ], ['id', 'title']), 'The whole procedures this server carries, named here because this is the call every task starts with. They are also served as typo3://guides resources, and a client that lists no resources renders none of them — four sessions in one week finished without learning they exist. Each is one typo3_rule_lookup call by documentId, which needs no resource list; a search over sections answers a question and never hands one of these over whole.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['root', 'environment', 'extensions', 'sites', 'commands', 'patches', 'guides', 'answeredBy'], []);
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
            '%s — %s, TYPO3 %s, PHP %s%s%s',
            $project['root'],
            $project['kind'],
            $project['typo3Version'] ?? 'version unknown',
            $project['phpConstraint'] ?? 'unconstrained',
            self::runtime($project['environment']),
            self::floor($project['corePhpConstraint']),
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
                . 'then whatever its CI configuration does.' . self::suites($project['kind'])
            : 'Commands this repository declares — these exist here, the core\'s testing suites do not.'
                . self::suites($project['kind'])
                . ' What each one does to the sources is read off its body, never by running it: a check reports '
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

        foreach (self::lifecycle($project['environment']) as $line) {
            $lines[] = $line;
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

        $guides = self::guides();
        foreach ($guides['lines'] as $line) {
            $lines[] = $line;
        }

        return ToolResult::create(
            implode("\n", $lines),
            $project + ['guides' => $guides['records'], 'answeredBy' => 'packages'],
        );
    }

    /**
     * The whole procedures this server carries, named where every task starts.
     *
     * Four sessions in one week finished without learning they exist. They are
     * served as `typo3://guides` resources and a client that lists no resources
     * renders none of them; `typo3_server_scope` names them and is the call an
     * agent skips precisely when the task looks legible without orientation.
     * This tool is the one the instructions open every task with, so the
     * inventory is here and the detail stays there (`D-ANS-061`,
     * `feedback/2026-08-07-231203`).
     *
     * Last in the answer rather than first. What this tool is called for is the
     * installation, and an inventory that pushes those facts down has traded one
     * discovery problem for another.
     *
     * @return array{lines: array<int, string>, records: array<int, array{id: string, title: string}>}
     */
    private static function guides(): array
    {
        $records = [];
        $lines = ['', 'Whole procedures this server carries, each one typo3_rule_lookup with that documentId — no '
            . 'resource list needed, and none of them is answered by a search over sections:'];
        foreach (Documents::documents() as $document) {
            $records[] = ['id' => $document['id'], 'title' => $document['title']];
            $lines[] = sprintf('- %s — %s', $document['id'], $document['title']);
        }

        return ['lines' => $lines, 'records' => $records];
    }

    /**
     * Where the suites this list says are absent are run.
     *
     * The sentence named an absence and nothing that has it, and a session read
     * it and reached for a `Build/bin/phpunit` the checkout does not contain —
     * `D-ANS-031`. Only in a core checkout: everywhere else there are no core
     * suites to point at, and runTests.sh is not in that repository.
     */
    private static function suites(string $kind): string
    {
        if ($kind !== Instance::KIND_CORE_CHECKOUT) {
            return '';
        }

        return ' The core\'s suites are run by Build/Scripts/runTests.sh, which no manifest here declares. '
            . 'typo3_test_run_guide names the ones a change needs, with the invocation.';
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
     * @param array{via: string, php: ?string, source: string, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
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
     * The third PHP number, on the same line as the other two.
     *
     * A task that has to state what PHP a package supports needs the floor the
     * core requires, and the two numbers already here are the wrong ones to
     * take it from: the project's own constraint is what it accepts and may be
     * absent, the environment's is what the container happens to run. A session
     * asked to declare an extension's `php` had `phpConstraint: null` and a
     * DDEV container at 8.4 in this answer, and read `^8.2` out of the vendor
     * tree by hand (`feedback/2026-08-04-055638`, `D-KNW-055`). Here rather
     * than in a section of its own, because it is the line the first call of a
     * workflow is read for. Stated even where it repeats the project's own,
     * which is the ordinary case in a core checkout: a number the answer drops
     * where the two agree cannot be told from one it never read.
     */
    private static function floor(?string $constraint): string
    {
        if ($constraint === null) {
            return '';
        }

        return sprintf(', and the installed core requires %s — the lowest a package here may declare', $constraint);
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
     * @param array{via: string, php: ?string, source: string, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
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

    /**
     * What the environment runs by itself, which the commands above never
     * covered: those are what a caller may run, these run without being asked.
     *
     * `R-PRJ-009`. A boot of a demo site took its schema update, its extension
     * setup and its backend user out of `.ddev/config.yaml` by hand, beside an
     * answer that had opened the same file for one PHP version
     * (`feedback/2026-08-03-154501`). Said even where there are none, because
     * an answer that names no hook reads as "there is none" whether this looked
     * or not.
     *
     * @param array{via: string, php: ?string, source: string, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     * @return array<int, string>
     */
    private static function lifecycle(?array $environment): array
    {
        if ($environment === null || $environment['via'] !== Typo3Cli::VIA_DDEV) {
            return [];
        }

        $lines = [''];
        $lines[] = $environment['hooks'] === []
            ? 'That DDEV project declares no hooks in .ddev/config.yaml or the config.*.yaml beside it, so the '
                . 'commands above are the whole of what runs here.'
            : 'What that DDEV project runs without being asked. The commands above are what you may run; these fire '
                . 'at the stage each one names, in the order .ddev/config.yaml and the config.*.yaml beside it '
                . 'state them:';
        foreach ($environment['hooks'] as $hook) {
            $lines[] = sprintf(
                '- %s, %s: %s',
                $hook['stage'],
                $hook['service'] === null ? 'on the host' : 'in the ' . $hook['service'] . ' container',
                $hook['command'],
            );
        }

        if ($environment['providers'] !== []) {
            $lines[] = '';
            $lines[] = 'Where its database and files come from — the recipes below .ddev/providers/ this repository '
                . 'wrote. DDEV puts its own into every project and marks them #ddev-generated; those are not this '
                . 'project\'s and are left out:';
            foreach ($environment['providers'] as $provider) {
                $lines[] = sprintf(
                    '- %s (%s)',
                    $provider['operations'] === []
                        ? $provider['name'] . ', which declares neither a pull nor a push command'
                        : implode(', ', array_map(
                            static fn(string $operation): string => 'ddev ' . $operation . ' ' . $provider['name'],
                            $provider['operations'],
                        )),
                    $provider['source'],
                );
            }
        }

        return $lines;
    }
}
