<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Server;

use Mcp\Server\Transport\StdioTransport;
use Typo3CmsMcp\Installation\Instance;

/**
 * What `bin/typo3-cms-mcp` runs.
 *
 * With no argument it speaks MCP over stdin and stdout, which is how a client
 * launches it. Everything else is somebody at a terminal, and starting the
 * transport for them would look like a hang — so every other word ends in a
 * message and an exit code.
 *
 * This is also the one place that hands a working directory to Instance. The
 * client starts this process inside the session it is working in, so the
 * working directory is the agent's own and an installation found from it is the
 * one being worked on. Nothing else may do this, which is what `R-DIS-001` holds:
 * a request-serving endpoint has no such relationship to its callers, and its
 * document root may itself sit inside a TYPO3 installation.
 */
final class Entrypoint
{
    /** @param array<int, string> $arguments what the shell passed, without the binary */
    public static function run(array $arguments, string $binary = 'typo3-cms-mcp'): int
    {
        $command = $arguments[0] ?? null;

        if (in_array($command, ['install', 'update'], true)) {
            return self::setUp($command, array_slice($arguments, 1), $binary);
        }

        if ($command === null) {
            Instance::discoverFrom(getcwd() ?: null);
            self::reportExclusionsThatTookNothingAway();
            Factory::create()->run(new StdioTransport());

            return 0;
        }

        if (in_array($command, ['help', '--help', '-h'], true)) {
            fwrite(STDOUT, self::usage());

            return 0;
        }

        fwrite(STDERR, 'typo3-cms-mcp: no such command "' . $command . "\".\n\n" . self::usage());

        return 2;
    }

    /**
     * An excluded name that took no tool away, said on stderr before the
     * transport starts.
     *
     * stdout is the protocol from the next line on, so stderr is the one
     * channel a started server has left — which is where the stdio transport
     * puts a server's own output, for a client to capture, forward or ignore.
     * `src/bootstrap.php` says the other startup problem this binary has there
     * too.
     *
     * It is a warning and the server starts: the list is read once, out of an
     * environment nobody validates it against, and a name gone stale under a
     * rename would otherwise take every tool down with it. What it costs
     * meanwhile is one tool in the list that the caller did not want, which is
     * context rather than harm — `D-AUD-005`.
     *
     * The two reasons are said apart because what somebody has to change
     * differs: a name no tool answers to is corrected, a name this server keeps
     * offering is dropped from the variable. `typo3_server_scope` says the same
     * thing in-band, for the client that shows this stream to nobody.
     */
    private static function reportExclusionsThatTookNothingAway(): void
    {
        $unknown = ExcludedTools::unknown();
        if ($unknown !== []) {
            fwrite(STDERR, sprintf(
                'typo3-cms-mcp: %s names %s, which this server does not offer, so %s excluded nothing. '
                . "typo3_server_scope lists the tools it does offer.\n",
                ExcludedTools::VARIABLE,
                implode(', ', $unknown),
                count($unknown) === 1 ? 'it' : 'they',
            ));
        }

        $offeredAnyway = ExcludedTools::offeredAnyway();
        if ($offeredAnyway !== []) {
            fwrite(STDERR, sprintf(
                'typo3-cms-mcp: %s names %s, which this server offers whatever the variable says, so %s '
                . "excluded nothing. typo3_server_scope says which tools are really gone.\n",
                ExcludedTools::VARIABLE,
                implode(', ', $offeredAnyway),
                count($offeredAnyway) === 1 ? 'it' : 'they',
            ));
        }
    }

    /**
     * Writes the client configuration and the task skills into the directory
     * this was run in, which is the project being set up.
     *
     * @param array<int, string> $arguments
     */
    private static function setUp(string $command, array $arguments, string $binary): int
    {
        $directory = getcwd();
        if ($directory === false) {
            fwrite(STDERR, "typo3-cms-mcp: cannot determine the current directory.\n");

            return 1;
        }

        $agent = null;
        $drafts = false;
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, '--agent=')) {
                $agent = substr($argument, strlen('--agent='));
            }
            if ($argument === '--drafts') {
                $drafts = true;
            }
        }

        try {
            $installer = new Installer($directory, $binary);
            $message = $command === 'install'
                ? $installer->install($agent, $drafts)
                : $installer->update($agent, $drafts);
        } catch (\RuntimeException $exception) {
            fwrite(STDERR, 'typo3-cms-mcp: ' . $exception->getMessage() . ".\n");

            return 1;
        }

        fwrite(STDOUT, $message . "\n");

        return 0;
    }

    private static function usage(): string
    {
        return "Usage: typo3-cms-mcp [command]\n\n"
            . "With no command this speaks MCP over stdin and stdout, which is how a\n"
            . "client launches it. The commands below set that up, in the directory\n"
            . "they are run in.\n\n"
            . "  install [--agent=<client>]  Write the client configuration and the\n"
            . "          [--drafts]          task skills. Without --agent, the entry\n"
            . "                              in .mcp.json and the skills in\n"
            . "                              .agents/skills, which every client that\n"
            . "                              reads those two finds on its own.\n"
            . "  update [--agent=<client>]   Republish the skills and rewrite the\n"
            . "         [--drafts]           client entry, which a project that has\n"
            . "                              since required this server or gained a\n"
            . "                              DDEV configuration has outgrown.\n"
            . "                              Without --agent, for every client\n"
            . "                              installed here.\n"
            . "  help                        This text.\n\n"
            . "--drafts also publishes the task skills nobody has reviewed yet, so they\n"
            . "can be tried where they are actually loaded. It is a choice per run on\n"
            . "both commands: an update that does not ask for them takes them back out,\n"
            . "which is the way off one.\n\n"
            . 'Clients: ' . implode(', ', Installer::agents()) . "\n";
    }
}
