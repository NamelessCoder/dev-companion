<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Cli;
use Typo3CmsMcp\Todo;

final class CliTest extends TestCase
{
    /**
     * The queue is worked before what recurs every session, and this is the
     * test because the failure was silent for as long as it existed. A sighting
     * is due while anything is unjudged, feedback arrive from every session
     * everywhere, and one session judges a handful — so with the sightings
     * asked first, every session opened on the same one and no queued item was
     * ever reached. Nothing was broken, nothing failed, and the queue simply
     * never came up.
     */
    #[Test]
    public function theSightingsWaitForAnEmptyQueue(): void
    {
        self::assertNotSame([], Todo::items(), 'nothing is queued, and the test says nothing about an empty queue');
        self::assertNotSame([], Todo::sightings(), 'nothing recurs every session, so nothing could wait for the queue');

        ob_start();
        Cli::run(['next']);
        $printed = (string) ob_get_clean();

        foreach (Todo::sightings() as $sighting) {
            self::assertStringNotContainsString(
                $sighting['title'],
                $printed,
                '"' . $sighting['title'] . '" is a sighting and came up while the queue still had ' . count(Todo::items()) . ' items',
            );
        }
    }

    /**
     * Every recurring todo is one of the two, and the cadence is what says
     * which: a clock makes it an appointment, `session` makes it a sighting.
     * `bin/cli next` asks the one group before the queue and the other after
     * it, so a todo in neither would be asked at no point at all.
     */
    #[Test]
    public function whatRecursIsEitherAnAppointmentOrASighting(): void
    {
        $split = array_merge(Todo::appointments(), Todo::sightings());

        self::assertEqualsCanonicalizing(
            array_column(Todo::recurring(), 'title'),
            array_column($split, 'title'),
            'the two groups are not the recurring todos',
        );
    }
}
