<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * The other end of `todo:claim`: one finished branch back onto `main`.
 *
 * The setup was a command and the return was a page, and the asymmetry cost
 * exactly what it looks like it would. A session sent to bring three branches
 * home read `working-todos-in-parallel.md` whole and then `TodoClaim.php` whole
 * — 46 KB — to find out what four git commands were, and it did that before
 * running the first of them. What a caller needs from a procedure is that it
 * happens in the right order, and an order is the one thing prose cannot hold
 * somebody to.
 *
 * The order is the whole content. The rebase is what makes the merge a
 * fast-forward, because `main` moved while the session ran; `composer ci` runs
 * after that rebase and nowhere else, because that is the first moment the work
 * stands on what `main` has become; and the worktree comes down after the
 * merge, never before, because removed early it takes the only checkout the
 * rebase and the suite can run in.
 *
 * One branch, and the next one starts again from the top. `main` moved when the
 * last merge landed, so a branch rebased before it is behind again — running two
 * merges from one rebase is the mistake `--ff-only` exists to catch, and this
 * command cannot make it because each name it is given is carried through all
 * four steps before the next is looked at.
 *
 * What it does not do is decide that a session has ended. Nothing here can see
 * that, and a branch merged out from under a session that was still writing is
 * worse than a branch that waited: the names are the caller's, and with none it
 * reports what is standing instead.
 */
#[AsCommand(
    name: 'todo:home',
    description: 'bring one finished branch home: rebase, check, fast-forward, and take the worktree down',
)]
final class TodoHome
{
    /**
     * @param array<int, string> $worktree
     */
    public function __invoke(
        OutputInterface $output,
        Application $application,
        #[Argument('the worktrees whose sessions have ended, by directory name')]
        array $worktree = [],
    ): int {
        // The mirror of the refusal in `todo:claim`, and for the mirrored
        // reason. There the claims would go onto somebody's branch; here the
        // fast-forward would be onto one, and `main` — where every other
        // session reads what is in hand — would not move at all.
        $root = Paths::root();
        if (Todo::linked()) {
            Cli::errors($output)->writeln(
                "This is a worktree, and a branch comes home in the checkout it was cut from.\n"
                . 'Nothing here fast-forwards `main`, because `main` is not what this stands on.',
            );

            return 1;
        }

        $standing = self::standing($root);
        if ($worktree === []) {
            return self::report($output, $root, $standing);
        }

        $on = Todo::standing($root);
        if ($on !== 'main') {
            Cli::errors($output)->writeln(sprintf(
                "This checkout stands on %s, and a claim comes home onto `main`.\n"
                . 'Check `main` out first, or the merge below lands where nobody is looking for it.',
                $on === '' ? 'no branch git can name' : $on,
            ));

            return 1;
        }

        $home = [];
        foreach ($worktree as $one) {
            $name = basename(rtrim((string) $one, '/'));
            if (!in_array($name, $standing, true)) {
                Cli::errors($output)->writeln(sprintf(
                    '%s is no worktree below .worktrees/ — `bin/cli todo:home` names the ones that are.',
                    $one,
                ));

                return 1;
            }

            $branch = self::bring($output, $root, $name);
            if ($branch !== null) {
                $home[] = $branch;
            }
            $output->writeln('');
        }

        if ($home === []) {
            return 1;
        }

        $moved = self::released($output, $home);
        self::listings($output, $application, $root, $moved);

        $output->writeln('── repository:check');
        $worst = $application->doRun(new StringInput('repository:check'), $output);

        return count($home) === count($worktree) ? $worst : max(1, $worst);
    }

