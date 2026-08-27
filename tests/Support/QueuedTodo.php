<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Support;

use PHPUnit\Framework\Attributes\After;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Todo;

/**
 * A queue of the test's own, for the cases that need a todo in it to hold.
 *
 * The queue empties, which is not the repository running out but the state the
 * sightings are reached in. Three cases opened with
 * `assertNotSame([], Todo::items())` instead, which made the ordinary commit
 * that finishes the last todo the one commit that could not be green. A
 * precondition that is a state rather than a property of this checkout is made
 * here rather than asserted, and the suite may not skip itself past one either
 * (`D-FBK-013`). It is made below the system temporary directory and `Todo` is
 * pointed at it for the length of the case, because a unit test writes into no
 * directory this repository keeps (`R-COD-003`).
 *
 * @phpstan-import-type Section from Todo
 */
trait QueuedTodo
{
    /** What a fixture is called, so a case can pick its own out of what it wrote. */
    private const MARKER = 'phpunit-todo-fixture';

    /** The queue this case is writing into, made on the first write. */
    private ?string $ownQueue = null;

    /**
     * How many this case has queued, which is what keeps two of them apart.
     *
     * An id is derived from what the todo is named after (`D-DOC-061`), and two
     * fixtures of one case are named after the same thing on the same day. The
     * count is what the repository has instead — the instant a card or a hand
     * written todo is created — and it is per case, so a name is the same on
     * every run.
     */
    private int $queued = 0;

    /**
     * What this case wrote, by file name, in the order it wrote them.
     *
     * A todo is named by its id and nothing else — `D-DOC-061` — so a fixture
     * is told from the queue it sits in rather than from anything readable in
     * its path.
     *
     * @var array<int, string>
     */
    private array $written = [];

    #[After]
    public function removeQueuedTodos(): void
    {
        // Both seams go back before anything else, so a case that fails
        // halfway still leaves the next one reading this checkout's own queue
        // and starting this machine's own git.
        Todo::useDirectory(null);
        Checkouts::useRunner(null);

        if ($this->ownQueue === null) {
            return;
        }

        $queue = $this->ownQueue;
        $this->ownQueue = null;
        $this->queued = 0;
        $this->written = [];
        if (!is_dir($queue)) {
            return;
        }

        foreach (Finder::create()->files()->in($queue) as $file) {
            unlink($file->getPathname());
        }
        // Deepest first, so a directory is empty by the time it is removed.
        $directories = [];
        foreach (Finder::create()->directories()->in($queue) as $directory) {
            $directories[] = $directory->getPathname();
        }
        usort($directories, static fn(string $a, string $b): int => substr_count($b, '/') <=> substr_count($a, '/'));
        foreach ($directories as $directory) {
            @rmdir($directory);
        }
        @rmdir($queue);
    }

    /**
     * The queue this case writes into, made once and pointed at.
     *
     * Empty rather than a copy of the real one: what these cases hold is what
     * a claim, a release and the order `todo:next` reads do to a queue, and
     * every one of them says which todos are in it first. A copy would make
     * the case depend on what this checkout happens to be carrying, which is
     * the dependency the fixtures existed to remove.
     */
    private function ownQueue(): string
    {
        if ($this->ownQueue !== null) {
            return $this->ownQueue;
        }

        // A root of its own with a `todo` in it, because a todo's path is
        // relative to that root and moving one resolves the two against each
        // other.
        $root = sys_get_temp_dir() . '/' . self::MARKER . '-' . getmypid() . '-' . bin2hex(random_bytes(6));
        foreach (['open', 'waiting', 'recurring', 'reference'] as $stage) {
            mkdir($root . '/todo/' . $stage, 0o777, true);
        }

        Todo::useDirectory($root . '/todo');

        return $this->ownQueue = $root;
    }

    /**
     * One queued todo, behind whatever this case has already queued.
     *
     * It is `low` and carries today's id, which is what puts it last: below
     * everything judged higher and behind the other `low` ones because they
     * are older. A case about the order says which priority and which day it
     * needs instead.
     *
     * What it serves and what its step says are the same two parameters: a case
     * about one todo needs neither, and a case about the relation between two
     * of them — the card a feedback arrived with and the todo a judgement replaced it
     * with — is about nothing else.
     *
     * @return Section
     */
    private function queueATodo(
        string $priority = 'low',
        ?string $day = null,
        string $serves = 'todo/',
        string $step = 'The step this fixture stands for.',
        string $seed = self::MARKER,
    ): array {
        $name = Todo::id($seed . ++$this->queued, $day) . '.md';
        $this->written[] = $name;
        file_put_contents(
            $this->ownQueue() . '/todo/open/' . $name,
            '# ' . self::MARKER . "\n\n**Serves:** " . $serves . "\n**Priority:** " . $priority
            . "\n\n" . $step . "\n",
        );

        return $this->ownTodos(Todo::items(), $name)[0];
    }

