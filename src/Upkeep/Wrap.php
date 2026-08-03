<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

/**
 * Markdown rewrapped to the column this repository already writes at.
 *
 * A rename sweeps a word out of a hundred paragraphs and leaves every one of
 * them ragged, and the reflow that follows was a throwaway script each time.
 * The script is the part worth keeping: what it does is the same every time,
 * and what it must not touch — a fence, a table, a code span — is the same
 * every time too.
 *
 * Only the wrapping moves. Every word, in order, comes out the same, which is
 * what `ProseTest` asserts rather than trusts: a formatter that can drop a word
 * is worse than no formatter, because the loss reads as an edit somebody made.
 */
final class Wrap
{
    /**
     * Where a line ends.
     *
     * Read off the corpus rather than chosen. Wrapping every paragraph greedily
     * and counting the ones that came out unchanged puts the minimum at 80, by
     * a margin over every column on either side of it — which is the column
     * whoever wrapped these files by hand was aiming at.
     */
    public const COLUMN = 80;

    /** What stands in for a space that may not be broken at. */
    private const KEPT = "\x00";

    /** Spans a line break would break: a code span, and a markdown link. */
    private const UNBREAKABLE = '/`[^`\n]*`|\[[^\]\n]*\]\([^)\n]*\)/';

    /**
     * A markdown document, rewrapped.
     *
     * What is left alone is everything a line break means something in: the
     * front matter, a fenced or indented code block, a table row, a heading, a
     * quote, a link definition. The rest is a paragraph, and a list item starts
     * one of its own so the marker stays on the line the item begins with.
     */
    public static function document(string $markdown): string
    {
        $lines = explode("\n", $markdown);
        $out = [];
        $paragraph = [];

        $fence = null;
        $verbatim = false;
        $blank = true;

        // The front matter is data in a markdown file's clothing: its lines are
        // neither prose nor a code block, and joining two of them would lose a
        // key rather than reflow a sentence.
        $matter = $lines[0] === '---';

        foreach ($lines as $index => $line) {
            if ($matter) {
                $out[] = $line;
                $matter = $index === 0 || $line !== '---';

                continue;
            }

            if ($fence !== null) {
                $out[] = $line;
                if (preg_match('/^\s{0,3}' . preg_quote($fence, '/') . '/', $line) === 1) {
                    $fence = null;
                }

                continue;
            }

            if (preg_match('/^\s{0,3}(```+|~~~+)/', $line, $match) === 1) {
                $out = [...$out, ...self::flush($paragraph)];
                $paragraph = [];
                $fence = $match[1];
                $out[] = $line;
                $blank = false;

                continue;
            }

            $item = preg_match('/^(\s*)(?:[-*+]|\d+\.)\s/', $line) === 1;

            // An indented code block opens after a blank line and nowhere else.
            // Inside a list, four spaces is the continuation of the item above.
            if ($verbatim && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1) {
                $out[] = $line;

                continue;
            }
            $verbatim = false;

            if (self::isVerbatim($line) || ($blank && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1)) {
                $out = [...$out, ...self::flush($paragraph)];
                $paragraph = [];
                $out[] = $line;
                $verbatim = preg_match('/^(?: {4}|\t)/', $line) === 1;
                $blank = trim($line) === '';

                continue;
            }

            if ($item) {
                $out = [...$out, ...self::flush($paragraph)];
                $paragraph = [];
            }

            $paragraph[] = $line;
            $blank = false;
        }

        return implode("\n", [...$out, ...self::flush($paragraph)]);
    }

    /**
     * A line no wrapping may join to another.
     *
     * The blank line ends a paragraph, and the rest carry their meaning in
     * standing alone: a heading, a table row, a quote, the `---` of a front
     * matter or a rule, the link definitions a generated listing ends with, and
     * a line of HTML.
     */
    private static function isVerbatim(string $line): bool
    {
        $trimmed = trim($line);

        return $trimmed === ''
            || str_starts_with($trimmed, '#')
            || str_starts_with($trimmed, '|')
            || str_starts_with($trimmed, '>')
            || str_starts_with($trimmed, '<')
            || preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trimmed) === 1
            || preg_match('/^\[[^\]]+\]:\s/', $trimmed) === 1;
    }

    /**
     * One paragraph, wrapped: the first line keeps what it opens with, and
     * every line after it lines up under the first word.
     *
     * @param list<string> $paragraph
     *
     * @return list<string>
     */
    private static function flush(array $paragraph): array
    {
        if ($paragraph === []) {
            return [];
        }

        preg_match('/^(\s*)((?:[-*+]|\d+\.)\s+)?/', $paragraph[0], $match);
        $first = $match[1] . ($match[2] ?? '');
        $continuation = str_repeat(' ', mb_strlen($first));

        $text = implode(' ', array_map(
            static fn(string $line): string => trim($line),
            $paragraph,
        ));
        // The marker is carried by the opening prefix, so the text it belongs
        // to starts after it.
        $text = (string) preg_replace('/^(?:[-*+]|\d+\.)\s+/', '', $text);

        return self::lines($text, $first, $continuation);
    }

    /**
     * Greedy wrapping, with the spans that may not be broken kept whole.
     *
     * A word longer than what is left of the column goes on the next line; one
     * longer than the column itself goes on a line of its own rather than being
     * cut, because a broken URL is worse than a long line.
     *
     * @return list<string>
     */
    private static function lines(string $text, string $first, string $continuation): array
    {
        $masked = (string) preg_replace_callback(
            self::UNBREAKABLE,
            static fn(array $match): string => str_replace(' ', self::KEPT, $match[0]),
            $text,
        );

        $lines = [];
        $current = $first;
        $empty = true;
        foreach (preg_split('/\s+/', trim($masked)) ?: [] as $word) {
            if ($word === '') {
                continue;
            }
            if (!$empty && mb_strlen($current) + 1 + mb_strlen($word) > self::COLUMN) {
                $lines[] = $current;
                $current = $continuation;
                $empty = true;
            }
            $current .= $empty ? $word : ' ' . $word;
            $empty = false;
        }
        if (!$empty) {
            $lines[] = $current;
        }

        return array_map(static fn(string $line): string => str_replace(self::KEPT, ' ', $line), $lines);
    }
}
