<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Contribution;

use Typo3CmsMcp\Http\Fetch;

/**
 * The issue tracker a core patch starts from, read over its Redmine API.
 *
 * What it costs by hand is counted in `feedback/2026-08-02-144511` and `145217`:
 * a browser-shaped request is refused, a browser-shaped user agent gets HTTP 200
 * with a challenge page — a success wrapping a non-answer — and the JSON that
 * finally arrives has to be searched for the part that decides anything. Which
 * is not the description: it is the journal, where the maintainer who closed the
 * issue said why.
 *
 * So an answer is only an answer when it parses as the API. Anything else with a
 * 200 in front of it is the protection or a portal, and reading it as "no issue"
 * would be the same mistake in the other direction.
 *
 * The `.json` endpoint is asked for rather than the page, because the fields
 * this answers with — the tracker, the target version, the custom field the
 * reported version lives in, the relations, the journal — are fields there and
 * would be scraped anywhere else.
 *
 * Which agent gets through was measured on 2026-08-03: this server's own
 * `typo3-cms-mcp/<version>` and a plain `curl/…` both answer 200 with JSON, and
 * a `Mozilla/5.0 …` answers 200 with a 7.5 kB challenge page. So the first
 * attempt is already the shape that works, and the retry below is what happens
 * if that stops being true.
 *
 * Two ways in, because two questions are asked of one tracker and only one of
 * them starts from a number. Whether another issue describes the same bug is
 * asked before a patch is written, and the same two sessions searched for it by
 * hand: `/search.json` is that call (`D-ANS-038`).
 */
final class Forge
{
    public const HOST = 'https://forge.typo3.org';

    /**
     * How many journal entries come back. The decision is usually in the last
     * few, and an issue with two hundred comments is one nobody reads whole.
     */
    private const NOTES = 15;

    /**
     * The most hits a search answers with. The order is the tracker's own and
     * nothing here ranks, so a caller who reaches the end of one asks again in
     * other words rather than deeper — which is the answer to a set that looks
     * too narrow (`D-ANS-038`).
     */
    private const HITS = 25;

