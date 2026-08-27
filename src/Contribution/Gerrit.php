<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Http\Recent;

/**
 * The review server the core's patches live on, read over its REST API.
 *
 * The one question every core task asks before it starts — is there a change for
 * this issue already — is not answerable from a checkout, and four sessions in
 * one week answered it by hand, XSSI prefix and all (`D-FBK-027`). Read-only,
 * and no credential: everything here is what the anonymous REST API serves,
 * while voting, uploading and abandoning are the caller's to do through git and
 * the web UI.
 *
 * @phpstan-type Answer array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, more: bool, cause: ?string}
 */
final class Gerrit
{
    public const HOST = 'https://review.typo3.org';

    /** The one project this server is about, as the review server spells it. */
    public const PROJECT = 'Packages/TYPO3.CMS';

    /**
     * Seconds a found change is held for.
     *
     * Short, because the caller is the one who changes these answers: it pushes
     * a patch and asks about it, or amends one and asks which patch set is
     * current. Long enough that the same question inside one task is asked of
     * the review server once.
     */
    public const HELD_FOR = 30;

    private readonly Fetch $fetch;

    /** @var (\Closure(string): ?string)|null */
    private readonly ?\Closure $transport;

    /**
     * The tracker, asked one question: what the issues a commit message names
     * are about (`D-ANS-098`). Built where that question is asked rather than in
     * the constructor, because `Forge` builds a `Gerrit` of its own and two
     * constructors calling each other never return.
     */
    private ?Forge $tracker = null;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport);
        $this->transport = $transport;
    }

    /**
     * The changes that name a Forge issue in their commit message.
     *
     * `message:` is what asks the question — `Resolves:` and `Related:` are
     * where the issue number sits — but it is not the whole of the answer. The
     * index behind that operator also carries the change's own number, so a
     * query for issue 88556 comes back with change 88556 as well, whatever that
     * change is about. Five of seven calls in one session were that
     * (`feedback/2026-08-05-033826`), every one of them a MERGED core change
     * with a plausible subject and nothing to do with the issue.
     *
     * That is the most expensive answer this tool can give, because both core
     * skills treat a hit as grounds to stop: somebody has a patch up, so the
     * triage is that it is under review. So what the server matched is held
     * against what the commit message actually says, and a change that does not
     * name the issue is not handed back.
     *
     * @return Answer
     */
    public function changesForIssue(string $issue, int $limit = 10): array
    {
        $number = ltrim(trim($issue), '#');
        $answer = $this->search('message:' . $number, $limit, self::CURRENT_COMMIT);

        $named = [];
        foreach ($answer['changes'] as $change) {
            if (self::names($change, $number)) {
                $message = is_string($change['message'] ?? null) ? $change['message'] : '';
                if ($message !== '') {
                    $change['releases'] = self::releases($message);
                }
                // Read to decide this and handed back by a change read by name
                // alone: what a caller asked here is which changes exist, and
                // the message is the 0.9 KB a hit grows by (`D-ANS-100`) on an
                // answer that carries up to 25 of them — `D-ANS-112`.
                $change['message'] = null;
                $named[] = $change;
            }
        }

        $answer['dropped'] = count($answer['changes']) - count($named);
        $answer['changes'] = $named;
        if ($answer['status'] === 'answered' && $named === []) {
            $answer['status'] = 'empty';
        }

        return $answer;
    }

    /**
     * The changes matching words, a path, or both.
     *
     * The question a triage opens with is neither handle: whether anybody has an
     * open change on a file, and whether anybody ever tried this fix.
     * `feedback/2026-08-24-110833` answered both with calls it composed itself,
     * because a core clone carries what landed and says nothing about what is
     * open (`D-ANS-100`).
     *
     * The boundary is the issue search's, and the commit message is outside it:
     * a hit is 1.6 KB without it and 2.5 KB with it, and nothing on this path
     * reads it (`D-ANS-100`).
     *
     * @return Answer
     */
    public function changesMatching(string $words, string $path, bool $open = false, int $limit = 10): array
    {
        $query = self::matching($words, $path, $open);
        if ($query === '') {
            return ['status' => 'empty', 'query' => '', 'changes' => [], 'dropped' => 0, 'more' => false, 'cause' => null];
        }

        return $this->search($query, $limit);
    }

    /**
     * The one query the words, the path and the narrowing come to.
     *
     * Composed here rather than passed through, so what reaches the server is a
     * query this side can state and the caller can rerun. Every part of the form
     * was measured, and each is a query that fails without failing — the
     * quoting, the two alternatives a path becomes, and the `^` that is Gerrit's
     * marker rather than part of the pattern are all `D-ANS-100`.
     */
    private static function matching(string $words, string $path, bool $open): string
    {
        $terms = [];
        foreach (preg_split('~\s+~', trim($words), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            $terms[] = self::quoted($word);
        }
        $file = trim(trim($path), '/');
        if ($file !== '') {
            $whole = self::escaped($file);
            $terms[] = 'file:' . self::quoted('^' . $whole . '/.*|' . $whole);
        }
        if ($terms === []) {
            return '';
        }

        return implode(' ', $open ? ['status:open', ...$terms] : $terms);
    }

    /**
     * The most rows one call answers with, measured against review.typo3.org on
     * 2026-08-25: `n=1000` came back with 500 and said there was more.
     */
    private const PAGE = 500;

    /**
     * How many of those the enumeration reads before it stops.
     *
     * The whole open core backlog was 855 changes on 2026-08-25 — two calls,
     * 1.1 MB and half a second — so this is headroom rather than a bound
     * anybody has met, and the answer says where it cut.
     */
    private const PAGES = 4;

    /**
     * The changes the review backlog is waiting on, ordered by age.
     *
     * The question two sessions asked on one day and neither could put to this
     * server: which open changes are old, small, voted on, still merging, and
     * whose. Both wrote the predicates by hand and one scored 859 changes in a
     * script of its own (`D-ANS-107`).
     *
     * The order is this side's and has to be. The review server sorts by last
     * activity, offers no predicate on the created date and states no total, so
     * oldest-first is the matched set read whole and sorted here — which is what
     * the page bound above is for and what `read` and `complete` report.
     *
     * @param string $order `oldest` by when a change was filed, `stale` by when
     *                      it last moved
     * @param int $maxSize insertions plus deletions, 0 for no bound
     * @param int $minCodeReview the lowest Code-Review vote the change must
     *                           carry, 0 for no bound
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, more: bool, cause: ?string, read: int, complete: bool}
     */
    public function backlog(
        string $order = 'oldest',
        int $maxSize = 0,
        int $minCodeReview = 0,
        bool $negativeVotes = true,
        bool $mergeable = false,
        string $branch = '',
        string $updatedBefore = '',
        string $owner = '',
        string $reviewedBy = '',
        string $involving = '',
        string $reviewableBy = '',
        int $limit = 10,
    ): array {
        $query = self::waiting(
            $maxSize,
            $minCodeReview,
            $negativeVotes,
            $mergeable,
            $branch,
            $updatedBefore,
            $owner,
            $reviewedBy,
            $involving,
            $reviewableBy,
        );

        $changes = [];
        $answer = ['status' => 'empty', 'query' => $query, 'changes' => [], 'dropped' => 0, 'more' => false, 'cause' => null];
        for ($read = 0; $read < self::PAGES; ++$read) {
            $answer = $this->page($query, self::PAGE, $read * self::PAGE, '');
            if ($answer['status'] === 'unavailable') {
                return [...$answer, 'read' => count($changes), 'complete' => false];
            }
            $changes = [...$changes, ...$answer['changes']];
            if (!$answer['more']) {
                break;
            }
        }

        // Ascending, so the front of the list is the end the caller asked for.
        // Both fields are the one fixed-width timestamp the review server
        // writes, which sorts as text.
        $on = $order === 'stale' ? 'updated' : 'created';
        usort($changes, static fn(array $one, array $other): int => strcmp((string) $one[$on], (string) $other[$on]));

        return [
            'status' => $changes === [] ? 'empty' : 'answered',
            'query' => $query,
            'changes' => array_slice($changes, 0, max(1, $limit)),
            'dropped' => 0,
            'more' => count($changes) > $limit,
            'cause' => null,
            'read' => count($changes),
            'complete' => !$answer['more'],
        ];
    }

    /**
     * The one query the filters of a backlog come to.
     *
     * Composed here rather than passed through, the way the words and the path
     * are (`D-ANS-100`): the operators, the quoting and the escaping stay on
     * this side and the answer states what they produced.
     *
     * `-is:wip` is in every one of them and is not a filter a caller sets. A
     * change its own author marked work in progress is not offered for review,
     * which is what this enumerates — and it is 411 of the 855 open core changes
     * measured on 2026-08-25, so leaving them in is a backlog that is half
     * somebody else's drafts.
     *
     * `before:` is the date operator and it reads the last update rather than
     * the creation: the review server indexes no created date at all, which is
     * also why the ordering is this side's.
     */
    private static function waiting(
        int $maxSize,
        int $minCodeReview,
        bool $negativeVotes,
        bool $mergeable,
        string $branch,
        string $updatedBefore,
        string $owner,
        string $reviewedBy,
        string $involving,
        string $reviewableBy,
    ): string {
        $terms = ['project:' . self::quoted(self::PROJECT), 'status:open', '-is:wip'];
        if ($maxSize > 0) {
            $terms[] = 'delta:<=' . $maxSize;
        }
        if ($minCodeReview > 0) {
            $terms[] = 'label:Code-Review>=' . $minCodeReview;
        }
        if (!$negativeVotes) {
            // Both labels, because either one blocks: a Code-Review-1 is a
            // reviewer objecting and a Verified-1 is the pipeline failing.
            $terms[] = '-label:Code-Review<=-1';
            $terms[] = '-label:Verified<=-1';
        }
        if ($mergeable) {
            $terms[] = 'is:mergeable';
        }
        if ($branch !== '') {
            $terms[] = 'branch:' . self::quoted($branch);
        }
        if ($updatedBefore !== '') {
            $terms[] = 'before:' . self::quoted($updatedBefore);
        }
        if ($owner !== '') {
            $terms[] = 'owner:' . self::quoted($owner);
        }
        if ($reviewedBy !== '') {
            $terms[] = 'reviewedby:' . self::quoted($reviewedBy);
        }
        // One query rather than two reads, which is where this differs from the
        // tracker: Gerrit's parser takes the alternation and Redmine ANDs its
        // filters, so `D-ANS-089` had to union two answers by hand.
        if ($involving !== '') {
            $terms[] = '(owner:' . self::quoted($involving) . ' OR reviewedby:' . self::quoted($involving) . ')';
        }
        // The complement of that union, and both operators together: what a
        // reviewer with time asks for is the changes that are neither theirs nor
        // already judged by them, and either half alone is a set nobody asked
        // for — `D-ANS-109`.
        if ($reviewableBy !== '') {
            $terms[] = '-owner:' . self::quoted($reviewableBy);
            $terms[] = '-reviewedby:' . self::quoted($reviewableBy);
        }

        return implode(' ', $terms);
    }

    /** One value Gerrit's query parser reads as a value and not as syntax. */
    private static function quoted(string $value): string
    {
        return '"' . addcslashes($value, '"\\') . '"';
    }

    /** One path RE2 matches as itself rather than as a pattern. */
    private static function escaped(string $path): string
    {
        return preg_replace('~[\\\\.+*?()\[\]{}^$|]~', '\\\\$0', $path) ?? $path;
    }

    /**
     * How many issues one batched query names.
     *
     * Measured against review.typo3.org on 2026-08-08: 36 issue numbers asked
     * twelve to a query answered 36 changes in 3 calls. What bounds it is the
     * URL rather than a documented limit, so a page is asked in batches rather
     * than in one query — `D-ANS-069`.
     */
    private const BATCHED = 12;

    /**
     * Which of these issues the review server holds a change for.
     *
     * The question a backlog row asks is the one `changesForIssue()` answers
     * for a single issue, and asking it per row is a call per row. `message:`
     * takes an alternation, so a page is a handful of calls: the changes come
     * back for the whole batch and are sorted onto the issues their commit
     * messages actually name.
     *
     * That last part is why the batch is not cheaper than it looks. The index
     * behind `message:` matches a change's own number as well, so a query for
     * twelve issues answers changes belonging to none of them — the same false
     * positive `changesForIssue()` drops, and dropped here by the same rule.
     *
     * @param list<int> $issues
     * @return array<int, list<array<string, mixed>>>
     */
    public function changesForIssues(array $issues): array
    {
        $wanted = [];
        foreach ($issues as $issue) {
            if ($issue > 0) {
                $wanted[$issue] = (string) $issue;
            }
        }

        $found = [];
        foreach (array_chunk(array_values($wanted), self::BATCHED) as $batch) {
            $query = implode(' OR ', array_map(static fn(string $number): string => 'message:' . $number, $batch));
            foreach ($this->search($query, self::MOST, self::CURRENT_COMMIT)['changes'] as $change) {
                // What a row carries is the number, so a change without one is
                // no handle and is nothing to report a row as having.
                if (($change['number'] ?? 0) < 1) {
                    continue;
                }
                $named = array_filter($batch, static fn(string $number): bool => self::names($change, $number));
                // After the message was read against every number in the batch,
                // and not inside that loop: it is what the rule reads.
                $change['message'] = null;
                foreach ($named as $number) {
                    $found[(int) $number][] = $change;
                }
            }
        }

        return $found;
    }

    /**
     * Whether this change's commit message really names the issue.
     *
     * Two places carry the number without meaning it. The `Reviewed-on:`
     * trailer a merged change gains ends in the change's own number, which is
     * exactly the false positive above wearing the evidence that would clear
     * it; and any other URL in the message can carry digits. Both are dropped
     * before the message is read, so what is left is prose and trailers —
     * `Resolves: #88556`, `Related: #88556`, an issue named in a sentence.
     *
     * A change whose message did not come back is judged by the one rule that
     * needs none: it is the false positive when its own number is the number
     * that was asked for, and it is an ordinary hit otherwise.
     *
     * @param array<string, mixed> $change
     */
    private static function names(array $change, string $number): bool
    {
        $message = is_string($change['message'] ?? null) ? $change['message'] : '';
        if ($message === '') {
            return ((string) ($change['number'] ?? '')) !== $number;
        }

        $prose = preg_replace('~https?://\S+~', ' ', $message) ?? $message;
        $prose = preg_replace('~^Change-Id:.*$~mi', ' ', $prose) ?? $prose;

        return preg_match('~(?<![\d.])' . preg_quote($number, '~') . '(?![\d.])~', $prose) === 1;
    }

    /**
     * One change by its number or its Change-Id, the changes sharing that id,
     * and the review they are in.
     *
     * @return Answer
     */
    public function change(string $change, int $limit = 1, string $messages = 'none'): array
    {
        return $this->named('change:', $change, $limit, $messages);
    }

    /**
     * The same answer for the one handle a checkout hands over: a commit.
     *
     * A session triaging old issues holds hashes out of `git log` and nothing
     * else, and `change:` refuses one — `change:cc880c67777` answers HTTP 400
     * `Invalid change format`, which reads here as the review server not
     * answering at all (`D-ANS-106`). `commit:` takes it, abbreviated as a
     * caller pastes it, and what it names is one change; the siblings the query
     * after it finds are the backports, which is the set the question "which
     * branches carry this fix" is actually about.
     *
     * @return Answer
     */
    public function commit(string $commit, int $limit = 1, string $messages = 'none'): array
    {
        return $this->named('commit:', $commit, $limit, $messages);
    }

    /**
     * One change the caller named, the changes sharing its Change-Id, and the
     * review they are in.
     *
     * Nothing is filtered here. A caller naming a change has named it, and the
     * answer is that change whatever its commit message says. What the handle
     * decided until `D-ANS-080` is whether the backport is in the answer at all,
     * so the second query is what makes the handles answer the same set, and
     * it is asked whether there is a sibling or not because only the answer
     * says. The votes come with it and the comments are one further call, made
     * only where the change says it carries one (`D-ANS-079`). The review log is
     * what `$messages` asks for, on every change the answer carries.
     *
     * The relation chain comes with them, on every change the answer carries,
     * because a change read alone says a feature exists where the stack under
     * it says what the feature consists of (`D-ANS-094`). So do the issues the
     * commit message names, which is what joins the patch to the tracker
     * (`D-ANS-098`), and the branches its `Releases:` trailer claims
     * (`D-ANS-106`).
     *
     * This is also where the patch itself is established: the paths the current
     * patch set touches and the commit message whole, so a caller reaches both
     * without putting the change on disk (`D-ANS-112`).
     *
     * The log is read on every call rather than where `$messages` asks for it,
     * because one fact in it is about the change and not about the review: a
     * patch set carrying git conflict markers is reported there and nowhere else
     * (`D-ANS-121`). What `$messages` decides is what the caller is handed.
     *
     * @param string $operator the Gerrit query prefix the handle belongs to
     * @return Answer
     */
    private function named(string $operator, string $change, int $limit, string $messages): array
    {
        $options = self::REVIEW . self::CURRENT_COMMIT . self::CURRENT_FILES . self::MESSAGES;
        $handle = trim($change);
        $answer = $this->search($operator . $handle, $limit, $options);

        $named = $answer['changes'][0] ?? null;
        $id = is_string($named['changeId'] ?? null) ? $named['changeId'] : '';
        if (count($answer['changes']) === 1 && $id !== '' && strcasecmp($id, $handle) !== 0) {
            $siblings = $this->search('change:' . $id, $limit, $options);
            // A query that did not answer is not an absence of siblings, so the
            // change that was named stands rather than being replaced by
            // nothing.
            if ($siblings['status'] === 'answered') {
                // The named change first, and `n` applied after it. Gerrit
                // orders by last activity, so `change:<id>&n=1` answers the
                // sibling that moved most recently — measured on 2026-08-14,
                // where change 95169 asked for by number came back as 93202.
                $changes = $answer['changes'];
                foreach ($siblings['changes'] as $sibling) {
                    if (($sibling['number'] ?? null) !== ($named['number'] ?? null)) {
                        $changes[] = $sibling;
                    }
                }
                $siblings['changes'] = array_slice($changes, 0, max(1, $limit));
                $answer = $siblings;
            }
        }

        foreach ($answer['changes'] as $index => $found) {
            $answer['changes'][$index]['comments'] = $this->comments($found['number'], $found['commentCount']);
            $answer['changes'][$index]['chain'] = $this->chain($found['number']);
            $message = is_string($found['message'] ?? null) ? $found['message'] : '';
            if ($message !== '') {
                $answer['changes'][$index]['releases'] = self::releases($message);
            }
            if (!is_array($found['messages'])) {
                continue;
            }
            $answer['changes'][$index]['conflicts'] = self::conflicts($found['messages'], $found['patchSet']);
            $written = array_values(array_filter(
                $found['messages'],
                static fn(array $message): bool => $message['bot'] === false,
            ));
            // Counted whichever way it was asked, so a log full of pipeline
            // reports answering zero here is Gerrit no longer tagging its
            // service users rather than a change no bot has been near.
            $answer['changes'][$index]['botMessageCount'] = count($found['messages']) - count($written);
            // The log itself is still what `$messages` asks for: it is 50 KB in
            // a caller's context, and the fact above is the one thing that moved
            // out of it.
            $answer['changes'][$index]['messages'] = match ($messages) {
                'people' => $written,
                'all' => $found['messages'],
                default => null,
            };
        }
        $answer['changes'] = $this->issues($answer['changes']);

        return $answer;
    }

    /**
     * Every change, carrying the issues its commit message names, each filled
     * with what says whether to read it.
     *
     * The message is the only thing joining a patch to the tracker, and a
     * session that does not know a second issue is named has nothing to notice
     * by: `feedback/2026-08-24-100458` walked four calls to reach an issue the
     * first change already named (`D-ANS-098`). One bulk read for every change
     * in the answer rather than one per issue, which is the read a relation is
     * already filled by (`R-ANS-029`); where the tracker cannot be reached the
     * numbers stand with the fields empty, because the handle is what the walk
     * needs.
     *
     * @param list<array<string, mixed>> $changes
     * @return list<array<string, mixed>>
     */
    private function issues(array $changes): array
    {
        $numbers = [];
        foreach ($changes as $index => $change) {
            $message = is_string($change['message'] ?? null) ? $change['message'] : '';
            if ($message === '') {
                continue;
            }
            $changes[$index]['issues'] = self::trailers($message);
            $numbers = [...$numbers, ...array_column($changes[$index]['issues'], 'issue')];
        }
        if ($numbers === []) {
            return $changes;
        }

        $fields = $this->tracker()->fields($numbers);
        foreach ($changes as $index => $change) {
            foreach ($change['issues'] ?? [] as $at => $named) {
                $read = $fields[$named['issue']] ?? null;
                $changes[$index]['issues'][$at]['subject'] = $read['subject'] ?? '';
                $changes[$index]['issues'][$at]['tracker'] = $read['tracker'] ?? '';
                $changes[$index]['issues'][$at]['status'] = $read['status'] ?? '';
            }
        }

        return $changes;
    }

    /**
     * The issues a commit message's `Resolves:` and `Related:` trailers name.
     *
     * The two are told apart because they are different claims: what the patch
     * closes, and what it touches. Only those lines are read, so a number in a
     * URL or in a quoted log line is not an issue this answer invents — the
     * `Reviewed-on:` trailer a merged change gains ends in the change's own
     * number, which is the one that would be wrong most often.
     *
     * A trailer naming no number names no issue. The current patch set of
     * change 91563 carries the line `Resolves: #` with nothing after it, and an
     * issue nobody can look up is worse than none.
     *
     * @return list<array<string, mixed>>
     */
    private static function trailers(string $message): array
    {
        if (preg_match_all('~^(Resolves|Related):(.*)$~mi', $message, $lines, PREG_SET_ORDER) === false) {
            return [];
        }

        $named = [];
        foreach ($lines as $line) {
            preg_match_all('~#(\d+)~', $line[2], $found);
            foreach ($found[1] as $number) {
                // The first trailer naming an issue is the claim it is answered
                // with, which is `Resolves:` where a message carries both: the
                // core writes what it closes above what it touches.
                $named[(int) $number] ??= [
                    'issue' => (int) $number,
                    'trailer' => strtolower($line[1]),
                    'subject' => '',
                    'tracker' => '',
                    'status' => '',
                    'url' => Forge::HOST . '/issues/' . (int) $number,
                ];
            }
        }

        return array_values($named);
    }

    /**
     * What Gerrit calls a file's status, in the words the answer says it in.
     *
     * A letter is unreadable where a path is not, and the change beside it
     * already answers `NEW` and `MERGED` under the same word — so this is
     * `action` and it is spelled out. A file the patch set only edits carries
     * no status at all, which is why `modified` has no letter here.
     */
    private const ACTIONS = ['A' => 'added', 'D' => 'deleted', 'R' => 'renamed', 'C' => 'copied', 'W' => 'rewritten'];

    /**
     * The paths the current patch set touches, each with what it does to one.
     *
     * The first of the four things a review is told to establish, and the one a
     * session went to the checkout for: eight changes were fetched into the
     * user's own working tree to triage a shortlist that needed nothing but
     * this (`D-ANS-112`). What stays outside is the diff — a file list decides
     * a triage and the hunks are what a fetch is for.
     *
     * Sorted by path, because the order Gerrit sends is its own and a reader
     * scanning for a subsystem reads the neighbours together.
     *
     * Gerrit omits a line count that is zero and both of them on a binary, so
     * an absent count is no lines rather than an unstated number — which is
     * where this differs from the change's own insertions and deletions.
     *
     * @return list<array<string, mixed>>|null null where the query did not ask
     *                                         for them
     */
    private static function files(mixed $files): ?array
    {
        if (!is_array($files)) {
            return null;
        }
        ksort($files);

        $touched = [];
        foreach ($files as $path => $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $status = is_string($entry['status'] ?? null) ? $entry['status'] : '';
            $touched[] = [
                'path' => (string) $path,
                'action' => self::ACTIONS[$status] ?? 'modified',
                'insertions' => self::counted($entry['lines_inserted'] ?? null) ?? 0,
                'deletions' => self::counted($entry['lines_deleted'] ?? null) ?? 0,
                // What makes the two zeros above mean nothing: a binary has no
                // lines to count, and reading it as an untouched file is the
                // one misreading this list invites.
                'binary' => ($entry['binary'] ?? null) === true,
                // The path it was renamed or copied from, which is the whole of
                // what makes those two different from a delete and an add.
                'movedFrom' => is_string($entry['old_path'] ?? null) ? $entry['old_path'] : null,
            ];
        }

        return $touched;
    }

    /**
     * The branches a commit message's `Releases:` trailer names.
     *
     * The line the reporting session reached last and had already contradicted:
     * it read `git branch -r --contains` on the `main` commit, said two
     * branches, and the trailer said three (`D-ANS-106`). It is one claim and
     * the changes sharing the Change-Id are the other, which is why this is a
     * field of its own rather than something folded into them.
     *
     * A branch is whatever the author wrote between the commas, because the
     * spelling is the branch's — `main`, `13.4` — and a name nothing here
     * recognises is still what the trailer says.
     *
     * @return list<string>
     */
    private static function releases(string $message): array
    {
        preg_match_all('~^Releases:(.*)$~mi', $message, $lines);

        $named = [];
        foreach ($lines[1] as $line) {
            foreach (preg_split('~[,\s]+~', trim($line), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $branch) {
                if (!in_array($branch, $named, true)) {
                    $named[] = $branch;
                }
            }
        }

        return $named;
    }

    /** The tracker, built where an answer needs it and not before. */
    private function tracker(): Forge
    {
        return $this->tracker ??= new Forge($this->transport);
    }

    /**
     * The comments left on one change, oldest first.
     *
     * Its own endpoint, because no query option carries them. What comes back is
     * a file to a list of comments, and `/PATCHSET_LEVEL` is the file a comment
     * on the change itself is filed under. Each one carries the thread it is in
     * and what that thread stands at, read off the reply relation that is
     * already here (`D-ANS-111`). Nothing here decides whether a question was
     * answered: the state, the reply it is under and the patch set it was left
     * on are handed over and the reviewer reads them, because a thread somebody
     * resolved can still hold an open question (`D-ANS-079`).
     *
     * @param int $count what the change says it carries, so the call is made
     *                   only where there is something to fetch
     * @return list<array<string, mixed>>|null null where it could not be read
     */
    private function comments(int $number, int $count): ?array
    {
        if ($number < 1) {
            return null;
        }
        if ($count < 1) {
            return [];
        }

        $url = self::HOST . '/changes/' . $number . '/comments';
        /** @var list<array<string, mixed>>|null $held */
        $held = Recent::held($url, self::HELD_FOR);
        if ($held !== null) {
            return $held;
        }

        $decoded = Fetch::decode($this->fetch->get($url, ['Accept: application/json']));
        if ($decoded === null) {
            return null;
        }

        $comments = [];
        foreach ($decoded as $file => $left) {
            foreach (is_array($left) ? $left : [] as $comment) {
                if (!is_array($comment)) {
                    continue;
                }
                $author = is_array($comment['author'] ?? null) ? $comment['author'] : [];
                $comments[] = [
                    'id' => is_string($comment['id'] ?? null) ? $comment['id'] : '',
                    'author' => is_string($author['name'] ?? null) ? $author['name'] : '',
                    'on' => is_string($comment['updated'] ?? null) ? $comment['updated'] : '',
                    'patchSet' => isset($comment['patch_set']) && is_numeric($comment['patch_set'])
                        ? (int) $comment['patch_set']
                        : 0,
                    'file' => (string) $file,
                    // Absent on a comment about the change rather than about a
                    // place in it, which is every one filed under
                    // `/PATCHSET_LEVEL`.
                    'line' => isset($comment['line']) && is_numeric($comment['line']) ? (int) $comment['line'] : null,
                    'unresolved' => (bool) ($comment['unresolved'] ?? false),
                    'inReplyTo' => is_string($comment['in_reply_to'] ?? null) ? $comment['in_reply_to'] : null,
                    // Filled by `threaded()`, once the whole list is in the
                    // order a thread is read in.
                    'thread' => '',
                    'threadUnresolved' => false,
                    'message' => is_string($comment['message'] ?? null) ? trim($comment['message']) : '',
                ];
            }
        }
        // Chronological across the files, because a thread is read in the order
        // it was written and a reply sits under a comment on the same file.
        usort($comments, static fn(array $one, array $other): int => strcmp($one['on'], $other['on']));
        $comments = self::threaded($comments);

        Recent::hold($url, $comments);

        return $comments;
    }

    /**
     * The same comments, each saying which thread it is in and what that thread
     * stands at.
     *
     * Gerrit's REST documentation states both halves: `unresolved_comment_count`
     * is the "number of unresolved inline comment threads", and the state of
     * resolution of a thread "is stored in the last comment in that thread
     * chronologically". So the flag on one comment is one writer's, and the
     * thread is what the review server counts — which is why tallying the flags
     * answered a different number from the one printed beside it
     * (`D-ANS-111`).
     *
     * A reply whose parent is not in the list starts a thread of its own. That
     * is what a comment answering a draft looks like from here, and putting it
     * nowhere would drop it from the answer.
     *
     * @param list<array<string, mixed>> $comments oldest first, so a parent is
     *                                             read before the reply under it
     * @return list<array<string, mixed>>
     */
    private static function threaded(array $comments): array
    {
        $of = [];
        $state = [];
        foreach ($comments as $comment) {
            $parent = $comment['inReplyTo'];
            $thread = is_string($parent) && isset($of[$parent]) ? $of[$parent] : $comment['id'];
            $of[$comment['id']] = $thread;
            // Chronological, so the last write is the last comment's.
            $state[$thread] = $comment['unresolved'];
        }

        foreach ($comments as $index => $comment) {
            $comments[$index]['thread'] = $of[$comment['id']];
            $comments[$index]['threadUnresolved'] = $state[$of[$comment['id']]];
        }

        return $comments;
    }

    /**
     * The changes this one is stacked on and the changes stacked on it, child
     * first.
     *
     * Its own endpoint, because no query option carries the relation, and asked
     * by the change number: `/changes/<Change-Id>/…/related` answered 404
     * `Multiple changes found` on 2026-08-21 for the backport pair `D-ANS-080`
     * put in this answer. Nothing in the change payload says beforehand whether
     * there is a chain, so this is paid on every change and an empty one is the
     * ordinary answer rather than a failure — which is where it differs from
     * the comments beside it.
     *
     * The two revision numbers per entry are two facts. Gerrit names the patch
     * set that is in the chain and the patch set that change stands at now, and
     * they are one apart on a merged entry already, so acting on a chain entry
     * without holding the two against each other reads the stack as more
     * current than it is (`D-ANS-094`).
     *
     * @return list<array<string, mixed>>|null null where it could not be read
     */
    private function chain(int $number): ?array
    {
        if ($number < 1) {
            return null;
        }

        $url = self::HOST . '/changes/' . $number . '/revisions/current/related';
        /** @var list<array<string, mixed>>|null $held */
        $held = Recent::held($url, self::HELD_FOR);
        if ($held !== null) {
            return $held;
        }

        $decoded = Fetch::decode($this->fetch->get($url, ['Accept: application/json']));
        if (!is_array($decoded['changes'] ?? null)) {
            return null;
        }

        $chain = [];
        foreach ($decoded['changes'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $commit = is_array($entry['commit'] ?? null) ? $entry['commit'] : [];
            $related = isset($entry['_change_number']) && is_numeric($entry['_change_number'])
                ? (int) $entry['_change_number']
                : 0;
            $chain[] = [
                'number' => $related,
                'status' => is_string($entry['status'] ?? null) ? $entry['status'] : '',
                'subject' => is_string($commit['subject'] ?? null) ? $commit['subject'] : '',
                'thisChange' => $related === $number,
                'patchSet' => isset($entry['_current_revision_number']) && is_numeric($entry['_current_revision_number'])
                    ? (int) $entry['_current_revision_number']
                    : 0,
                'chainedAt' => isset($entry['_revision_number']) && is_numeric($entry['_revision_number'])
                    ? (int) $entry['_revision_number']
                    : 0,
                // The same two fields a change URL is built from, which this
                // endpoint answers per entry — `D-ANS-103`.
                'url' => self::url($related, is_string($entry['project'] ?? null) ? $entry['project'] : ''),
            ];
        }

        Recent::hold($url, $chain);

        return $chain;
    }

    /**
     * The option that makes the answer say which patch set it is about.
     *
     * A change query answers with the change and not with its revisions, so the
     * commit a reviewer would hold its checkout against is absent by default —
     * which is what a review of the wrong patch set looks like from here. It is
     * served over the same anonymous path as everything else (`D-ANS-033`),
     * verified against `review.typo3.org` on 2026-08-03 for a `change:` lookup
     * and a `message:` search alike.
     */
    private const CURRENT_REVISION = '&o=CURRENT_REVISION';

    /**
     * The option that adds the commit message to the current revision.
     *
     * Both forms ask for it and read it for different things: the issue search
     * holds what the server matched against what the message says, and a change
     * lookup lifts the issues its trailers name out of it (`D-ANS-098`). No
     * second round trip, and 1.4 KB on the 13.1 KB change 95015 answers with —
     * measured against `review.typo3.org` on 2026-08-24, over the same
     * anonymous path as everything else.
     */
    private const CURRENT_COMMIT = '&o=CURRENT_COMMIT';

    /**
     * The option that adds the paths the current patch set touches.
     *
     * Asked only where a caller named a change, beside the votes and the
     * comments. Measured against `review.typo3.org` on 2026-08-26 over the same
     * anonymous path as everything else: change 95369 answers 19.8 KB without
     * it and 23.8 KB with it, for the 25 files it touches. Of the 200 open core
     * changes read that day the median touches 5 files and the largest 233, so
     * what a change lookup grows by is regularly under a kilobyte —
     * `D-ANS-112`.
     */
    private const CURRENT_FILES = '&o=CURRENT_FILES';

    /**
     * The options that answer what state the review is in: the value every
     * voter holds per label, and the account each of them is.
     *
     * Asked only where a caller named a change. An issue search answers up to
     * 25 of them and asks whether a patch exists at all, which no vote on one
     * changes — and the options are what they cost: change 93319 came back at
     * 1.9 KB plain and 14.3 KB with them, measured against `review.typo3.org`
     * on 2026-08-14.
     */
    private const REVIEW = '&o=DETAILED_LABELS&o=DETAILED_ACCOUNTS';

    /**
     * The review log, fetched on every change read by name and handed on only
     * where the caller asked for it. The same change is 57.9 KB with it.
     *
     * What it carries that the labels do not is why a vote is gone. Gerrit
     * writes "Outdated Votes: * Code-Review+1 (copy condition: …)" into the
     * message of the upload that dropped it, and the label state afterwards
     * looks exactly like a change nobody has voted on (`D-ANS-079`). What it
     * carries that no other payload does at all is the conflict report below.
     *
     * That is why a change read by name asks for it whatever the caller wanted
     * the log for: over 50 open core changes read both ways on 2026-08-27 it
     * cost a median of 6.3 KB on this server's own fetch — `D-ANS-121`. A search
     * answers up to 25 changes and the enumeration reads up to 2000, so neither
     * asks for it.
     */
    private const MESSAGES = '&o=MESSAGES';

    /**
     * The sentence Gerrit opens its conflict report with.
     *
     * The report is matched on this rather than on the "Cherry Picked from
     * branch …" line above it: a rebase through the web UI writes the same list
     * under "Patch Set N was rebased", and that is 13 of the 39 reports the core
     * project carries — `D-ANS-121`.
     */
    private const CONFLICTED = 'The following files contain Git conflicts:';

    /**
     * The tag Gerrit puts on an account that is a machine.
     *
     * Read off the account rather than off a list of names, which is what
     * `Forge::BOTS` has to be: the tracker's journal carries no such field, and
     * `o=DETAILED_ACCOUNTS` carries this one. The `autogenerated:gerrit:*` tag
     * `D-ANS-079` names is a different fact — those messages are the uploader's
     * own, and they are where the copy condition that dropped a vote is written,
     * which is what the log is fetched for.
     */
    private const SERVICE_USER = 'SERVICE_USER';

    /**
     * The most changes one query answers with. A batched query asks for all of
     * them: twelve issues answered twelve changes when it was measured, so the
     * headroom is what an issue with several patches costs rather than a bound
     * anybody has met.
     */
    private const MOST = 25;

    /**
     * @param string $options what this query asks for beyond the current
     *                        revision, as the query string carries it
     * @return Answer
     */
    private function search(string $query, int $limit, string $options = ''): array
    {
        return $this->page($query, max(1, min(self::MOST, $limit)), 0, self::CURRENT_REVISION . $options);
    }

    /**
     * One page of a query, as the review server hands it over.
     *
     * `more` is the server's own `_more_changes`, which it sets on the last row
     * of a page it cut. It is read rather than inferred from the count: a set of
     * exactly as many changes as were asked for is a whole set as often as a cut
     * one, and only the flag separates them.
     *
     * @param int $skip how many matches to pass over, which is the only way past
     *                  the first page — the review server offers no offset in
     *                  any other form
     * @param string $options everything this query asks for beyond the change
     *                        itself, as the query string carries it
     * @return Answer
     */
    private function page(string $query, int $take, int $skip, string $options): array
    {
        $url = self::HOST . '/changes/?q=' . rawurlencode($query) . '&n=' . $take
            . ($skip > 0 ? '&S=' . $skip : '') . $options;
        /** @var Answer|null $held */
        $held = Recent::held($url, self::HELD_FOR);
        if ($held !== null) {
            return $held;
        }

        $body = $this->fetch->get($url, ['Accept: application/json']);
        if ($body === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'dropped' => 0, 'more' => false, 'cause' => 'source-not-answering'];
        }

        $decoded = Fetch::decode($body);
        if ($decoded === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'dropped' => 0, 'more' => false, 'cause' => 'source-not-parseable'];
        }

        $changes = [];
        // The flag rides on the last row of a page the server cut and on no
        // other, so what survives the loop is the end of what came back.
        $more = false;
        foreach ($decoded as $entry) {
            if (is_array($entry)) {
                $more = ($entry['_more_changes'] ?? false) === true;
                $changes[] = self::change_($entry);
            }
        }

        $answer = [
            'status' => $changes === [] ? 'empty' : 'answered',
            'query' => $query,
            'changes' => $changes,
            'dropped' => 0,
            'more' => $more,
            'cause' => null,
        ];
        // Only a change that was found. "No change for this issue" is the
        // answer the caller can falsify itself by pushing one, and it is the
        // question asked immediately after a push — so it is fetched every
        // time, and a stale "there is none" cannot be what sends somebody to
        // write a patch that exists.
        if ($answer['status'] === 'answered') {
            Recent::hold($url, $answer);
        }

        return $answer;
    }

    /**
     * One change, in the fields a caller asked this question for.
     *
     * The patch set is the one a review turns on: a change is a series of them
     * and the checkout in front of the reviewer is one commit, which is what
     * settles it against a local `HEAD`. What the Change-Id is carried for is
     * `D-ANS-080`.
     *
     * @param array<string, mixed> $entry
     * @return array<string, mixed>
     */
    private static function change_(array $entry): array
    {
        $number = isset($entry['_number']) && is_numeric($entry['_number']) ? (int) $entry['_number'] : 0;
        $patchSet = isset($entry['current_revision_number']) && is_numeric($entry['current_revision_number'])
            ? (int) $entry['current_revision_number']
            : 0;

        $revision = is_string($entry['current_revision'] ?? null) ? $entry['current_revision'] : '';
        $revisions = is_array($entry['revisions'] ?? null) ? $entry['revisions'] : [];
        $current = is_array($revisions[$revision] ?? null) ? $revisions[$revision] : [];
        $commit = is_array($current['commit'] ?? null) ? $current['commit'] : [];
        $project = is_string($entry['project'] ?? null) ? $entry['project'] : '';

        return [
            'number' => $number,
            // What `o=CURRENT_COMMIT` adds, where it was asked for. Null is a
            // message that did not come back, which is every query that did not
            // ask; a search that reads it for the issues it names nulls it
            // again before answering.
            'message' => is_string($commit['message'] ?? null) ? $commit['message'] : null,
            // What `o=CURRENT_FILES` adds, on a change read by name. Null the
            // same way, and an empty list would be a patch set touching
            // nothing.
            'files' => self::files($current['files'] ?? null),
            // The one handle that survives a rebase onto another branch, and
            // what a reviewer holds the commit in front of it against.
            'changeId' => is_string($entry['change_id'] ?? null) ? $entry['change_id'] : '',
            'subject' => is_string($entry['subject'] ?? null) ? $entry['subject'] : '',
            'status' => is_string($entry['status'] ?? null) ? $entry['status'] : '',
            'branch' => is_string($entry['branch'] ?? null) ? $entry['branch'] : '',
            'patchSet' => $patchSet,
            'commit' => is_string($entry['current_revision'] ?? null) ? $entry['current_revision'] : '',
            'project' => $project,
            'updated' => is_string($entry['updated'] ?? null) ? $entry['updated'] : '',
            // The five fields the review server sends unasked and this server
            // dropped until `D-ANS-107`. Size, whether it still merges and how
            // many threads are open are what a reviewer picks a change by, and
            // `created` is the one date `updated` beside it does not carry: an
            // old change touched last week is being worked on.
            'created' => is_string($entry['created'] ?? null) ? $entry['created'] : '',
            'insertions' => self::counted($entry['insertions'] ?? null),
            'deletions' => self::counted($entry['deletions'] ?? null),
            // Null where the server named nothing, which is what a merge it has
            // not computed yet looks like. False is "it no longer applies".
            'mergeable' => is_bool($entry['mergeable'] ?? null) ? $entry['mergeable'] : null,
            'url' => self::url($number, $project),
            // Null where the server named no patch set: a ref names one, and
            // there is nothing to fetch by a zero.
            'fetch' => $patchSet > 0 && $number > 0 ? [
                'ref' => self::ref($number, $patchSet),
                'remote' => self::HOST . '/' . $project,
            ] : null,
            // Null rather than empty where neither the votes nor the submit
            // rule came back, which is what a server too old to send either
            // looks like. What each of the two fills is in `labels()`.
            'labels' => self::labels($entry['labels'] ?? null, $entry['submit_records'] ?? null),
            // In the payload of every query, which is what lets the comments be
            // fetched only where there are some.
            'commentCount' => isset($entry['total_comment_count']) && is_numeric($entry['total_comment_count'])
                ? (int) $entry['total_comment_count']
                : 0,
            'unresolvedCommentCount' => self::counted($entry['unresolved_comment_count'] ?? null) ?? 0,
            // Filled by `change()`, each from an endpoint of its own.
            'comments' => null,
            'chain' => null,
            // Filled by `change()` out of the commit message, so null is a
            // message that did not come back rather than a patch naming no
            // issue — which is the empty list.
            'issues' => null,
            // The same, for the branches the `Releases:` trailer claims. Filled
            // wherever the message came back, which is a change read by name
            // and an issue search alike.
            'releases' => null,
            'messages' => is_array($entry['messages'] ?? null) ? self::messages($entry['messages']) : null,
            // Counted by `change()`, before the filter it is the measure of.
            'botMessageCount' => null,
            // Filled by `change()` out of that log, which is the only place
            // Gerrit reports a patch set carrying conflict markers. Null is a
            // log that did not come back; the empty list is a patch set nothing
            // was reported on — `D-ANS-121`.
            'conflicts' => null,
            // Read here instead, because the payload carries the two fields
            // whatever the query asked for.
            'cherryPickOf' => self::cherryPickOf($entry, $project),
        ];
    }

    /**
     * Where a person reads one change.
     *
     * The path names the project where the payload carries one, because that is
     * the form Gerrit redirects to. Where it carries none the number stands
     * alone and the review server resolves it: a path naming no project renders
     * a page about nothing, and asserting one this side does not know is the
     * same answer worn as a fact — `D-ANS-103`.
     */
    private static function url(int $number, string $project): string
    {
        if ($number < 1) {
            return self::HOST;
        }

        return $project === ''
            ? self::HOST . '/c/' . $number
            : self::HOST . '/c/' . $project . '/+/' . $number;
    }

    /** One number the review server stated, or null where it stated none. */
    private static function counted(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * The order the statuses of one label are read in when the rules disagree.
     *
     * The worst of them is what the label stands at, because that is what a
     * caller acts on: `REJECT` is a vote blocking the change and `NEED` is a
     * vote nobody has cast, and reading the second where the first holds is a
     * candidate picked out of a backlog that cannot be submitted at all.
     * `IMPOSSIBLE` sits between them — no vote available can satisfy the rule.
     */
    private const SEVERITY = ['REJECT', 'IMPOSSIBLE', 'NEED', 'OK'];

    /**
     * What each label stands at, who put it there, and whether that is enough.
     *
     * Two payloads fill this and they arrive apart. `submit_records` is on every
     * row the review server sends, asked for or not, and it carries the state of
     * each label the submit rule names; `o=DETAILED_LABELS` is what adds the
     * voters, and it costs 0.9 KB a row, so a search does not ask for it
     * (`D-ANS-107`). So a search answers the states with `votes` null, and a
     * change read by name answers both.
     *
     * `all` is every voter with the value they hold, the zeros included: a
     * reviewer who was added and has not voted is a fact about the review.
     * Whether a label is satisfied is the submit rule's judgement rather than
     * one made here — Verified runs -1 to +2 on review.typo3.org, so no
     * threshold read off the values would be right, and a rule that requires
     * the label at all is what makes the question mean anything.
     *
     * @return list<array<string, mixed>>|null null where the row carried neither
     */
    private static function labels(mixed $labels, mixed $records): ?array
    {
        $states = [];
        foreach (is_array($records) ? $records : [] as $record) {
            $required = is_array($record) && is_array($record['labels'] ?? null) ? $record['labels'] : [];
            foreach ($required as $entry) {
                $name = is_array($entry) && is_string($entry['label'] ?? null) ? $entry['label'] : '';
                $status = is_array($entry) && is_string($entry['status'] ?? null) ? $entry['status'] : '';
                // MAY is the label a rule permits and does not ask for, which
                // is not an unmet condition and is not a met one either.
                if ($name === '' || $status === 'MAY') {
                    continue;
                }
                $held = $states[$name] ?? null;
                $states[$name] = $held === null || self::worse($status, $held) ? $status : $held;
            }
        }
        // Every label the voters name and every label a rule names, because
        // either payload can carry one the other does not.
        $named = [...array_keys(is_array($labels) ? $labels : []), ...array_keys($states)];

        $answer = [];
        foreach (array_unique($named) as $name) {
            $name = (string) $name;
            $label = is_array($labels) && is_array($labels[$name] ?? null) ? $labels[$name] : null;
            $answer[] = [
                'label' => $name,
                // Empty where no submit rule names this label, which is not the
                // same as a rule it fails.
                'state' => $states[$name] ?? '',
                'satisfied' => isset($states[$name]) ? $states[$name] === 'OK' : null,
                'votes' => $label === null ? null : self::votes($label),
            ];
        }
        if ($answer === []) {
            return is_array($labels) ? [] : null;
        }

        return $answer;
    }

    /** Whether one label status is the more consequential of two. */
    private static function worse(string $status, string $than): bool
    {
        $at = array_search($status, self::SEVERITY, true);
        $held = array_search($than, self::SEVERITY, true);

        return $at !== false && ($held === false || $at < $held);
    }

    /**
     * Everyone on one label with the value they hold.
     *
     * @param array<string, mixed> $label
     * @return list<array<string, mixed>>
     */
    private static function votes(array $label): array
    {
        $votes = [];
        foreach (is_array($label['all'] ?? null) ? $label['all'] : [] as $vote) {
            if (!is_array($vote)) {
                continue;
            }
            $votes[] = [
                'voter' => is_string($vote['name'] ?? null) ? $vote['name'] : '',
                'value' => isset($vote['value']) && is_numeric($vote['value']) ? (int) $vote['value'] : 0,
                // Empty on a reviewer who holds no vote, since nothing was
                // cast to carry a date.
                'on' => is_string($vote['date'] ?? null) ? $vote['date'] : '',
            ];
        }

        return $votes;
    }

    /**
     * The review log, oldest first, each message said to be a machine's or not.
     *
     * @param array<mixed> $messages
     * @return list<array<string, mixed>>
     */
    private static function messages(array $messages): array
    {
        $log = [];
        foreach ($messages as $message) {
            if (!is_array($message)) {
                continue;
            }
            $author = is_array($message['author'] ?? null) ? $message['author'] : [];
            $tags = is_array($author['tags'] ?? null) ? $author['tags'] : [];
            $log[] = [
                'author' => is_string($author['name'] ?? null) ? $author['name'] : '',
                'on' => is_string($message['date'] ?? null) ? $message['date'] : '',
                'patchSet' => isset($message['_revision_number']) && is_numeric($message['_revision_number'])
                    ? (int) $message['_revision_number']
                    : 0,
                'bot' => in_array(self::SERVICE_USER, $tags, true),
                'message' => is_string($message['message'] ?? null) ? trim($message['message']) : '',
            ];
        }

        return $log;
    }

    /**
     * The paths the conflict report on this patch set names.
     *
     * A change created with the web **Cherry pick** action can land with the
     * markers committed into a shipped file, and every other field of the answer
     * reads as a healthy new patch set: the change payload carries no such
     * field, no revision carries one either, and only the message says it
     * (`D-ANS-121`).
     *
     * Held against the patch set the message was written about, which is the
     * whole of the field: a report on a patch set somebody has replaced is
     * history, and 7 of the 8 open core changes carrying one are that.
     *
     * @param array<mixed> $log the review log, as `messages()` answers it
     * @return list<string>
     */
    private static function conflicts(array $log, int $patchSet): array
    {
        $named = [];
        foreach ($log as $message) {
            if (!is_array($message) || ($message['patchSet'] ?? null) !== $patchSet) {
                continue;
            }
            $said = is_string($message['message'] ?? null) ? $message['message'] : '';
            $at = strpos($said, self::CONFLICTED);
            if ($at === false) {
                continue;
            }
            foreach (explode("\n", substr($said, $at + strlen(self::CONFLICTED))) as $line) {
                $path = trim($line);
                if ($path === '') {
                    continue;
                }
                // The list runs to the first line that is not one of its items,
                // so whatever Gerrit writes under it stays out of the paths.
                if (!str_starts_with($path, '* ')) {
                    break;
                }
                $path = trim(substr($path, 2));
                if ($path !== '' && !in_array($path, $named, true)) {
                    $named[] = $path;
                }
            }
        }

        return $named;
    }

    /**
     * The change and the patch set this one was cherry-picked from.
     *
     * Two fields the payload carries unasked, which is why this is read here
     * rather than out of the log beside it. It is provenance and not a warning:
     * 133 of 400 recent merged core changes carry it and 17 of those ever
     * conflicted — `D-ANS-121`.
     *
     * @param array<string, mixed> $entry
     * @return array{change: int, patchSet: int, url: string}|null
     */
    private static function cherryPickOf(array $entry, string $project): ?array
    {
        $change = self::counted($entry['cherry_pick_of_change'] ?? null) ?? 0;
        if ($change < 1) {
            return null;
        }

        return [
            'change' => $change,
            'patchSet' => self::counted($entry['cherry_pick_of_patch_set'] ?? null) ?? 0,
            'url' => self::url($change, $project),
        ];
    }

    /**
     * The ref one patch set is fetchable by — the join between which patch set
     * is current and having it on disk.
     *
     * Gerrit files every patch set under the change number modulo 100, written
     * as two digits: `refs/changes/79/95179/1`, and `refs/changes/04/4/1` for a
     * change numbered under ten. That is Gerrit's own rule rather than this
     * instance's configuration — its access control reference states the format
     * under *Special references*, and `RefNames.shard()` is the padding.
     * Measured against review.typo3.org on 2026-08-09: `refs/changes/00/2000/2`
     * resolves and `refs/changes/0/2000/2` does not, and no change there is
     * numbered under ten at all.
     */
    private static function ref(int $number, int $patchSet): string
    {
        return sprintf('refs/changes/%02d/%d/%d', $number % 100, $number, $patchSet);
    }
}
