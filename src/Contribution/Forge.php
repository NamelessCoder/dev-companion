<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Search\Text;

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
 * `typo3-dev-companion/<version>` and a plain `curl/…` both answer 200 with JSON, and
 * a `Mozilla/5.0 …` answers 200 with a 7.5 kB challenge page. So the first
 * attempt is already the shape that works, and the retry below is what happens
 * if that stops being true.
 *
 * Three ways in, because three questions are asked of one tracker and only one
 * of them starts from a number. Whether another issue describes the same bug is
 * asked before a patch is written, and the same two sessions searched for it by
 * hand: `/search.json` is that call (`D-ANS-038`). Which issues are still open
 * after all these years is asked before there is an issue at all, and no number
 * and no wording reaches it: `/issues.json` filtered and sorted is that one.
 */
final class Forge
{
    public const HOST = 'https://forge.typo3.org';

    /** The core's own project on the tracker, which is the one this reads. */
    public const PROJECT = 'typo3cms-core';

    /**
     * Seconds an answered read is held for.
     *
     * Longer than the review server's, because nothing the caller does through
     * this server changes what the tracker says: an issue's status, its target
     * version and its comments move when somebody else works on them, at the
     * pace people work. What this is against is a session walking a list of
     * issues and asking the tracker the same thing a dozen times.
     */
    public const HELD_FOR = 300;

    /**
     * Seconds the project's own category list is held for.
     *
     * Two orders of magnitude longer than an issue's, because it answers how
     * the core files its issues rather than what happened to one of them. A
     * category is added when a subsystem is, which is a thing that happens
     * between releases, while a session filtering by area asks for the same
     * names on every call it makes.
     */
    public const CATEGORIES_HELD_FOR = 86400;

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

    /**
     * The most issues an enumeration answers with. Higher than a search's,
     * because a triage picks candidates out of the set it is shown and a set
     * that has to be paged through is one nobody sees the shape of.
     */
    private const LISTED = 50;

    /**
     * The tracker ids `/trackers.json` answered with on 2026-08-05.
     *
     * The API filters by id and nobody says "tracker 1", so the caller's word
     * is translated here. Held as data rather than fetched: a twelfth tracker
     * would cost every enumeration a second round trip to find out about, and
     * what it costs to be missing is one filter nobody can ask for.
     */
    private const TRACKERS = [
        'Bug' => 1,
        'Feature' => 2,
        'Major Feature' => 5,
        'Support' => 3,
        'Task' => 4,
        'Story' => 6,
        'Suggestion' => 7,
        'Impediment' => 9,
        'Epic' => 10,
        'Work Package' => 11,
        'Topic' => 12,
    ];

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
        $url = self::HOST . '/issues/' . rawurlencode($number) . '.json?include=journals,relations,attachments';

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

        $found = self::issueOf($answer['part'], $number);
        $found['relations'] = $this->related($found['relations']);

