<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Feedback;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Profile;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Scope;
use Typo3CmsMcp\ToolResult;
use Typo3CmsMcp\Typo3Cli;
use Typo3CmsMcp\Versions;

/**
 * What this server covers, what it deliberately does not, and which tool to
 * call when.
 */
final class ServerScope extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_server_scope';
    }

    public static function description(): string
    {
        return 'Orientation for this server: what it covers and at which depth, what it deliberately does not cover, and which tool to call when. Start here when it is unclear whether this server can answer a question at all, or which of the lookups is the right one.';
    }

    public static function inputSchema(): array
    {
        return self::noArguments();
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'purpose' => Schema::string('What this server is for.'),
            'instructions' => Schema::string('The boundary statement clients receive at initialize time.'),
            'covers' => Schema::listOf(Schema::object([
                'topic' => Schema::string(),
                'depth' => Schema::string('How deeply the topic is covered.'),
                'tools' => Schema::listOf(Schema::string()),
                'source' => Schema::string('Knowledge file or typo3:// resource behind the topic.'),
                'provenance' => ['type' => 'string', 'enum' => ['core-only', 'transferable', 'installation'], 'description' => 'What the answers are worth outside the core. core-only: the contribution process and the scripts of that repository. transferable: a convention that holds wherever TYPO3 is written. installation: answered by the installation being read rather than from a snapshot.'],
            ], ['topic', 'depth', 'tools', 'source', 'provenance'])),
            'doesNotCover' => Schema::listOf(Schema::object([
                'topic' => Schema::string(),
                'why' => Schema::string(),
                'instead' => Schema::string('What to do instead of asking this server.'),
            ], ['topic', 'why', 'instead'])),
            'checkoutDiscovery' => Schema::listOf(Schema::object([
                'establish' => Schema::string(),
                'how' => Schema::string(),
            ], ['establish', 'how'])),
            'routing' => Schema::listOf(Schema::object([
                'when' => Schema::string(),
                'call' => Schema::string(),
            ], ['when', 'call'])),
            'versions' => Schema::listOf(Schema::object([
                'major' => Schema::integer(),
                'branch' => Schema::string('The branch that line is verified against.'),
                'status' => Schema::string('lts, stable, or development.'),
            ], ['major', 'branch', 'status']), 'The TYPO3 versions the knowledge is bound to. A statement outside a range is left out when a target version is known.'),
            'profile' => Schema::object([
                'active' => ['type' => 'string', 'enum' => ['all', 'project'], 'description' => 'Which half of the server this client is offered. all: every tool. project: the same server without the core contribution surface, because a project or extension repository cannot follow it.'],
                'via' => ['type' => 'string', 'enum' => ['environment', 'installation'], 'description' => 'Whether the profile was named by the environment variable or followed from the kind of installation that was found.'],
                'omits' => Schema::listOf(Schema::string(), 'The tools this profile leaves out of the tool list. Empty in the all profile.'),
                'variable' => Schema::string('Environment variable that names the profile outright.'),
                'misconfiguration' => Schema::nullableString('Set when the variable named a profile that does not exist. The derived one is used instead.'),
            ], ['active', 'via', 'omits', 'variable']),
            'installation' => Schema::object([
                'found' => ['type' => 'boolean', 'description' => 'Whether there is an installation to read at all.'],
                'root' => Schema::nullableString('Absolute path of the installation.'),
                'kind' => Schema::nullableString('core-checkout or composer-project.'),
                'via' => Schema::nullableString('How it was determined: discovery (walked up from the start directory) or environment (named by TYPO3_MCP_ROOT).'),
                'startedFrom' => Schema::nullableString('Where the search started, or the configured value.'),
                'searched' => Schema::listOf(Schema::string(), 'The directories the search walked. A failure here means a layout that cannot be read or a server started in the wrong place — this says which.'),
                'packageCount' => Schema::integer('TYPO3 packages found in it.'),
                'misconfiguration' => Schema::nullableString('Set when a configured value could not be followed. Nothing falls back to a discovered installation.'),
                'console' => Schema::object([
                    'reachable' => ['type' => 'boolean', 'description' => 'False means every installation-backed tool answers with answeredBy: nothing.'],
                    'via' => Schema::nullableString('ddev, php, or override.'),
                    'php' => Schema::nullableString('The PHP version it runs on, where that is known.'),
                    'command' => Schema::nullableString('The invocation, as it is run.'),
                    'reason' => Schema::nullableString('Why it cannot be run. Null when it can.'),
                    'caveat' => Schema::nullableString('What limits the console that was found — a project whose containers are stopped answers what its files say and fails on everything that boots TYPO3 against its database. Null when nothing limits it.'),
                ], ['reachable']),
                'settings' => Schema::object([
                    'root' => Schema::string('Environment variable that names the installation root.'),
                    'console' => Schema::string('Environment variable that names the console command.'),
                ], ['root', 'console']),
            ], ['found', 'searched', 'packageCount', 'console']),
        ], ['purpose', 'covers', 'doesNotCover', 'routing', 'versions', 'profile', 'installation']);
    }

    public static function answer(array $args): ToolResult
    {
        $scope = Scope::offered();

        $lines = [
            // Before the purpose rather than after it: the purpose describes
            // the whole server, and a client that reads what it holds first and
            // that half of it is missing second has been told and then
            // corrected.
            self::profileLine(),
            '',
            $scope['purpose'],
            '',
            'Covered, and how deeply. Each topic says what its answers are worth outside the core: '
            . 'core-only is the contribution process and the scripts that belong to that repository, '
            . 'transferable is a convention that holds wherever TYPO3 is written, and installation is '
            . 'answered by the installation this server was started in rather than from any snapshot.',
        ];
        foreach ($scope['covers'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['depth'];
            $lines[] = 'Tools: ' . implode(', ', $entry['tools']);
            $lines[] = 'Source: ' . $entry['source'] . ' (' . $entry['provenance'] . ')';
        }

        $lines[] = '';
        // Stated here as well as in the initialize instructions, because this
        // is the tool an agent calls when it does not know how to use the
        // server, and a client is free not to surface instructions at all.
        $lines[] = 'Query this server in English, whatever language you are speaking with the user. Its '
            . 'knowledge is written in English and its matching is lexical, so a query in another language '
            . 'reaches only the words the two happen to share and otherwise comes back empty.';

        $lines[] = '';
        $lines[] = 'Versions this knowledge is bound to:';
        foreach (Versions::covered() as $version) {
            $lines[] = '- TYPO3 v' . $version['major'] . ' (' . $version['branch'] . ', ' . $version['status'] . ')';
        }
        $lines[] = 'A statement that does not hold on all of them carries the range it holds on. Pass targetVersion '
            . 'to have the ones that do not apply left out; without it, the version of the installation being read '
            . 'decides, and where there is none nothing is filtered.';

        $lines[] = '';
        // What the list is worth read from the other side. A caller cannot tell
        // a boundary from a gap by the size of an answer, and the two ask for
        // opposite reactions: leave, or say what was missing.
        $lines[] = 'Deliberately not covered — and this list is the boundary: a subject that is not on it is in '
            . 'scope, so a thin answer to it is a gap in the knowledge base rather than a limit of it.'
            . (Feedback::isAvailable() ? ' Record one with typo3_feedback_record instead of going elsewhere.' : '');
        foreach ($scope['doesNotCover'] as $entry) {
            $lines[] = '## ' . $entry['topic'];
            $lines[] = $entry['why'];
            $lines[] = 'Instead: ' . $entry['instead'];
        }

        $lines[] = '';
        $lines[] = 'Which tool to call when:';
        foreach ($scope['routing'] as $entry) {
            $lines[] = '- ' . $entry['when'] . ' → ' . $entry['call'];
        }

        // Which installation is being read is the one thing a caller cannot
        // check for itself, and reading the wrong one would be worse than
        // reading none — so it is stated, with where the search started.
        $instance = Instance::describe();
        $lines[] = '';
        if ($instance === null) {
            $lines[] = 'No TYPO3 installation was found from the directory this server was started in, so every answer '
                . 'comes from the bundled knowledge base alone. Questions about what is registered in an '
                . 'installation — which icon identifiers exist, which labels — cannot be answered here.';
            if (Instance::searched() !== []) {
                // Where it looked is the difference between "this layout cannot
                // be read" and "the client started the server somewhere else",
                // and the caller can check neither without being told.
                $lines[] = 'Looked in: ' . implode(', ', Instance::searched())
                    . ' — none of them declares a TYPO3 core checkout or holds Composer metadata with TYPO3 packages in it.';
            }
            $lines[] = sprintf(
                'Naming it outright is the way out: set %s to the installation root, and %s to the command that '
                . 'reaches its console where that is not a path this server would find on its own.',
                Instance::ROOT_VARIABLE,
                Typo3Cli::CONSOLE_VARIABLE,
            );
        } else {
            $lines[] = sprintf(
                'Found the TYPO3 installation at %s (%s, %s, from %s), which holds %d packages. '
                . 'If that is not the installation you are working on, this server was started in the wrong '
                . 'directory — or set %s to the one you mean.',
                $instance['root'],
                $instance['kind'],
                $instance['via'] === Instance::VIA_ENVIRONMENT
                    ? 'named by ' . Instance::ROOT_VARIABLE
                    : 'found by walking up',
                $instance['startedFrom'],
                count(Instance::packages()),
                Instance::ROOT_VARIABLE,
            );
        }
        if (Instance::misconfiguration() !== '') {
            $lines[] = 'The configuration says otherwise and could not be followed: '
                . Instance::misconfiguration() . '.';
        }

        // What the installation can be asked is a different question from
        // whether one was found, and the answer is actionable often enough to
        // belong here rather than in a failing tool call.
        $console = Typo3Cli::resolve();
        if ($instance !== null && $console === null) {
            $lines[] = 'Its console cannot be run right now, so questions that only the installation can answer — which '
                . 'labels exist, which backend modules are registered — have no answer here: ' . Typo3Cli::reason() . '. '
                . 'Where the command that would work is known, ' . Typo3Cli::CONSOLE_VARIABLE
                . ' states it, for example "ddev exec .build/bin/typo3".';
        }
        if ($instance !== null && $console !== null && $console['via'] === Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is invoked as "%s", which %s states, so those answers come from the installation '
                . 'itself rather than from a bundled snapshot.',
                implode(' ', $console['command']),
                Typo3Cli::CONSOLE_VARIABLE,
            );
        }
        if ($instance !== null && $console !== null && $console['via'] !== Typo3Cli::VIA_OVERRIDE) {
            $lines[] = sprintf(
                'Its console is reachable via %s on PHP %s, so those answers come from the installation itself '
                . 'rather than from a bundled snapshot.',
                $console['via'],
                $console['php'] === '' ? 'an unreported version' : $console['php'],
            );
        }
        if ($instance !== null && Typo3Cli::caveat() !== '') {
            $lines[] = 'Reachable is not the same as ready here: ' . Typo3Cli::caveat() . '.';
        }

        $lines[] = '';
        $lines[] = 'Every lookup and guide is read-only. typo3_documentation_lookup reads the official, versioned '
            . 'manuals at docs.typo3.org; apart from that and the installation named above, nothing is fetched, '
            . 'executed, or looked up online.';
        if (Feedback::isAvailable()) {
            // Naming the one write next to the read-only claim, not after it:
            // a blanket "everything is read-only" followed by a tool that
            // creates a file contradicts both the annotations and the behaviour.
            $lines[] = 'The one exception is typo3_feedback_record, this server\'s only write: '
                . 'it creates a new markdown feedback under feedback/ and touches nothing else. '
                . 'Missing something that belongs here? Leave feedback about it.';
        }

        return ToolResult::create(implode("\n", $lines), $scope + [
            'versions' => Versions::covered(),
            'profile' => [
                'active' => Profile::active(),
                'via' => Profile::via(),
                'omits' => Profile::omitted(),
                'variable' => Profile::VARIABLE,
                'misconfiguration' => Profile::misconfiguration() === '' ? null : Profile::misconfiguration(),
            ],
            'installation' => self::installationReport(),
        ]);
    }

    /**
     * Which half of the server this client is being offered, and why.
     *
     * A shorter tool list than the documentation describes is otherwise
     * indistinguishable from a broken server, and the caller has no way to
     * check: it sees the list it was given and nothing else.
     */
    private static function profileLine(): string
    {
        $line = sprintf('Profile "%s", %s. ', Profile::active(), Profile::via() === Profile::VIA_ENVIRONMENT
            ? 'named by ' . Profile::VARIABLE
            : 'following from the installation this server was started in');

        $line .= Profile::omitted() === []
            ? sprintf(
                'Every tool this server has is offered. In a project or extension repository, %s=%s offers the same '
                . 'server without the core contribution surface.',
                Profile::VARIABLE,
                Profile::PROJECT,
            )
            : sprintf(
                'The core contribution surface is not offered here — a project or extension repository has no '
                . 'Build/Scripts/, no Gerrit remote and no Forge issue — so %s are missing from the tool list, and so '
                . 'are the topics they answered. What transfers is still here. %s=%s offers them anyway.',
                implode(', ', Profile::omitted()),
                Profile::VARIABLE,
                Profile::ALL,
            );

        if (Profile::misconfiguration() !== '') {
            $line .= ' The configuration says otherwise and could not be followed: ' . Profile::misconfiguration() . '.';
        }

        return $line;
    }

    /**
     * The installation diagnostic as data.
     *
     * It used to be in the text alone, and a client that renders
     * structuredContent and drops the text block never saw it. What the caller
     * got instead was five tools answering {"matchCount": 0, "answeredBy":
     * "nothing"} — indistinguishable from a registry that really is empty, and
     * read as one: an extension with forty registered icons was reported as
     * registering none, twice.
     *
     * @return array<string, mixed>
     */
    private static function installationReport(): array
    {
        $instance = Instance::describe();
        $console = Typo3Cli::resolve();

        return [
            'found' => $instance !== null,
            'root' => $instance['root'] ?? null,
            'kind' => $instance['kind'] ?? null,
            'via' => $instance['via'] ?? null,
            'startedFrom' => $instance['startedFrom'] ?? null,
            'searched' => Instance::searched(),
            'packageCount' => count(Instance::packages()),
            'misconfiguration' => Instance::misconfiguration() === '' ? null : Instance::misconfiguration(),
            'console' => [
                'reachable' => $console !== null,
                'via' => $console['via'] ?? null,
                'php' => ($console['php'] ?? '') === '' ? null : $console['php'],
                'command' => $console === null ? null : implode(' ', $console['command']),
                'reason' => $console === null ? Typo3Cli::reason() : null,
                // Reachable and ready are two questions, and the second one has
                // its own answer: a console reached through an interpreter on
                // this machine while the project's containers are stopped runs,
                // and cannot boot TYPO3 against a database that is not there.
                'caveat' => Typo3Cli::caveat() === '' ? null : Typo3Cli::caveat(),
            ],
            'settings' => [
                'root' => Instance::ROOT_VARIABLE,
                'console' => Typo3Cli::CONSOLE_VARIABLE,
            ],
        ];
    }
}
