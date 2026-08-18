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
    /**
     * What the answer says where nothing has been installed below the root yet.
     *
     * Said rather than left to the three fields that go quiet: `typo3Version:
     * null` is what a root with no readable core answers and `extensions: []`
     * is what a project with no extensions of its own answers, so neither of
     * them tells a caller that the install has not happened (`D-ANS-085`). It
     * is also the state the installation workflow is entered in, which is why
     * the rest of the answer is composed for it rather than withheld.
     */
    private const NOTHING_INSTALLED = 'Nothing is installed below this root yet: no Composer metadata under the '
        . 'vendor directory it declares. So this is what the repository declares and not what is running — the '
        . 'TYPO3 version, the PHP floor the installed core requires and the extension list are read out of the '
        . 'installed tree and arrive once composer install has run. Everything else here is read from the files as '
        . 'they stand.';

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
        return 'Describe the repository this server was started in and the TYPO3 installation it has made: its TYPO3 and PHP constraints, including the PHP floor the installed core requires and not only the one this project declares, the extensions that are its own rather than TYPO3\'s, the sites it configures with the site sets each depends on, and the commands it declares in composer.json and package.json — each marked a check that hands the code back as it was, a change that rewrites something, or unknown where the declared body does not say. Read from files only, no console and no database, so it answers on a fresh clone as well. Before composer install has run it says so in installed, and only three fields wait for that install: the TYPO3 version, the PHP floor the core requires, and the extension list. It states how those PHP numbers stand to each other, which none of them says alone: whether the floor this project declares clears what the installed core requires, and whether anything configured here ever runs that floor or only some higher version inside the range. It also names the environment the repository configures to run itself in: a DDEV project states the PHP its container runs, which is a different interpreter from the caller\'s shell and where the commands below are run, plus what that environment runs without being asked — each hook as the stage it fires at and the command it runs, and the pull recipes its database and files come from. Call it before booting such a project or before recommending or running a check — these are the commands that exist in this repository, and the ones marked check are what a task told not to change files may run.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'root' => Schema::nullableString('Absolute path of the repository this describes. Null when no project root was found to describe.'),
            'kind' => Schema::string('core-checkout or composer-project. What the root declares itself to be, not whether anything is installed in it — installed says that.'),
            'installed' => ['type' => 'boolean', 'description' => 'Whether the packages this repository declares are installed below it. False is a clone nobody has run composer install in, which is the state a boot or an installation task starts in — everything else here is read from the repository\'s own files and answers either way. What false costs is the three fields that come out of the installed tree: typo3Version and corePhpConstraint are null and extensions is empty, and none of the three tells you that on its own.'],
            'typo3Version' => Schema::nullableString('The TYPO3 version installed here, read from the core package. Null where nothing is installed yet, which installed is what says.'),
            'phpConstraint' => Schema::nullableString('What composer.json requires of PHP. What the project declares, not what runs it — see environment.'),
            'coreConstraint' => Schema::nullableString('What it requires of typo3/cms-core.'),
            'corePhpConstraint' => Schema::nullableString('What the installed typo3/cms-core requires of PHP, out of that package\'s own composer.json — the lowest a package here may declare it supports. Neither of the other two PHP numbers: not what this project declares, and not what environment.php runs. Not derivable from the TYPO3 major either — v13.4 and v14.3 both require ^8.2, v12.4 requires ^8.1. Null where no core package was found to read.'),
            'phpRelation' => [
                'type' => ['object', 'null'],
                'description' => 'How the three PHP numbers above stand to each other, which none of them says on its own. Derived from the constraints and the environment as the files spell them — nothing was executed on any of these versions, so this is what the project claims and not evidence that any of it works. Null where phpConstraint names no floor: the project requires no PHP, or spells it in a way this will not claim to read, and a constraint it cannot read costs this object rather than buying a wrong relation.',
                'properties' => [
                    'floor' => Schema::string('The lowest PHP phpConstraint admits, as major.minor. What the project promises to run on, and the number its own commands are worth holding against.'),
                    'coreFloor' => Schema::nullableString('The same, read off corePhpConstraint. Null where no core package was found to read one from.'),
                    'againstCore' => ['type' => ['string', 'null'], 'enum' => [Project::PHP_BELOW, Project::PHP_SAME, Project::PHP_ABOVE, null], 'description' => 'Where floor sits against coreFloor. below: the project declares support for a PHP its own installed core refuses, so the promise cannot be kept. same: it declares what the core requires. above: it declares more than the core needs, which is a range the project narrowed itself and can widen without touching a dependency. Null where coreFloor is.'],
                    'inEnvironment' => ['type' => ['string', 'null'], 'enum' => [Project::PHP_BELOW, Project::PHP_SAME, Project::PHP_ABOVE, null], 'description' => 'Where the PHP environment.php states sits against floor. same: the declared floor is the version the commands are run on. above: the environment runs higher, so the floor is a version nothing configured here ever executes — a claim no check tests. below: the environment runs a PHP the project says it does not support. Null where there is no environment or it states no version. Only the floors are compared, so a version over what the constraint\'s own upper bound allows reads here like one inside it.'],
                ],
                'required' => ['floor', 'coreFloor', 'againstCore', 'inEnvironment'],
            ],
            'environment' => [
                'type' => ['object', 'null'],
                'description' => 'The environment this repository configures to run itself in, read from that environment\'s own files. Null means nothing here configures one that this server reads — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what it reads — so the commands below run wherever the caller runs them.',
                'properties' => [
                    'via' => ['type' => 'string', 'enum' => ['ddev', 'override'], 'description' => 'ddev: the repository carries a .ddev/config.yaml. override: nothing in the files says so, and TYPO3_DEV_COMPANION_CONSOLE names a command that reaches this installation somewhere other than the caller\'s own shell.'],
                    'php' => Schema::nullableString('The PHP that environment runs, where its files state it. Null is not "none": a DDEV project that states no php_version gets the default of the installed DDEV, and an environment named by TYPO3_DEV_COMPANION_CONSOLE states its version nowhere this server can read. typo3_server_scope reports the version the console actually answers on.'),
                    'source' => Schema::string('Where this was read: the .ddev config file that states the version last, or TYPO3_DEV_COMPANION_CONSOLE.'),
                    'project' => Schema::nullableString('The DDEV project name, which is what every ddev command takes and what the containers are named after: ddev-<project>-web and ddev-<project>-db. Where no file states it, DDEV uses the directory name and so does this. Null where the environment is not DDEV.'),
                    'hostnames' => Schema::listOf(Schema::string(), 'The hostnames those files declare the site is served under: <project>.ddev.site, every additional_hostnames entry with the same top-level domain, and every additional_fqdns entry as written. What the configuration declares, not what is running — the ports the router binds and its address on the container network are not in these files, and `ddev describe -j` is what carries them. Empty where the environment is not DDEV.'),
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
                'required' => ['via', 'php', 'source', 'project', 'hostnames', 'entered', 'hooks', 'providers'],
            ],
            'extensions' => Schema::listOf(Schema::object([
                'key' => Schema::string(),
                'path' => Schema::string('Relative to the project root.'),
                'origin' => ['type' => 'string', 'enum' => ['project', 'third-party', 'fixture'], 'description' => 'project: inside the repository, so what it is working on. third-party: installed as a dependency. fixture: shipped by the repository\'s test setup, below a Tests/ directory, so it exists to be loaded by a suite rather than developed.'],
            ], ['key', 'path', 'origin']), 'Extensions that are not TYPO3 system extensions. Read from Composer\'s installed metadata, so where installed is false this is empty because nothing has been installed rather than because the repository has none.'),
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
            'guides' => Schema::listOf(Schema::guideReference(), 'The whole procedures this server carries, named here because this is the call every task starts with. They are also served as typo3://guides resources, and a client that lists no resources renders none of them — four sessions in one week finished without learning they exist. Each is one typo3_rule_lookup call by documentId, which needs no resource list; a search over sections answers a question and never hands one of these over whole.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['root', 'installed', 'phpRelation', 'environment', 'extensions', 'sites', 'commands', 'patches', 'guides', 'answeredBy'], []);
    }

    public static function answer(array $args): ToolResult
    {
        $project = Project::describe();
        if ($project === null) {
            return Unsupported::because(
                'no repository declaring TYPO3 was found to describe',
            );
        }

        $lines = [sprintf(
            '%s — %s, TYPO3 %s, PHP %s%s%s',
            $project['root'],
            $project['kind'],
            match (true) {
                !$project['installed'] => 'not installed here yet',
                $project['typo3Version'] === null => 'version unknown',
                default => $project['typo3Version'],
            },
            $project['phpConstraint'] ?? 'unconstrained',
            self::runtime($project['environment']),
            self::floor($project['corePhpConstraint']),
        )];

        if (!$project['installed']) {
            $lines[] = '';
            $lines[] = self::NOTHING_INSTALLED;
        }

        $relation = self::relation($project['phpRelation'], $project['environment']);
        if ($relation !== '') {
            $lines[] = '';
            $lines[] = $relation;
        }

        $lines[] = '';
        $lines[] = match (true) {
            // The list is Composer's metadata rather than anything the manifest
            // declares, so an empty one here would read as a repository with no
            // extensions where it is a repository with nothing installed.
            !$project['installed'] => 'Extensions: not readable until the install has run — which extensions are '
                . 'here is what Composer wrote, not what composer.json requires.',
            $project['extensions'] === [] => 'Extensions: none beyond TYPO3\'s own.',
            default => 'Extensions that are not TYPO3\'s own:',
        };
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

        $site = self::site($project['environment']);
        if ($site !== '') {
            $lines[] = '';
            $lines[] = $site;
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
     * They are served as `typo3://guides` resources and a client that lists no
     * resources renders none of them, while `typo3_server_scope` is the call an
     * agent skips precisely when the task looks legible without orientation.
     * This tool is the one the instructions open every task with, so the
     * inventory is here and the detail stays there (`D-ANS-061`). Last in the
     * answer rather than first, because what this tool is called for is the
     * installation.
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
     * @param array{via: string, php: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
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
     * core requires, and the two numbers already here are the wrong ones to take
     * it from: one is what the project accepts and may be absent, the other what
     * the container happens to run (`D-KNW-055`). Here rather than in a section
     * of its own, because it is the line the first call of a workflow is read
     * for, and stated even where it repeats the project's own — a number the
     * answer drops where the two agree cannot be told from one it never read.
     */
    private static function floor(?string $constraint): string
    {
        if ($constraint === null) {
            return '';
        }

        return sprintf(', and the installed core requires %s — the lowest a package here may declare', $constraint);
    }

    /**
     * How the three numbers on the line above stand to each other.
     *
     * The line states them and states which is which; what it never said is the
     * relation, and the relation is the defect (`D-ANS-082`). Stated even where
     * the three agree, for the reason `floor()` states the core's number where
     * it repeats the project's own: a line the answer drops when nothing is
     * wrong cannot be told from one it never computed.
     *
     * @param array{floor: string, coreFloor: ?string, againstCore: ?string, inEnvironment: ?string}|null $relation
     * @param array{via: string, php: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function relation(?array $relation, ?array $environment): string
    {
        if ($relation === null) {
            return '';
        }

        $sentences = [sprintf('Those PHP numbers, as they stand to each other. This project promises %s.', $relation['floor'])];
        $sentences[] = match ($relation['againstCore']) {
            Project::PHP_BELOW => sprintf(
                'The installed core requires %s, so that promise cannot be kept here: the core refuses the version '
                    . 'this project says it supports.',
                (string) $relation['coreFloor'],
            ),
            Project::PHP_ABOVE => sprintf(
                'The installed core requires %s, so this project asks for more than its dependency needs — a range it '
                    . 'narrowed itself, and one it can widen without touching anything it does not own.',
                (string) $relation['coreFloor'],
            ),
            Project::PHP_SAME => sprintf('The installed core requires %s as well, so the two agree.', (string) $relation['coreFloor']),
            default => 'Nothing readable here says what the installed core requires, so there is no second floor to '
                . 'hold that against.',
        };
        $sentences[] = match ($relation['inEnvironment']) {
            Project::PHP_SAME => sprintf(
                'The environment runs %s, which is that floor, so it is the version the commands above are actually '
                    . 'executed on.',
                (string) $environment['php'],
            ),
            Project::PHP_ABOVE => sprintf(
                'The environment runs %s, above that floor, so nothing configured here ever executes the version this '
                    . 'project promises — it is a claim, and every check passes without testing it.',
                (string) $environment['php'],
            ),
            Project::PHP_BELOW => sprintf(
                'The environment runs %s, below that floor, so the commands above are executed on a PHP this project '
                    . 'says it does not support.',
                (string) $environment['php'],
            ),
            default => 'No environment here states a PHP, so there is nothing to say which of the versions in that '
                . 'range gets run.',
        };
        $sentences[] = 'All of it read from these files. Nothing was executed on any of these versions, and only the '
            . 'floors were compared — a version over what a constraint\'s own upper bound allows reads here like one '
            . 'inside it.';

        return implode(' ', $sentences);
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
     * @param array{via: string, php: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
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
     * What the environment serves, which is not the same question as where the
     * commands run and is answered whether the repository declares any.
     *
     * The running half is named rather than guessed at: bound ports and a
     * container address are not in these files, and `R-DIS-006` is why nothing
     * here starts anything to find out.
     *
     * @param array{via: string, php: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
     */
    private static function site(?array $environment): string
    {
        if ($environment === null || $environment['via'] !== Typo3Cli::VIA_DDEV) {
            return '';
        }

        return sprintf(
            'That project is named %s, so its containers are ddev-%s-web and ddev-%s-db, and its files serve it at '
                . '%s. What they do not carry is what a browser needs beyond the name: the ports the router binds '
                . 'and its address on the container network come from "ddev describe -j" and "docker inspect".',
            (string) $environment['project'],
            (string) $environment['project'],
            (string) $environment['project'],
            implode(', ', $environment['hostnames']),
        );
    }

    /**
     * What the environment runs by itself, which the commands above never
     * covered: those are what a caller may run, these run without being asked.
     *
     * `R-PRJ-009`. Said even where there are none, because an answer that names
     * no hook reads as "there is none" whether this looked or not.
     *
     * @param array{via: string, php: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null $environment
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
