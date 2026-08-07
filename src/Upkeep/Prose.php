<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Coverage;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The prose this repository writes about itself, measured.
 *
 * AGENTS.md asks for one point per sentence and says that length is a symptom.
 * Every other rule it states is held by something — the names by a test, the
 * shapes by a check — and that one was held by whoever happened to reread the
 * paragraph. What it costs when nothing measures is a lead sentence of 96
 * words, which is where this started.
 *
 * A sentence over the measure is not an error, and this reports rather than
 * judges: a long sentence can be the right one, and a rewrite driven by a
 * counter produces two short sentences saying what one said. The one place it
 * is held is the bold opening of a requirement or a decision, because that
 * sentence has a job nothing else does — a reader who stops after it knows what
 * was settled — and a reader cannot stop after 96 words.
 */
final class Prose
{
    /**
     * Where a sentence stops being one point.
     *
     * Not a style ceiling. It is the length at which the sentences in this
     * repository reliably turned out to be two — measured over the 47 leads
     * that ran past it, every one of which came apart into a rule and the
     * enumeration behind it.
     */
    public const MEASURE = 30;

    /**
     * The files this repository writes about itself.
     *
     * `feedback/` is deliberately absent. A feedback is a session's report
     * written somewhere else, and measuring somebody else's prose against this
     * repository's rule would be a report about the wrong thing.
     *
     * @return list<string>
     */
    public static function documents(): array
    {
        $files = array_filter(
            ['AGENTS.md', 'readme.md'],
            static fn(string $file): bool => is_file(Paths::root() . '/' . $file),
        );

        $directories = array_values(array_filter(
            array_map(
                static fn(string $directory): string => Paths::root() . '/' . $directory,
                ['decisions', 'requirements', 'todo', 'documentation', 'scenarios', 'skills', 'knowledge/documents'],
            ),
            is_dir(...),
        ));
        if ($directories !== []) {
            foreach (Finder::create()->files()->in($directories)->name('*.md') as $file) {
                $files[] = substr($file->getPathname(), strlen(Paths::root()) + 1);
            }
        }

        sort($files);

        return $files;
    }

    /**
     * What one file measures: how many sentences, and the ones over the measure.
     *
     * @return array{file: string, sentences: int, over: list<array{words: int, text: string}>}
     */
    public static function measure(string $file): array
    {
        $over = [];
        $sentences = self::sentences((string) file_get_contents(Paths::root() . '/' . $file));
        foreach ($sentences as $sentence) {
            $words = count(explode(' ', $sentence));
            if ($words > self::MEASURE) {
                $over[] = ['words' => $words, 'text' => $sentence];
            }
        }

        usort($over, static fn(array $a, array $b): int => $b['words'] <=> $a['words']);

        return ['file' => $file, 'sentences' => count($sentences), 'over' => $over];
    }

    /**
     * The bold opening of every requirement and decision, split into sentences.
     *
     * Two sentences there are legitimate — a decision that removes something
     * and says what replaced it is two points and reads as two. What is held is
     * each of them, not their sum.
     *
     * @return list<array{id: string, words: int, text: string}>
     */
    public static function leadsOverTheMeasure(): array
    {
        $over = [];
        $entries = [...array_values(Requirements::all()), ...array_values(Decisions::all())];
        foreach ($entries as $entry) {
            foreach (self::sentences($entry['statement']) as $sentence) {
                $words = count(explode(' ', $sentence));
                if ($words > self::MEASURE) {
                    $over[] = ['id' => $entry['id'], 'words' => $words, 'text' => $sentence];
                }
            }
        }

        return $over;
    }

    /**
     * The prose a client is handed before it has asked anything.
     *
     * `documents()` is what this repository writes about itself and reaches no
     * file in `src/`, so the half a caller actually pays for was counted by
     * nobody: on 2026-08-03 the connect payload measured 118202 characters, of
     * which 14502 were the tool descriptions and 11507 the input schema fields,
     * and the 1219 taken off that day were found by somebody rereading rather
     * than by a report (`R-COD-002`).
     *
     * Every string here is written by this repository and read by a machine
     * that cannot skim, which is the argument for holding it to the same
     * measure — `D-DOC-002`. What it is not is a budget: `R-ANS-013` is what
     * holds the instructions to a size, and this counts sentences.
     *
     * What it counts is the prose alone, so its weight is not the 118202 above
     * and the two may not be compared. That figure was the whole connect
     * payload — the schemas with their keys, types and enums, which nobody
     * writes sentences in. This is the part somebody wrote and can shorten.
     *
     * @return list<array{where: string, text: string}>
     */
    public static function payload(): array
    {
        $prose = [['where' => 'instructions', 'text' => Coverage::instructions()]];
        foreach (Registry::definitions() as $definition) {
            $name = (string) $definition['name'];
            $prose[] = ['where' => $name . ' description', 'text' => (string) $definition['description']];
            foreach ([['inputSchema', 'input'], ['outputSchema', 'output']] as [$key, $side]) {
                foreach (self::descriptions(is_array($definition[$key] ?? null) ? $definition[$key] : []) as $field => $text) {
                    $prose[] = ['where' => $name . ' ' . $side . ' ' . $field, 'text' => $text];
                }
            }
        }

        return $prose;
    }