    /**
     * The four steps for one branch, in the order that is the point of them.
     *
     * Each one stops the sequence where it fails and says what state it left,
     * because every stopping point here is a different thing to do next: a
     * rebase that conflicts is read, a red suite is fixed on the branch, and a
     * merge that will not fast-forward is the assumption of this procedure
     * being wrong somewhere.
     *
     * @return string|null the branch that is now on `main`, or null where it is not
     */
    private static function bring(OutputInterface $output, string $root, string $name): ?string
    {
        $path = $root . '/.worktrees/' . $name;
        $output->writeln($name);

        [$read, $branch] = Checkouts::run(['git', '-C', $path, 'rev-parse', '--abbrev-ref', 'HEAD']);
        $branch = trim($branch);
        if ($read !== 0 || $branch === '' || $branch === 'HEAD') {
            Cli::errors($output)->writeln('    stands on no branch, so there is nothing here to merge.');

            return null;
        }

        // A session that died mid-write leaves a tree the rebase would refuse
        // and the merge would silently leave behind. Either way the half that
        // is done is on nobody's branch, which is the state this whole
        // arrangement exists to prevent.
        [, $dirty] = Checkouts::run(['git', '-C', $path, 'status', '--porcelain']);
        if (trim($dirty) !== '') {
            Cli::errors($output)->writeln(
                "    has changes nobody committed, and nothing below would carry them:\n"
                . self::indent(trim($dirty)) . "\n"
                . '    Commit them on ' . $branch . ' or throw them away, then ask again.',
            );

            return null;
        }

        [$rebased, $said] = Checkouts::run(['git', '-C', $path, 'rebase', 'main']);
        if ($rebased !== 0) {
            Checkouts::run(['git', '-C', $path, 'rebase', '--abort']);
            Cli::errors($output)->writeln(
                "    does not rebase onto main, and the rebase is put back where it was:\n" . self::indent(trim($said)),
            );

            return null;
        }
        $output->writeln('    rebased onto main');

        [$green, $said] = Checkouts::run(['composer', 'ci'], $path);
        if ($green !== 0) {
            Cli::errors($output)->writeln(
                "    is rebased and red, so nothing merged and the worktree is still there:\n" . self::indent(self::tail($said)),
            );

            return null;
        }
        $output->writeln('    composer ci is green on what main has become');

        [$merged, $said] = Checkouts::run(['git', '-C', $root, 'merge', '--ff-only', $branch]);
        if ($merged !== 0) {
            Cli::errors($output)->writeln(
                "    will not fast-forward, which is what this procedure stops for:\n" . self::indent(trim($said)),
            );

            return null;
        }
        $output->writeln('    merged into main as a fast-forward');

        [$removed, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'remove', '.worktrees/' . $name]);
        if ($removed !== 0) {
            Cli::errors($output)->writeln('    is merged, and the worktree is still standing: ' . trim($said));

            return $branch;
        }

        [$deleted, $said] = Checkouts::run(['git', '-C', $root, 'branch', '-d', $branch]);
        $output->writeln($deleted === 0
            ? '    worktree removed, ' . $branch . ' deleted'
            : '    worktree removed, and ' . $branch . ' is still there: ' . trim($said));

