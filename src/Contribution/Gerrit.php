<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Contribution;

use Typo3CmsMcp\Http\Fetch;
use Typo3CmsMcp\Http\Recent;

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
     * `message:` searches the commit message, which is where `Resolves:` and
     * `Related:` put the issue number, so this answers "has somebody already
     * fixed this" rather than "is there a change called this".
     *
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, cause: ?string}
     */
    public function changesForIssue(string $issue, int $limit = 10): array
    {
        $number = ltrim(trim($issue), '#');

        return $this->search('message:' . $number, $limit);
    }

    /**
     * One change by its number, the form a review URL carries.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, cause: ?string}
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
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, cause: ?string}
     */
    private function search(string $query, int $limit): array
    {
        $url = self::HOST . '/changes/?q=' . rawurlencode($query) . '&n=' . max(1, min(25, $limit)) . self::CURRENT_REVISION;
        $held = Recent::held($url, self::HELD_FOR);
        if (is_array($held)) {
            return $held;
        }

        $body = $this->fetch->get($url, ['Accept: application/json']);
        if ($body === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'cause' => 'source-not-answering'];
        }

        $decoded = Fetch::decode($body);
        if ($decoded === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'cause' => 'source-not-parseable'];
        }

        $changes = [];
        foreach ($decoded as $entry) {
            if (is_array($entry)) {
                $changes[] = self::change_($entry);
            }
        }

        $answer = [
            'status' => $changes === [] ? 'empty' : 'answered',
            'query' => $query,
            'changes' => $changes,
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

        return [
            'number' => $number,
            'subject' => is_string($entry['subject'] ?? null) ? $entry['subject'] : '',
            'status' => is_string($entry['status'] ?? null) ? $entry['status'] : '',
            'branch' => is_string($entry['branch'] ?? null) ? $entry['branch'] : '',
            'patchSet' => $patchSet,
            'commit' => is_string($entry['current_revision'] ?? null) ? $entry['current_revision'] : '',
            'project' => is_string($entry['project'] ?? null) ? $entry['project'] : '',
            'updated' => is_string($entry['updated'] ?? null) ? $entry['updated'] : '',
            'url' => $number > 0 ? self::HOST . '/c/' . ($entry['project'] ?? '') . '/+/' . $number : self::HOST,
        ];
    }
}
