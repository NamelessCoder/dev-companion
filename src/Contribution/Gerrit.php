<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Http\Recent;

/**
 * The review server the core's patches live on, read over its REST API.
 *
 * The one question every core task asks before it starts — is there a change
 * for this issue already — is not answerable from a checkout, and four sessions
 * in one week answered it by hand: `q=message:<issue>` against
 * `review.typo3.org`, then stripping the XSSI prefix the response opens with
 * before anything can parse it. That is the round trip this replaces
 * (`D-FBK-027`).
 *
 * Read-only, and no credential. Everything here is what the anonymous REST API
 * serves; voting, uploading and abandoning are the caller's to do through git
 * and the web UI, and this server never acts on somebody's review.
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
        $answer = $this->search('message:' . $number, $limit, true);

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
            foreach ($this->search($query, self::MOST, true)['changes'] as $change) {
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
     * One change by its number, the form a review URL carries.
     *
     * Nothing is filtered here. A caller naming a change has named it, and the
     * answer is that change whatever its commit message says.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}
     */
    public function change(string $change, int $limit = 1): array
    {
        return $this->search('change:' . trim($change), $limit);
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
     * The most changes one query answers with. A batched query asks for all of
     * them: twelve issues answered twelve changes when it was measured, so the
     * headroom is what an issue with several patches costs rather than a bound
     * anybody has met.
     */
    private const MOST = 25;

    /**
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, dropped: int, cause: ?string}
     */
    private function search(string $query, int $limit, bool $withMessage = false): array
    {
        $url = self::HOST . '/changes/?q=' . rawurlencode($query) . '&n=' . max(1, min(self::MOST, $limit))
            . self::CURRENT_REVISION . ($withMessage ? self::CURRENT_COMMIT : '');
        $held = Recent::held($url, self::HELD_FOR);
        if (is_array($held)) {
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
                if (!$withMessage) {
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
     * One change, in the fields a caller asked this question for: whether it
     * exists, what it is called, which branch it targets, whether it is still
     * open, and which patch set the answer is about.
     *
     * The last of those is the one a review turns on. A change is a series of
     * patch sets and the checkout in front of the reviewer is one commit, so
     * the number alone says nothing about whether the two are the same thing;
     * the commit is what settles it against a local `HEAD`.
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
