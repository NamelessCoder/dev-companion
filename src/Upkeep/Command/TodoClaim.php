<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Upkeep\Checkouts;
use Typo3CmsMcp\Upkeep\Cli;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * Taking the front of the queue on, and putting the sessions in front of it.
 *
 * `bin/cli todo:next` hands the same first todo to everybody who asks, because
 * the queue is an order rather than an assignment. That is what has to change
 * before two sessions can work at once, and this is the change: the todos come
 * out of the queue in one move, so the second session is offered the item behind
 * them rather than the one somebody is already writing.
 *
 * It carries the setup out rather than printing it, and the reason is the order
 * rather than the typing. A claim has to be committed to `main` before the
 * worktree is cut from it, or the worktree carries no file saying what it was
 * made for; the failure surfaces in the session, hours later, as a refusal
 * nobody can place. Three steps that must happen in one order are one step, and
 * this is it: the claims, the commit that carries them, a worktree apiece with
 * its own `composer install`, and the message the sessions are started with.
 *
 * The branch is derived from the todo and the worktree named after the branch,
 * so a second claim of a todo somebody once worked would land on both. Neither
 * is refused and neither is reused: the claim is given the first free name and
 * records it, because a worktree that quietly attaches to an old branch is the
 * one failure here that looks like success.
 *
 * The overlap it reports is the only one it can see. Two todos that serve the
 * same entry are two sessions likely to edit one file, and nothing here knows
 * which files a step will touch — so it is a warning to read before the
 * sessions start, not a refusal.
 */
