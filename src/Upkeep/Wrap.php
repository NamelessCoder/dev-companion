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

    /**
     * What opens a list item: a bullet or a number, and text for it to carry.
     *
     * The number is closed by a full stop or a bracket, both of which markdown
     * reads as a marker, and runs to nine digits, which is where markdown stops
     * reading one. Captured are the indent, the marker and the gap after it,
     * because together they are what the item's first line opens with.
     */
    private const MARKER = '/^(\s*)([-*+]|\d{1,9}[.)])([ \t]+)(?=\S)/';

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
        $open = null;
        /** @var array<int, string> the marker of each list still open, by the indent it stands at */
        $lists = [];

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
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
                $fence = $match[1];
                $out[] = $line;
                $blank = false;

                continue;
            }

            $marker = self::marker($line);
            $item = $marker !== null && ($paragraph === [] || self::interrupts($marker, $lists));

            // An indented code block opens after a blank line and nowhere else.
            // Inside a list, four spaces is the continuation of the item above.
            if ($verbatim && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1) {
                $out[] = $line;

                continue;
            }
            $verbatim = false;

            if (self::isVerbatim($line) || ($blank && !$item && preg_match('/^(?: {4}|\t)/', $line) === 1)) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
                $out[] = $line;
                $verbatim = preg_match('/^(?: {4}|\t)/', $line) === 1;
                $blank = trim($line) === '';

                continue;
            }

            // A field is one line, or one line and what hangs under it. Joined
            // to the field below it, `**Serves:**` and `**Priority:**` become
            // one line that `Todo` reads as neither.
            $field = preg_match('/^\*\*[^*]+:\*\*/', trim($line)) === 1;
            $under = $paragraph !== []
                && preg_match('/^\*\*[^*]+:\*\*/', trim($paragraph[0])) === 1
                && self::indent($line) <= self::indent($paragraph[0]);

            if ($item || $field || $under) {
                $out = [...$out, ...self::flush($paragraph, $open !== null)];
                $paragraph = [];
            }

            // What the open paragraph is: the marker it began with, or nothing
            // where it began with prose. `flush()` is told rather than asked,
            // because the same line reads as a marker or not depending on what
            // stands above it.
            if ($paragraph === []) {
                $open = $item ? $marker : null;
            }
            // An item opens a list at its own indent and closes every list
            // deeper than it; a paragraph that is nobody's item closes the ones
            // at its indent too. What is left is the lists this line stands
            // inside, which is what the next marker is read against.
            if ($item || $paragraph === []) {
                $indent = self::indent($line);
                $lists = array_filter(
                    $lists,
                    static fn(int $at): bool => $at < $indent,
                    ARRAY_FILTER_USE_KEY,
                );
                if ($item) {
                    $lists[$indent] = $marker[1];
                }
            }
            $paragraph[] = $line;
            $blank = false;
        }

        return implode("\n", [...$out, ...self::flush($paragraph, $open !== null)]);
    }

    /**
     * What a line opens a list item with, where it opens one at all: its indent
     * and its marker.
     *
     * @return array{string, string}|null
     */
    private static function marker(string $line): ?array
    {
        if (preg_match(self::MARKER, $line, $match) !== 1) {
            return null;
        }

        return [$match[1], $match[2]];
    }

    /**
     * Whether an item may open where a paragraph is already running.
     *
     * Markdown lets a bullet and a `1.` interrupt a paragraph and no other
     * number, which would otherwise open an item wherever a wrapped line
     * happens to begin with a figure — `D-KNW-049` has one starting `5432.`.
     * The exception is a list already running: its next item stands at the same
     * indent and closes with the same delimiter, which is what `2.` under `1.`
     * is and a stray figure is not.
     *
     * The list it is read against is any of the ones still open, not only the
     * paragraph directly above. A step that follows a nested bullet is the
     * outer list's next item, and read against the bullet alone it is a figure
     * at the head of a line: `1830ee9` joined three steps of
     * `typo3-core-patch-development` into the sub-bullet above them that way,
     * and nothing reported it because no word moved.
     *
     * @param array{string, string}  $marker
     * @param array<int, string>     $lists
     */
    private static function interrupts(array $marker, array $lists): bool
    {
        if (!ctype_digit($marker[1][0]) || in_array($marker[1], ['1.', '1)'], true)) {
            return true;
        }

        $open = $lists[mb_strlen($marker[0])] ?? null;

        return $open !== null
            && ctype_digit($open[0])
            && substr($open, -1) === substr($marker[1], -1);
    }

    /** How far a line is indented. */
    private static function indent(string $line): int
    {
        preg_match('/^(\s*)/', $line, $match);

        return mb_strlen($match[1]);
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
            // One code span or one link and nothing else is an entry rather
            // than a sentence — what a contract case lists under **Held by:**,
            // one test per line. Packed two to a line they stop being a list.
            || preg_match('/^(?:`[^`]+`|\[[^\]]+\]\([^)]+\))[.,;:]?$/', $trimmed) === 1
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
     * Whether it is a list item is what `document()` decided rather than what
     * the first line looks like from here, because the same line reads as one
     * or not depending on what stood above it.
     *
     * @param list<string> $paragraph
     *
     * @return list<string>
     */
    private static function flush(array $paragraph, bool $item): array
    {
        if ($paragraph === []) {
            return [];
        }

        // A field on one line stays on one line, however wide. `**Serves:**`
        // and the path after it are one field, and split over two lines the
        // label is all `Todo` finds — a long path is a long line, and that is
        // the cheaper of the two.
        if (!isset($paragraph[1]) && preg_match('/^\*\*[^*]+:\*\*/', trim($paragraph[0])) === 1) {
            return [$paragraph[0]];
        }

        preg_match('/^(\s*)/', $paragraph[0], $match);
        $first = $match[1];
        if ($item) {
            preg_match(self::MARKER, $paragraph[0], $match);
            // The marker is carried by the opening prefix, so the text it
            // belongs to starts after it.
            $first = $match[1] . $match[2] . $match[3];
            $paragraph[0] = mb_substr($paragraph[0], mb_strlen($first));
        }
        $continuation = str_repeat(' ', mb_strlen($first));

        // A hanging indent is the block's own, not an accident of wrapping: it
        // is what makes `**Waiting on:**` in a todo one field over several
        // lines rather than a field and a paragraph. Where the second line sits
        // further in than the first, that is where the rest of them go.
        if (isset($paragraph[1]) && self::indent($paragraph[1]) > mb_strlen($continuation)) {
            $continuation = str_repeat(' ', self::indent($paragraph[1]));
        }

        $text = implode(' ', array_map(
            static fn(string $line): string => trim($line),
            $paragraph,
        ));

        return self::lines($text, $first, $continuation);
    }

    /**
     * One run of text, wrapped — for whatever writes markdown without having a
     * document to hand, which is every generator here.
     *
     * It is the same wrapping the corpus is held to, and that is the point of
     * it being reachable: a generator with a column of its own writes a file
     * `prose:format` then disagrees with, and the two rewrite each other.
     */
    public static function text(string $text, string $continuation = ''): string
    {
        return implode("\n", self::lines($text, '', $continuation));
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
