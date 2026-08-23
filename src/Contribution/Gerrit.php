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
 */
final class Gerrit
{
    public const HOST = 'https://review.typo3.org';

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

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport);
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
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}
     */
    public function changesForIssue(string $issue, int $limit = 10): array
    {
        $number = ltrim(trim($issue), '#');
        $answer = $this->search('message:' . $number, $limit, self::CURRENT_COMMIT);

        $named = [];
        foreach ($answer['changes'] as $change) {
            if (self::names($change, $number)) {
                // The message was read to decide this and is not part of the
                // answer: what a caller asked for is which changes exist, and
                // the commit is what it holds a checkout against.
                unset($change['message']);
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
                unset($change['message']);
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
     * Nothing is filtered here. A caller naming a change has named it, and the
     * answer is that change whatever its commit message says. What the handle
     * decided until `D-ANS-080` is whether the backport is in the answer at all,
     * so the second query is what makes the two handles answer the same set, and
     * it is asked whether there is a sibling or not because only the answer
     * says. The votes come with it and the comments are one further call, made
     * only where the change says it carries one (`D-ANS-079`). The review log is
     * what `$messages` asks for, on every change the answer carries.
     *
     * The relation chain comes with them, on every change the answer carries,
     * because a change read alone says a feature exists where the stack under
     * it says what the feature consists of (`D-ANS-094`).
     *
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}
     */
    public function change(string $change, int $limit = 1, string $messages = 'none'): array
    {
        $options = self::REVIEW . ($messages === 'none' ? '' : self::MESSAGES);
        $handle = trim($change);
        $answer = $this->search('change:' . $handle, $limit, $options);

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
            if (!is_array($found['messages'])) {
                continue;
            }
            $written = array_values(array_filter(
                $found['messages'],
                static fn(array $message): bool => $message['bot'] === false,
            ));
            // Counted whichever way it was asked, so a log full of pipeline
            // reports answering zero here is Gerrit no longer tagging its
            // service users rather than a change no bot has been near.
            $answer['changes'][$index]['botMessageCount'] = count($found['messages']) - count($written);
            if ($messages === 'people') {
                $answer['changes'][$index]['messages'] = $written;
            }
        }

        return $answer;
    }

    /**
     * The comments left on one change, oldest first.
     *
     * Its own endpoint, because no query option carries them. What comes back is
     * a file to a list of comments, and `/PATCHSET_LEVEL` is the file a comment
     * on the change itself is filed under. Nothing here decides whether one was
     * answered: `unresolved`, the reply it is under and the patch set it was
     * left on are handed over and the reviewer reads them, because either field
     * alone answers that question wrongly (`D-ANS-079`).
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
                    'message' => is_string($comment['message'] ?? null) ? trim($comment['message']) : '',
                ];
            }
        }
        // Chronological across the files, because a thread is read in the order
        // it was written and a reply sits under a comment on the same file.
        usort($comments, static fn(array $one, array $other): int => strcmp($one['on'], $other['on']));

        Recent::hold($url, $comments);

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
     * Asked for only where the answer has to be held against what the message
     * says, which is the issue search: a query naming a change is answered with
     * that change and has nothing to check. Verified against `review.typo3.org`
     * on 2026-08-05, over the same anonymous path as everything else.
     */
    private const CURRENT_COMMIT = '&o=CURRENT_COMMIT';

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
     * The review log, which is asked for rather than answered with. The same
     * change is 57.9 KB with it.
     *
     * What it carries that the labels do not is why a vote is gone. Gerrit
     * writes "Outdated Votes: * Code-Review+1 (copy condition: …)" into the
     * message of the upload that dropped it, and the label state afterwards
     * looks exactly like a change nobody has voted on (`D-ANS-079`).
     */
    private const MESSAGES = '&o=MESSAGES';

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
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}
     */
    private function search(string $query, int $limit, string $options = ''): array
    {
        $url = self::HOST . '/changes/?q=' . rawurlencode($query) . '&n=' . max(1, min(self::MOST, $limit))
            . self::CURRENT_REVISION . $options;
        /** @var array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}|null $held */
        $held = Recent::held($url, self::HELD_FOR);
        if ($held !== null) {
            return $held;
        }

        $body = $this->fetch->get($url, ['Accept: application/json']);
        if ($body === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'dropped' => 0, 'cause' => 'source-not-answering'];
        }

        $decoded = Fetch::decode($body);
        if ($decoded === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'dropped' => 0, 'cause' => 'source-not-parseable'];
        }

        $changes = [];
        foreach ($decoded as $entry) {
            if (is_array($entry)) {
                $change = self::change_($entry);
                // What was not asked for cannot be in the answer, and the
                // commit message is asked for by one query alone.
                if (!str_contains($options, self::CURRENT_COMMIT)) {
                    unset($change['message']);
                }
                $changes[] = $change;
            }
        }

        $answer = [
            'status' => $changes === [] ? 'empty' : 'answered',
            'query' => $query,
            'changes' => $changes,
            'dropped' => 0,
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
        $commit = is_array($revisions[$revision] ?? null) ? $revisions[$revision] : [];
        $commit = is_array($commit['commit'] ?? null) ? $commit['commit'] : [];
        $project = is_string($entry['project'] ?? null) ? $entry['project'] : '';

        return [
            'number' => $number,
            // What `o=CURRENT_COMMIT` adds, where it was asked for. Read to
            // decide whether the change is about the issue, and dropped before
            // the answer is handed over.
            'message' => is_string($commit['message'] ?? null) ? $commit['message'] : '',
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
            'url' => $number > 0 ? self::HOST . '/c/' . $project . '/+/' . $number : self::HOST,
            // Null where the server named no patch set: a ref names one, and
            // there is nothing to fetch by a zero.
            'fetch' => $patchSet > 0 && $number > 0 ? [
                'ref' => self::ref($number, $patchSet),
                'remote' => self::HOST . '/' . $project,
            ] : null,
            // Null rather than empty where the review was not read at all: a
            // search asks for none of it, and "nobody has voted" is a different
            // answer to a reviewer than "nobody asked".
            'labels' => is_array($entry['labels'] ?? null)
                ? self::labels($entry['labels'], $entry['submit_records'] ?? null)
                : null,
            // In the payload of every query, which is what lets the comments be
            // fetched only where there are some.
            'commentCount' => isset($entry['total_comment_count']) && is_numeric($entry['total_comment_count'])
                ? (int) $entry['total_comment_count']
                : 0,
            // Filled by `change()`, each from an endpoint of its own.
            'comments' => null,
            'chain' => null,
            'messages' => is_array($entry['messages'] ?? null) ? self::messages($entry['messages']) : null,
            // Counted by `change()`, before the filter it is the measure of.
            'botMessageCount' => null,
        ];
    }

    /**
     * What each label stands at, and whether that is enough.
     *
     * `all` is every voter with the value they hold, the zeros included: a
     * reviewer who was added and has not voted is a fact about the review.
     * Whether a label is satisfied is the submit rule's judgement rather than
     * one made here — Verified runs -1 to +2 on review.typo3.org, so no
     * threshold read off the values would be right, and a rule that requires
     * the label at all is what makes the question mean anything.
     *
     * @param array<string, mixed> $labels
     * @return list<array<string, mixed>>
     */
    private static function labels(array $labels, mixed $records): array
    {
        $satisfied = [];
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
                $satisfied[$name] = ($satisfied[$name] ?? true) && $status === 'OK';
            }
        }

        $answer = [];
        foreach ($labels as $name => $label) {
            $votes = [];
            foreach (is_array($label) && is_array($label['all'] ?? null) ? $label['all'] : [] as $vote) {
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
            $answer[] = [
                'label' => (string) $name,
                'satisfied' => $satisfied[(string) $name] ?? null,
                'votes' => $votes,
            ];
        }

        return $answer;
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
