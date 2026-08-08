<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * What a Forge issue actually says, including the part that decides it.
 *
 * Four round trips and a trap by hand: the request refused, a browser-shaped
 * one answered 200 with a challenge page, then JSON whose decision sits in the
 * journal rather than in the description (`D-FBK-027`).
 *
 * A number is not the only way in. Whether somebody else already reported this
 * is asked before a patch and no number answers it, so `query` searches the
 * tracker by words — the step two sessions took by hand between reading an
 * issue and looking for its patch (`D-ANS-038`).
 *
 * Nor is a wording. A triage starts before there is an issue in hand at all,
 * and what it needs is the backlog ordered by age or by neglect, which no
 * number holds and no query reaches: the issue nobody has touched since 2015 is
 * worded the way nobody thinks of. `open` is that way in.
 */
final class ForgeLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_forge_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Read the TYPO3 issue tracker at forge.typo3.org before writing a patch. Pass issue with a number to read that one: subject, tracker, status, target version, the TYPO3 and PHP versions it was reported against, the related issues with their subjects, the review changes its comments name, the files hanging off it — which on a report about rendering is where the evidence usually is — and the comments, where a maintainer who closed or reassigned it said why, which the description never says. Or pass query with words to find out which other issues describe the same thing, which the relations of one issue only answer for what somebody linked by hand. Or pass open to enumerate the core project\'s unresolved issues without holding a number or a wording — oldest filed or longest untouched, narrowed by tracker and by date, which is where a triage of the backlog starts; the count of everything that matched comes back with the page, so a limited answer says whether it is the whole set. Each entry carries its number, subject, tracker, status and URL. A call carries issue, query or open, never two of them. An issue that does not exist is answered as such, and so is a tracker that could not be reached. Reading only, and no credential: commenting, assigning and closing stay yours.';
    }

    public static function annotations(): array
    {
        return [
            'readOnlyHint' => true,
            'destructiveHint' => false,
            'idempotentHint' => true,
            'openWorldHint' => true,
        ];
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'issue' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Forge issue number, with or without the leading #, for example "110348". Reads that one issue whole, comments included — narrow those with notes when reading many. A call carries issue, query or open, never two of them.',
                ],
                'query' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Words to search the tracker for, for example "image cache busting". Answers the issues whose text matches them — which is how a duplicate nobody has linked is found at all, since the relations of an issue only carry what somebody linked by hand. Nothing is ranked and one wording does not settle it: ask again in the reporter\'s words as well as your own, because an issue worded differently is invisible to this. A call carries issue, query or open, never two of them.',
                ],
                'open' => [
                    'type' => 'string',
                    'enum' => ['oldest', 'stale'],
                    'description' => 'Enumerate the core project\'s unresolved issues instead of reading one or matching words: "oldest" orders them by when they were filed, "stale" by how long nobody has touched them. The two answer different questions about one backlog — filed long ago is about the report, untouched for years is about the attention it got — and an issue that is both is the candidate a triage is looking for. Unresolved is the tracker\'s own set of open statuses, so New, Accepted, Under Review, Needs Feedback, On Hold and Postponed are all in it. Narrow with tracker, createdBefore and updatedBefore. A call carries issue, query or open, never two of them.',
                ],
                'notes' => [
                    'type' => 'string',
                    'enum' => ['all', 'people'],
                    'default' => 'all',
                    'description' => 'Which comments come back with an issue. "all" is every one of them and is what you want when reading a single issue — the comments are where the decision is, and on a report worth reading the one that settles it is regularly the last of sixteen. "people" drops the patch-set pings a review bot wrote, which on some issues is half the volume and carries nothing a reader was going to use; the change numbers in them are lifted into reviews either way, so nothing is lost by it. Ask for it when sweeping candidates, where the cost of reading ten issues is what decides whether the comments get read at all. How many were dropped is answered whichever you ask for. Narrows issue and is ignored by query and open.',
                ],
                'tracker' => [
                    'type' => 'string',
                    'enum' => ['Bug', 'Feature', 'Major Feature', 'Support', 'Task', 'Story', 'Suggestion', 'Impediment', 'Epic', 'Work Package', 'Topic'],
                    'description' => 'Only issues filed under this tracker, for example "Bug". Worth setting before reading a set: an old Bug and an old Feature are two different findings, since one claims something is broken today and the other that something was wanted once. Narrows open and is ignored by issue and query.',
                ],
                'category' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only issues the core files under this area, in your own words: "rte", "backend ui", "workspaces", "fluid". Matched against the project\'s own category names one word at a time, so a half-remembered name reaches the right area and a word naming several — "backend" — selects all of them and says which. That is the way in for "are there known bugs in the RTE" and "the oldest issues in the backend UI", which no wording of the report itself reaches. The categories that exist come back with every answer, so a word matching none is corrected without a second call. Narrows open and is ignored by issue and query.',
                ],
                'createdBefore' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only issues filed before this day, as YYYY-MM-DD. Narrows open and is ignored by issue and query.',
                ],
                'updatedBefore' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only issues nobody has touched since this day, as YYYY-MM-DD. This is the one that finds a report everybody has walked past, which age alone does not: an issue filed in 2009 and commented on last month is being worked. Narrows open and is ignored by issue and query.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 50,
                    'default' => 15,
                    'description' => 'How many entries come back. A search answers with at most 25 whatever is asked for, because a set that has to be paged through is answered by other words rather than by more of these.',
                ],
            ],
            'oneOf' => [
                ['required' => ['issue']],
                ['required' => ['query']],
                ['required' => ['open']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => ['type' => 'string', 'enum' => ['answered', 'empty', 'unavailable']],
            'source' => Schema::string('The tracker the answer came from.'),
            'url' => Schema::string('What was read, so the same question can be asked again by hand.'),
            'query' => Schema::string('The words the tracker was searched for, so a set that looks too narrow can be asked again in other words. Empty where an issue was read by number and where the open issues were enumerated.'),
            'total' => Schema::integer('How many issues matched in total, of which results carries at most limit. Where the two differ the answer is a page and not the set, and asking for more of it is a narrower filter rather than a bigger limit. Zero where an issue was read by number.'),
            'categories' => Schema::listOf(Schema::string(), 'Every area the core files its issues under, read from the project itself. Answered on every enumeration, so a category word that matched none is corrected from the answer rather than from a second call. Empty where an issue was read by number and where words were searched for.'),
            'categoriesUsed' => Schema::listOf(Schema::string(), 'The categories the category word resolved to, in the tracker\'s own spelling. Empty where none was asked for — and empty where the word matched none, which is answered as no issues and is a statement about the word rather than about the backlog.'),
            'issue' => [
                'type' => ['object', 'null'],
                'description' => 'The issue, where status says answered and a number was asked for. Null otherwise.',
                'properties' => [
                    'id' => Schema::integer(),
                    'subject' => Schema::string(),
                    'status' => Schema::string('New, Accepted, Resolved, Closed, Rejected — the tracker\'s own word.'),
                    'tracker' => Schema::string('Bug, Feature, Task, Epic.'),
                    'priority' => Schema::string(),
                    'assignedTo' => Schema::string('Who the tracker says holds this, empty where nobody does. An assignee is not a promise that somebody is working on it — on an issue nothing has moved on for years it is usually who last did.'),
                    'targetVersion' => Schema::string('The release it is scheduled for, empty where none is set.'),
                    'typo3Version' => Schema::string('The TYPO3 version it was reported against, which is not the version it still reproduces on.'),
                    'phpVersion' => Schema::string(),
                    'createdOn' => Schema::string(),
                    'updatedOn' => Schema::string(),
                    'url' => Schema::string('Where a person reads it.'),
                    'description' => Schema::string('The report as it was written, which is what the reporter saw and not what was decided.'),
                    'relations' => Schema::listOf(Schema::object([
                        'issue' => Schema::integer('The other issue.'),
                        'relation' => Schema::string('duplicates, relates, blocked, precedes.'),
                        'subject' => Schema::string('What the other issue is about, so it can be judged without being read. Empty where the tracker did not answer the one call that fills the whole set.'),
                        'tracker' => Schema::string('Bug, Feature, Task.'),
                        'status' => Schema::string('Where the other issue stands.'),
                        'url' => Schema::string('Where a person reads it.'),
                    ], ['issue', 'relation', 'subject', 'tracker', 'status', 'url']), 'Issues this one is filed against, which is where a duplicate, a blocker, and the issue a revert was filed under are named. Each carries its subject, so which of them is worth reading is decided from here rather than from one call each.'),
                    'reviews' => Schema::listOf(Schema::object([
                        'change' => Schema::integer('The change number on review.typo3.org, which is what typo3_gerrit_lookup takes as change.'),
                        'changeId' => Schema::string('The Change-Id the commit message carries, empty where no note named one. typo3_gerrit_lookup takes this too, and it is what survives a rebase onto another branch.'),
                        'patchSet' => Schema::integer('The highest patch set a note mentioned, zero where none did. The review server may be further along.'),
                        'on' => Schema::string('When the last note naming this change was written, which is how old the reference is and not when the change last moved.'),
                        'url' => Schema::string('Where a person reads the change.'),
                    ], ['change', 'changeId', 'patchSet', 'on', 'url']), 'The review changes the journal names, lifted out of the prose that carries them. Nothing here says what state a change is in: a note says what was true the day it was written, and typo3_gerrit_lookup answers what is true now. Empty where no note named one.'),
                    'attachments' => Schema::listOf(Schema::object([
                        'filename' => Schema::string('The name the file was uploaded under, which is also how a comment refers to it: Redmine writes an inline image as !name.png! and the text around it says nothing else about it.'),
                        'contentType' => Schema::string('image/png, image/jpeg, text/plain.'),
                        'size' => Schema::integer('Bytes.'),
                        'on' => Schema::string('When it was uploaded, which is what says which comment it belongs to.'),
                        'url' => Schema::string('Where the file itself is. It answers without a credential, and reading it is the caller\'s: nothing here fetches or transcribes one.'),
                    ], ['filename', 'contentType', 'size', 'on', 'url']), 'The files hanging off the issue. On a report about rendering these are usually screenshots, and they are regularly where the evidence is: a comment that consists of !image.jpg! references reads as an empty comment otherwise. Empty where the issue carries none.'),
                    'noteCount' => Schema::integer('How many comments the issue carries in total.'),
                    'botNoteCount' => Schema::integer('How many of those a review bot wrote, which notes: "people" is what drops. Answered whichever way notes was asked, so a journal full of patch-set pings answering zero here is the list of bot names gone stale rather than an issue nobody pushed a patch for.'),
                    'notes' => Schema::listOf(Schema::object([
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'note' => Schema::string(),
                    ], ['author', 'on', 'note']), 'The most recent comments, oldest first. A closure, a reassignment and a "we will not do this" are here rather than in the description.'),
                ],
                'required' => ['id', 'subject', 'status', 'tracker', 'priority', 'assignedTo', 'targetVersion', 'typo3Version', 'phpVersion', 'createdOn', 'updatedOn', 'url', 'description', 'relations', 'attachments', 'reviews', 'noteCount', 'botNoteCount', 'notes'],
            ],
            'results' => Schema::listOf(Schema::object([
                'issue' => Schema::integer('The issue number, which is what this tool reads whole.'),
                'subject' => Schema::string(),
                'tracker' => Schema::string('Bug, Feature, Task, Epic.'),
                'status' => Schema::string('Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.'),
                'category' => Schema::string('The area the core files it under, empty where none is set. A search hit is a title and carries none of the four fields below, so they are read for the whole page in one further call — and empty here means that call did not reach the tracker rather than that the issue has no area.'),
                'assignedTo' => Schema::string('Who the tracker says holds this, empty where nobody does. What it decides for a triage is whether the issue is free to take, and on an old one it is usually who last touched it rather than who is on it.'),
                'createdOn' => Schema::string('When it was filed.'),
                'updatedOn' => Schema::string('When anything last moved on it, which is the measure of neglect rather than of age.'),
                'url' => Schema::string('Where a person reads it.'),
            ], ['issue', 'subject', 'tracker', 'status', 'category', 'assignedTo', 'createdOn', 'updatedOn', 'url']), 'The issues the query matched or the enumeration selected, in the tracker\'s own order — nothing here ranks them, and what an entry is worth is the caller\'s to judge. Empty where an issue was read by number.'),
            'unavailable' => [
                'type' => ['object', 'null'],
                'description' => 'Why nothing was answered, where status says unavailable. Null otherwise.',
                'properties' => [
                    'cause' => [
                        'type' => 'string',
                        'enum' => ['source-not-answering', 'source-not-parseable'],
                        'description' => 'source-not-answering: the tracker did not answer this time. '
                            . 'source-not-parseable: something answered with a page rather than with the API, '
                            . 'which is what the bot protection in front of it looks like from here.',
                    ],
                    'reason' => Schema::string(),
                ],
                'required' => ['cause', 'reason'],
            ],
        ], ['status', 'source', 'url', 'query', 'total', 'categories', 'categoriesUsed', 'issue', 'results', 'unavailable']);
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $query = is_string($args['query'] ?? null) ? trim($args['query']) : '';
        $open = is_string($args['open'] ?? null) ? trim($args['open']) : '';
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 15;

        if ($issue !== '') {
            return self::read($issue, is_string($args['notes'] ?? null) ? trim($args['notes']) : 'all');
        }
        if ($open !== '') {
            return self::enumerated(
                $open,
                is_string($args['tracker'] ?? null) ? trim($args['tracker']) : '',
                is_string($args['category'] ?? null) ? trim($args['category']) : '',
                is_string($args['createdBefore'] ?? null) ? trim($args['createdBefore']) : '',
                is_string($args['updatedBefore'] ?? null) ? trim($args['updatedBefore']) : '',
                $limit,
            );
        }

        return self::searched($query, $limit);
    }

    /**
     * Why nothing was answered, in the caller's terms rather than the
     * transport's — one shape for all three ways in, because what a caller does
     * about it is the same whichever question it asked.
     *
     * @return array{cause: string, reason: string}|null
     */
    private static function unreachable(?string $cause): ?array
    {
        if ($cause === null) {
            return null;
        }

        return [
            'cause' => $cause,
            'reason' => $cause === 'source-not-answering'
                ? 'The tracker did not answer. It is reachable at ' . Forge::HOST . ' in a browser; nothing here can answer this offline.'
                : 'Something answered with a page rather than with the API. The tracker sits behind bot protection, and what it challenges is a browser-shaped request.',
        ];
    }

    /** One issue, whole, which is what a number is asked for. */
    private static function read(string $issue, string $notes): ToolResult
    {
        $answer = (new Forge())->issue($issue, $notes);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => '',
            'total' => 0,
            'categories' => [],
            'categoriesUsed' => [],
            'issue' => $answer['issue'],
            'results' => [],
            'unavailable' => self::unreachable($answer['cause']),
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create('TYPO3 issue tracker: ' . $answer['url'] . "\nCould not answer: " . $data['unavailable']['reason'], $data);
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create('TYPO3 issue tracker: no issue ' . $issue . ' at ' . Forge::HOST . '.', $data);
        }

        $found = $answer['issue'];
        $lines = [
            sprintf('#%d %s', $found['id'], $found['subject']),
            sprintf('%s · %s · priority %s · %s', $found['tracker'], $found['status'], $found['priority'], $found['url']),
        ];
        $lines[] = $found['assignedTo'] !== ''
            ? 'Assigned to ' . $found['assignedTo'] . ' — which says who holds it and not that somebody is working on it.'
            : 'Assigned to nobody.';
        if ($found['targetVersion'] !== '') {
            $lines[] = 'Target version: ' . $found['targetVersion'];
        }
        if ($found['typo3Version'] !== '') {
            $lines[] = 'Reported against TYPO3 ' . $found['typo3Version']
                . ($found['phpVersion'] !== '' ? ', PHP ' . $found['phpVersion'] : '')
                . ' — which is what the reporter had, not what it still reproduces on.';
        }
        foreach ($found['relations'] as $relation) {
            // The subject, so which relation is worth an issue read is decided
            // here rather than by reading all of them.
            $lines[] = rtrim(sprintf(
                'Relation: %s #%d — %s',
                $relation['relation'],
                $relation['issue'],
                implode(' · ', array_filter([$relation['tracker'], $relation['status'], $relation['subject']])),
            ), ' —');
        }
        $lines[] = '';
        $lines[] = '## Reported';
        $lines[] = $found['description'];
        if ($found['reviews'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Changes on review.typo3.org (%d)', count($found['reviews']));
            $lines[] = 'Named in the comments below and lifted out of them. What state one is in now is a'
                . ' typo3_gerrit_lookup call — pass the number as change, or the Change-Id, which is what survives a'
                . ' rebase onto another branch. A comment says what was true the day it was written.';
            foreach ($found['reviews'] as $review) {
                $lines[] = '- ' . implode(' · ', array_filter([
                    $review['change'] > 0 ? 'change ' . $review['change'] : '',
                    $review['patchSet'] > 0 ? 'patch set ' . $review['patchSet'] : '',
                    $review['changeId'],
                    $review['on'] !== '' ? 'named ' . substr($review['on'], 0, 10) : '',
                    $review['url'],
                ]));
            }
        }
        if ($found['attachments'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Attachments (%d)', count($found['attachments']));
            $lines[] = 'On a report about rendering these are usually where the evidence is, and Redmine writes an'
                . ' inline image into a comment as !filename! — so a comment below that is nothing but a filename is'
                . ' referring to one of these. Read the ones the report turns on; this server does not fetch them.';
            foreach ($found['attachments'] as $file) {
                $lines[] = sprintf(
                    '- %s · %s · %s',
                    $file['filename'],
                    implode(' · ', array_filter([
                        $file['contentType'],
                        $file['size'] > 0 ? sprintf('%.0f kB', $file['size'] / 1000) : '',
                        $file['on'] !== '' ? substr($file['on'], 0, 10) : '',
                    ])),
                    $file['url'],
                );
            }
        }
        if ($found['notes'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Comments (%d of %d, oldest first)', count($found['notes']), $found['noteCount']);
            $lines[] = 'What was decided is here rather than above.';
            if ($notes === 'people') {
                $lines[] = sprintf(
                    '%d of them a review bot wrote and they were dropped. The changes they named are above. Ask for'
                        . ' notes "all" to read them; a count of 0 on an issue with patch-set pings means this filter'
                        . ' does not know the bot that wrote them.',
                    $found['botNoteCount'],
                );
            }
            foreach ($found['notes'] as $note) {
                $lines[] = '';
                $lines[] = sprintf('**%s**, %s', $note['author'], $note['on']);
                $lines[] = $note['note'];
            }
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * The issues a set of words matches, with what a caller has to know about
     * the set: these words found it, and other words find other issues.
     *
     * An empty answer is where that matters most. A report worded differently
     * is invisible to a word match, so nothing matching is a statement about
     * the query and never about whether the bug was reported — `D-ANS-038`
     * names reading it the other way as the failure this is written against.
     */
    private static function searched(string $query, int $limit): ToolResult
    {
        $answer = (new Forge())->search($query, $limit);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => $answer['query'],
            'total' => $answer['total'],
            'categories' => [],
            'categoriesUsed' => [],
            'issue' => null,
            'results' => $answer['results'],
            'unavailable' => self::unreachable($answer['cause']),
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create(
                'TYPO3 issue tracker, searched for "' . $answer['query'] . "\"\nCould not answer: " . $data['unavailable']['reason'],
                $data,
            );
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create(
                'TYPO3 issue tracker: no issue matches "' . $answer['query'] . '" at ' . Forge::HOST . ".\n"
                . 'These words matched nothing, which is not that nobody reported it: an issue worded differently is '
                . 'invisible to a word search. Ask again in the words a reporter would have used.',
                $data,
            );
        }

        $lines = [
            sprintf('TYPO3 issue tracker: %d issues match "%s"', count($answer['results']), $answer['query']),
            'These words, in the tracker\'s own order and unranked. Another wording finds another set, so this is'
                . ' which issues mention it rather than which one it duplicates. Read one whole by passing its number as issue.',
        ];
        foreach ($answer['results'] as $hit) {
            $lines[] = '';
            $lines[] = sprintf('## #%d %s', $hit['issue'], $hit['subject']);
            // The tracker and the status where the title carried them, which is
            // every hit the tracker words as it words its own; the area, the
            // assignee and the two dates where the fields behind them were
            // reachable.
            $lines[] = implode(' · ', array_filter([
                $hit['tracker'],
                $hit['status'],
                $hit['category'],
                $hit['assignedTo'] !== '' ? 'assigned to ' . $hit['assignedTo'] : '',
                $hit['createdOn'] !== '' ? 'filed ' . substr($hit['createdOn'], 0, 10) : '',
                $hit['updatedOn'] !== '' ? 'last touched ' . substr($hit['updatedOn'], 0, 10) : '',
                $hit['url'],
            ]));
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * The open issues, ordered by the thing that was asked about them.
     *
     * What this owes a caller beyond the entries is the size of what it is
     * looking at. A backlog answers a filter with thousands, and a page of
     * thirty read as the set is a triage that believes it has seen the problem.
     * So the count of everything that matched leads, and where it is larger
     * than the page the answer says which way to make it smaller: a narrower
     * filter and not a larger limit, because the order is the tracker's and
     * more of it is more of the same end.
     */
    private static function enumerated(
        string $order,
        string $tracker,
        string $category,
        string $createdBefore,
        string $updatedBefore,
        int $limit,
    ): ToolResult {
        $answer = (new Forge())->open($order, $tracker, $category, $createdBefore, $updatedBefore, $limit);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => '',
            'total' => $answer['total'],
            'categories' => $answer['categories'],
            'categoriesUsed' => $answer['categoriesUsed'],
            'issue' => null,
            'results' => $answer['results'],
            'unavailable' => self::unreachable($answer['cause']),
        ];

        $narrowed = implode(', ', array_filter([
            $tracker !== '' ? 'tracker ' . $tracker : '',
            $answer['categoriesUsed'] !== [] ? 'in ' . implode(' and ', $answer['categoriesUsed']) : '',
            $createdBefore !== '' ? 'filed before ' . $createdBefore : '',
            $updatedBefore !== '' ? 'untouched since ' . $updatedBefore : '',
        ]));
        $selection = 'open issues of the TYPO3 Core project'
            . ($narrowed !== '' ? ', ' . $narrowed : '')
            . ', ' . ($order === 'stale' ? 'longest untouched first' : 'oldest filed first');

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create(
                'TYPO3 issue tracker, ' . $selection . "\nCould not answer: " . $data['unavailable']['reason'],
                $data,
            );
        }
        // A word that named no category is a different answer to a filter that
        // excluded everything, and reading it as an empty backlog is the one
        // mistake this path can make.
        if ($category !== '' && $answer['categoriesUsed'] === []) {
            return ToolResult::create(
                'TYPO3 issue tracker: "' . $category . '" names no area the core files issues under, so nothing was'
                . " read. That is about the word and not about the backlog.\nThe areas are: "
                . implode(', ', $answer['categories']),
                $data,
            );
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create(
                'TYPO3 issue tracker: nothing open matches ' . $selection . ".\n"
                . 'The filters excluded everything, which is an answer about them and not about the backlog. Widen the '
                . 'dates or drop the tracker.',
                $data,
            );
        }

        $shown = count($answer['results']);
        $lines = [
            sprintf('TYPO3 issue tracker: %d of %d %s', $shown, $answer['total'], $selection),
        ];
        $lines[] = $shown < $answer['total']
            ? 'This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one'
                . ' tracker — rather than by a larger limit, because the order is the tracker\'s own and more of it is more'
                . ' of the same end.'
            : 'That is the whole set on these filters.';
        $lines[] = 'Age is a candidate and never a finding: read one whole by passing its number as issue, and what it'
            . ' still claims is established in the checkout rather than off this list.';
        if ($answer['categoriesUsed'] !== []) {
            // Where the reporter filed it, which is not everything about the
            // subject: three of the RTE reports a session went looking for on
            // 2026-08-05 sat under System/Bootstrap/Configuration and under
            // Link Handling.
            $lines[] = 'An area is where an issue was filed and not everything it is about. A report about this one'
                . ' regularly sits under another area, so what came back is a floor rather than the set — query the'
                . ' words as well where the question is about a subject.';
        }
        foreach ($answer['results'] as $entry) {
            $lines[] = '';
            $lines[] = sprintf('## #%d %s', $entry['issue'], $entry['subject']);
            $lines[] = implode(' · ', array_filter([
                $entry['tracker'],
                $entry['status'],
                $entry['category'],
                $entry['assignedTo'] !== '' ? 'assigned to ' . $entry['assignedTo'] : 'unassigned',
                $entry['createdOn'] !== '' ? 'filed ' . substr($entry['createdOn'], 0, 10) : '',
                $entry['updatedOn'] !== '' ? 'last touched ' . substr($entry['updatedOn'], 0, 10) : '',
                $entry['url'],
            ]));
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }
}
