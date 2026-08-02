<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Contribution;

use Typo3CmsMcp\Http\Fetch;

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
     * What every JSON response opens with, to stop a browser executing it as a
     * script. It is not JSON and has to come off before the body parses.
     */
    private const XSSI_PREFIX = ")]}'";

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
     * @return array{status: 'answered'|'empty'|'unavailable', query: string, changes: list<array<string, mixed>>, cause: ?string}
     */
    private function search(string $query, int $limit): array
    {
        $url = self::HOST . '/changes/?q=' . rawurlencode($query) . '&n=' . max(1, min(25, $limit));
        $body = $this->fetch->get($url, ['Accept: application/json']);
        if ($body === null) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'cause' => 'source-not-answering'];
        }

        $decoded = json_decode(self::withoutXssiPrefix($body), true);
        if (!is_array($decoded)) {
            return ['status' => 'unavailable', 'query' => $query, 'changes' => [], 'cause' => 'source-not-parseable'];
        }

        $changes = [];
        foreach ($decoded as $entry) {
            if (is_array($entry)) {
                $changes[] = self::change_($entry);
            }
        }

        return [
            'status' => $changes === [] ? 'empty' : 'answered',
            'query' => $query,
            'changes' => $changes,
            'cause' => null,
        ];
    }

    /**
     * One change, in the fields a caller asked this question for: whether it
     * exists, what it is called, which branch it targets, and whether it is
     * still open.
     *
     * @param array<string, mixed> $entry
     * @return array<string, mixed>
     */
    private static function change_(array $entry): array
    {
        $number = isset($entry['_number']) && is_numeric($entry['_number']) ? (int) $entry['_number'] : 0;

        return [
            'number' => $number,
            'subject' => is_string($entry['subject'] ?? null) ? $entry['subject'] : '',
            'status' => is_string($entry['status'] ?? null) ? $entry['status'] : '',
            'branch' => is_string($entry['branch'] ?? null) ? $entry['branch'] : '',
            'project' => is_string($entry['project'] ?? null) ? $entry['project'] : '',
            'updated' => is_string($entry['updated'] ?? null) ? $entry['updated'] : '',
            'url' => $number > 0 ? self::HOST . '/c/' . ($entry['project'] ?? '') . '/+/' . $number : self::HOST,
        ];
    }

    /**
     * The prefix is a fixed string rather than a pattern: anything else at the
     * head of the body is a proxy or a login page, and treating it as noise to
     * skip would parse whatever came after it as a review.
     */
    private static function withoutXssiPrefix(string $body): string
    {
        $body = ltrim($body);
        if (!str_starts_with($body, self::XSSI_PREFIX)) {
            return $body;
        }

        return ltrim(substr($body, strlen(self::XSSI_PREFIX)));
    }

}
