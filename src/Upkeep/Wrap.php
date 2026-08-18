<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

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
     * The same in reStructuredText: a literal, a role, an embedded link.
     *
     * The literal comes first because it is written with two backticks and the
     * role with one, and read the other way round every literal is seen as an
     * empty role followed by loose words.
     */
    private const RST_UNBREAKABLE = '/``[^`]*``|:[a-z:]+:`[^`\n]*`|`[^`\n]*`__?/';

    /**
     * The rule a simple table is drawn with, above its head and at its foot.
     *
     * Two runs at least, because one run of `=` under a line of text is how
     * a heading is underlined. Read as a table opening, that swallowed
     * every line to the next heading and the file came out unwrapped rather
     * than wrong — which is the failure that does not announce itself.
     */
    private const RST_RULE = '/^\s*={2,}(\s+={2,})+\s*$/';

    /**
     * What underlines a heading. Any of them, not only the four this repository
     * writes, because a file it did not write is still not to be broken.
     */
    private const RST_UNDERLINE = '/^([=\-~"\'#*+^`_:.]){2,}\s*$/';

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
     * A reStructuredText document, rewrapped.
     *
     * The markup is different enough to be read separately rather than as
     * markdown with exceptions. Almost everything structural here is carried by
     * a line break or by an indent — a heading is a line and the rule under it,
     * a directive owns whatever is indented below it, a table is drawn — so
     * what may be rewrapped is a smaller set than in markdown rather than a
     * larger one, and this errs towards leaving a line alone.
     */
    public static function rst(string $rst): string
    {
        $lines = explode("\n", $rst);
        $out = [];
        $paragraph = [];
        $open = null;
        $lists = [];

        // What a block owns: everything indented past it, until something
        // stands at or left of the column it opened at.
        $owned = null;
        $table = -1;

        foreach ($lines as $index => $line) {
            $trimmed = trim($line);
            $indent = self::indent($line);

            if ($owned !== null) {
                if ($trimmed === '' || $indent > $owned) {
                    $out[] = $line;

                    continue;
                }
                $owned = null;
            }

            // A drawn table is verbatim, rules and rows alike: every column
            // boundary in it is a character position, so one rewrapped row
            // moves the text out of the column it belongs to and the parser
            // reports content standing in a gap.
            //
            // Where it ends is found rather than toggled. A simple table is
            // drawn with three rules and not two — above the head, under it,
            // and at the foot — so a state flipped by each one is inside the
            // table exactly when it is not.
            if ($index > $table && preg_match(self::RST_RULE, $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $table = self::tableEnds($lines, $index);
                for ($row = $index; $row <= $table; $row++) {
                    $out[] = $lines[$row];
                }

                continue;
            }
            if ($index <= $table) {
                continue;
            }

            // A directive, a comment, a label: the line itself and everything
            // indented under it.
            if (preg_match('/^\s*\.\.(\s|$)/', $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;
                $owned = $indent;

                continue;
            }

            // A heading is the text and the rule under it, and rewrapping the
            // text would leave the rule the wrong length.
            if (isset($lines[$index + 1]) && $trimmed !== ''
                && preg_match(self::RST_UNDERLINE, $lines[$index + 1]) === 1
                && mb_strlen(trim($lines[$index + 1])) >= mb_strlen($trimmed)
            ) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;

                continue;
            }
            // A rule the branch above did not claim, which is a heading whose
            // underline is shorter than its text. It is still a heading and
            // still nobody's paragraph: written out without flushing first, it
            // would land above the line it underlines.
            if (preg_match(self::RST_UNDERLINE, $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;

                continue;
            }

            // A literal block is what an indented run after `::` is, and a
            // field list is one line each.
            if ($trimmed === '' || preg_match('/^\s*:[^:\s][^:]*:(\s|$)/', $line) === 1) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
                $out[] = $line;
                if ($trimmed !== '') {
                    $owned = $indent;
                }

                continue;
            }
            if ($paragraph === [] && str_ends_with(rtrim((string) end($out)), '::')) {
                $out[] = $line;
                $owned = $indent - 1;

                continue;
            }

            $marker = self::marker($line);
            $item = $marker !== null && ($paragraph === [] || self::interrupts($marker, $lists));
            if ($item) {
                $out = [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)];
                $paragraph = [];
            }
            if ($paragraph === []) {
                $open = $item ? $marker : null;
            }
            if ($item || $paragraph === []) {
                $lists = array_filter($lists, static fn(int $at): bool => $at < $indent, ARRAY_FILTER_USE_KEY);
                if ($item) {
                    $lists[$indent] = $marker[1];
                }
            }
            $paragraph[] = $line;
        }

        return implode("\n", [...$out, ...self::flush($paragraph, $open !== null, self::RST_UNBREAKABLE)]);
    }

    /**
     * Where the table opening at $from is drawn to.
     *
     * The foot is the rule with a blank line or the end of the file after
     * it, which is what tells it from the rule under the head — that one
     * always carries a row.
     *
     * @param list<string> $lines
     */
    private static function tableEnds(array $lines, int $from): int
    {
        for ($at = $from + 1; $at < count($lines); $at++) {
            if (preg_match(self::RST_RULE, $lines[$at]) !== 1) {
                continue;
            }
            if (!isset($lines[$at + 1]) || trim($lines[$at + 1]) === '') {
                return $at;
            }
        }

        // A table nothing closes is left as it stands rather than rewrapped.
        return count($lines) - 1;
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
     * number, which would otherwise open an item wherever a wrapped line happens
     * to begin with a figure — `D-KNW-049` has one starting `5432.`. The
     * exception is a list already running, and it is read against any of the
     * ones still open rather than the paragraph directly above: a step following
     * a nested bullet is the outer list's next item, and read against the bullet
     * alone it is a figure at the head of a line.
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
    private static function flush(array $paragraph, bool $item, string $unbreakable = self::UNBREAKABLE): array
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

        return self::lines($text, $first, $continuation, $unbreakable);
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

    /** A run of text whose first and following lines stand at the same indent. */
    public static function indented(string $text, string $indent): string
    {
        return implode("\n", self::lines($text, $indent, $indent));
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
    private static function lines(string $text, string $first, string $continuation, string $unbreakable = self::UNBREAKABLE): array
    {
        $masked = (string) preg_replace_callback(
            $unbreakable,
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
