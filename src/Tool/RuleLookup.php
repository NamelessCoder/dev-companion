<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Knowledge\Documents;
use Typo3CmsMcp\Knowledge\Hints;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\Versions;
use Typo3CmsMcp\Result\Miss;
use Typo3CmsMcp\Result\Prose;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;
use Typo3CmsMcp\Search\TermSearch;

/**
 * The local TYPO3 core contribution rules and script notes, by topic.
 */
final class RuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_rule_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Search the TYPO3 rules and procedures this server carries, by topic. The core contribution process is most of it: the commit message conventions, which branches take a patch today, the changelog entry each change type owes, the Gerrit push and amend workflow with both refspecs, and the notes beside runTests.sh. It answers outside a core checkout too — setting up an extension manual, PHPUnit in an extension, Playwright in a project — and there the core-only documents are withheld and named rather than dropped in silence. What comes back is the sections that matched, each naming the document it was cut from, which is readable whole as its typo3://guides resource.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Topic to look up, in English, for example testing, review, deprecation, or code style.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the answer has to hold on, for example "13.4" or "14". A section bound to another major is left out. Defaults to every major this repository declares typo3/cms-core for, or to the installation this server was started in; where there is neither, every section comes back with the range it holds for.'],
            ],
            'required' => ['query'],
        ];
    }

    /**
     * The knowledge lookup shape plus the boundary, because most of this corpus
     * is the core repository's own.
     */
    public static function outputSchema(): array
    {
        $schema = Schema::knowledgeLookup();
        $schema['properties']['scope'] = Schema::scope();
        $schema['properties']['withheldDocuments'] = Schema::listOf(Schema::object([
            'id' => Schema::string(),
            'title' => Schema::string(),
        ], ['id', 'title']), 'Documents that matched and were left out because they answer for the core repository '
            . 'alone. Empty inside the core. Each is still readable in full as its typo3://guides resource, which is '
            . 'the way to get one deliberately rather than by accident.');
        $schema['required'][] = 'scope';

        return $schema;
    }

    public static function answer(array $args): ToolResult
    {
        $query = (string) ($args['query'] ?? '');
        $targets = Versions::targets(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);

        // This tool is asked about a topic rather than about paths, so the call
        // has one scope — the same reading typo3_script_lookup makes.
        $scope = Scope::of('', $query);
        $outsideCore = $scope->isOutsideTheCore();

        $found = Documents::search($query, [], 6, $targets);
        // Withheld per document rather than per call: this corpus is the
        // contribution process and the commit conventions at once, and only the
        // first half stops at the core repository. Dropping the tool whole —
        // which is what the project profile does — takes the second half with
        // it, and a caller writing a commit message in their own project needs
        // exactly that.
        $results = $outsideCore
            ? array_values(array_filter($found, static fn(array $r): bool => !Documents::isCoreOnly($r['id'])))
            : $found;
        $withheld = self::withheldDocuments($found, $results);

        // The prose and the hints are two corpora, and which one
        // holds a subject is this server's business, not the caller's: site
        // sets are a hint, the Gerrit workflow is prose, and the question is
        // phrased the same way either way. The hints transfer, so they are
        // returned on both sides of the boundary.
        $hints = Hints::find([], $query, 3)['matchedHints'];

        // The boundary is only the reason for a miss where it withheld
        // something. Blaming it wherever a hint had matched printed "No section
        // that holds outside the core matched" inside the core, on a call that
        // withheld nothing, and a judging run read that back as an answer —
        // `D-ANS-037`. Every other empty result is a miss and gets the miss
        // answer.
        if ($results === [] && $withheld === []) {
            return self::noMatch($query, $scope, $outsideCore, $hints, $targets);
        }

        $lines = [];
        if ($withheld !== []) {
            $lines[] = Scope::OUTSIDE_CORE_NOTICE;
            $lines[] = '';
            $lines[] = 'Withheld for that reason: ' . implode(', ', array_map(
                static fn(array $document): string => $document['title'],
                $withheld,
            )) . '. Each is readable in full as typo3://guides/<id> where the work really is a core patch.';
            $lines[] = '';
            // What is withheld here has a counterpart outside the core, and it
            // is a tool rather than a second copy of the page: the commit
            // conventions of a repository of your own are what
            // typo3_commit_message_guide composes for workflow="project", which
            // is the one measure this repository writes by as well — D-DOC-013.
            $lines[] = 'For a repository of your own, the same subjects are answered by '
                . 'typo3_commit_message_guide with workflow="project" and by typo3_hint_lookup.';
            $lines[] = '';
        }
        $lines[] = $results === []
            ? sprintf('No section that holds outside the core matched "%s".', $query)
            : Prose::sections($results);
        if ($hints !== []) {
            $lines[] = "\n" . self::alsoInHints($hints);
        }

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($results),
            'matches' => Prose::records($results),
            'scope' => $scope->value,
            'withheldDocuments' => $withheld,
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
        ]);
    }

    /**
     * What the knowledge base does cover, for a query that reached none of it.
     *
     * It carries the same fields a hit does, boundary included: a client that
     * validates the answer must not have to branch on which of the two it got,
     * and a miss is where it is most likely to look.
     *
     * The words that emptied it come first, because that is what the caller can
     * act on in the same call: the topic list says what is here, and the subset
     * says which part of this query reaches it (`R-ANS-006`). The subsets are
     * counted over the documents this call may answer from, so one offered
     * outside the core is not answered with the withholding notice.
     *
     * @param array<int, array<string, mixed>> $hints
     * @param array<int, int> $targets
     */
    private static function noMatch(string $query, Scope $scope, bool $outsideCore, array $hints, array $targets): ToolResult
    {
        $visible = array_values(array_filter(
            array_column(Documents::documents(), 'id'),
            static fn(string $id): bool => !$outsideCore || !Documents::isCoreOnly($id),
        ));

        $blocks = [sprintf('No knowledge section matched "%s".', $query)];
        $subsets = Documents::largestReachingSubsets($query, $visible, $targets);
        if ($subsets !== []) {
            $blocks[] = Miss::largestReaching($subsets, count(TermSearch::terms($query)), 'section', 'sections');
        }
        if ($hints !== []) {
            $blocks[] = self::alsoInHints($hints);
        }
        $blocks[] = "This knowledge base covers:\n" . implode("\n", array_map(
            static fn(array $document): string => '- ' . $document['title'] . ': ' . implode(', ', $document['topics']),
            Documents::topics()
        ));
        $blocks[] = 'For backend UI components use typo3_component_lookup, and call typo3_server_scope for what '
            . 'this server covers at all. '
            . 'If the topic should be covered here, leave a feedback with typo3_feedback_record.';

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'matchCount' => 0,
            'matches' => [],
            'scope' => $scope->value,
            'withheldDocuments' => [],
            'alsoInHints' => array_map(
                static fn(array $hint): array => ['id' => $hint['id'], 'title' => $hint['title']],
                $hints,
            ),
            'documents' => Documents::topics(),
        ]);
    }

    /**
     * The hints that answer the same question, as the call that returns them.
     *
     * @param array<int, array<string, mixed>> $hints
     */
    private static function alsoInHints(array $hints): string
    {
        return "The hints also cover this — call typo3_hint_lookup with the id:\n"
            . implode("\n", array_map(
                static fn(array $hint): string => '- ' . $hint['id'] . ' — ' . $hint['title'],
                $hints,
            ));
    }

    /**
     * The documents a match was dropped from, once each.
     *
     * Named rather than silently missing: an answer that is thinner than the
     * corpus and does not say so reads as "nobody wrote this down", which is
     * the one thing it does not mean.
     *
     * @param array<int, array<string, mixed>> $found
     * @param array<int, array<string, mixed>> $kept
     * @return array<int, array{id: string, title: string}>
     */
    private static function withheldDocuments(array $found, array $kept): array
    {
        $keptIds = array_column($kept, 'id');

        $withheld = [];
        foreach ($found as $result) {
            if (!in_array($result['id'], $keptIds, true)) {
                $withheld[$result['id']] = ['id' => $result['id'], 'title' => $result['title']];
            }
        }

        return array_values($withheld);
    }
}
