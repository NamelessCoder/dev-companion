<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\TaskIntents;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Tests\Support\TemporaryInstallation;
use Typo3CmsMcp\Tool\Registry;

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
        $result = Registry::call('typo3_architecture_lookup', ['id' => 'language-files']);

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
            $text = Registry::call($tool, ['query' => $query, 'task' => $query])->text;
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

    #[Test]
    public function whoIsObligedIsWrittenAsDataToo(): void
    {
        // Same rule as the version range, for the other question a statement
        // can answer differently per caller: an answer cannot filter or mark
        // what is phrased inside the sentence, and "core" is the only value
        // there is — a second one is a vocabulary change, not a data entry.
        foreach (ArchitectureHints::load() as $hint) {
            foreach (array_merge([$hint], $hint['hints']) as $entry) {
                if (($entry['binding'] ?? null) !== null) {
                    self::assertSame(
                        ArchitectureHints::BINDING_CORE,
                        $entry['binding'],
                        $hint['id'] . ' binds to something this server has no vocabulary for',
                    );
                }
            }
        }

        // The task intents answer the same question about the same caller and
        // spelled it coreOnly: true, which is the same axis in a second
        // vocabulary — and a boolean cannot carry the value a third audience
        // would need. One name, one enforced value, both corpora.
        foreach (TaskIntents::load() as $intent) {
            if ($intent['binding'] !== null) {
                self::assertSame(
                    ArchitectureHints::BINDING_CORE,
                    $intent['binding'],
                    $intent['id'] . ' binds to something this server has no vocabulary for',
                );
            }
        }
    }

    #[Test]
    public function anExtensionThatServesTwoMajorsIsAnsweredForBoth(): void
    {
        // One codebase, two majors: what arrived in 14 and what is still true
        // on 13 are both rules its author has to hold, and the difference
        // between them is the constraint the code is written around.
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'type' => 'typo3-cms-extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame([13, 14], Versions::targets(), 'the declaration decides where there is one');
        self::assertSame(14, Versions::target(), 'what the repository runs is still a single version');
        self::assertSame([14], Versions::targets('14'), 'a caller who names a version is asking about it');

        $statements = implode("\n", array_column(
            ArchitectureHints::byId('extension-files', Versions::targets())['hints'],
            'text',
        ));
        self::assertStringContainsString(
            'what makes a directory an extension outside Composer',
            $statements,
            'the rule that still holds on 13.4 is dropped when the answer is filtered to the installed major',
        );
        self::assertStringContainsString(
            'deprecated fallback',
            $statements,
            'and the rule that arrived in 14 is in the same answer',
        );
    }

    #[Test]
    public function theAnswerSaysWhichMajorsItWasComposedFor(): void
    {
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_architecture_lookup', ['id' => 'extension-files']);

        self::assertSame([13, 14], $result->data['targetVersions']);
        self::assertSame(14, $result->data['targetVersion']);
        self::assertStringContainsString('TYPO3 v13 and v14 at once', $result->text);
        self::assertStringContainsString('^13.4 || ^14.3', $result->text);
    }

    #[Test]
    public function aStatedMajorSaysWhichOtherOneItLeftOut(): void
    {
        // How the widening is switched off in practice: a session reads 14.3.0
        // out of typo3_project_scope and states it, because restating what the
        // repository runs looks like the accurate thing to do. Narrowing is
        // then correct — it was asked for — but invisible, and what comes back
        // is the answer this filtering was changed to stop giving. So the
        // answer names the major it was composed for, the ones the repository
        // declares beside it, and that their statements are missing.
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^13.4 || ^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $narrowed = Registry::call('typo3_architecture_lookup', ['id' => 'extension-files', 'targetVersion' => '14.3']);

        self::assertSame([14], $narrowed->data['targetVersions'], 'what the caller stated still wins');
        self::assertStringContainsString('Answered for TYPO3 v14 alone', $narrowed->text);
        self::assertStringContainsString('^13.4 || ^14.3', $narrowed->text);
        self::assertStringContainsString('only on v13 is missing from this answer', $narrowed->text);
        self::assertStringContainsString('Leave targetVersion out', $narrowed->text);

        // The task guide is where the review stated its version first, and it
        // said nothing at all about it.
        $guide = Registry::call('typo3_task_guide', ['task' => 'Review this extension', 'targetVersion' => '14.3']);
        self::assertStringContainsString('Answered for TYPO3 v14 alone', $guide->text);

        // A repository that declares one major has no wider answer to point
        // at, and the sentence stays the one it always was.
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $single = Registry::call('typo3_architecture_lookup', ['id' => 'extension-files', 'targetVersion' => '14.3']);
        self::assertStringContainsString('Answered for TYPO3 v14: statements that do not hold', $single->text);
        self::assertStringNotContainsString('Answered for TYPO3 v14 alone', $single->text);
        self::assertStringNotContainsString(
            'Answered for TYPO3',
            Registry::call('typo3_task_guide', ['task' => 'Review this extension', 'targetVersion' => '14.3'])->text,
            'and the ordinary task guide says nothing about versions at all',
        );
    }

    #[Test]
    public function aConstraintIsReadByAskingItAboutEachCoveredMajor(): void
    {
        self::assertSame([13, 14], Versions::declared('^13.4 || ^14.3'));
        self::assertSame([13], Versions::declared('^13.4'));
        self::assertSame([13], Versions::declared('~13.4'));
        self::assertSame([12, 13, 14], Versions::declared('>=12.4 <15'));
        self::assertSame([12, 13, 14], Versions::declared('>=12.4,<15.0'));
        self::assertSame(Versions::majors(), Versions::declared('*'));

        // Unreadable is not "everything": the caller falls back to the single
        // installed version, which is what this has always answered.
        self::assertSame([], Versions::declared('dev-main'));
        self::assertSame([], Versions::declared(null));
        self::assertSame([], Versions::declared('   '));
    }

    #[Test]
    public function aPinToOneMajorIsTellableFromAConstraintThatSpansTwo(): void
    {
        // What `bin/cli catalog check` asks of the Fluid constraint in each core
        // checkout, and the reason D-VER-3 needs no engine axis: the pin is what
        // makes the TYPO3 major carry the engine, so a constraint that stops
        // pinning has to be tellable from one that never did. Asked over engine
        // majors rather than covered TYPO3 ones, which is the point of asking per
        // major instead of parsing a range.
        $spans = static fn(string $constraint): array => array_values(array_filter(
            range(1, 20),
            static fn(int $major): bool => Versions::admits($constraint, $major),
        ));

        self::assertSame([2], $spans('^2.15.0'));
        self::assertSame([4], $spans('^4.6.1'));
        self::assertSame([5], $spans('~5.3.1'));
        self::assertSame([4, 5], $spans('^4.6.1 || ^5.0'));
        self::assertSame([4, 5], $spans('>=4.6.1 <6'));

        // Unreadable is not a pin either: a constraint nothing here can read is a
        // constraint nobody can say the engine major from.
        self::assertSame([], $spans('dev-main'));
    }

    #[Test]
    public function oneDeclaredMajorAnswersExactlyAsBefore(): void
    {
        $root = $this->composerProject('vendor', '14.3.5');
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/extension',
            'require' => ['typo3/cms-core' => '^14.3'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertSame([14], Versions::targets());
        self::assertStringContainsString(
            'Answered for TYPO3 v14',
            Registry::call('typo3_architecture_lookup', ['id' => 'extension-files'])->text,
        );
    }
}