        return [
            'status' => 'answered',
            'url' => $url,
            'issue' => $found,
            'cause' => null,
        ];
    }

    /**
     * The relations, with what it takes to decide whether to read one.
     *
     * A relation came back as a number and a word, which costs one issue read
     * to evaluate — so a caller holding four of them evaluates none. On 15984
     * that was four, and the one that answered what a fix would cost was
     * `#32756`, "Massive Memory Leak in 4.5.8+ / 4.6": the issue the 2012
     * revert was filed under, found afterwards in a git commit message
     * (`D-ANS-064`).
     *
     * The price is one bulk read per issue answer rather than one per relation,
     * which is what makes this a fix and not a trade. Where it cannot be
     * reached the relations stand as they came back.
     *
     * @param list<array<string, mixed>> $relations
     * @return list<array<string, mixed>>
     */
    private function related(array $relations): array
    {
        $fields = $this->fields(array_column($relations, 'issue'));
        foreach ($relations as $at => $relation) {
            $read = $fields[$relation['issue']] ?? null;
            $relations[$at]['subject'] = $read['subject'] ?? '';
            $relations[$at]['tracker'] = $read['tracker'] ?? '';
            $relations[$at]['status'] = $read['status'] ?? '';
            $relations[$at]['url'] = self::HOST . '/issues/' . $relation['issue'];
        }

        return $relations;
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
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, query: string, total: int, results: list<array<string, mixed>>, cause: ?string}
     */
    public function search(string $query, int $limit = 15): array
    {
        $words = trim($query);
        $url = self::HOST . '/search.json?q=' . rawurlencode($words)
            . '&issues=1&limit=' . max(1, min(self::HITS, $limit));

        $answer = $this->api($url, 'results');
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'query' => $words, 'total' => 0, 'results' => [], 'cause' => $answer['cause']];
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
            'total' => $answer['total'],
            'results' => $this->filled($results),
            'cause' => null,
        ];
    }

    /**
     * The fields a search hit is not made of, read for the whole page at once.
     *
     * `/search.json` answers with a title and a URL, so the area, the assignee
     * and the two dates are absent from every hit — and a record carrying them
     * empty is a false statement rather than a missing one: it reads as an
     * issue nobody has categorised, nobody holds and nothing has moved on. A
     * session took 50 of 50 rows that way on 2026-08-05
     * (`feedback/2026-08-05-033902`), and the search path is where a triage
     * asks about age.
     *
     * Where it cannot be reached the hits stand as they came back. A search
     * that answered is not turned into an outage by a second call that did not.
     *
     * @param list<array<string, mixed>> $results
     * @return list<array<string, mixed>>
     */
    private function filled(array $results): array
    {
        $fields = $this->fields(array_column($results, 'issue'));
        if ($fields === []) {
            return $results;
        }

        foreach ($results as $at => $hit) {
            $read = $fields[$hit['issue']] ?? null;
            if ($read === null) {
                continue;
            }
            $results[$at]['category'] = $read['category'];
            $results[$at]['assignedTo'] = $read['assignedTo'];
            $results[$at]['createdOn'] = $read['createdOn'];
            $results[$at]['updatedOn'] = $read['updatedOn'];
            // The tracker and the status are read off the title, which is the
            // tracker's own wording and usually parses. Where it did not, they
            // are fields here.
            $results[$at]['tracker'] = $hit['tracker'] !== '' ? $hit['tracker'] : $read['tracker'];
            $results[$at]['status'] = $hit['status'] !== '' ? $hit['status'] : $read['status'];
        }

        return $results;
    }

    /**
     * The fields of a set of issues, read in one request.
     *
     * `/issues.json` filtered by an id list answers subject, tracker, status,
     * area, assignee and both dates for the whole set at once, which is what
     * makes filling a search page and an issue's relations cheaper than
     * explaining why they are bare. `status_id=*` is what keeps a closed one in
     * it — both callers ask about issues that are usually closed, and the
     * default of that endpoint is open ones.
     *
     * An empty answer is what could not be reached, and every caller here reads
     * it as "leave what came back alone".
     *
     * @param list<mixed> $numbers
     * @return array<int, array<string, mixed>>
     */
    private function fields(array $numbers): array
    {
        $wanted = [];
        foreach ($numbers as $number) {
            if (is_int($number) && $number > 0) {
                $wanted[$number] = $number;
            }
        }
        if ($wanted === []) {
            return [];
        }

        $url = self::HOST . '/issues.json?' . http_build_query([
            'issue_id' => implode(',', $wanted),
            'status_id' => '*',
            'limit' => count($wanted),
        ]);
        $answer = $this->api($url, 'issues');
        if ($answer['part'] === null) {
            return [];
        }

        $fields = [];
        foreach ($answer['part'] as $entry) {
            if (is_array($entry)) {
                $read = self::entry($entry);
                $fields[$read['issue']] = $read;
            }
        }

        return $fields;
    }

    /**
     * The open issues of the core project, oldest or stalest first.
     *
     * What this answers is the question a triage starts from and neither of the
     * two above reaches: an issue nobody has looked at since 2015 is found by
     * no number, because nobody holds it, and by no wording, because its
     * wording is the one nobody thought of. So the filter is the way in — the
     * tracker's own `status_id=open`, which is every status it has not marked
     * closed and therefore includes `Postponed`.
     *
     * `total` comes back with the page, because a caller shown 30 of something
     * has to be able to see whether 30 was the set or the first screenful of
     * it.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, total: int, categories: list<string>, categoriesUsed: list<string>, results: list<array<string, mixed>>, cause: ?string}
     */
    public function open(
        string $order = 'oldest',
        string $tracker = '',
        string $category = '',
        string $createdBefore = '',
        string $updatedBefore = '',
        int $limit = 15,
    ): array {
        $categories = $this->categories();
        $used = $category === '' ? [] : self::named($categories, $category);

        $filters = [
            'status_id' => 'open',
            'sort' => $order === 'stale' ? 'updated_on:asc' : 'created_on:asc',
            'limit' => (string) max(1, min(self::LISTED, $limit)),
        ];
        if (isset(self::TRACKERS[$tracker])) {
            $filters['tracker_id'] = (string) self::TRACKERS[$tracker];
        }
        if ($used !== []) {
            // The tracker's own alternation, which is what makes a word naming
            // three categories one call rather than three.
            $filters['category_id'] = implode('|', array_map(
                static fn(string $name): int => $categories[$name],
                $used,
            ));
        }
        // A date the tracker cannot read is answered with the unfiltered set,
        // which is the wrong answer wearing a right one's shape. Only a date
        // reaches the query.
        if (self::isDate($createdBefore)) {
            $filters['created_on'] = '<=' . $createdBefore;
        }
        if (self::isDate($updatedBefore)) {
            $filters['updated_on'] = '<=' . $updatedBefore;
        }

        $known = array_keys($categories);
        $url = self::HOST . '/projects/' . self::PROJECT . '/issues.json?' . http_build_query($filters);

        // A word matching no category would otherwise be answered with the
        // unfiltered backlog, which is a set about everything wearing the shape
        // of a set about one thing.
        if ($category !== '' && $used === []) {
            return ['status' => 'empty', 'url' => $url, 'total' => 0, 'categories' => $known, 'categoriesUsed' => [], 'results' => [], 'cause' => null];
        }

        $answer = $this->api($url, 'issues');
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'categories' => $known, 'categoriesUsed' => $used, 'results' => [], 'cause' => $answer['cause']];
        }

        $results = [];
        foreach ($answer['part'] as $entry) {
            if (is_array($entry)) {
                $results[] = self::entry($entry);
            }
        }

        return [
            'status' => $results === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => $answer['total'],
            'categories' => $known,
            'categoriesUsed' => $used,
            'results' => $results,
            'cause' => null,
        ];
    }

    /** A day the tracker can filter on, and nothing else. */
    private static function isDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }

    /**
     * The categories the core project files its issues under, name to id.
     *
     * Read from the project rather than written down here. A list in this file
     * is one the core can add to without anything reporting it, and the
     * addition is exactly the subsystem somebody would be filtering for — a
     * category nobody can name is a filter that answers nothing and looks like
     * an empty backlog. What keeps it from being a round trip per call is the
     * hold, not a copy.
     *
     * @return array<string, int>
     */
    public function categories(): array
    {
        $url = self::HOST . '/projects/' . self::PROJECT . '.json?include=issue_categories';
        $answer = $this->api($url, 'project', self::CATEGORIES_HELD_FOR);

        $categories = [];
        $listed = $answer['part']['issue_categories'] ?? null;
        foreach (is_array($listed) ? $listed : [] as $category) {
            if (is_array($category) && is_string($category['name'] ?? null) && is_numeric($category['id'] ?? null)) {
                $categories[$category['name']] = (int) $category['id'];
            }
        }

        return $categories;
    }

    /**
     * The categories a caller's words name, in the tracker's own spelling.
     *
     * Nobody types "RTE (rtehtmlarea + ckeditor)". They type "rte", and they
     * type "backend ui" for a name that carries neither word whole, so the
     * words are matched one at a time and at a word boundary — a substring
     * match answers "rte" with every category whose name contains "reporte" or
     * "Renderer".
     *
     * Carrying every word is preferred and carrying one is the fallback,
     * because the first is what an exact name does and the second is what a
     * half-remembered one does. Which categories that produced is answered back,
     * since "backend ui" reaching three of them is a set the caller may want to
     * narrow and cannot see from the issues alone.
     *
     * @param array<string, int> $categories
     * @return list<string>
     */
    public static function named(array $categories, string $words): array
    {
        $terms = preg_split('/\s+/', trim($words)) ?: [];
        $terms = array_values(array_filter($terms, static fn(string $term): bool => $term !== ''));
        if ($terms === []) {
            return [];
        }

        $all = [];
        $any = [];
        foreach (array_keys($categories) as $name) {
            $carried = 0;
            foreach ($terms as $term) {
                $carried += Text::containsWord($name, $term) ? 1 : 0;
            }
            if ($carried === count($terms)) {
                $all[] = $name;
            } elseif ($carried > 0) {
                $any[] = $name;
            }
        }

        return $all !== [] ? $all : $any;
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
     * @return array{status: int, part: ?array<mixed>, total: int, cause: ?string}
     */
    private function api(string $url, string $key, int $heldFor = self::HELD_FOR): array
    {
        $held = Recent::held($url, $heldFor);
        if (is_array($held)) {
            return $held;
        }

        $response = $this->fetch->read($url, ['Accept: application/json']);
        if ($response['body'] === null) {
            return ['status' => $response['status'], 'part' => null, 'total' => 0, 'cause' => 'source-not-answering'];
        }

        $part = self::part($response['body'], $key);
        if ($part === null) {
            $part = self::part($this->fetch->read($url, ['Accept: application/json'], Fetch::PLAIN_AGENT)['body'], $key);
        }

        $answer = [
            'status' => $response['status'],
            'part' => $part['part'] ?? null,
            'total' => $part['total'] ?? 0,
            'cause' => $part === null ? 'source-not-parseable' : null,
        ];
        // Only what the tracker actually answered. A 404 for an issue nobody
        // has filed yet and a body the protection replaced are both states of
        // this minute, and holding either turns one bad minute into five.
        if ($part !== null) {
            Recent::hold($url, $answer);
        }

        return $answer;
    }

    /**
     * The part of an API answer a call was asking for — the issue, the hits,
     * the page of issues — or null for everything that is not one.
     *
     * The tracker's own count of what matched comes with it, because a page is
     * not the set and only the envelope says which of the two the caller is
     * holding. An answer carrying no count is one issue read whole, where the
     * question does not arise.
     *
     * @return array{part: array<mixed>, total: int}|null
     */
    private static function part(?string $body, string $key): ?array
    {
        $decoded = Fetch::decode($body);
        if (!is_array($decoded[$key] ?? null)) {
            return null;
        }

        $total = $decoded['total_count'] ?? null;

        return ['part' => $decoded[$key], 'total' => is_numeric($total) ? (int) $total : 0];
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
            // A search hit is a title and a URL. The four below are fields of
            // the issue, and `filled()` is what reads them for the whole page —
            // what is left empty here is what that call could not reach.
            'category' => '',
            'assignedTo' => '',
            'createdOn' => '',
            'updatedOn' => '',
            'url' => $url !== '' ? $url : self::HOST . '/issues/' . $issue,
        ];
    }

    /**
     * One issue of an enumeration, in the same shape a search hit comes back
     * in, so a caller reads one record rather than two.
     *
     * Everything here is a field, where a search hit has to be read out of its
     * title — which is what makes the two dates answerable at all. They are the
     * two a triage sorts on and they say different things: filed long ago is
     * about the report, untouched for years is about the attention it got.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private static function entry(array $raw): array
    {
        $issue = isset($raw['id']) && is_numeric($raw['id']) ? (int) $raw['id'] : 0;

        return [
            'issue' => $issue,
            'subject' => is_string($raw['subject'] ?? null) ? trim($raw['subject']) : '',
            'tracker' => self::name($raw['tracker'] ?? null),
            'status' => self::name($raw['status'] ?? null),
            'category' => self::name($raw['category'] ?? null),
            'assignedTo' => self::name($raw['assigned_to'] ?? null),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . $issue,
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
            'assignedTo' => self::name($raw['assigned_to'] ?? null),
            'targetVersion' => self::name($raw['fixed_version'] ?? null),
            'typo3Version' => self::custom($raw, 'TYPO3 Version'),
            'phpVersion' => self::custom($raw, 'PHP Version'),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . (int) ($raw['id'] ?? $number),
            'description' => is_string($raw['description'] ?? null) ? trim($raw['description']) : '',
            'relations' => $relations,
            'attachments' => self::attachments($raw),
            // Read from every note rather than from the ones that come back, so
            // a patch-set ping older than the bound is still a handle.
            'reviews' => self::reviews($notes),
            'noteCount' => count($notes),
            'notes' => array_slice($notes, -self::NOTES),
        ];
    }

    /**
     * The review changes the journal names, as handles rather than as prose.
     *
     * They are in the payload already and only inside a sentence — "Patch set 3
     * of change I98ea… has been pushed to the review server. It is available at
     * http://review.typo3.org/1186" — where they read as history already told.
     * A session triaging 15984 never called `typo3_gerrit_lookup` and never
     * loaded its schema, and answered the question from `git log --all --grep`
     * instead (`D-ANS-064`).
     *
     * Nothing is claimed about the change's state. The journal says what was
     * true on the day somebody wrote the note, and what is true now is one
     * `typo3_gerrit_lookup` call away.
     *
     * Two passes, because the two handles arrive in different notes: the bot's
     * names the change id and the number together, a human's later note is a
     * bare URL. Which one a note carries is what the second pass resolves.
     *
     * @param list<array{author: string, on: string, note: string}> $notes
     * @return list<array<string, mixed>>
     */
    private static function reviews(array $notes): array
    {
        $numberOf = [];
        foreach ($notes as $note) {
            [$numbers, $changeId] = self::handles($note['note']);
            if ($changeId !== '' && count($numbers) === 1) {
                $numberOf[strtolower($changeId)] = $numbers[0];
            }
        }

        $reviews = [];
        foreach ($notes as $note) {
            [$numbers, $changeId] = self::handles($note['note']);
            if ($numbers === [] && $changeId !== '') {
                $numbers = [$numberOf[strtolower($changeId)] ?? 0];
            }
            foreach ($numbers as $number) {
                $key = $number > 0 ? (string) $number : strtolower($changeId);
                if ($key === '') {
                    continue;
                }
                $review = $reviews[$key] ?? [
                    'change' => $number,
                    'changeId' => '',
                    'patchSet' => 0,
                    'on' => '',
                    'url' => $number > 0 ? Gerrit::HOST . '/c/' . $number : Gerrit::HOST,
                ];
                // Only where the note names one change. A note naming two
                // carries a change id belonging to neither of them for certain.
                if ($changeId !== '' && count($numbers) === 1) {
                    $review['changeId'] = $changeId;
                }
                $review['patchSet'] = max($review['patchSet'], self::patchSet($note['note']));
                // The journal is in order, so the last note naming a change is
                // what says how old the reference is.
                $review['on'] = $note['on'];
                $reviews[$key] = $review;
            }
        }

        return array_values($reviews);
    }

    /**
     * The change numbers and the change id one note names.
     *
     * A review URL carries the number in two shapes — `review.typo3.org/1186`
     * from before the move to the current server, and
     * `review.typo3.org/c/Packages/TYPO3.CMS/+/38419` since. A URL with neither
     * is a query rather than a change: `review.typo3.org/#q,status:open+…`
     * names a topic and no number, and matching digits anywhere in the URL
     * would report it as change 3129.
     *
     * @return array{list<int>, string}
     */
    private static function handles(string $note): array
    {
        preg_match_all('~review\.typo3\.org/(?:c/[^\s]*?\+/)?(\d+)~', $note, $found);
        $numbers = array_values(array_unique(array_map(intval(...), $found[1])));

        preg_match('~\bI[0-9a-f]{40}\b~i', $note, $id);

        return [$numbers, $id[0] ?? ''];
    }

    /** Which patch set the note is about, and zero where it does not say. */
    private static function patchSet(string $note): int
    {
        return preg_match('~\bpatch set (\d+)~i', $note, $matched) === 1 ? (int) $matched[1] : 0;
    }

    /**
     * The files hanging off an issue, named rather than fetched.
     *
     * On a bug report about rendering, the evidence is regularly a screenshot,
     * and Redmine's inline syntax puts it into a comment as `!name.jpg!` — so
     * the text of that comment is a bare filename and reads as an empty
     * comment. On #88556 two of the seven attachments decided the triage: one
     * showed the editor's source view, the other the reporter's literal
     * database content, and a session that read only the text would have filed
     * one wrong verdict for two different defects
     * (`feedback/2026-08-05-033846`).
     *
     * What comes back is the list and not the bytes. The URLs answer without a
     * credential, an image is read by a caller that can read images, and this
     * server transcribes nothing.
     *
     * @param array<string, mixed> $raw
     * @return list<array<string, mixed>>
     */
    private static function attachments(array $raw): array
    {
        $attachments = [];
        foreach (is_array($raw['attachments'] ?? null) ? $raw['attachments'] : [] as $file) {
            if (!is_array($file) || !is_string($file['filename'] ?? null)) {
                continue;
            }
            $attachments[] = [
                'filename' => $file['filename'],
                'contentType' => is_string($file['content_type'] ?? null) ? $file['content_type'] : '',
                'size' => isset($file['filesize']) && is_numeric($file['filesize']) ? (int) $file['filesize'] : 0,
                'on' => is_string($file['created_on'] ?? null) ? $file['created_on'] : '',
                'url' => is_string($file['content_url'] ?? null) ? $file['content_url'] : '',
            ];
        }

        return $attachments;
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
