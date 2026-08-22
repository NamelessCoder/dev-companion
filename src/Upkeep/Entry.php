<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The head a requirement and a decision are both written with: front matter, a
 * heading naming the id and the title, and the sentence under it.
 *
 * One format read in one place. `Requirements` and `Decisions` had the same six
 * lines and the same `frontMatterValue()` each, which is how the two could have
 * drifted on what a heading looks like while every check went on passing.
 *
 * An entry rather than a record, because in TYPO3 a record is a row in the
 * database and the corpus names `Record::get()` as that — a class called
 * `Record` here answers a backticked name that belongs to somebody else.
 */
final class Entry
{
    /**
     * @return array{frontMatter: string, heading: string, title: string, statement: string}
     */
    public static function head(string $contents): array
    {
        preg_match('/^---\R(.*?)\R---\R/s', $contents, $matter);
        preg_match('/^# (\S+) — (.*)$/m', $contents, $heading);

        // Everything under the first paragraph is what the entry was written
        // from, so the statement ends where that paragraph does.
        $body = (string) preg_replace('/^---\n.*?\n---\n\n# [^\n]*\n\n/s', '', $contents);

        return [
            'frontMatter' => $matter[1] ?? '',
            'heading' => $heading[1] ?? '',
            'title' => $heading[2] ?? '',
            'statement' => trim(str_replace('**', '', self::firstParagraph($body))),
        ];
    }

    /** One key of the front matter, or nothing where it is not written. */
    public static function frontMatterValue(string $frontMatter, string $key): string
    {
        return preg_match('/^' . $key . ':\s*(.*)$/m', $frontMatter, $matches) === 1 ? trim($matches[1]) : '';
    }

    /**
     * The first paragraph of a body, which is where a statement ends.
     *
     * A pattern that will not compile is the one way `preg_split()` answers
     * `false`, and these are literals — so the empty string is what a body with
     * nothing in it gives, and there is no second case to read.
     */
    public static function firstParagraph(string $body): string
    {
        return (preg_split('/\R\R/', $body, 2) ?: [''])[0];
    }
}