    private readonly Fetch $fetch;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport);
    }

    /**
     * One issue, with what was decided about it.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, issue: ?array<string, mixed>, cause: ?string}
     */
    public function issue(string $issue): array
    {
        $number = ltrim(trim($issue), '#');
        $url = self::HOST . '/issues/' . rawurlencode($number) . '.json?include=journals,relations';

        $answer = $this->api($url, 'issue');
        // A tracker that says 404 has answered: there is no such issue, which
        // is a different thing to tell a caller than that it could not be
        // reached.
        if ($answer['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'issue' => null, 'cause' => null];
        }
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'issue' => null, 'cause' => $answer['cause']];
        }

        return [
            'status' => 'answered',
            'url' => $url,
            'issue' => self::issueOf($answer['part'], $number),
            'cause' => null,
        ];
    }

    /**
     * The issues whose text matches these words.
     *
     * What it answers is which other issues mention this, and not which one is
     * the duplicate: the order is the tracker's own, nothing here ranks, and one
     * wording does not settle the question — three phrasings of the same bug
     * returned 15, 134 and 279 hits on 2026-08-03. So the query comes back with
     * the answer, because a caller holding a narrow set has to be able to see
     * which words produced it (`D-ANS-038`).
     *
     * `issues=1` is what keeps wiki pages, forum posts and changesets out of an
     * answer whose entries are issue numbers.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, query: string, results: list<array<string, mixed>>, cause: ?string}
     */
    public function search(string $query, int $limit = 15): array
    {
        $words = trim($query);
        $url = self::HOST . '/search.json?q=' . rawurlencode($words)
            . '&issues=1&limit=' . max(1, min(self::HITS, $limit));

        $answer = $this->api($url, 'results');
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'query' => $words, 'results' => [], 'cause' => $answer['cause']];
        }

        $results = [];
        foreach ($answer['part'] as $hit) {
            if (is_array($hit)) {
                $results[] = self::hit($hit);
            }
        }

        return [
            'status' => $results === [] ? 'empty' : 'answered',
            'url' => $url,
            'query' => $words,
            'results' => $results,
            'cause' => null,
        ];
    }

    /**
     * One read of the API, as the part of the answer that was asked for.
     *
     * The three states are the same whichever question was asked, and so is the
     * one retry: a body that did not parse is the protection rather than an
     * outage, and the way past it is a plainer agent rather than a more
     * browser-like one. One, because a second failure is an answer about the
     * host rather than about the request.
     *
     * @return array{status: int, part: ?array<mixed>, cause: ?string}
     */
    private function api(string $url, string $key): array
    {
        $response = $this->fetch->read($url, ['Accept: application/json']);
        if ($response['body'] === null) {
            return ['status' => $response['status'], 'part' => null, 'cause' => 'source-not-answering'];
        }

        $part = self::part($response['body'], $key);
        if ($part === null) {
            $part = self::part($this->fetch->read($url, ['Accept: application/json'], Fetch::PLAIN_AGENT)['body'], $key);
        }

        return [
            'status' => $response['status'],
            'part' => $part,
            'cause' => $part === null ? 'source-not-parseable' : null,
        ];
    }

    /**
     * The part of an API answer a call was asking for — the issue, the hits —
     * or null for everything that is not one.
     *
     * @return array<mixed>|null
     */
    private static function part(?string $body, string $key): ?array
    {
        $decoded = Fetch::decode($body);

        return is_array($decoded[$key] ?? null) ? $decoded[$key] : null;
    }

    /**
     * One hit, as the identity and the triage state a caller sorting a set of
     * them needs.
     *
     * `title` arrives as `Bug #105403 (Under Review): f:image and cache busting
     * issue`, so the tracker and the status are readable here rather than in a
     * second call per hit. A title in some other shape is not a broken hit —
     * the number and the URL are fields of their own — so what cannot be read
     * off it is left empty and the whole title stands as the subject.
     *
     * @param array<string, mixed> $entry
     * @return array<string, mixed>
     */
    private static function hit(array $entry): array
    {
        $title = is_string($entry['title'] ?? null) ? trim($entry['title']) : '';
        $issue = isset($entry['id']) && is_numeric($entry['id']) ? (int) $entry['id'] : 0;
        $tracker = '';
        $status = '';
        $subject = $title;
        if (preg_match('~^(.+?) #(\d+) \((.+?)\): (.*)$~s', $title, $matched) === 1) {
            $tracker = $matched[1];
            $issue = $issue > 0 ? $issue : (int) $matched[2];
            $status = $matched[3];
            $subject = $matched[4];
        }

        $url = is_string($entry['url'] ?? null) ? trim($entry['url']) : '';

        return [
            'issue' => $issue,
            'subject' => $subject,
            'tracker' => $tracker,
            'status' => $status,
            'url' => $url !== '' ? $url : self::HOST . '/issues/' . $issue,
        ];
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private static function issueOf(array $raw, string $number): array
    {
        $journals = is_array($raw['journals'] ?? null) ? $raw['journals'] : [];
        $notes = [];
        foreach ($journals as $journal) {
            if (!is_array($journal)) {
                continue;
            }
            $note = is_string($journal['notes'] ?? null) ? trim($journal['notes']) : '';
            if ($note === '') {
                // A journal with no note is a field change, which the fields
                // below already report in their current state.
                continue;
            }
            $notes[] = [
                'author' => self::name($journal['user'] ?? null),
                'on' => is_string($journal['created_on'] ?? null) ? $journal['created_on'] : '',
                'note' => $note,
            ];
        }

        // A relation names both sides, and which of the two is the other issue
        // depends on who filed it. Taking one field blindly reports an issue as
        // related to itself, which is what the first live call did.
        $own = (int) ($raw['id'] ?? $number);
        $relations = [];
        foreach (is_array($raw['relations'] ?? null) ? $raw['relations'] : [] as $relation) {
            if (!is_array($relation)) {
                continue;
            }
            $from = (int) ($relation['issue_id'] ?? 0);
            $to = (int) ($relation['issue_to_id'] ?? 0);
            $other = $from === $own ? $to : $from;
            if ($other === 0 || $other === $own) {
                continue;
            }
            $relations[] = [
                'issue' => $other,
                'relation' => is_string($relation['relation_type'] ?? null) ? $relation['relation_type'] : '',
            ];
        }

        return [
            'id' => (int) ($raw['id'] ?? $number),
            'subject' => is_string($raw['subject'] ?? null) ? $raw['subject'] : '',
            'status' => self::name($raw['status'] ?? null),
            'tracker' => self::name($raw['tracker'] ?? null),
            'priority' => self::name($raw['priority'] ?? null),
            'targetVersion' => self::name($raw['fixed_version'] ?? null),
            'typo3Version' => self::custom($raw, 'TYPO3 Version'),
            'phpVersion' => self::custom($raw, 'PHP Version'),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . (int) ($raw['id'] ?? $number),
            'description' => is_string($raw['description'] ?? null) ? trim($raw['description']) : '',
            'relations' => $relations,
            'noteCount' => count($notes),
            'notes' => array_slice($notes, -self::NOTES),
        ];
    }

    /** @param mixed $field */
    private static function name($field): string
    {
        return is_array($field) && is_string($field['name'] ?? null) ? $field['name'] : '';
    }

    /**
     * A custom field is a list rather than a map, so it is found by the name it
     * carries. The TYPO3 version an issue was reported against lives in one.
     *
     * @param array<string, mixed> $raw
     */
    private static function custom(array $raw, string $name): string
    {
        foreach (is_array($raw['custom_fields'] ?? null) ? $raw['custom_fields'] : [] as $field) {
            if (is_array($field) && ($field['name'] ?? null) === $name) {
                $value = $field['value'] ?? '';

                return is_string($value) ? $value : implode(', ', array_filter((array) $value, is_string(...)));
            }
        }

        return '';
    }
}
