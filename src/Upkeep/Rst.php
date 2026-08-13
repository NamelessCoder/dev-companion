<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The reStructuredText this repository writes, said once.
 *
 * Two generators write pages — `ToolSurface` the derived half and `ToolAnswers`
 * the recorded one — and both open sections, fence code and mark up a name.
 * Written out twice they drift on the first convention that moves, and the
 * underline character is exactly the kind of thing that moves: nothing in the
 * markup fixes it, so a document that opens a section with `-` and another that
 * opens one with `~` are both valid and render as different levels.
 *
 * So the levels are declared here rather than agreed. `LEVELS` is the order,
 * outermost first, and it is the same order in every page this repository
 * writes — which is what lets a reader of two pages read one structure.
 */
final class Rst
{
    /**
     * The underline of each heading level, outermost first.
     *
     * The characters are the ones TYPO3's own manuals use, so a page from here
     * and a page from there are the same document to anybody editing both.
     */
    private const LEVELS = ['=', '-', '~', '"'];

    /** What a directive's content is indented by, and what `.editorconfig` says for `*.rst`. */
    public const INDENT = '    ';

    /**
     * A heading and the underline that makes it one.
     *
     * The underline is as long as the text rather than a fixed run: shorter is
     * a warning in every parser, and longer is untidy in a corpus that is read
     * as often in a text editor as in a browser.
     *
     * @return list<string>
     */
    public static function heading(string $text, int $level = 0): array
    {
        if (!isset(self::LEVELS[$level])) {
            throw new \InvalidArgumentException(sprintf(
                'reStructuredText is written here at %d heading levels, and level %d is not one of them.',
                count(self::LEVELS),
                $level,
            ));
        }

        return [$text, str_repeat(self::LEVELS[$level], mb_strlen($text)), ''];
    }

    /**
     * A code block, as the directive rather than as an indented literal.
     *
     * The directive is what carries the language, and the language is what the
     * theme colours the block with — a bare `::` block renders grey.
     *
     * @return list<string>
     */
    public static function code(string $language, string $code): array
    {
        $lines = array_map(
            static fn(string $line): string => $line === '' ? '' : self::INDENT . $line,
            explode("\n", rtrim($code, "\n")),
        );

        return ['.. code-block:: ' . $language, '', ...$lines, ''];
    }

    /**
     * A name, marked up as one.
     *
     * The double backtick is the whole reason this exists: a single one is a
     * role in reStructuredText and renders as emphasis or as nothing, so every
     * `typo3_hint_lookup` written the markdown way is silently not a literal.
     */
    public static function literal(string $text): string
    {
        return '``' . $text . '``';
    }

    /**
     * Another page of this corpus, by the path the generator knows it under.
     *
     * `:doc:` rather than a relative link, because the renderer resolves it
     * against the document tree and writes whatever the output extension turns
     * out to be. A link written to the file would have to be rewritten on the
     * way into the copy, which is a second place that knows what a page is
     * called.
     */
    public static function doc(string $title, string $target): string
    {
        return sprintf(':doc:`%s <%s>`', $title, self::target($target));
    }

    /**
     * A labelled place in this corpus, wherever it sits.
     *
     * This is what `:doc:` cannot do and what markdown could not do at all: a
     * reference to a heading in another page that the renderer resolves and
     * fails loudly on. `Site::page()` used to drop such a link whole rather
     * than land the reader on the wrong half of a page — `D-DOC-029`.
     */
    public static function ref(string $title, string $label): string
    {
        return sprintf(':ref:`%s <%s>`', $title, $label);
    }

    /**
     * What a menu shows the page as, where its heading is a sentence —
     * `D-DOC-031`. It stands above everything else, because the parser takes a
     * field list as metadata only before the title.
     *
     * @return list<string>
     */
    public static function navigationTitle(string $label): array
    {
        return [':navigation-title: ' . $label, ''];
    }

    /**
     * The anchor a `:ref:` reaches, written above the heading it names.
     *
     * @return list<string>
     */
    public static function label(string $name): array
    {
        return ['.. _' . $name . ':', ''];
    }

    /**
     * A drawing, with the sentence that stands in for it where it cannot be
     * seen.
     *
     * The description is the `:alt:` rather than a caption: it says what the
     * drawing shows for a reader who is not looking at it, which is not the
     * same as a title under the figure.
     *
     * Every drawing here is a diagram rather than a picture, and a diagram is
     * drawn at the width of the column it lands in, which is not the width its
     * labels were set for. `:zoomable:` is what gives that back.
     *
     * @return list<string>
     */
    public static function image(string $path, string $alt): array
    {
        return [
            '.. image:: ' . $path,
            self::INDENT . ':zoomable:',
            self::INDENT . ':alt: ' . Wrap::text($alt, self::INDENT . '      '),
            '',
        ];
    }

    /**
     * What a page is called once the extension is off it.
     *
     * The renderer addresses a document by its path without one, and a `:doc:`
     * that carries `.rst` resolves against a file called `page.rst.rst` in
     * every parser this corpus has been through.
     */
    private static function target(string $path): string
    {
        return (string) preg_replace('/\.rst$/', '', $path);
    }
}