        return $branch;
    }

    /**
     * The claim a merged branch left in `progress/`, put back.
     *
     * A finished todo is a deletion the merge already carried, so what is still
     * there is what the session did not finish — and `progress/` is a state for
     * as long as a branch is live. The branch is gone by the time this runs, so
     * the same file is now a lock on a todo with nothing behind it.
     *
     * @param array<int, string> $home the branches that came home
     *
     * @return array<int, string> the paths the claims moved between
     */
    private static function released(OutputInterface $output, array $home): array
    {
        $moved = [];
        foreach (Todo::progress() as $claim) {
            if (!in_array($claim['branch'], $home, true)) {
                continue;
            }

            $from = $claim['path'];
            $to = Todo::release($claim);
            $moved[] = $from;
            $moved[] = $to;
            $output->writeln($claim['title']);
            $output->writeln($claim['waitingOn'] === ''
                ? '    released into the queue, its branch having come home'
                : '    released into waiting, on ' . $claim['waitingOn']);
            $output->writeln('');
        }

        return $moved;
    }

    /**
     * The listing at the foot of each group readme, which is the one thing a
     * per-branch run cannot get right.
     *
     * It is generated from every file in the group, and a worktree sees only
     * its own new entry — so entries merged from two branches leave it short by
     * one, on the line every session is told not to touch. This is that command
     * run before `requirements:check` has to ask for it, and the commit is here
     * for the reason `todo:claim`'s is: a step left over for somebody to carry
     * out by reading is the step that gets skipped.
     *
     * @param array<int, string> $moved the claim paths a release left behind
     */
    private static function listings(OutputInterface $output, Application $application, string $root, array $moved): void
    {
        $application->doRun(new StringInput('requirements:index'), $output);
        $application->doRun(new StringInput('decisions:index'), $output);

        $paths = $moved;
        [, $changed] = Checkouts::run(['git', '-C', $root, 'status', '--porcelain', '--', 'requirements', 'decisions']);
        foreach (preg_split('/\R/', trim($changed)) ?: [] as $line) {
            $path = trim(substr(trim($line), 2));
            if (str_ends_with($path, 'readme.md')) {
                $paths[] = $path;
            }
        }

        $output->writeln('');
        if ($paths === []) {
            $output->writeln('The group listings were already what the merged entries make them.');

            return;
        }

        // Exactly the files this command wrote. The checkout it runs in is one
        // somebody is working, and a commit that swept up their afternoon is
        // worse than one nobody made.
        [$staged, $said] = Checkouts::run(array_merge(['git', '-C', $root, 'add', '--'], $paths));
        if ($staged !== 0) {
            Cli::errors($output)->writeln('The listings are rewritten and nothing is staged: ' . trim($said));

            return;
        }

        [$committed, $said] = Checkouts::run(array_merge([
            'git', '-C', $root, 'commit', '--only',
            '--message', '[TASK] Take up what the merged branches left for main to write',
            '--message', "A group listing is generated from every file in the group, and a worktree\n"
                . "sees only its own entry. A claim in progress/ is a state for as long as its\n"
                . 'branch is live, and these branches have come home.',
            '--',
        ], $paths));
        if ($committed !== 0) {
            Cli::errors($output)->writeln('The listings are rewritten and not committed: ' . trim($said));

            return;
        }

        foreach ($paths as $path) {
            $output->writeln('    ' . $path);
        }
        $output->writeln('');
    }

    /**
     * What is standing, for a caller who has not said which branch is done.
     *
     * The state of each rather than the name alone: which branch it is on, and
     * whether anything on it is uncommitted. A worktree with a dirty tree is
     * the one this command will refuse, and finding that out before naming it
     * costs nothing.
     *
     * @param array<int, string> $standing
     */
    private static function report(OutputInterface $output, string $root, array $standing): int
    {
        if ($standing === []) {
            $output->writeln('No worktree is standing, so no branch is waiting to come home.');

            return 0;
        }

        foreach ($standing as $name) {
            $path = $root . '/.worktrees/' . $name;
            [, $branch] = Checkouts::run(['git', '-C', $path, 'rev-parse', '--abbrev-ref', 'HEAD']);
            [, $dirty] = Checkouts::run(['git', '-C', $path, 'status', '--porcelain']);
            $output->writeln($name);
            $output->writeln(sprintf(
                '    %s · %s',
                trim($branch) === '' ? 'on no branch' : trim($branch),
                trim($dirty) === '' ? 'nothing uncommitted' : 'changes nobody committed',
            ));
        }

        $output->writeln('');
        $output->writeln('One of them at a time: `bin/cli todo:home <worktree>`.');

        return 0;
    }

    /**
     * The worktrees below `.worktrees/`, which is where a claim puts one.
     *
     * Read off git rather than off the directory: a `.worktrees/` entry git has
     * forgotten is not one this command can merge, and the logs `todo:claim`
     * writes live in there beside the real ones.
     *
     * @return array<int, string>
     */
    private static function standing(string $root): array
    {
        [$listed, $said] = Checkouts::run(['git', '-C', $root, 'worktree', 'list', '--porcelain']);
        if ($listed !== 0) {
            return [];
        }

        $names = [];
        foreach (preg_split('/\R/', trim($said)) ?: [] as $line) {
            if (!str_starts_with($line, 'worktree ')) {
                continue;
            }
            $path = substr($line, strlen('worktree '));
            if (str_starts_with($path, $root . '/.worktrees/')) {
                $names[] = basename($path);
            }
        }

        return $names;
    }

    /** The end of a captured run, which is where a suite says what failed. */
    private static function tail(string $said, int $lines = 30): string
    {
        $all = preg_split('/\R/', trim($said)) ?: [];

        return implode("\n", array_slice($all, -$lines));
    }

    private static function indent(string $text): string
    {
        return implode("\n", array_map(
            static fn(string $line): string => $line === '' ? $line : '    ' . $line,
            preg_split('/\R/', $text) ?: [],
        ));
    }
}
