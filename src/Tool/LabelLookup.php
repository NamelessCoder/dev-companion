<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Labels;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Search\LabelSearch;

/**
 * Labels registered in the installation, answered by the installation.
 *
 * The console searches the packages it has active, which is what makes the
 * answer right: a project extension's labels are in it, and so are the resource
 * overrides the installation applies. Neither follows from a core checkout, and
 * neither could be shipped as a snapshot.
 */
final class LabelLookup extends ReadOnlyTool
{
    /**
     * What `language:domain:search` prints instead of a payload when nothing
     * matched — the one exit-0-without-JSON that is an answer.
     *
     * Read in `.checkouts/14.3` and `.checkouts/main`, where
     * `TranslationDomainSearchCommand` warns with this text and returns
     * SUCCESS; the command does not exist before 14.0, and there the console
     * exits non-zero. Matching the text rather than the exit code fails in the
     * safe direction: if the wording moves, an empty result reads as nothing
     * established, which costs a fallback rather than an answer that is wrong.
     */
    private const NOTHING_MATCHED = 'No language resource files found';

    /**
     * The source-language rule, carried here because this is where the caller
     * still is when it decides — `R-ANS-015`.
     *
     * Both sessions that wrote German into a source XLF had called this tool.
     * Neither called `typo3_task_guide`, whose `labels` intent holds the same
     * rule, and neither asked for a hint: every other route opens on the word
     * label, which a session describing its work as a content element has no
     * reason to type.
     */
    private const SOURCE_LANGUAGE = "\n\nWrite a new trans-unit in English in the unprefixed source file, and put any "
        . 'other wording in the locale-prefixed file beside it — de.locallang.xlf for locallang.xlf — under the same '
        . 'unit id. A source file that is not English is a defect to correct in place rather than a convention to '
        . 'continue, and adding an en.-prefixed file is not that correction: typo3_hint_lookup with '
        . 'id=language-files has what it is.';

