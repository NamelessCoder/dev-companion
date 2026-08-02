<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Typo3CmsMcp\Feedback\Channel;
use Typo3CmsMcp\Feedback\Redaction;
use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Paths;
use Typo3CmsMcp\Tool\Registry;

/**
 * Feedback is the one part of the server that writes, so these tests write too.
 * Every feedback recorded here is removed again in tearDown; the marker in the
 * observation makes a leftover recognizable.
 */
final class FeedbackTest extends TestCase
{
    private const MARKER = 'phpunit-feedback-fixture';

    protected function tearDown(): void
    {
        Instance::discoverFrom(null);
        $directories = array_values(array_filter([Paths::feedback(), Paths::feedbackArchive()], is_dir(...)));
        if ($directories === []) {
            return;
        }

        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md')->contains(self::MARKER) as $file) {
            unlink($file->getPathname());
        }
    }

    #[Test]
    public function aNoteBecomesOneMarkdownFileWithFrontMatter(): void
    {
        $file = Channel::record([
            'observation' => self::MARKER . ' the lookup found nothing',
            'category' => 'missing-knowledge',
            'tool' => 'typo3_component_lookup',
            'query' => 'query=badge',
            'suggestion' => 'add the component',
        ]);

        self::assertStringStartsWith('feedback/', $file);
        $contents = (string) file_get_contents(Paths::root() . '/' . $file);
        self::assertStringContainsString('category: missing-knowledge', $contents);
        self::assertStringContainsString('status: open', $contents);
        self::assertStringContainsString('tool: typo3_component_lookup', $contents);
        self::assertStringContainsString('## Observation', $contents);
        self::assertStringContainsString('## Query', $contents);
        self::assertStringContainsString('## Suggestion', $contents);
    }

    /**
     * A feedback is prose an agent wrote, so the heading cut out of its first
     * line has to be cut by characters. `substr()` counts bytes: an em dash
     * landing across the boundary was written as its first byte or two, the
     * file stopped being valid UTF-8, and `grep` then treated it as binary and
     * matched nothing in it — which is how the one in the store was found,
     * three weeks after it was recorded.
     */
    #[Test]
    public function aHeadingIsCutBetweenCharactersRatherThanBytes(): void
    {
        // The dash sits where a 97-byte cut goes through the middle of it.
        $observation = self::MARKER . ' ' . str_repeat('a', 94 - strlen(self::MARKER)) . '— and the rest of the line';

        $contents = (string) file_get_contents(Paths::root() . '/' . Channel::record(['observation' => $observation]));

        preg_match('/^# (.*)$/m', $contents, $heading);
        self::assertSame($heading[1], mb_convert_encoding($heading[1], 'UTF-8', 'UTF-8'), 'the heading was cut through a character');
        self::assertStringEndsWith('...', $heading[1]);
    }

    #[Test]
    public function theAgentNeverControlsWhereTheNoteIsWritten(): void
    {
        $file = Channel::record([
            'observation' => '../../' . self::MARKER . " escape attempt\nsecond line",
        ]);

        self::assertSame('feedback/' . basename($file), $file);
        self::assertStringNotContainsString('..', $file);
        self::assertFileExists(Paths::feedback() . '/' . basename($file));
    }

    #[Test]
    public function aNoteSaysWhichDirectoryItWasWrittenFrom(): void
    {
        // What the stdio entrypoint does at startup: the session's own working
        // directory, which is what makes the feedback checkable later.
        Instance::discoverFrom('/home/somebody/projects/a-site');

        $file = Channel::record(['observation' => self::MARKER . ' asked in a project']);

        self::assertStringContainsString(
            'directory: /home/somebody/projects/a-site',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function aNoteWithoutACallerDirectoryClaimsNone(): void
    {
        // The HTTP case: no entrypoint handed a directory in, so the server's
        // own working directory is not passed off as the caller's.
        Instance::discoverFrom(null);

        $file = Channel::record(['observation' => self::MARKER . ' asked over http']);

        self::assertStringNotContainsString(
            'directory:',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function aNoteSaysWhichModelLeftIt(): void
    {
        // Half the feedback are about what a session did rather than about what an
        // answer said, and that is one model's behaviour. Unattributed, two
        // models' habits arrive as one report.
        $file = Channel::record([
            'observation' => self::MARKER . ' the skill was read and its lookups were not run',
            'model' => 'claude-opus-5',
        ]);

        self::assertStringContainsString(
            'model: claude-opus-5',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
        self::assertSame('claude-opus-5', self::noteFor($file)['model']);
    }

    #[Test]
    public function aNoteWithoutAModelSaysSoRatherThanCarryingNone(): void
    {
        // The write never fails on the attribution — the feedback is worth more
        // than the name — but an unattributed one says it is unattributed,
        // which a missing front-matter line cannot.
        $file = Channel::record(['observation' => self::MARKER . ' recorded by nobody in particular']);

        self::assertStringContainsString(
            'model: unknown',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
        self::assertSame(Channel::UNATTRIBUTED, self::noteFor($file)['model']);
    }

    #[Test]
    public function theRecordedNoteIsReportedWhereItActuallyIs(): void
    {
        // A relative path is relative to somewhere the caller has never been:
        // one recorded from a site package was reported as feedback/<name>.md,
        // looked for under that project, not found, and reported back as a
        // failed write.
        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' recorded through the tool',
            'model' => 'claude-opus-5',
        ]);

        $path = $result->data['path'] ?? '';
        self::assertIsString($path);
        self::assertStringStartsWith(Paths::root() . '/feedback/', $path);
        self::assertFileExists($path);
        self::assertStringContainsString($path, $result->text);
        // And it says whose checkout that is, because the caller cannot tell.
        self::assertStringContainsString('not the project you are working in', $result->text);
    }

    #[Test]
    public function anUnknownCategoryFallsBackToIdea(): void
    {
        $file = Channel::record([
            'observation' => self::MARKER . ' something',
            'category' => 'nonsense',
        ]);

        self::assertStringContainsString(
            'category: idea',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );
    }

    #[Test]
    public function anEmptyObservationIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Channel::record(['observation' => '   ']);
    }

    /**
     * The feedback of 2026-07-31 praised typo3_configuration_lookup for
     * returning the effective runtime value, and proved it by pasting the live
     * encryption key of the audited site into a repository that is committed and
     * pushed. The path and the shape were the finding; the 96 characters
     * established nothing further and left the installation that owns them.
     */
    #[Test]
    public function aValueThatLooksLikeACredentialNeverReachesTheFile(): void
    {
        $key = str_repeat('9f2b7c4d', 12);
        $redacted = [];

        $file = Channel::record([
            'observation' => self::MARKER . ' called typo3_configuration_lookup with path SYS/encryptionKey. '
                . 'The key ' . $key . ' is the active value, hardcoded in config/system/settings.php.',
        ], $redacted);

        $contents = (string) file_get_contents(Paths::root() . '/' . $file);
        self::assertStringNotContainsString($key, $contents);
        self::assertStringContainsString('[redacted: a 96-character hexadecimal value]', $contents);
        self::assertSame(['observation: a 96-character hexadecimal value'], $redacted);
        // What the finding was actually made of stays, or the guard costs more
        // than the leak it prevents.
        self::assertStringContainsString('SYS/encryptionKey', $contents);
        self::assertStringContainsString('config/system/settings.php', $contents);
        // The filename is built from the observation too, and it is the copy
        // that would have survived every grep of the file itself.
        self::assertStringNotContainsString($key, $file);
    }

    #[Test]
    public function everyFieldAFeedbackIsWrittenFromIsRead(): void
    {
        // The same value in the second field is how a session works around a
        // guard on the first without meaning to: the query is where it says
        // what it called, and the suggestion is where it says what to do about
        // it. All three are prose, and all three land in one file.
        $key = str_repeat('9f2b7c4d', 12);
        $redacted = [];

        $file = Channel::record([
            'observation' => self::MARKER . ' the key ' . $key . ' came back from the installation',
            'query' => 'path=SYS/encryptionKey returned ' . $key,
            'suggestion' => 'say that ' . $key . ' is hardcoded rather than generated',
        ], $redacted);

        self::assertStringNotContainsString($key, (string) file_get_contents(Paths::root() . '/' . $file));
        self::assertSame([
            'observation: a 96-character hexadecimal value',
            'query: a 96-character hexadecimal value',
            'suggestion: a 96-character hexadecimal value',
        ], $redacted);
    }

    #[Test]
    public function whatASessionQuotesAboutTheCoreIsLeftAlone(): void
    {
        // A rule that redacts a revision or a class name costs more than the
        // leak it prevents: these are what feedback about core patches is made
        // of, and 64 characters is where the threshold clears the longest of
        // them.
        $quoted = 'reviewed 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0 against '
            . 'typo3/sysext/core/Classes/Localization/TranslationDomainMapper, see '
            . 'Feature-109444-AddDefaultValueSupportForCountrySelectFormElement, and the '
            . 'recovery is `install:password:set` rather than guessing — the install tool '
            . 'password: it never prints the value it used';

        $redaction = Redaction::of($quoted);

        self::assertSame($quoted, $redaction->text);
        self::assertSame([], $redaction->removed);
    }

    #[Test]
    public function aValueGoesAndTheNameThatSaysWhatItWasStays(): void
    {
        // The half a length rule cannot see. A password is short, and the only
        // thing that says it is one is the name written next to it.
        $redaction = Redaction::of(
            '$GLOBALS[\'TYPO3_CONF_VARS\'][\'BE\'][\'installToolPassword\'] = \'$argon2i$v=19$m=65536$b0RTZ1p6\';',
        );

        self::assertStringNotContainsString('argon2i', $redaction->text);
        self::assertStringContainsString('installToolPassword', $redaction->text);
        self::assertStringContainsString('[redacted: the value of `installToolPassword`]', $redaction->text);
        self::assertSame(['the value of `installToolPassword`'], $redaction->removed);
    }

    #[Test]
    public function aPasswordInADatabaseUrlGoesWithoutTheHostGoingWithIt(): void
    {
        $redaction = Redaction::of('the DSN is mysqli://typo3:s3cr3tpw@db:3306/typo3');

        self::assertSame(
            'the DSN is mysqli://typo3:[redacted: the password in a URL]@db:3306/typo3',
            $redaction->text,
        );
        self::assertSame(['the password in a URL'], $redaction->removed);
    }

    #[Test]
    public function aLongBase64ValueIsTakenOutAndAWordIsNot(): void
    {
        $token = 'TXlTdXBlclNlY3JldFRva2VuVmFsdWVXaXRoUGFkZGluZzEyMzQ1Njc4OTBBQkNERUZHSElKS0xNTk9Q';

        self::assertSame(
            ['a 80-character base64 value'],
            Redaction::of('the header carried ' . $token . ' verbatim')->removed,
        );
        // Same alphabet, same length, one long camel-cased word: what the
        // corpus is full of and what a shorter threshold took out with it.
        self::assertSame([], Redaction::of(str_repeat('AnotherLongIdentifierName', 3))->removed);
    }

    #[Test]
    public function theToolSaysWhatItTookOutOfWhatItWasHanded(): void
    {
        // Marked rather than silent, and said rather than only marked. The
        // archive keeps a session's report because the report is the evidence,
        // so an altered one has to say so — and the session that wrote it is
        // the only reader who still knows what stood there.
        $result = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' the key ' . str_repeat('9f2b7c4d', 12) . ' is the active one',
            'model' => 'claude-opus-5',
        ]);

        self::assertSame(['observation: a 96-character hexadecimal value'], $result->data['redacted']);
        self::assertStringContainsString('One value was taken out', $result->text);
        self::assertStringContainsString('a 96-character hexadecimal value', $result->text);
        self::assertStringContainsString('[redacted: ...]', $result->text);

        // And an ordinary feedback says nothing about redaction at all.
        $ordinary = Registry::call('typo3_feedback_record', [
            'observation' => self::MARKER . ' the icon lookup found nothing for content-accordion',
            'model' => 'claude-opus-5',
        ]);
        self::assertSame([], $ordinary->data['redacted']);
        self::assertStringNotContainsString('redacted', $ordinary->text);
    }

    /**
     * The corpus is what the thresholds were settled against, so it is what
     * says whether they still hold: 207 recorded feedback, one value among them
     * that had to go, and every git revision, class path and changelog
     * identifier beside it left standing.
     *
     * A second file appearing here means one of two things, and both are worth
     * stopping for: a rule got greedier, or a feedback carrying a live value
     * was written by a hand rather than through the channel that guards it.
     */
    #[Test]
    public function theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor(): void
    {
        $directories = array_values(array_filter([Paths::feedback(), Paths::feedbackArchive()], is_dir(...)));
        self::assertNotEmpty($directories);

        $hits = [];
        $read = 0;
        foreach (Finder::create()->files()->in($directories)->depth(0)->name('*.md') as $file) {
            $contents = (string) file_get_contents($file->getPathname());
            if (str_contains($contents, self::MARKER)) {
                continue;
            }

            ++$read;
            $removed = Redaction::of($contents)->removed;
            if ($removed !== []) {
                $hits[$file->getFilename()] = $removed;
            }
        }

        self::assertGreaterThan(100, $read, 'the corpus this was measured against was not read');
        self::assertSame([
            '2026-07-31-185900-after-the-audit-i-invoked-typo3-cms-mcp.md' => ['a 96-character hexadecimal value'],
        ], $hits);
    }

    /**
     * A session files one feedback per subject and files them in one breath, so
     * they open on the same sentence — the one that says which session this
     * is — and that sentence is longer than a filename has room for. Eight
     * feedback of 2026-08-01 were named
     * `debrief-of-the-typo3-14-testimonials-session` to the character, differing
     * by their timestamp alone, and they were about eight different things.
     *
     * The first of a series keeps the opening, because nothing yet says it is
     * one. Every feedback after it is named after what it alone says.
     */
    #[Test]
    public function notesThatOpenAlikeAreNamedAfterWhatTellsThemApart(): void
    {
        $opening = self::MARKER . ' debrief of the session, missed item: ';

        $first = Channel::record(['observation' => $opening . 'nothing said which pid the records go to']);
        $second = Channel::record(['observation' => $opening . 'the fixture harness was written by hand']);
        $third = Channel::record(['observation' => $opening . 'clearing the cache meant deleting files']);

        self::assertStringContainsString('debrief-of-the-session', $first);
        self::assertStringContainsString('the-fixture-harness-was-written', $second);
        self::assertStringContainsString('clearing-the-cache-meant-deleting', $third);
        self::assertStringNotContainsString('debrief-of-the-session', $second, 'the name says what both feedback say');
        self::assertStringNotContainsString('debrief-of-the-session', $third, 'the name says what both feedback say');
    }

    #[Test]
    public function recordedNotesAreListedNewestFirst(): void
    {
        $file = Channel::record(['observation' => self::MARKER . ' a listed feedback']);

        $found = Channel::all('open', null, 100);
        $files = array_column($found, 'file');

        self::assertContains($file, $files);
        foreach ($found as $feedback) {
            self::assertSame('open', $feedback['status']);
            self::assertNotSame('', $feedback['title']);
        }
    }

    #[Test]
    public function aNoteThatWasWorkedOffKeepsEverythingItSaid(): void
    {
        // Closing a feedback used to mean deleting it, which left the agent that
        // wrote it seeing the file simply stop existing — and left the closed
        // half of the list as bare filenames, with the front matter that says
        // what the feedback was about gone with the file.
        $file = Channel::record([
            'observation' => self::MARKER . ' the archive keeps what the feedback said',
            'category' => 'tool-gap',
            'tool' => 'typo3_component_lookup',
        ]);

        $archived = Channel::archive($file);
        self::assertSame('feedback/archive/' . basename($file), $archived);
        self::assertFileDoesNotExist(Paths::root() . '/' . $file);
        self::assertStringContainsString(
            'status: closed',
            (string) file_get_contents(Paths::root() . '/' . $archived),
        );

        self::assertNotContains($file, array_column(Channel::all('open', null, 200), 'file'));
        $feedback = self::noteFor($archived);
        self::assertSame('closed', $feedback['status']);
        self::assertSame('tool-gap', $feedback['category']);
        self::assertSame(['typo3_component_lookup'], $feedback['tools']);

        // And the filters reach it, which is the half the deleted feedback lost.
        self::assertContains(
            $archived,
            array_column(Channel::all('closed', 'tool-gap', 200, 'typo3_component_lookup'), 'file'),
        );
    }

    #[Test]
    public function aNoteThatWasWorkedOffIsStillAnswerableFor(): void
    {
        // What came of a feedback is the commit that archived it, which is the one
        // thing the agent that reported the gap cannot see for itself: without
        // it the same gap is reported again, and a request that shipped in the
        // meantime is dropped silently.
        // Built rather than found. Reading whatever this repository happens to
        // have archived asserts the working tree, not the code: with 138 files
        // in there it can never fail, and in a fresh clone of a checkout that
        // had archived nothing it would have had nothing to say either.
        $archived = $this->archived('Answer label lookups with the translation domain');

        $closed = Channel::all('closed', null, 200);
        $mine = array_values(array_filter(
            $closed,
            static fn(array $feedback): bool => $feedback['file'] === $archived,
        ));

        self::assertCount(1, $mine, 'the archived feedback did not read back as closed');
        self::assertSame('closed', $mine[0]['status']);
        self::assertStringStartsWith('feedback/archive/', $mine[0]['file']);
        self::assertNotNull($mine[0]['closedBy']);
        self::assertSame('abc1234', $mine[0]['closedBy']['commit']);
        // The subject is the sentence that says what happened to it.
        self::assertSame('Answer label lookups with the translation domain', $mine[0]['closedBy']['subject']);

        // An open feedback is in the same list and says it is open.
        $file = Channel::record(['observation' => self::MARKER . ' open beside the closed ones']);
        $all = Channel::all('all', null, 200);
        self::assertContains($file, array_column($all, 'file'));
        self::assertContains('closed', array_column($all, 'status'));
    }

    /**
     * One archived feedback, as `bin/cli feedback:archive` leaves it: below
     * feedback/archive/, with the commit that closed it written into its front
     * matter. Removed again by tearDown, like every other fixture here.
     */
    private function archived(string $subject): string
    {
        $name = '2026-07-28-120000-' . self::MARKER . '.md';
        $path = Paths::feedbackArchive() . '/' . $name;

        file_put_contents($path, implode("\n", [
            '---',
            'date: 2026-07-28T12:00:00+00:00',
            'category: wrong-answer',
            'status: closed',
            'closed: 2026-07-28',
            'commit: abc1234',
            'subject: "' . $subject . '"',
            'tool: typo3_label_lookup',
            '---',
            '',
            '# ' . self::MARKER . ' one that was worked off',
            '',
            'The observation this was recorded with.',
            '',
        ]));

        return 'feedback/archive/' . $name;
    }

    #[Test]
    public function onlyAnOpenNoteCanBeArchived(): void
    {
        $file = Channel::record(['observation' => self::MARKER . ' archived once']);
        Channel::archive($file);

        // The same feedback twice is the mistake this catches: the second call
        // would otherwise overwrite the answer the first one recorded.
        $this->expectException(\InvalidArgumentException::class);
        Channel::archive($file);
    }

    #[Test]
    public function theListCanBeRestrictedToACategory(): void
    {
        Channel::record(['observation' => self::MARKER . ' a bug feedback', 'category' => 'bug']);

        foreach (Channel::all('all', 'bug', 100) as $feedback) {
            self::assertSame('bug', $feedback['category']);
        }
    }

    #[Test]
    public function severalToolsStaySeveralToolsRatherThanOneWord(): void
    {
        // An observation about the four tools that go quiet together is a
        // normal one. Stripping the separator ran their names into
        // "typo3_label_lookuptypo3_icon_lookup", which no filter can match.
        $file = Channel::record([
            'observation' => self::MARKER . ' both lookups went quiet',
            'tool' => 'typo3_label_lookup, typo3_icon_lookup',
        ]);

        self::assertStringContainsString(
            'tool: typo3_label_lookup, typo3_icon_lookup',
            (string) file_get_contents(Paths::root() . '/' . $file)
        );

        $feedback = self::noteFor($file);
        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], $feedback['tools']);
    }

    #[Test]
    public function aListOfToolsIsAcceptedAsOne(): void
    {
        $file = Channel::record([
            'observation' => self::MARKER . ' recorded with a list',
            'tool' => ['typo3_label_lookup', 'typo3_icon_lookup'],
        ]);

        self::assertSame(['typo3_label_lookup', 'typo3_icon_lookup'], self::noteFor($file)['tools']);
    }

    #[Test]
    public function theListCanBeRestrictedToOneTool(): void
    {
        $file = Channel::record([
            'observation' => self::MARKER . ' about two tools',
            'tool' => 'typo3_label_lookup typo3_icon_lookup',
        ]);

        // The obvious thing to want from a backlog: every feedback about one tool,
        // including the ones that name it alongside others.
        $files = array_column(Channel::all('all', null, 100, 'typo3_icon_lookup'), 'file');
        self::assertContains($file, $files);

        foreach (Channel::all('all', null, 100, 'typo3_icon_lookup') as $feedback) {
            self::assertContains('typo3_icon_lookup', $feedback['tools']);
        }
        self::assertNotContains($file, array_column(Channel::all('all', null, 100, 'typo3_rule_lookup'), 'file'));
    }

    /**
     * @return array{file: string, date: string, category: string, status: string, model: string, tool: string, tools: array<int, string>, title: string}
     */
    private static function noteFor(string $file): array
    {
        foreach (Channel::all('all', null, 200) as $feedback) {
            if ($feedback['file'] === $file) {
                return $feedback;
            }
        }

        self::fail($file . ' was written but is not listed');
    }
}
