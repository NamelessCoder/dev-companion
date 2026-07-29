<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\ArchitectureHints;
use Typo3CmsMcp\Instance;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tools;
use Typo3CmsMcp\Versions;

/**
 * Which TYPO3 an answer is for, and what that leaves out.
 *
 * A convention that is current on the development line may not exist on the LTS
 * a site runs. Handing it over anyway produces code that fails at runtime and
 * fails silently — which is why the range is data rather than a sentence.
 */
final class VersionsTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        Instance::discoverFrom(null);
    }

    #[Test]
    public function theCoveredVersionsAreDeclaredInOnePlaceAndSorted(): void
    {
        $covered = Versions::covered();

        self::assertNotSame([], $covered);
        self::assertSame(Versions::majors(), array_values(array_unique(Versions::majors())));
        $sorted = Versions::majors();
        sort($sorted);
        self::assertSame($sorted, Versions::majors());
        foreach ($covered as $version) {
            self::assertNotSame('', $version['branch'], 'a covered version names the branch it is verified against');
        }
    }

    #[Test]
    public function aStatedVersionWinsOverTheInstallationBeingRead(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', '13.4.33'));

        self::assertSame(13, Versions::target(null), 'the installation answers when nobody stated one');
        self::assertSame(12, Versions::target('12.4'), 'a caller working on another line says so');
        self::assertSame(15, Versions::target('v15'));
    }

    #[Test]
    public function withoutAnyVersionNothingIsFiltered(): void
    {
        self::assertNull(Versions::target(null));
        self::assertTrue(Versions::holds(14, null, null));
        self::assertTrue(Versions::holds(null, 13, null));
    }

    #[Test]
    public function aRangeIsWhatItSays(): void
    {
        self::assertTrue(Versions::holds(14, null, 14));
        self::assertFalse(Versions::holds(14, null, 13));
        self::assertTrue(Versions::holds(null, 13, 12));
        self::assertFalse(Versions::holds(null, 13, 14));
        self::assertTrue(Versions::holds(13, 14, 14));
        self::assertFalse(Versions::holds(13, 14, 15));

        self::assertSame('', Versions::label(null, null));
        self::assertSame('TYPO3 v14 and newer', Versions::label(14, null));
        self::assertSame('up to TYPO3 v13', Versions::label(null, 13));
        self::assertSame('TYPO3 v13 to v14', Versions::label(13, 14));
    }

    #[Test]
    public function aStatementIsLeftOutOfAnAnswerItDoesNotHoldFor(): void
    {
        // Translation domains do not exist below the version they arrived in,
        // and the domain string is syntactically fine there — the label just
        // renders empty.
        $statements = static fn(?int $target): array => array_column(
            ArchitectureHints::byId('language-files', $target)['hints'],
            'text'
        );

        $onThirteen = implode("\n", $statements(13));
        self::assertStringNotContainsString('translation domain', $onThirteen);

        $onFourteen = implode("\n", $statements(14));
        self::assertStringContainsString('translation domain', $onFourteen);
    }

    #[Test]
    public function withoutATargetTheStatementComesBackWithItsRange(): void
    {
        $result = Tools::call('typo3_architecture_lookup', ['id' => 'language-files']);

        self::assertNull($result->data['targetVersion']);
        $bound = array_values(array_filter(
            $result->data['hints'][0]['hints'],
            static fn(array $statement): bool => $statement['since'] !== null || $statement['until'] !== null,
        ));
        self::assertNotSame([], $bound, 'a bound statement is still returned, with the range beside it');
        self::assertStringContainsString('TYPO3 v', $bound[0]['versions']);
        self::assertStringContainsString('[TYPO3 v', $result->text);
    }

    #[Test]
    public function proseSaysThatItIsNotTheBoundHalf(): void
    {
        // The markdown documents are the long form of what the hints carry, and
        // they carry no range at all — a section describing a shape that
        // arrived in v13 reads on v12 exactly as it reads on main. Rather than
        // a second binding mechanism for prose, the answer says which of the
        // two the caller is holding and where the bound form is.
        foreach (['typo3_rule_lookup' => 'event listener', 'typo3_script_lookup' => 'unit tests'] as $tool => $query) {
            $text = Tools::call($tool, ['query' => $query, 'task' => $query])->text;
            self::assertStringContainsString('not filtered by version', $text, $tool);
            self::assertStringContainsString('typo3_architecture_lookup with targetVersion', $text, $tool);
        }
    }

    #[Test]
    public function theRangeIsNeverWrittenIntoTheSentence(): void
    {
        // A version in the prose cannot be filtered, re-rendered or checked,
        // and it is the thing that goes stale silently.
        foreach (ArchitectureHints::load() as $hint) {
            foreach ($hint['hints'] as $statement) {
                self::assertDoesNotMatchRegularExpression(
                    '/\bTYPO3 v\d|\bsince v?\d|\bfrom v\d/i',
                    $statement['text'],
                    $hint['id'] . ' dates a statement in its prose instead of binding it',
                );
            }
        }
    }
}
