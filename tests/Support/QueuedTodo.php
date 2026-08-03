<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Support;

use PHPUnit\Framework\Attributes\After;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Upkeep\Checkouts;
use Typo3CmsMcp\Upkeep\Todo;

/**
 * A queued todo of the test's own, for the cases that need one to hold.
 *
 * The queue empties. That is not the repository running out — it is the state
 * the sightings are reached in, and the whole of what `**Every:** session`
 * means. Three cases opened with `assertNotSame([], Todo::items())` instead,
 * which made the ordinary commit that finishes the last todo the one commit
 * that could not be green, and left the session that wrote it holding a failure
 * about nothing it had done. A precondition that is a state rather than a
 * property of this checkout is made here rather than asserted — the suite may
 * not skip itself past one either (`StructureTest::noTestSkipsItselfInsteadOfHolding`),
 * and both roads out lead here. See `D-FBK-013`.
 *
 * It goes at the end of the queue, where it displaces nothing that a case about
 * the front of the queue is reading, and carries a marker so that wherever the
 * move under test has left it, it is found again and taken away.
 *
 * @phpstan-import-type Section from Todo
 */
trait QueuedTodo
{
    /**
     * What makes a leftover recognizable. Fixtures are written into the real
     * `todo/`, because a claim, a release and the order `todo:next` reads are
     * all properties of that directory and of nothing a temporary copy could
     * stand in for.
     */
    private const MARKER = 'phpunit-todo-fixture';

    #[After]
    public function removeQueuedTodos(): void
    {
        $directories = array_values(array_filter([
            Todo::directory() . '/open',
            Todo::directory() . '/progress',
            Todo::directory() . '/waiting',
        ], is_dir(...)));

        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md')->contains(self::MARKER) as $file) {
            unlink($file->getPathname());
        }
        @rmdir(Todo::directory() . '/progress');

        // The git a case stubbed goes back with the files it wrote. A static
        // seam left standing is answered from a table written for another
        // case, and the test that reads it passes for the wrong reason.
        Checkouts::useRunner(null);
    }

    /**
     * One queued todo, behind whatever the queue already has.
     *
     * It is `low` and stamped today, which is what puts it last however long the
     * queue is: below everything somebody has judged higher, and behind the
     * other `low` ones because they are older. A case about the front of the
     * queue is therefore reading something this fixture cannot have displaced.
     *
     * A case that is about the order says which priority and which stamp it
     * needs instead.
     *
     * @return Section
     */
    private function queueATodo(string $priority = 'low', ?string $stamp = null): array
    {
        $name = sprintf('%s-%s.md', $stamp ?? date('Y-m-d-His'), self::MARKER);
        file_put_contents(
            Todo::directory() . '/open/' . $name,
            '# ' . self::MARKER . "\n\n**Serves:** todo/\n**Priority:** " . $priority
            . "\n\nThe step this fixture stands for.\n",
        );

        return $this->ownTodos(Todo::items(), $name)[0];
    }

    /**
     * This test's own todos among the repository's, in the order they were
     * handed over. Sessions working several todos at once leave real claims in
     * `progress/` while the suite runs, so what a case wrote is only readable
     * by picking it back out.
     *
     * @param array<int, Section> $todos
     * @param string|null         $named one file of them, where the case wrote more than one
     *
     * @return array<int, Section>
     */
    private function ownTodos(array $todos, ?string $named = null): array
    {
        return array_values(array_filter(
            $todos,
            static fn(array $todo): bool => str_contains($todo['path'], $named ?? self::MARKER),
        ));
    }
}
