<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * The one form the hand-written JSON below knowledge/ is written in.
 *
 * Two indentations grew side by side — the hints and the scope at four spaces,
 * the catalog at two — because nothing ever said which one this repository
 * writes. What that costs is a diff: a file reindented by whichever editor last
 * opened it shows every line as changed, and the statement somebody actually
 * edited is somewhere in it.
 *
 * The form is PHP's own pretty print at the indentation `.editorconfig` states,
 * with slashes and unicode left alone. Key order is untouched: in
 * server-scope.json and in the hints the order is the order an answer is read
 * out in, and sorting it would rewrite what the tools say.
 */
final class Json
{
    /**
     * The indentation, which `.editorconfig` is where it is said — that file is
     * what every editor opening one of these already obeys, and a formatter
     * that disagreed with it would undo whoever typed the last line by hand.
     * `JsonTest` holds this to it.
     */
    public const INDENT = 2;

    /**
     * Slashes and unicode stay as they were written, because the corpus is full
     * of URLs and a reader of the file is meant to see one. The zero fraction is
     * kept for the same reason a version is a string here: a number that was
     * written 1.0 means something other than 1.
     */
    private const FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR;

    /** What PHP's pretty print indents by, and the only reason this reindents at all. */
    private const PRETTY_PRINT_INDENT = 4;

    /**
     * The files this holds, which are the ones a person edits.
     *
     * `scenarios/runs/` is JSON too and `scenario:record` writes it. A formatter
     * over those would be a second author of a file that already has one, and
     * the two would disagree the moment either changed.
     *
     * @return list<string> paths relative to the repository root
     */
    public static function files(): array
    {
        if (!is_dir(Paths::knowledge())) {
            return [];
        }

        $files = [];
        foreach (Finder::create()->files()->in(Paths::knowledge())->name('*.json')->sortByName() as $file) {
            $files[] = substr($file->getPathname(), strlen(Paths::root()) + 1);
        }

        return $files;
    }

    /**
     * What one file's contents look like once written in that form.
     *
     * Decoded to objects rather than to arrays: associatively, an empty object
     * and an empty array are the same PHP value, and a formatter that cannot
     * tell them apart turns every `{}` in the corpus into `[]` — a change no
     * reindentation was asked for and no reviewer would look for in a
     * whitespace commit.
     *
     * Reindented afterwards rather than by the encoder, which indents by four
     * and takes no say in it. Only the printer ever puts a run of spaces at the
     * start of a line: a newline inside a value is written `\n`, so there is no
     * multi-line string for this to reach into.
     *
     * @throws \JsonException on anything that is not JSON
     */
    public static function format(string $contents): string
    {
        $encoded = json_encode(json_decode($contents, false, 512, JSON_THROW_ON_ERROR), self::FLAGS);

        return (string) preg_replace_callback(
            '/^(?: {' . self::PRETTY_PRINT_INDENT . '})+/m',
            static fn(array $indent): string => str_repeat(' ', intdiv(strlen($indent[0]), self::PRETTY_PRINT_INDENT) * self::INDENT),
            $encoded,
        ) . "\n";
    }
}