    /**
     * One queued todo a worktree stands on, as the git this case is given
     * answers for it.
     *
     * A claim is a worktree and nothing else — `D-DOC-060` — so what a case
     * about one arranges is the answer `git worktree list` gives, not a file.
     */
    private function worktreeOn(string $branch, string $directory = self::MARKER): string
    {
        return 'worktree ' . $this->ownQueue() . '/.worktrees/' . $directory
            . "\nHEAD 0000000000000000000000000000000000000000\nbranch refs/heads/" . $branch . "\n";
    }

    /**
     * One recurring todo of this case's own, at the cadence it names.
     *
     * `session` is the one the sightings are read from, and a case about what
     * comes before the queue needs one to exist. It used to read whichever the
     * repository was carrying, which made the case pass or fail on a directory
     * it was not about.
     *
     * A cadence in days needs the other two lines with it, because an
     * appointment is due on both: the date it was last run, and what its own
     * command answers when it is run again.
     */
    private function recurATodo(
        string $every = 'session',
        string $title = self::MARKER . '-recurring',
        string $run = '',
        string $checked = '',
    ): void {
        file_put_contents(
            $this->ownQueue() . '/todo/recurring/' . $title . '.md',
            '# ' . $title . "\n\n**Serves:** todo/\n**Every:** " . $every
            . ($checked === '' ? '' : "\n**Checked:** " . $checked)
            . ($run === '' ? '' : "\n**Run:** " . $run)
            . "\n\nThe reading this fixture stands for.\n",
        );
    }

    /**
     * A git that answers the same thing whatever it is asked.
     *
     * What these cases are about is what this repository does with the answer,
     * and every one of them asks git once. A real worktree would be a directory
     * and a branch made on the machine the suite runs on, which is what
     * `R-COD-003` is about.
     */
    private function gitSaying(string $output): CommandRunner
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(['ok' => true, 'exitCode' => 0, 'output' => $output, 'error' => '']);

        return $git;
    }

    /**
     * A git that answers each command by what it is asked, and records the lot.
     *
     * A case about a sequence of git calls needs different answers to different
     * questions, which the one-answer stub above cannot give. The key is the
     * first argument after the `-C <path>` pair, so a case says `worktree` or
     * `rev-list` rather than repeating the whole line.
     *
     * @param array<string, array{0: int, 1: string}> $answers by the git subcommand
     * @param array<int, string>                      $asked   filled in with every command run
     */
    private function gitAnswering(array $answers, array &$asked): CommandRunner
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturnCallback(
            static function (array $command) use ($answers, &$asked): array {
                $asked[] = implode(' ', $command);
                $subcommand = $command[3] ?? '';
                [$exitCode, $output] = $answers[$subcommand] ?? [0, ''];

                return ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => ''];
            },
        );

        return $git;
    }

    /** One todo blocked on an answer, which is what `bin/cli todo:waiting` reports. */
    private function waitATodo(string $waitingOn = 'the answer this fixture stands for'): void
    {
        $name = Todo::id(self::MARKER . ++$this->queued) . '.md';
        $this->written[] = $name;
        file_put_contents(
            $this->ownQueue() . '/todo/waiting/' . $name,
            '# ' . self::MARKER . "\n\n**Serves:** todo/\n**Waiting on:** " . $waitingOn
            . "\n\nThe step this question blocks.\n",
        );
    }

    /**
     * This case's own todos among what it queued, in the order they were
     * handed over.
     *
     * @param array<int, Section> $todos
     * @param string|null         $named one file of them, where the case wrote more than one
     *
     * @return array<int, Section>
     */
    private function ownTodos(array $todos, ?string $named = null): array
    {
        $wanted = $named === null ? $this->written : [$named];

        return array_values(array_filter(
            $todos,
            static fn(array $todo): bool => in_array(basename($todo['path']), $wanted, true),
        ));
    }
}
