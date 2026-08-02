<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;
use Typo3CmsMcp\Upkeep\ToolAnswers;
use Typo3CmsMcp\Upkeep\ToolCalls;

/**
 * The recording of what the tools answered, as far as it can be held here.
 *
 * Not that it is current: it is a run against an installation, no test run has
 * one, and a page a command only some machines can produce may not be able to
 * turn the suite red. What is held is the shape it is written in — that the
 * data stays parseable after being cut, and that no absolute path survives into
 * a page every reader of this package gets.
 */
final class ToolAnswersTest extends TestCase
{
    #[Test]
    public function everyRecordedAnswerIsStillJsonAfterBeingCut(): void
    {
        $page = (string) file_get_contents(ToolAnswers::file());

        preg_match_all('/```json\n(.*?)\n```/s', $page, $blocks);
        self::assertNotSame([], $blocks[1], 'the recording carries no data at all');

        foreach ($blocks[1] as $block) {
            self::assertNotNull(
                json_decode($block, true),
                'a recorded block is not JSON, so what a cut left behind is a broken document: '
                    . substr($block, 0, 120),
            );
        }
    }

    /**
     * The page ships inside this package, so a path from the machine that
     * recorded it would be in every checkout of it. The substitutions are
     * `ToolAnswers`' own and the head says they happened.
     */
    #[Test]
    public function theRecordingCarriesNobodysDirectoryLayout(): void
    {
        $page = (string) file_get_contents(ToolAnswers::file());
        $home = (string) getenv('HOME');

        self::assertStringNotContainsString(Paths::root(), $page);
        if ($home !== '') {
            self::assertStringNotContainsString($home, $page);
        }
    }

    /**
     * The recording is of the table the contract test drives, so a call added
     * to one is a call the other shows. It may be older than the table — that
     * is the whole point of it not being checked — so what is asserted is that
     * every tool in the table has a section, not that the sections match call
     * for call.
     */
    #[Test]
    public function everyToolTheTableDrivesHasARecordedAnswer(): void
    {
        $page = (string) file_get_contents(ToolAnswers::file());

        $missing = [];
        foreach (array_unique(array_column(ToolCalls::all(), 0)) as $name) {
            if (!str_contains($page, '## `' . $name . '`')) {
                $missing[] = $name;
            }
        }

        self::assertSame([], $missing, 'driven by the table and recorded nowhere — run bin/cli tools:record');
    }

    /**
     * The two tools the table leaves out, and why, so a session that adds them
     * has to read the reason first: one writes, and the other answers with
     * prose somebody else wrote.
     */
    #[Test]
    public function theTableDrivesEveryToolItIsSafeToDrive(): void
    {
        $driven = array_unique(array_column(ToolCalls::all(), 0));
        $offered = array_column(Registry::definitions(), 'name');

        self::assertSame(
            ['typo3_feedback_record', 'typo3_feedback_list'],
            array_values(array_diff($offered, $driven)),
            'a tool joined or left the table without its reason being written down',
        );
    }
}