    /**
     * Every `description` in a JSON Schema, by the path that carries it.
     *
     * A schema nests — properties inside items inside properties — and a field
     * three levels down is read by the same client as the one at the top, so
     * the walk is the whole tree rather than one level of it.
     *
     * @param array<string, mixed> $schema
     * @return array<string, string>
     */
    private static function descriptions(array $schema, string $path = ''): array
    {
        $found = [];
        foreach ($schema as $key => $value) {
            if ($key === 'description' && is_string($value)) {
                $found[$path === '' ? '(self)' : $path] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            // `properties` and `items` are the schema's own structure and not
            // part of what a field is called, so they do not go into the path.
            $step = in_array($key, ['properties', 'items', 'oneOf', 'anyOf', 'allOf'], true)
                ? $path
                : trim($path . '.' . $key, '.');
            $found = [...$found, ...self::descriptions($value, $step)];
        }

        return $found;
    }

    /**
     * The payload's sentences that run past the measure, worst first.
     *
     * @return list<array{where: string, words: int, text: string}>
     */
    public static function payloadOverTheMeasure(): array
    {
        $over = [];
        foreach (self::payload() as $entry) {
            foreach (self::sentences($entry['text']) as $sentence) {
                $words = count(explode(' ', $sentence));
                if ($words > self::MEASURE) {
                    $over[] = ['where' => $entry['where'], 'words' => $words, 'text' => $sentence];
                }
            }
        }

        usort($over, static fn(array $a, array $b): int => $b['words'] <=> $a['words']);

        return $over;
    }

    /** What a client is handed at connect, in characters. */
    public static function payloadWeight(): int
    {
        return array_sum(array_map(
            static fn(array $entry): int => strlen($entry['text']),
            self::payload(),
        ));
    }

    /**
     * The sentences of a markdown file, as a reader meets them.
     *
     * A list item is a sentence of its own even where it has no full stop, and
     * a paragraph's line breaks are the wrapping rather than the writing, so
     * they are joined before anything is split. What is skipped is everything
     * that is not prose: front matter, code, tables, headings, quoted material
     * and the link definitions at the foot of a generated listing.
     *
     * @return list<string>
     */
    private static function sentences(string $markdown): array
    {
        $markdown = (string) preg_replace('/^---\R.*?\R---\R/s', '', $markdown);
        $markdown = (string) preg_replace('/^ {0,3}```.*?^ {0,3}```/ms', '', $markdown);

        $sentences = [];
        foreach (preg_split('/\R\s*\R/', $markdown) ?: [] as $paragraph) {
            // Four spaces is a code block, and a paragraph is either all of one
            // or none of it.
            if (preg_match('/^ {4}/', $paragraph) === 1) {
                continue;
            }
            foreach (preg_split('/\R(?=\s*(?:[-*]\s|\d+\.\s))/', $paragraph) ?: [] as $block) {
                $line = trim((string) preg_replace('/\s+/', ' ', $block));
                $line = trim((string) preg_replace('/^[-*]\s|^\d+\.\s/', '', $line));
                // A heading, a table row, quoted material, and the link
                // definitions a generated listing ends with.
                if ($line === '' || in_array($line[0], ['#', '|', '>'], true) || preg_match('/^\[[^\]]+\]:\s/', $line) === 1) {
                    continue;
                }
                foreach (preg_split('/(?<=[.!?])\s+/', $line) ?: [] as $sentence) {
                    $sentence = trim($sentence);
                    // Four words is a heading in disguise, a label line, or the
                    // remains of one that was split on an abbreviation.
                    if (count(explode(' ', $sentence)) > 4) {
                        $sentences[] = $sentence;
                    }
                }
            }
        }

        return $sentences;
    }
}