#[AsCommand(
    name: 'todo:claim',
    description: 'take the next todos out of the queue, one branch each, for sessions working at once',
)]
final class TodoClaim
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('how many sessions are going to work at once')]
        int $count = 1,
    ): int {
        // Claims are taken where `main` is. Asked in a worktree it would move
        // files nobody else can see, commit them onto somebody's branch and cut
        // worktrees from that — every step working, and the arrangement it is
        // setting up gone.
        if (Todo::linked()) {
            Cli::errors($output)->writeln(
                "This is a worktree, and claims are taken in the checkout it was cut from.\n"
                . 'Everything here would go onto this branch, where no other session reads it.',
            );

            return 1;
        }

        $items = Todo::items();
        if ($items === []) {
            Cli::errors($output)->writeln('The queue is empty, so there is nothing to take on.');

            return 1;
        }
        if ($count < 1) {
            Cli::errors($output)->writeln('A claim is for at least one session.');

            return 1;
        }
        if ($count > count($items)) {
            $output->writeln(sprintf(
                'The queue holds %d, which is what %d sessions get.',
                count($items),
                count($items),
            ));
        }

        $taken = array_slice($items, 0, $count);
        $root = Paths::root();
        $claims = [];
        foreach ($taken as $todo) {
            [$branch, $directory] = self::free($root, Todo::branch($todo));
            $claims[] = [
                'title' => $todo['title'],
                'branch' => $branch,
                'directory' => $directory,
                'from' => $todo['path'],
                'to' => Todo::claim($todo, null, $branch),
            ];
            $output->writeln($todo['title']);
            $output->writeln(sprintf('    %s · %s', end($claims)['to'], $branch));
        }
        $output->writeln('');

        foreach (self::overlapping($taken) as $what => $titles) {
            $output->writeln(sprintf(
                '%s is answered for twice, by %s — one entry two sessions will edit.',
                $what,
                implode(' and ', $titles),
            ));
            $output->writeln('');
        }

        if (!self::record($output, $root, $claims)) {
            return 1;
        }
        $standing = self::worktrees($output, $root, $claims);
        if ($standing !== []) {
            $output->writeln('');
            $output->writeln(self::handover($root, $standing));
        }

        return count($standing) === count($claims) ? 0 : 1;
    }

    /**
     * The first name this claim can have to itself, as the branch and the
     * directory named after it.
     *
     * Both are derived from the todo, so both are taken in exactly the same
     * case: the todo was claimed once before, by a session whose branch is
     * still there. The two are found together and carry the same number,
     * because a worktree whose name says one thing and whose branch says
     * another is worse than either collision.
     *
     * @return array{0: string, 1: string} the branch and the directory below the checkout
     */
    private static function free(string $root, string $derived): array
    {
        for ($number = 1;; $number++) {
            $suffix = $number === 1 ? '' : '-' . $number;
            $directory = '.worktrees/' . basename($derived) . $suffix;
            [$exists] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--verify', '--quiet', 'refs/heads/' . $derived . $suffix]);
            if ($exists !== 0 && !file_exists($root . '/' . $directory)) {
                return [$derived . $suffix, $directory];
            }
        }
    }

    /**
     * The claims onto `main`, in the commit the worktrees are cut from.
     *
     * Exactly the files this command moved and nothing beside them: the
     * checkout it runs in is one somebody is working, and a claim that swept up
     * half of their afternoon is worse than one nobody committed. `--only` is
     * that, and it leaves whatever else was staged where it was.
     *
     * @param array<int, array{title: string, branch: string, from: string, to: string, ...}> $claims
     */
    private static function record(OutputInterface $output, string $root, array $claims): bool
    {
        $paths = [];
        foreach ($claims as $claim) {
            $paths[] = $claim['from'];
            $paths[] = $claim['to'];
        }

        [$staged, $said] = Checkouts::run(array_merge(['git', '-C', $root, 'add', '--'], $paths));
        if ($staged !== 0) {
            Cli::errors($output)->writeln('The claims could not be staged, so nothing was committed: ' . trim($said));

            return false;
        }

        $lines = [];
        foreach ($claims as $claim) {
            $lines[] = '- ' . $claim['title'] . ' — ' . $claim['branch'];
        }
        [$committed, $said] = Checkouts::run(array_merge([
            'git', '-C', $root, 'commit', '--only',
            '--message', count($claims) === 1
                ? '[TASK] Take one todo out of the queue into a session of its own'
                : sprintf('[TASK] Take %s todos out of the queue into as many hands', self::counted(count($claims))),
            '--message', "Each claim names the branch its work is on and the day it was taken, so\n"
                . "the worktrees are cut from a `main` that already says who has what.\n\n"
                . implode("\n", $lines),
            '--',
        ], $paths));
        if ($committed !== 0) {
            Cli::errors($output)->writeln(
                "The claims are moved but not committed, and a worktree cut now would stand on\n"
                . 'nothing: ' . trim($said),
            );

            return false;
        }

        [, $revision] = Checkouts::run(['git', '-C', $root, 'rev-parse', '--short', 'HEAD']);
        $output->writeln(sprintf(
            'On `main` as %s, which is what the worktrees are cut from.',
            trim($revision),
        ));
        $output->writeln('');

        return true;
    }

    /**
     * One worktree per claim, each with the install that makes `bin/cli` in it
     * this checkout rather than the one next door.
     *
     * `vendor/` cannot be shared and is not worth trying to: `Paths::root()` is
     * the directory above `src/`, Composer resolves `src/` from where the
     * autoloader physically sits, and a symlinked one therefore points every
     * path in this repository back here. `.checkouts/` is the opposite case and
     * is linked on purpose — 861 MB a session only ever reads.
     *
     * A worktree that could not be finished is named and the rest go on. What
     * it must not do is stay half-made in silence, because the session started
     * against it fails at its first command with nothing saying why.
     *
     * @param array<int, array{title: string, branch: string, directory: string, ...}> $claims
     *
     * @return array<int, string> the directory of each one ready to be worked
     */
    private static function worktrees(OutputInterface $output, string $root, array $claims): array
    {
        $standing = [];
        foreach ($claims as $claim) {
            $path = $root . '/' . $claim['directory'];
            [$cut, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'add', '--quiet', $claim['directory'], '-b', $claim['branch']]);
            if ($cut !== 0) {
                Cli::errors($output)->writeln(sprintf('%s could not be made: %s', $claim['directory'], trim($said)));
                continue;
            }

            [$installed, $said] = Checkouts::run(['composer', 'install', '--no-interaction', '--quiet'], $path);
            if ($installed !== 0) {
                Cli::errors($output)->writeln(sprintf(
                    "%s has no `vendor/`, so `bin/cli` in it is nothing to start a session against:\n%s",
                    $claim['directory'],
                    trim($said),
                ));
                continue;
            }

            if (is_dir($root . '/.checkouts') && !file_exists($path . '/.checkouts')) {
                symlink('../../.checkouts', $path . '/.checkouts');
            }

            $output->writeln('    ' . $claim['directory']);
            $standing[] = $claim['directory'];
        }

        return $standing;
    }

    /**
     * What is left for the caller: where each session is started, and what
     * every one of them is sent.
     *
     * Both halves are printed filled in, and the second is why. The message was
     * a template once, `<absolute path to the worktree>` and all, and the run
     * that broke sent it as it stood — so the blanks came out of it and the
     * session reads its todo off the worktree instead. The half above it kept
     * one: *with that worktree as its working directory* is a property somebody
     * satisfies, which is a blank wearing prose, and the run of 2026-08-02
     * satisfied it with the directory that was already open — the checkout the
     * worktrees are cut from. So the directories are lines to run, one per
     * session, and the sentence over the message says the rest of this output
     * is not part of it. A caller with nothing to compose composes nothing
     * wrong.
     *
     * `todo:next --worktree` catches what still gets through, which is what it
     * caught that day. It costs a session to find out, so it is the net rather
     * than the answer.
     *
     * @param array<int, string> $standing the worktree directories below the checkout
     */
    public static function handover(string $root, array $standing): string
    {
        $lines = ['One session per worktree, and each is started in the directory that is its own:', ''];
        foreach ($standing as $directory) {
            $lines[] = '    cd ' . $root . '/' . $directory;
        }

        $lines[] = '';
        $lines[] = "Send each of them the message below and nothing else off this output. It is the\n"
            . "same for every session, and there is nothing in it to fill in: which todo is\n"
            . 'whose is read out of the worktree it stands in.';
        $lines[] = '';
        $lines[] = self::indent(Todo::BRIEFING);
        $lines[] = '';
        $lines[] = sprintf(
            "What a session started from a command line has to be given: %s.\n"
            . 'How the branches come home afterwards: %s.',
            Todo::LAUNCH,
            Todo::PARALLEL,
        );

        return implode("\n", $lines);
    }

    /**
     * A small count as the word it is read as, for the one line of this that a
     * person reads back later. Above nine the numeral is what a reader wants
     * anyway, and this repository has never claimed that many at once.
     */
    private static function counted(int $count): string
    {
        return [1 => 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'][$count] ?? (string) $count;
    }

    private static function indent(string $block): string
    {
        return (string) preg_replace('/^(?!$)/m', '    ', rtrim($block));
    }

    /**
     * What two of the claimed todos both answer for, counting entries only.
     *
     * A directory is what most of the queue serves, so counting one would put a
     * line under every claim ever taken — and a warning that is always there is
     * one nobody reads by the third time. It would also be saying nothing:
     * `decisions/` names the place a step reports to rather than the file it
     * edits, and the two todos under it usually touch different entries.
     *
     * @param array<int, array{title: string, serves: array<int, string>, ...}> $taken
     *
     * @return array<string, array<int, string>>
     */
    private static function overlapping(array $taken): array
    {
        $serving = [];
        foreach ($taken as $todo) {
            foreach ($todo['serves'] as $what) {
                if (!str_ends_with($what, '/')) {
                    $serving[$what][] = $todo['title'];
                }
            }
        }

        return array_filter($serving, static fn(array $titles): bool => count($titles) > 1);
    }
}
