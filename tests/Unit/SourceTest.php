<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\Source;

/**
 * What a tool says it can be answered from, held to what it does.
 *
 * The declaration is read twice over — a client reads it at the foot of every
 * description, and typo3_server_scope groups the tools by it to say what is
 * worth calling with nothing running. Both are wrong in the same way if a tool
 * declares a source it never answers from, and neither reader can tell.
 */
final class SourceTest extends TestCase
{
    #[Test]
    public function everyToolDeclaresWhatCanAnswerIt(): void
    {
        foreach (Registry::definitions() as $definition) {
            self::assertNotSame([], $definition['answersFrom'], $definition['name'] . ' declares no source');
        }
    }

    /**
     * The declaration is one place and the description a client reads is
     * generated from it, so a tool cannot claim a source it does not read —
     * `D-ANS-048`.
     */
    #[Decision('D-ANS-048')]
    #[Test]
    public function theDescriptionACallerReadsCarriesThem(): void
    {
        foreach (Registry::definitions() as $definition) {
            self::assertStringEndsWith(
                Source::clause(array_map(
                    static fn(string $value): Source => Source::from($value),
                    $definition['answersFrom'],
                )),
                $definition['description'],
                $definition['name'],
            );
        }
    }

    #[Decision('D-ANS-048')]
    #[Test]
    public function theAnsweredByCasesAreTheDeclaredSources(): void
    {
        // The field labels one answer, the declaration describes the tool, and
        // a tool offering `packages` in the field while declaring it cannot be
        // answered that way promises a fallback that will never arrive —
        // `D-ANS-048`.
        foreach (Registry::definitions() as $definition) {
            $cases = $definition['outputSchema']['properties']['answeredBy']['enum'] ?? null;
            if ($cases === null) {
                continue;
            }
            self::assertSame(
                array_values(array_intersect(
                    $definition['answersFrom'],
                    [Source::Installation->value, Source::Packages->value],
                )),
                $cases,
                $definition['name'],
            );
        }
    }

    /**
     * And the orientation answer groups the offered tools by the same
     * declaration, with each source's meaning read off the enum rather than
     * written twice — `D-ANS-048`.
     */
    #[Decision('D-ANS-048')]
    #[Test]
    public function theOrientationAnswerGroupsEveryOfferedTool(): void
    {
        $grouped = [];
        foreach (Registry::call('typo3_server_scope', [])->data['answersFrom'] as $entry) {
            self::assertNotSame([], $entry['tools'], $entry['source'] . ' is listed with no tool');
            self::assertSame(Source::from($entry['source'])->meaning(), $entry['meaning']);
            array_push($grouped, ...$entry['tools']);
        }

        $offered = array_column(Registry::definitions(), 'name');
        $grouped = array_values(array_unique($grouped));
        sort($offered);
        sort($grouped);

        self::assertSame($offered, $grouped, 'every offered tool stands under at least one source');
    }
}
