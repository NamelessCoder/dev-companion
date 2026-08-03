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
 */
final class Forge
{
    public const HOST = 'https://forge.typo3.org';

    /**
     * How many journal entries come back. The decision is usually in the last
     * few, and an issue with two hundred comments is one nobody reads whole.
     */
    private const NOTES = 15;

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

        $response = $this->fetch->read($url, ['Accept: application/json']);
        if ($response['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'issue' => null, 'cause' => null];
        }
        if ($response['body'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'issue' => null, 'cause' => 'source-not-answering'];
        }

        $decoded = self::decoded($response['body']);
        if ($decoded === null) {
            // 200 and a page rather than the API is the protection, not an
            // outage, and the way past it is a plainer agent rather than a
            // more browser-like one. One retry, because a second failure is
            // an answer about the host rather than about the request.
            $retry = $this->fetch->read($url, ['Accept: application/json'], Fetch::PLAIN_AGENT);
            $decoded = self::decoded($retry['body']);
        }
        if ($decoded === null) {
            return ['status' => 'unavailable', 'url' => $url, 'issue' => null, 'cause' => 'source-not-parseable'];
        }

        return [
            'status' => 'answered',
            'url' => $url,
            'issue' => self::issueOf($decoded, $number),
            'cause' => null,
        ];
    }

    /**
     * The issue an API answer carries, or null for everything that is not one.
     *
     * @return array<string, mixed>|null
     */
    private static function decoded(?string $body): ?array
    {
        $decoded = Fetch::decode($body);

        return is_array($decoded['issue'] ?? null) ? $decoded['issue'] : null;
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