    public static function name(): string
    {
        return 'typo3_label_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'Search the labels registered in the TYPO3 installation you are working in. Reuse is local to the translation resource already used at the consuming code: pass resource whenever it is known, and do not reference a match from another module or package merely because its text is identical. The console answers with the resource overrides the installation applies; where it cannot be reached — an installed TYPO3 whose database has no schema yet is the common case — the same packages\' XLF files are read instead.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'minLength' => 1, 'description' => 'Words from the label text or its trans-unit id, for example "save document" or "labels.title". Several words are matched independently, ignoring case and order: a label has to carry every one of them, in its text or in its id. When none carries all of them, the answer says how far each word reaches on its own.'],
                'extension' => ['type' => 'string', 'description' => 'Restrict the search to the extension that owns the consuming code.'],
                'resource' => ['type' => 'string', 'description' => 'Restrict the search to the exact XLF resource already used at the consuming code, for example "EXT:my_sitepackage/Resources/Private/Language/Backend/Import.xlf". A match from another resource is not a reuse candidate.'],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 200, 'default' => 25, 'description' => 'Maximum number of labels to return.'],
            ],
            'required' => ['query'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'terms' => Schema::listOf(Schema::object([
                'term' => Schema::string('One word of the query; a label has to carry every one of them.'),
                'matchCount' => Schema::integer('How many labels this word alone reaches — where to narrow when the query as a whole reaches none.'),
            ], ['term', 'matchCount'])),
            'labels' => Schema::listOf(Schema::object([
                'ref' => Schema::string('Translation domain reference (package.resource:key) — the canonical form.'),
                'domain' => Schema::string(),
                'key' => Schema::string('The trans-unit id.'),
                'source' => Schema::string('The label text in the searched locale.'),
                'resource' => Schema::string('The XLF file it lives in.'),
            ], ['ref', 'domain', 'key', 'source'])),
        ], ['query', 'resource', 'matchCount', 'answeredBy', 'terms', 'labels'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = trim((string) ($args['query'] ?? ''));
        $extension = trim((string) ($args['extension'] ?? ''));
        $resource = trim((string) ($args['resource'] ?? ''));
        $limit = (int) ($args['limit'] ?? 25);
        $terms = LabelSearch::terms($query);

        if ($extension === '' && preg_match('#^EXT:([^/]+)/#', $resource, $matches) === 1) {
            $extension = $matches[1];
        }
        $arguments = ['language:domain:search', LabelSearch::consoleOption($terms), '--json', '--crop=0'];
        if ($extension !== '') {
            $arguments[] = '--extension=' . $extension;
        }

        $answer = Typo3Cli::json($arguments);

        // The console prints a warning instead of a payload when nothing
        // matched, and exits successfully while doing it. That is an
        // installation that answered "none", not one that could not be asked —
        // and the difference decides whether the caller refines the query or
        // goes looking for a console that is not broken.
        //
        // The exit code cannot draw that line on its own, and reading it alone
        // put every other exit-0-without-payload on the "none" side. Nobody has
        // established what else lands there — the two obvious carriers do not:
        // Xdebug's connection warning goes to stderr, and a PHP deprecation is
        // swallowed by TYPO3's own error handler before it can print. What is
        // measured is the tool's side of it: four different stdouts, one of
        // them carrying an intact payload the decoder missed, all answered "no
        // label carries these words". An exit code of 0 certifies nothing about
        // the stream, so the warning is what says "none", and everything else
        // without a payload is nothing established.
        $establishedNone = $answer['exitCode'] === 0
            && str_contains($answer['output'], self::NOTHING_MATCHED);

        $answeredBy = 'installation';
        $candidates = [];
        if (!is_array($answer['data']) && !$establishedNone) {
            // The labels are in the packages' files whether or not the console
            // boots, and it needs a migrated database to boot. A weaker answer
            // beats none, as long as it says which one it is.
            $candidates = Labels::all($extension);
            if ($candidates === []) {
                return Unsupported::because(
                    self::whyNothingWasEstablished($answer),
                    ['query' => $query],
                );
            }
            $answeredBy = 'packages';
        }

        /** @var array<string, mixed> $data */
        $data = is_array($answer['data']) ? $answer['data'] : [];
        foreach ($data['items'] ?? [] as $item) {
            foreach ($item['labels'] ?? [] as $label) {
                $candidates[] = [
                    'ref' => (string) $label['domain'] . ':' . (string) $label['reference'],
                    'domain' => (string) $label['domain'],
                    'key' => (string) $label['reference'],
                    'source' => (string) $label['label'],
                    'resource' => (string) ($item['resource'] ?? ''),
                ];
            }
        }

        if ($resource !== '') {
            $candidates = array_values(array_filter(
                $candidates,
                static fn(array $label): bool => $label['resource'] === $resource,
            ));
        }

        // The console returned everything carrying any of the words; the query
        // asked for the labels carrying all of them.
        $labels = LabelSearch::carryingEvery($candidates, $terms);
        $termCounts = LabelSearch::perTermCounts($candidates, $terms);

        $total = count($labels);
        $shown = array_slice($labels, 0, $limit);
        $instance = Instance::describe();

        $fromFiles = $answeredBy === 'packages' ? sprintf(
            "\n\nRead from the XLF files of the installed packages: %s (%s). "
            . 'What that leaves out is the assembled runtime state — a label an installation replaces through '
            . 'LANG/resourceOverrides is shown here as its package ships it.',
            $answer['exitCode'] !== 0 ? 'the console could not be asked' : 'the console settled nothing',
            self::whyNothingWasEstablished($answer),
        ) : '';
        $reuseBoundary = $resource === ''
            ? "\n\nA match is reusable only when its resource is the one already used at the consuming code. "
                . 'A label from another module or package is not a shared vocabulary merely because its text matches; '
                . 'call again with resource once that usage context is known.'
            : "\n\nSearch restricted to the translation resource used at the consuming code: " . $resource . '.';

        if ($shown === []) {
            $lines = [sprintf(
                'No label in %s %s. This is an answer about your installation rather than about TYPO3 in general.',
                $resource !== '' ? $resource : ($instance['root'] ?? 'the installation'),
                count($terms) > 1 ? 'carries all of ' . LabelSearch::quoted($terms) : sprintf('matches "%s"', $query)
            )];

            $reached = array_values(array_filter($termCounts, static fn(array $t): bool => $t['matchCount'] > 0));
            if (count($terms) > 1 && $reached !== []) {
                $lines[] = '';
                $lines[] = 'On its own, ' . implode(', ', array_map(
                    static fn(array $t): string => sprintf('"%s" matches %d label(s)', $t['term'], $t['matchCount']),
                    $reached
                )) . ' — ask again with the one that narrows best.';
            }

            return ToolResult::create(implode("\n", $lines) . $reuseBoundary . self::SOURCE_LANGUAGE . $fromFiles, [
                'query' => $query,
                'resource' => $resource === '' ? null : $resource,
                'matchCount' => 0,
                'labels' => [],
                'terms' => $termCounts,
                'answeredBy' => $answeredBy,
            ]);
        }

        $lines = [sprintf(
            '%d label(s) in %s match "%s"%s:',
            $total,
            $resource !== '' ? $resource : ($instance['root'] ?? '?'),
            $query,
            $total > count($shown) ? sprintf(' — showing the first %d', count($shown)) : ''
        )];
        foreach ($shown as $label) {
            $lines[] = '- ' . $label['ref'];
            $lines[] = '  "' . $label['source'] . '"';
            $lines[] = '  ' . $label['resource'];
        }
        $lines[] = '';
        $lines[] = 'Reference a label by the domain form shown first (package.resource:key) — in TCA, in '
            . 'LanguageService::sL(), and in f:translate as separate domain and key attributes.';

        return ToolResult::create(implode("\n", $lines) . $reuseBoundary . self::SOURCE_LANGUAGE . $fromFiles, [
            'query' => $query,
            'resource' => $resource === '' ? null : $resource,
            'matchCount' => $total,
            'labels' => $shown,
            'terms' => $termCounts,
            'answeredBy' => $answeredBy,
        ]);
    }

    /**
     * Why the console settled neither the labels nor their absence.
     *
     * Two failures arrive here and they send the caller to different places.
     * One is a console that could not be run, and its own message is the
     * reason. The other ran, exited 0 and printed something that is neither
     * the payload nor the warning it prints when nothing matched — there is no
     * message to pass on, and "could not be asked" would send the caller after
     * a console that is working.
     *
     * @param array{error: string, exitCode: int, output: string, ok: bool, data: mixed} $answer
     */
    private static function whyNothingWasEstablished(array $answer): string
    {
        return $answer['exitCode'] !== 0 ? $answer['error'] : 'the console exited successfully with neither a '
            . 'JSON payload nor the warning it prints when nothing matched, so nothing it printed says whether '
            . 'these labels exist';
    }
}
