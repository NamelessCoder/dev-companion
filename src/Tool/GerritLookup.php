<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unreachable;

/**
 * Whether a core patch already exists, read from the review server.
 *
 * The question is asked before every core task and answerable from no checkout,
 * and the sessions that asked it paid a round trip for the search and another
 * for the XSSI prefix the response opens with. This is one call
 * (`D-FBK-027`).
 */
final class GerritLookup extends ReadOnlyTool
{
    /** The change is read from the review server at review.typo3.org. */
    protected const OPEN_WORLD = true;

    /** Why nothing was answered, in the caller's terms rather than the transport's. */
    private const UNREACHABLE = [
        Unreachable::NOT_ANSWERING => 'The review server did not answer. It is reachable at ' . Gerrit::HOST
            . ' in a browser; nothing here can answer this question offline.',
        Unreachable::NOT_PARSEABLE => 'The host answered with something that is not the review API, which is what a '
            . 'proxy or a login page looks like from here.',
    ];

    public static function name(): string
    {
        return 'typo3_gerrit_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Find out whether a TYPO3 core patch already exists and what state its review is in, from the review server at review.typo3.org. Pass issue with a Forge issue number to search the commit messages of every change for it — the question "has somebody already fixed this" — or change with the Change-Id from a commit message, or the change number a review URL ends with, to read the one it names. Or search the server without holding either: query takes words matched against the commit messages, path takes a repository path and answers the changes touching it, the two combine, and open narrows them to what is still under review. That is the direction a triage opens with — is anybody working on this file, and did anybody ever try this fix — and it is the review surface a checkout cannot see, since a core clone carries what landed and says nothing about what is open. Answers with the change number, its Change-Id, subject, status, target branch, review URL, and the patch set that is current on the server with the commit it is — which is what says whether a checkout is the revision under review. A change is answered together with the changes sharing its Change-Id, whichever handle named it — that is how a backport on a release branch is reached. It also carries the relation chain it sits in: the changes stacked on it and the changes it is built on, each with its number, its status and its subject, which is what says whether the change is one part of a larger feature and how far that feature has got. The two relations are different — a chain is changes built on one another, a shared Change-Id is one patch on several branches. A change read by name also carries the Forge issues its commit message names in its Resolves: and Related: trailers, each with its subject, tracker and status. That is the join between the patch and the tracker, and it is where a second issue named nowhere else in the review is seen. Each change also carries the ref that patch set is fetchable by and the review server to fetch it over, so getting it into a checkout takes no second lookup. A change read by name carries the review it is in as well: the value every voter holds per label and whether the submit rule is satisfied, and every comment left on it with its patch set, its file and line, whether the thread is unresolved and which comment it replies to. That is where a comment somebody left on an earlier patch set and nobody answered is read. Why a vote is gone is in the review log instead, which messages asks for. A call carries issue, change, or a search by query and path, never two of those. This reaches the network, and it reads: reviewing, voting and uploading stay yours.';
    }


    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'issue' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Forge issue number, with or without the leading #, for example "105403". Searches every change whose commit message names it, which is where Resolves: and Related: put it. A call carries issue, change, or a search by query and path, never two of those.',
                ],
                'change' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'One change to read, named either by the Change-Id its commit message carries, for example "I0f4c5b9a3e2d1c7b8a6f5e4d3c2b1a0f9e8d7c6b", or by the change number a review URL ends with, for example "89011". Prefer the Change-Id where the commit is in front of you: it is part of the patch being read, it survives being amended into a new patch set, and it cannot be mistaken for the Forge issue number the way a bare change number can. A call carries issue, change, or a search by query and path, never two of those.',
                ],
                'query' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Words to search the review server for, for example "impexp translation". Every word has to appear, and what they are matched against is the commit message — the subject and the body, so a change whose subject does not carry the word is still found. They are not matched against the diff: change 89000 added writePagesOrder and a search for that name answers nothing, so a zero says no commit message names the word rather than that nobody has touched the code. Ask again in the words a commit message would use, and pass path for the changes that touch a file whatever they are called. Combine it with path to narrow one by the other, and with open for what is still under review. A call carries issue, change, or a search by query and path, never two of those.',
                ],
                'path' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'A path in the repository, for example "typo3/sysext/impexp" or "typo3/sysext/impexp/Classes/Import.php". Answers the changes that touch it — the path itself and everything under it — which is the surface a checkout cannot see: a core clone carries what landed and says nothing about what is open. It is the way to ask whether somebody is already working on a file before writing a patch for it, and with open it is that question exactly. Without open it reaches the abandoned and the merged changes too, which is where an earlier attempt at the same fix is found. Combine it with query to narrow one by the other. A call carries issue, change, or a search by query and path, never two of those.',
                ],
                'open' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Narrow a search to the changes that are still under review. False, the default, reaches every state — which is what "has anybody ever tried this" needs, since an abandoned or merged attempt is the answer to it. True is "who is working on this now". Narrows query and path, and is ignored by issue and change.',
                ],
                'messages' => [
                    'type' => 'string',
                    'enum' => ['none', 'people', 'all'],
                    'default' => 'none',
                    'description' => 'The review log of a change: every message its patch sets and its reviewers left. Ask for it to find out why a vote is gone — Gerrit writes "Outdated Votes: * Code-Review+1 (copy condition: ...)" into the message of the upload that dropped it, and the labels afterwards look exactly like a change nobody has voted on. "none" leaves it out and is the default, since it is 57.9 KB against 14.3 KB on a change with 21 patch sets. "people" drops what a service user wrote, which on that change is 20 of 46 messages and every one of them a CI pipeline report. "all" keeps them. How many were dropped is answered whichever you ask for. Narrows change and is ignored by every other way in.',
                ],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 25, 'default' => 10],
            ],
            // A search is one way in carrying two arguments, so the path branch
            // is the one that excludes rather than the one that is excluded: a
            // call passing both would otherwise match two branches and fail the
            // rule it satisfies.
            'oneOf' => [
                ['required' => ['issue']],
                ['required' => ['change']],
                ['required' => ['query']],
                ['required' => ['path'], 'not' => ['required' => ['query']]],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'source' => Schema::string('The review server the answer came from.'),
            'query' => Schema::string('The Gerrit query this was answered with, so the same question can be asked again by hand.'),
            'changes' => Schema::listOf(Schema::object([
                'number' => Schema::integer('Change number, the digits its review URL ends with.'),
                'changeId' => Schema::string('The Change-Id its commit message carries, empty where the server named none. It survives an amend and a rebase onto another branch, so it is what to hold the commit in front of you against — and changes sharing one are the same patch on more than one branch, which passing it back as change reads all of.'),
                'subject' => Schema::string('The commit subject.'),
                'status' => Schema::string('NEW while it is open, MERGED once it landed, ABANDONED when it was given up.'),
                'branch' => Schema::string('The branch the change targets.'),
                'patchSet' => Schema::integer('The patch set that is current on the server, counting from 1. Zero where the server named none.'),
                'commit' => Schema::string('The commit the current patch set is. A checkout whose HEAD is another commit is not the revision under review.'),
                'project' => Schema::string('The Gerrit project it was pushed to.'),
                'updated' => Schema::string('When the change last moved.'),
                'url' => Schema::string('Where a person reads the review.'),
                'fetch' => [
                    'type' => ['object', 'null'],
                    'description' => 'How to get this patch set into a checkout. Null where the server named no '
                        . 'patch set, since a ref names one.',
                    'properties' => [
                        'ref' => Schema::string('The static ref this patch set is filed under. Every patch set keeps its own, so an earlier one stays fetchable after a newer is pushed.'),
                        'remote' => Schema::string('What to fetch that ref from. It is the review server rather than origin: a core clone fetches from the GitHub mirror, and refs/changes is not there.'),
                    ],
                    'required' => ['ref', 'remote'],
                ],
                'labels' => [
                    'type' => ['array', 'null'],
                    'description' => 'What the change stands at, one entry per label. Null where the review was '
                        . 'not read, which is every hit a search answers: a list with zeros in it means nobody '
                        . 'has voted, and that is a different answer.',
                    'items' => Schema::object([
                        'label' => Schema::string('Code-Review and Verified are the two the core project votes with.'),
                        'satisfied' => [
                            'type' => ['boolean', 'null'],
                            'description' => 'Whether the submit rule counts this label as met — false is the '
                                . 'ordinary state of an open change, and null means no rule asks for it. What it '
                                . 'stands at is in the votes: the range is the project\'s own, and Verified runs to '
                                . '+2 here, so a +1 is not the top of it.',
                        ],
                        'votes' => Schema::listOf(Schema::object([
                            'voter' => Schema::string(),
                            'value' => Schema::integer('What this voter holds now. Zero is a reviewer who was added and has not voted; a vote a later patch set dropped is absent rather than zero, and only the review log says it was ever there.'),
                            'on' => Schema::string('When it was cast, empty where nothing was.'),
                        ], ['voter', 'value', 'on']), 'Everyone on the label, those holding nothing included.'),
                    ], ['label', 'satisfied', 'votes']),
                ],
                'commentCount' => Schema::integer('How many comments the change carries, which the review server states whether or not they were read.'),
                'comments' => [
                    'type' => ['array', 'null'],
                    'description' => 'The comments left on the change, oldest first. Empty means it carries none. '
                        . 'Null means they were not read: a search asks for none, and a change lookup whose '
                        . 'comment call did not answer says so here rather than with an empty list — hold it '
                        . 'against commentCount.',
                    'items' => Schema::object([
                        'id' => Schema::string('What the inReplyTo of a reply names.'),
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'patchSet' => Schema::integer('The patch set it was left on. One older than the current patch set is a comment written about code that may since have changed, and it is still unanswered until somebody answers it.'),
                        'file' => Schema::string('The file it sits on. /PATCHSET_LEVEL is a comment on the change itself rather than on a place in it.'),
                        'line' => ['type' => ['integer', 'null'], 'description' => 'Null on a comment about the change rather than about a line.'],
                        'unresolved' => ['type' => 'boolean', 'description' => 'The flag on the thread, as whoever wrote or answered it last left it. It is not "nobody answered": a comment can carry a reply and stay unresolved, and one can be resolved with nothing written under it. Both are handed over and the reading is yours.'],
                        'inReplyTo' => ['type' => ['string', 'null'], 'description' => 'The id of the comment this answers, null where it starts a thread.'],
                        'message' => Schema::string('The comment as it was written.'),
                    ], ['id', 'author', 'on', 'patchSet', 'file', 'line', 'unresolved', 'inReplyTo', 'message']),
                ],
                'chain' => [
                    'type' => ['array', 'null'],
                    'description' => 'The relation chain this change sits in, child first: above it the changes '
                        . 'stacked on it, then itself, then the changes it is built on. This is the other relation '
                        . 'and not the Change-Id one — a chain is different changes built on one another, a shared '
                        . 'Change-Id is one patch on several branches, and reading the two as one set overstates '
                        . 'both. Empty means the change stands alone, which is the ordinary case. Null means the '
                        . 'chain was not read: a search asks for none, and a change lookup whose call did '
                        . 'not answer says so here rather than with an empty list.',
                    'items' => Schema::object([
                        'number' => Schema::integer('The entry\'s change number, which reads it by passing it back as change.'),
                        'status' => Schema::string('NEW, MERGED or ABANDONED — the entry\'s own state, not the state of the change this answer is about. A MERGED entry says that part of the stack landed.'),
                        'subject' => Schema::string('The commit subject of the patch set the chain names.'),
                        'thisChange' => [
                            'type' => 'boolean',
                            'description' => 'Whether this entry is the change the answer is about. Its place in '
                                . 'the list is what says how much is stacked on it and how much it is built on.',
                        ],
                        'patchSet' => Schema::integer('The patch set the entry stands at now.'),
                        'chainedAt' => Schema::integer('The patch set of the entry that the chain is built on. Lower than patchSet means the stack holds the older one and that change has moved on since, so act on the entry by its number rather than on the patch set named here.'),
                        'url' => Schema::string('Where a person reads that change.'),
                    ], ['number', 'status', 'subject', 'thisChange', 'patchSet', 'chainedAt', 'url']),
                ],
                'issues' => [
                    'type' => ['array', 'null'],
                    'description' => 'The Forge issues this change\'s commit message names in its Resolves: and '
                        . 'Related: trailers, each filled with what says whether to read it. That is the join '
                        . 'between the patch and the tracker, and it is where a second issue nobody mentioned '
                        . 'elsewhere is seen. Empty means the message names none. Null means the message was not '
                        . 'read: a search asks for none of this, and reading one hit by name is what answers it '
                        . 'there.',
                    'items' => Schema::object([
                        'issue' => Schema::integer('The issue number, which reads it whole by passing it to typo3_forge_lookup as issue.'),
                        'trailer' => Schema::string('resolves where the message carries Resolves:, related where it carries Related:. The two are different claims: what the patch closes, and what it touches.'),
                        'subject' => Schema::string('What the issue is about, so it can be judged without being read. Empty where the tracker did not answer the one call that fills the whole set.'),
                        'tracker' => Schema::string('Bug, Feature, Task.'),
                        'status' => Schema::string('Where the issue stands, which is the tracker\'s own state and not the state of this change.'),
                        'url' => Schema::string('Where a person reads it.'),
                    ], ['issue', 'trailer', 'subject', 'tracker', 'status', 'url']),
                ],
                'messages' => [
                    'type' => ['array', 'null'],
                    'description' => 'The review log, oldest first, where messages asked for it. Null otherwise, '
                        . 'which is the default and every hit a search answers.',
                    'items' => Schema::object([
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'patchSet' => Schema::integer('The patch set it was written about.'),
                        'bot' => ['type' => 'boolean', 'description' => 'Whether a service user wrote it, read off the account rather than off its name. On the core project that is the CI reporting a pipeline.'],
                        'message' => Schema::string('The message as it stands. The upload of a patch set carries the votes it dropped and the copy condition that dropped them, which is the one place that is written down.'),
                    ], ['author', 'on', 'patchSet', 'bot', 'message']),
                ],
                'botMessageCount' => [
                    'type' => ['integer', 'null'],
                    'description' => 'How many of the log a service user wrote, which messages: "people" is what '
                        . 'drops. Answered whichever way it was asked, so a log full of pipeline reports answering '
                        . 'zero here is Gerrit no longer tagging its service users rather than a change no bot has '
                        . 'been near. Null where the log was not read.',
                ],
            ]), 'The changes that matched, newest activity first.'),
            'unavailable' => Schema::unavailable([
                'source-not-answering' => 'review.typo3.org did not answer this time, and the same call may answer '
                    . 'the next.',
                'source-not-parseable' => 'something answered and it was not the review API, which is what a proxy '
                    . 'or a captive portal looks like from here.',
            ]),
            // Required and nullable, the shape `unavailable` beside it has. A
            // caller that has to branch on whether the key is there cannot
            // tell an answer with nothing to qualify from a server too old to
            // qualify anything, and this field exists precisely because that
            // distinction was being got wrong one level down.
            'indistinguishable' => [
                'type' => ['string', 'null'],
                'description' => 'Why an empty answer cannot be read as an absence, or null where it can. This '
                    . 'server reads the review server without credentials, so a change that is private or work in '
                    . 'progress is invisible to it and looks exactly like one nobody pushed. Null means empty '
                    . 'really does mean nothing matched.',
            ],
        ], ['status', 'source', 'query', 'changes', 'unavailable', 'indistinguishable']);
    }

    /**
     * Which of the two forms `change` was given in.
     *
     * A Change-Id is the `I` and forty hex digits a commit message carries; a
     * change number is the digits a review URL ends with. Saying "no change
     * with this number" back to a caller that passed a Change-Id is wrong
     * twice over, and one review read it as its own commit never having been
     * pushed (`feedback/2026-08-07-132416`).
     */
    private static function isChangeId(string $change): bool
    {
        return preg_match('/^I[0-9a-f]{40}$/i', $change) === 1;
    }

    /**
     * What an empty answer cannot separate, where it cannot separate anything.
     *
     * A caller that named one change has named something it read somewhere, and
     * an empty answer there is a restricted change at least as often as an
     * absent one: this server reads Gerrit anonymously (`R-ANS-027`). A search
     * owes the same caveat and one more of its own (`D-ANS-100`), and an issue
     * search owes neither — "no change names this issue" is a claim about a
     * query, and the text half states it there. Separated from `answer()` so it
     * can be held without a review server.
     *
     * @param string $direction the argument the caller passed, since what an
     *     empty answer fails to separate is different for each of the four
     * @param array{author: string, url: string}|null $review what Gerrit posted
     *     on the issue, where a search for one came back empty and the tracker
     *     had a note
     */
    public static function indistinguishable(string $status, string $direction, ?array $review = null): ?string
    {
        if ($status !== 'empty') {
            return null;
        }

        // The issue case, where the tracker settled it. Gerrit Code Review
        // posts a note on the issue for every patch set it receives, so a
        // review URL there and nothing here is not two possibilities: the
        // change exists and this reader may not see it. That is the report's
        // own idea, and it is only buildable on this side —
        // `feedback/2026-08-07-132416`.
        if ($review !== null) {
            return sprintf(
                'A change for this issue does exist and is not one an anonymous reader may see. %s posted %s on '
                    . 'the issue, and this server reads %s without credentials, so a private or work-in-progress '
                    . 'change is invisible to it. Read the change there while signed in.',
                $review['author'],
                $review['url'] === '' ? 'a review note' : $review['url'],
                Gerrit::HOST,
            );
        }

        $anonymous = 'This server reads ' . Gerrit::HOST . ' without credentials, so a change that is private or '
            . 'work in progress is invisible to it. ';

        return match ($direction) {
            'change' => $anonymous . 'Such a change answers exactly like one that does not exist, so this is either '
                . '"no such change" or "not one an anonymous reader may see", and nothing here separates them. Where '
                . 'the id came from a commit you have, the second is the more likely of the two.',
            // The word direction's own trap, and the one that reads as an
            // established negative: `feedback/2026-08-24-110833` took a zero for
            // an identifier as nobody having attempted the fix — `D-ANS-100`.
            'query' => $anonymous . 'A word is matched against the commit message rather than against the diff: '
                . 'change 89000 added `writePagesOrder`, and a search for that name answers nothing. So a zero says '
                . 'that no commit message names the word, not that nobody has touched the code. Ask again in the '
                . 'words a commit message would use, and pass `path` for the changes that touch a file whatever '
                . 'they are called.',
            'path' => $anonymous . 'Such a change answers exactly like a path nothing touches, so this is either '
                . '"nobody is working on it" or "nobody an anonymous reader may see is", and nothing here separates '
                . 'them. What is matched is the paths a change touches, so a fix for this file that landed '
                . 'elsewhere is not in the answer either.',
            default => null,
        };
    }

    /**
     * The workflow a patch set in front of a caller is in, where there is one.
     *
     * A review session that opened no skill asked this tool for a change and was
     * handed a ref, a remote and nothing about the work it had just begun
     * (`D-SKL-038`). Naming the two workflows costs three lines at the one
     * moment the caller is certainly reading, which is the placement `D-ANS-061`
     * earned. The `change` form alone, because a search has no one workflow to
     * name whichever way it was asked. Separated from `answer()` so it can be held without a
     * review server.
     */
    public static function workflow(string $status, string $change): ?string
    {
        if ($status !== 'answered' || trim($change) === '') {
            return null;
        }

        return 'A patch set in front of you opens one of two workflows: `typo3-core-patch-review` reviews it, '
            . 'and `typo3-core-patch-checkout` fetches it into a checkout and backs out again. Open the one '
            . "this task is before reading the diff.\n"
            . 'Both start at `typo3_project_describe`: which installation this checkout is, what it runs, and '
            . 'which whole procedures this server carries.';
    }

    /**
     * The newest review URL Gerrit posted on an issue, or null.
     *
     * Read from the journal rather than from the description, because that is
     * where it is: the note is authored by Gerrit itself and names the patch
     * set and the change. Only asked where a search over commit messages came
     * back empty, so the second host is reached on the path where the answer
     * would otherwise be a guess, and not on the ordinary one.
     *
     * The same cross-check for a caller that named a change rather than an
     * issue was measured on 2026-08-07 and is not built: searching the tracker
     * for `95162` costs 2.5 seconds and answers two issues, one of them
     * unrelated, and searching for the Change-Id answers nothing at all. That
     * is a second guess rather than evidence, and it is the case the report was
     * about.
     *
     * @return array{author: string, url: string}|null
     */
    private static function reviewPostedOnIssue(string $issue): ?array
    {
        $answer = (new Forge())->issue($issue);
        if ($answer['status'] !== 'answered' || !is_array($answer['issue'])) {
            return null;
        }

        $newest = null;
        foreach ((array) ($answer['issue']['notes'] ?? []) as $note) {
            if (!is_array($note) || !str_contains(strtolower((string) ($note['author'] ?? '')), 'gerrit')) {
                continue;
            }
            if (preg_match('~https?://\S*review\.typo3\.org/\S+~', (string) ($note['note'] ?? ''), $found) !== 1) {
                continue;
            }
            $newest = ['author' => (string) $note['author'], 'url' => rtrim($found[0], '.,)')];
        }

        return $newest;
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $change = is_string($args['change'] ?? null) ? trim($args['change']) : '';
        $query = is_string($args['query'] ?? null) ? trim($args['query']) : '';
        $path = is_string($args['path'] ?? null) ? trim($args['path']) : '';
        $open = (bool) ($args['open'] ?? false);
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 10;
        $messages = is_string($args['messages'] ?? null) ? trim($args['messages']) : 'none';

        // Which of the four the caller passed, which is what decides the query,
        // what a hit carries, and what an empty answer fails to separate. The
        // words carry the search where both were given, because their caveat is
        // the wider one.
        $direction = match (true) {
            $issue !== '' => 'issue',
            $change !== '' => 'change',
            $query !== '' => 'query',
            default => 'path',
        };

        $gerrit = new Gerrit();
        $answer = match ($direction) {
            'issue' => $gerrit->changesForIssue($issue, $limit),
            'change' => $gerrit->change($change, $limit, $messages),
            default => $gerrit->changesMatching($query, $path, $open, $limit),
        };

        // The tracker is asked only where the review server answered
        // nothing for an issue, which is the one path where a second host
        // buys an answer instead of a hedge. It cost 0.12 seconds measured
        // against forge.typo3.org on 2026-08-07.
        $review = $direction === 'issue' && $answer['status'] === 'empty'
            ? self::reviewPostedOnIssue($issue)
            : null;
        $indistinguishable = self::indistinguishable($answer['status'], $direction, $review);

        $data = [
            'status' => $answer['status'],
            'source' => Gerrit::HOST,
            'query' => $answer['query'],
            'changes' => $answer['changes'],
            'indistinguishable' => $indistinguishable,
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
        ];

        $lines = ['TYPO3 core review server: ' . Gerrit::HOST, 'Query: ' . $answer['query']];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $data['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = match ($direction) {
                'issue' => 'No change names this issue in its commit message. This reads the review server anonymously, so a change pushed as private is invisible here — the answer is that nothing public exists, not that nobody has fixed it.',
                'change' => 'No change an anonymous reader may see matches this ' . (self::isChangeId($change) ? 'Change-Id' : 'change number') . '.',
                default => 'No change an anonymous reader may see matches this search.',
            };
            if ($indistinguishable !== null) {
                $lines[] = $indistinguishable;
            }
            if ($answer['dropped'] > 0) {
                $lines[] = self::held($answer['dropped']);
            }
        } else {
            $named = false;
            $fetchable = false;
            $ids = [];
            $commented = false;
            $voted = false;
            $stacked = false;
            $moved = false;
            $tracked = false;
            foreach ($answer['changes'] as $entry) {
                $lines[] = '';
                $lines[] = sprintf('## %s (%s)', $entry['subject'], $entry['status']);
                $lines[] = sprintf('Change %d · %s · %s', $entry['number'], $entry['branch'], $entry['url']);
                if ($entry['changeId'] !== '') {
                    $ids[] = strtolower($entry['changeId']);
                    $lines[] = 'Change-Id: ' . $entry['changeId'];
                }
                if ($entry['patchSet'] > 0) {
                    $named = $named || $entry['commit'] !== '';
                    $lines[] = $entry['commit'] === ''
                        ? sprintf('Patch set %d', $entry['patchSet'])
                        : sprintf('Patch set %d · %s', $entry['patchSet'], $entry['commit']);
                }
                if ($entry['fetch'] !== null) {
                    $fetchable = true;
                    $lines[] = sprintf('Fetch: git fetch %s %s', $entry['fetch']['remote'], $entry['fetch']['ref']);
                }
                if ($entry['updated'] !== '') {
                    $lines[] = 'Last moved: ' . $entry['updated'];
                }
                foreach ($entry['labels'] ?? [] as $label) {
                    $voted = true;
                    $lines[] = self::vote($label);
                }
                $commented = $commented || ($entry['comments'] ?? []) !== [];
                $stacked = $stacked || ($entry['chain'] ?? []) !== [];
                $tracked = $tracked || ($entry['issues'] ?? []) !== [];
                foreach ($entry['chain'] ?? [] as $related) {
                    $moved = $moved || self::behind($related);
                }
                // Only a change read by name asked for any of it, so silence
                // elsewhere is not a claim that it could not be read — which a
                // search would otherwise make about every hit it answers.
                $lines = [
                    ...$lines,
                    ...self::issues($entry, $direction === 'change'),
                    ...self::chain($entry, $direction === 'change'),
                    ...self::comments($entry, $direction === 'change'),
                    ...self::log($entry, $messages),
                ];
            }
            if ($tracked) {
                $lines[] = '';
                $lines[] = 'The issues above are what the commit message names, and a status there is the issue\'s '
                    . 'own rather than this change\'s. Pass one to `typo3_forge_lookup` as `issue` to read it whole, '
                    . 'which is where a maintainer said why something was closed or reassigned.';
            }
            if ($stacked) {
                $lines = [...$lines, ...self::relations($moved)];
            }
            if ($commented) {
                $lines[] = '';
                $lines[] = '`unresolved` is the flag on the thread as its last writer left it, not a judgement '
                    . 'that nobody answered: a comment can carry a reply and stay unresolved, and one can be '
                    . 'resolved with nothing written under it. Which of them this review would otherwise make a '
                    . 'second time is yours to read.';
            }
            if ($voted && $messages === 'none') {
                $lines[] = '';
                $lines[] = 'A vote a later patch set dropped is absent here rather than zero, and the copy '
                    . 'condition that dropped it is written in the review log alone — ask again with '
                    . '`messages: "people"` where a label stands at nothing and you need to know whether it '
                    . 'ever stood elsewhere.';
            }
            // What the pair is, said where a reader would otherwise read two
            // changes with one subject as a duplicate — `D-ANS-080`.
            if (count($ids) !== count(array_unique($ids))) {
                $lines[] = '';
                $lines[] = 'More than one change above carries the same Change-Id. That is what a backport keeps, '
                    . 'so they are one patch on the branches each of them names. Gerrit relates them by nothing '
                    . 'else, and the state of one says nothing about the state of the other.';
            }
            // The one thing this answer knows and the checkout does not: which
            // revision the review is of. Nothing here can read a local `HEAD`,
            // so the comparison is the caller's and the sentence is what says
            // there is one to make.
            if ($named) {
                $lines[] = '';
                $lines[] = 'Hold the commit against `git rev-parse HEAD` in the checkout. Where the two '
                    . 'differ, the checkout is not the revision under review, and a review says which of '
                    . 'the two it read.';
            }
            // The remote is spelled out because `origin` is the wrong one, and
            // wrong in the way that reads as the change not existing —
            // `D-SKL-021` measured the fetch coming back empty over the mirror.
            if ($fetchable) {
                $lines[] = '';
                $lines[] = 'The fetch goes to the review server rather than to `origin`: a core clone fetches '
                    . 'from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach '
                    . 'FETCH_HEAD` is what puts the checkout on the patch set afterwards.';
            }
            // A search is the one direction whose set has no natural end, so a
            // full page is as likely to be where the answer stopped as where the
            // matches did, and a caller counting it reports the limit.
            if ($direction !== 'change' && count($answer['changes']) === $limit) {
                $lines[] = '';
                $lines[] = sprintf(
                    'The answer stopped at the %d asked for, so this is a page of what matched rather than the whole '
                        . 'of it. Narrow it with more words, a longer path or open before reading the count as one.',
                    $limit,
                );
            }
            if ($answer['dropped'] > 0) {
                $lines[] = '';
                $lines[] = self::held($answer['dropped']);
            }
        }

        $workflow = self::workflow($answer['status'], $change);
        if ($workflow !== null) {
            $lines[] = '';
            $lines[] = $workflow;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * One label, as a reviewer picking the change up reads it: where it stands
     * and who put it there.
     *
     * The values carry their sign, because +1 and -1 are the vote and 1 is a
     * number. A label nobody has voted on still lists its reviewers at 0, which
     * is who was asked rather than who answered.
     *
     * @param array<string, mixed> $label
     */
    private static function vote(array $label): string
    {
        $held = [];
        foreach ($label['votes'] as $vote) {
            $held[] = sprintf('%s %s', $vote['voter'], $vote['value'] === 0 ? '0' : sprintf('%+d', $vote['value']));
        }

        return sprintf(
            '%s: %s%s',
            $label['label'],
            match ($label['satisfied']) {
                true => 'satisfied',
                false => 'not satisfied',
                default => 'not required',
            },
            $held === [] ? ' · nobody has been asked' : ' · ' . implode(' · ', $held),
        );
    }

    /**
     * What a relation chain is, and which of the two relations in this answer
     * it is.
     *
     * Read as the other one, a chain would say the Change-Id was the whole of
     * the work — so the paragraph the pair gets under `D-ANS-080` is what this
     * one sits beside, and neither says what the other says. The staleness
     * sentence is printed only where an entry is behind, because it is a
     * warning about entries in this answer rather than a property of chains.
     * Separated from `answer()` so it can be held without a review server.
     *
     * @return list<string>
     */
    public static function relations(bool $moved): array
    {
        $lines = ['', 'A relation chain is a stack of different changes built on one another, listed child first: '
            . 'what stands above a change is stacked on it, and what stands below it is what it is built on. Each '
            . 'entry\'s status is that entry\'s own, so a MERGED entry says that change landed and says nothing '
            . 'about the change you asked for. Gerrit relates a chain by the commits, which is not the Change-Id '
            . 'relation a backport keeps, and neither set contains the other.'];
        if ($moved) {
            $lines[] = '';
            $lines[] = 'An entry chained at an earlier patch set than it stands at now has moved on since the '
                . 'stack was built on it. Read it by its number rather than acting on the patch set the chain '
                . 'names.';
        }

        return $lines;
    }

    /**
     * Whether the stack holds an earlier patch set of this entry than the one
     * it stands at now.
     *
     * @param array<string, mixed> $related
     */
    private static function behind(array $related): bool
    {
        return $related['chainedAt'] > 0 && $related['chainedAt'] < $related['patchSet'];
    }

    /**
     * The stack the change sits in, where there is one.
     *
     * A change read alone says a feature exists; the stack under it says what
     * the feature consists of, which parts landed and which were given up
     * (`D-ANS-094`). Nothing is printed for a change standing alone, which is
     * the ordinary case rather than a finding. Separated from `answer()` so it
     * can be held without a review server.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the chain was asked for, which only a change
     *                   read by name does
     * @return list<string>
     */
    public static function chain(array $entry, bool $read): array
    {
        if ($entry['chain'] === null) {
            return $read
                ? ['', 'The relation chain of this change could not be read: the review server answered the change '
                    . 'and not what it is stacked on, so this says nothing about whether there is a stack.']
                : [];
        }
        if ($entry['chain'] === []) {
            return [];
        }

        $place = array_search(true, array_column($entry['chain'], 'thisChange'), true);
        $lines = ['', $place === false
            ? sprintf('### Relation chain (%d changes)', count($entry['chain']))
            : sprintf(
                '### Relation chain (%d changes, %d stacked on this one and %d under it)',
                count($entry['chain']),
                $place,
                count($entry['chain']) - $place - 1,
            )];
        foreach ($entry['chain'] as $related) {
            $said = [sprintf('%d · %s · %s', $related['number'], $related['status'], $related['subject'])];
            if ($related['thisChange']) {
                $said[] = 'this change';
            }
            if (self::behind($related)) {
                $said[] = sprintf('chained at patch set %d, now at %d', $related['chainedAt'], $related['patchSet']);
            }
            $said[] = $related['url'];
            $lines[] = '- ' . implode(' · ', $said);
        }

        return $lines;
    }

    /**
     * The issues the commit message names, where it was read.
     *
     * The trailer is said with each of them, because what a patch closes and
     * what it touches are different claims and a reader acting on the second as
     * the first reports work that is not being done here. Nothing is printed
     * for a message naming none, which a change outside the core project is
     * ordinarily. Separated from `answer()` so it can be held without a review
     * server — `D-ANS-098`.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the commit message was asked for, which an
     *                   read by name does
     * @return list<string>
     */
    public static function issues(array $entry, bool $read): array
    {
        if ($entry['issues'] === null) {
            return $read
                ? ['', 'The commit message of this change did not come back, so nothing here says which issues it '
                    . 'names. The review page carries it.']
                : [];
        }
        if ($entry['issues'] === []) {
            return [];
        }

        $lines = ['', sprintf('### Issues named in the commit message (%d)', count($entry['issues']))];
        foreach ($entry['issues'] as $named) {
            $lines[] = sprintf(
                '- %s #%d — %s',
                $named['trailer'],
                $named['issue'],
                implode(' · ', array_filter([$named['tracker'], $named['status'], $named['subject'], $named['url']])),
            );
        }

        return $lines;
    }

    /**
     * The comments, and what an absent one means where the change said it has
     * some.
     *
     * A reply names the comment it answers by an id nobody reads, so the author
     * it answers is looked up here — which is also the field a caller has to
     * hold against `unresolved` to read the thread at all.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the review was read for this change, which an
     *                   read by name does
     * @return list<string>
     */
    private static function comments(array $entry, bool $read): array
    {
        if ($entry['comments'] === null) {
            return $read && $entry['commentCount'] > 0
                ? ['', sprintf(
                    'The %d comment%s on this change could not be read: the review server answered the change and '
                        . 'not its comments, so this says nothing about whether one of them is unanswered.',
                    $entry['commentCount'],
                    $entry['commentCount'] === 1 ? '' : 's',
                )]
                : [];
        }
        if ($entry['comments'] === []) {
            return [];
        }

        $by = [];
        foreach ($entry['comments'] as $comment) {
            $by[$comment['id']] = $comment['author'];
        }

        $unresolved = count(array_filter($entry['comments'], static fn(array $comment): bool => $comment['unresolved']));
        $lines = ['', sprintf('### Comments (%d, %d unresolved)', count($entry['comments']), $unresolved)];
        foreach ($entry['comments'] as $comment) {
            $said = [$comment['author'], 'patch set ' . $comment['patchSet']];
            if ($comment['file'] !== '/PATCHSET_LEVEL') {
                $said[] = $comment['line'] === null
                    ? $comment['file']
                    : $comment['file'] . ':' . $comment['line'];
            }
            $said[] = $comment['unresolved'] ? 'unresolved' : 'resolved';
            if (isset($by[$comment['inReplyTo']])) {
                $said[] = 'answering ' . $by[$comment['inReplyTo']];
            }
            $lines[] = '';
            $lines[] = '- ' . implode(' · ', $said);
            $lines[] = self::quoted($comment['message']);
        }

        return $lines;
    }

    /**
     * What somebody wrote, indented under the line that says who and where.
     *
     * The blank line inside a message keeps no indent, because trailing spaces
     * are what a diff and a terminal both show and neither is what the message
     * says.
     */
    private static function quoted(string $said): string
    {
        return implode("\n", array_map(
            static fn(string $line): string => $line === '' ? '' : '  ' . $line,
            explode("\n", $said),
        ));
    }

    /**
     * The review log, where it was asked for.
     *
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    private static function log(array $entry, string $messages): array
    {
        if ($entry['messages'] === null || $entry['messages'] === []) {
            return [];
        }

        $lines = ['', sprintf(
            '### Review log (%d messages, %d %s)',
            count($entry['messages']),
            (int) ($entry['botMessageCount'] ?? 0),
            $messages === 'people' ? 'more a service user wrote held back' : 'of them a service user\'s',
        )];
        foreach ($entry['messages'] as $message) {
            $lines[] = '';
            $lines[] = sprintf('- %s · %s · patch set %d', $message['on'], $message['author'], $message['patchSet']);
            $lines[] = self::quoted($message['message']);
        }

        return $lines;
    }

    /**
     * What the same query answers by hand and this one does not.
     *
     * The `query` field is there so the question can be asked again outside
     * this server, and a hand-run one comes back with more than this — so what
     * was held back is said, rather than left as a difference the caller finds
     * and reads as this answer being short.
     */
    private static function held(int $dropped): string
    {
        return sprintf(
            '%d change%s the review server matched by its own change number rather than by its commit message %s '
                . 'held back. The number a query carries is indexed both ways there, so a search for an issue '
                . 'answers with the change of the same number whatever it is about.',
            $dropped,
            $dropped === 1 ? '' : 's',
            $dropped === 1 ? 'was' : 'were',
        );
    }
}
