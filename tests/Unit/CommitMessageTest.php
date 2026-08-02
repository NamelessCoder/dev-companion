<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Typo3CmsMcp\Knowledge\CommitMessage;

final class CommitMessageTest extends TestCase
{
    #[Test]
    public function theDraftCarriesKeywordIssueAndReleases(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Show hidden records in impexp import preview',
            'issue' => '106123',
            'releases' => ['main', '13.4'],
        ]);

        self::assertStringStartsWith('[BUGFIX] Show hidden records', $result['message']);
        self::assertStringContainsString("\nResolves: #106123", $result['message']);
        self::assertStringContainsString("\nReleases: main, 13.4", $result['message']);
    }

    #[Test]
    public function proseIsWrappedAtSeventyTwoCharacters(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Fix it',
            'issue' => '1',
            'body' => 'The import preview filtered hidden records because the query applied '
                . 'the default restrictions, which the preview never asked for.',
        ]);

        foreach ($this->bodyLines($result['message']) as $line) {
            self::assertLessThanOrEqual(72, mb_strlen($line), 'unwrapped line: ' . $line);
        }
        self::assertSame([], $this->checkCodes($result['checks'], 'body-line-too-long'));
    }

    #[Test]
    public function aLongUrlIsKeptIntactAndReported(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html';

        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Document it',
            'issue' => '1',
            'body' => 'See ' . $url . ' for details.',
        ]);

        self::assertStringContainsString($url, $result['message']);
        self::assertNotSame([], $this->checkCodes($result['checks'], 'body-line-too-long'));
    }

    #[Test]
    public function fencedCodeAndIndentedBlocksAreNotReflowed(): void
    {
        $body = "Explanation.\n\n```php\n\$queryBuilder->getRestrictions()->removeAll();\n```\n\n    indented output stays indented";

        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Keep structure',
            'issue' => '1',
            'body' => $body,
        ]);

        self::assertStringContainsString("```php\n\$queryBuilder->getRestrictions()->removeAll();\n```", $result['message']);
        self::assertStringContainsString("\n    indented output stays indented", $result['message']);
    }

    #[Test]
    public function aListItemKeepsItsMarkerAndGetsAHangingIndent(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Wrap lists',
            'issue' => '1',
            'body' => '- the first item is long enough that it has to be wrapped across two lines to fit',
        ]);

        $lines = $this->bodyLines($result['message']);
        self::assertStringStartsWith('- the first item', $lines[0]);
        self::assertStringStartsWith('  ', $lines[1], 'a wrapped list item continues indented');
    }

    #[Test]
    public function aMissingIssueIsAnError(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing']);

        self::assertContains('missing-issue', array_column($result['checks'], 'code'));
        self::assertStringContainsString('Resolves: #ISSUE_NUMBER', $result['message']);
    }

    #[Test]
    public function summaryLengthIsCheckedAgainstBothLimits(): void
    {
        $long = CommitMessage::create(['changeType' => 'TASK', 'summary' => str_repeat('a', 80), 'issue' => '1']);
        self::assertContains('summary-too-long', array_column($long['checks'], 'code'));

        $preferred = CommitMessage::create(['changeType' => 'TASK', 'summary' => str_repeat('a', 60), 'issue' => '1']);
        self::assertContains('summary-length-preferred', array_column($preferred['checks'], 'code'));
    }

    #[Test]
    public function deprecationRulesAreEnforced(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'BUGFIX',
            'summary' => 'Deprecate something',
            'issue' => '1',
            'isDeprecation' => true,
            'isBreaking' => true,
        ]);

        $codes = array_column($result['checks'], 'code');
        self::assertContains('deprecation-breaking-prefix', $codes);
        self::assertContains('deprecation-keyword', $codes);
        self::assertContains('changelog-required', $codes);
    }

    #[Test]
    public function aCleanDraftReportsThatNothingIsWrong(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
        ]);

        self::assertSame(['no-issues-found'], array_column($result['checks'], 'code'));
    }

    #[Test]
    public function theDraftNeverCarriesAReleaseTheCallerDidNotName(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing', 'issue' => '1']);

        self::assertStringContainsString('Releases: RELEASE_TARGET', $result['message']);
        self::assertContains('missing-releases', array_column($result['checks'], 'code'));
    }

    #[Test]
    public function neitherPlaceholderCouldBeReadAsAnAnswer(): void
    {
        $message = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'Do a thing'])['message'];

        preg_match('/^Resolves: (.*)$/m', $message, $resolves);
        preg_match('/^Releases: (.*)$/m', $message, $releases);

        // What the real values look like: a Forge issue is digits, a release
        // target is main or a minor. A placeholder that fits either shape reads
        // as a decision somebody made, which is what D-GUI-001 rejected "main" for.
        self::assertDoesNotMatchRegularExpression('/^#\d+$/', $resolves[1]);
        self::assertDoesNotMatchRegularExpression('/^(main|\d+\.\d+)$/', $releases[1]);
        self::assertSame(CommitMessage::ISSUE_PLACEHOLDER, $resolves[1]);
        self::assertSame(CommitMessage::RELEASE_PLACEHOLDER, $releases[1]);
    }

    #[Test]
    public function aPlaceholderHandedBackIsStillAnUnansweredField(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nBody.\n\nResolves: #ISSUE_NUMBER\nReleases: RELEASE_TARGET\n");
        $result = CommitMessage::create($parsed['input']);

        $codes = array_column(array_merge($parsed['checks'], $result['checks']), 'code');
        self::assertContains('missing-issue', $codes, 'the placeholder is not a Forge issue');
        self::assertContains('missing-releases', $codes, 'the placeholder is not a release target');
        self::assertNotContains('no-issues-found', $codes);
        self::assertStringContainsString('Resolves: #ISSUE_NUMBER', $result['message']);
        self::assertStringContainsString('Releases: RELEASE_TARGET', $result['message']);
    }

    #[Test]
    public function aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing(): void
    {
        $parsed = CommitMessage::parse("Fix the thing\n\nBody.\n\nResolves: #1\n");
        $result = CommitMessage::create($parsed['input']);

        $codes = array_column(array_merge($parsed['checks'], $result['checks']), 'code');
        self::assertContains('missing-releases', $codes, 'the draft has no release target either');
        self::assertStringNotContainsString('Releases: main', $result['message']);
    }

    #[Test]
    public function everyCheckCarriesALevelACodeAndAMessage(): void
    {
        $result = CommitMessage::create(['changeType' => 'TASK', 'summary' => 'do a thing']);

        foreach ($result['checks'] as $check) {
            self::assertContains($check['level'], ['error', 'warning', 'info']);
            self::assertMatchesRegularExpression('/^[a-z][a-z-]+$/', $check['code']);
            self::assertNotSame('', $check['message']);
        }
    }

    #[Test]
    public function outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Add a sponsor logo field',
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertStringNotContainsString('Resolves:', $result['message']);
        self::assertStringNotContainsString('Releases:', $result['message']);
        self::assertSame(['no-issues-found'], array_column($result['checks'], 'code'));
    }

    #[Test]
    public function outsideTheCoreTheSubjectAndBodyRulesStillHold(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => str_repeat('a', 80),
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
            'body' => 'The sponsor records need an additional logo field because the '
                . 'rendering has to fall back to the organisation logo otherwise.',
        ]);

        self::assertContains('summary-too-long', array_column($result['checks'], 'code'));
        foreach ($this->bodyLines($result['message']) as $line) {
            self::assertLessThanOrEqual(72, mb_strlen($line), 'unwrapped line: ' . $line);
        }
    }

    #[Test]
    public function outsideTheCoreATrailerTheCallerWroteIsStillKept(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'TASK',
            'summary' => 'Add a sponsor logo field',
            'issue' => '66',
            'workflow' => CommitMessage::WORKFLOW_PROJECT,
        ]);

        self::assertStringContainsString("\nResolves: #66", $result['message']);
    }

    #[Test]
    public function theSecurityKeywordIsTheRepositoryOwnOutsideTheCore(): void
    {
        $parsed = CommitMessage::parse(
            "[SECURITY] Update typo3/cms-* to 13.4.33\n",
            CommitMessage::WORKFLOW_PROJECT
        );

        self::assertSame('SECURITY', $parsed['input']['changeType']);
        self::assertNotContains('security-keyword', array_column($parsed['checks'], 'code'));
    }

    #[Test]
    public function aSecurityCommitAssembledForTheCoreIsRefusedToo(): void
    {
        $result = CommitMessage::create([
            'changeType' => 'SECURITY',
            'summary' => 'Fix the thing',
            'issue' => '1',
            'releases' => ['main'],
        ]);

        self::assertContains('security-keyword', array_column($result['checks'], 'code'));
    }

    #[Test]
    public function aWorkflowNobodyKnowsIsTheCoreOne(): void
    {
        self::assertSame(CommitMessage::WORKFLOW_CORE, CommitMessage::workflow('githubb'));
        self::assertSame(CommitMessage::WORKFLOW_CORE, CommitMessage::workflow(null));
        self::assertSame(CommitMessage::WORKFLOW_PROJECT, CommitMessage::workflow('Project'));
    }

    #[Test]
    public function anExistingMessageIsSplitBackIntoItsParts(): void
    {
        $parsed = CommitMessage::parse(
            "[!!!][FEATURE] Add a thing\n\nWhy it was added.\n\n"
            . "Resolves: #1\nResolves: #2\nRelated: #3\nReleases: main, 13.4\nChange-Id: I1234\n"
        );

        self::assertSame('FEATURE', $parsed['input']['changeType']);
        self::assertTrue($parsed['input']['isBreaking']);
        self::assertSame('Add a thing', $parsed['input']['summary']);
        self::assertSame(['#1', '#2'], array_map(
            static fn(string $issue): string => CommitMessage::normalizeIssue($issue) ?? '',
            $parsed['input']['issues']
        ));
        self::assertSame(['main', '13.4'], $parsed['input']['releases']);
        self::assertSame('Why it was added.', $parsed['input']['body']);
        self::assertSame(['Change-Id: I1234'], $parsed['input']['extraTrailers']);
    }

    #[Test]
    public function anAmendKeepsTheChangeId(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main\nChange-Id: I1234\n");
        $result = CommitMessage::create($parsed['input']);

        self::assertStringEndsWith("Releases: main\nChange-Id: I1234", $result['message']);
    }

    #[Test]
    public function aMissingKeywordIsReported(): void
    {
        $parsed = CommitMessage::parse("Fix the thing\n\nBody.\n\nResolves: #1\n");

        self::assertContains('missing-keyword', array_column($parsed['checks'], 'code'));
    }

    #[Test]
    public function theSecurityKeywordIsRejected(): void
    {
        $parsed = CommitMessage::parse("[SECURITY] Fix the thing\n\nResolves: #1\nReleases: main\n");

        self::assertContains('security-keyword', array_column($parsed['checks'], 'code'));
        self::assertSame('', $parsed['input']['changeType']);
    }

    #[Test]
    public function aMissingBlankLineAfterTheSummaryIsReported(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\nBody right below.\n\nResolves: #1\nReleases: main\n");

        self::assertContains('body-blank-line', array_column($parsed['checks'], 'code'));
    }

    #[Test]
    public function aColonSentenceAtTheEndOfTheBodyIsNotATrailer(): void
    {
        $parsed = CommitMessage::parse("[TASK] Do a thing\n\nNote: this stays body text.\n\nResolves: #1\nReleases: main\n");

        self::assertSame('Note: this stays body text.', $parsed['input']['body']);
        self::assertSame([], $parsed['input']['extraTrailers']);
    }

    #[Test]
    public function anEmptyMessageIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CommitMessage::parse("   \n");
    }

    /** @return array<int, string> */
    private function bodyLines(string $message): array
    {
        $lines = explode("\n", $message);
        array_shift($lines); // subject
        $body = [];
        foreach ($lines as $line) {
            if (preg_match('/^(Resolves|Related|Releases):/', $line) === 1) {
                break;
            }
            if (trim($line) !== '') {
                $body[] = $line;
            }
        }

        return $body;
    }

    /**
     * @param array<int, array{level: string, code: string, message: string}> $checks
     * @return array<int, string>
     */
    private function checkCodes(array $checks, string $code): array
    {
        return array_values(array_filter(
            array_column($checks, 'code'),
            static fn(string $found): bool => $found === $code
        ));
    }
}
