<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Yaml\Yaml;

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
     * @return array{matter: array<string, mixed>, heading: string, written: string, statement: string}
     */
    public static function head(string $contents): array
    {
        preg_match('/^---\R(.*?)\R---\R/s', $contents, $matter);
        preg_match('/^# (\S+) — (.*)$/m', $contents, $heading);

        // Everything under the first paragraph is what the entry was written
        // from, so the statement ends where that paragraph does.
        $body = (string) preg_replace('/^---\n.*?\n---\n\n# [^\n]*\n\n/s', '', $contents);

        return [
            'matter' => self::matter($matter[1] ?? ''),
            'heading' => $heading[1] ?? '',
            // What the heading says the title is. The front matter is the
            // title; this is what a check holds it to, so the two cannot drift.
            'written' => $heading[2] ?? '',
            'statement' => trim(str_replace('**', '', self::firstParagraph($body))),
        ];
    }

    /**
     * The front matter as data, which is what it is.
     *
     * Read with a YAML parser rather than a line at a time: it carries a list
     * since the tests moved into it, and a hand-rolled reader for one is a
     * third parser over a format somebody else already implements.
     *
     * @return array<string, mixed>
     */
    public static function matter(string $frontMatter): array
    {
        if (trim($frontMatter) === '') {
            return [];
        }

        try {
            // A date is asked for as a date rather than left to become the
            // Unix timestamp the parser answers by default, so `2026-08-23`
            // comes back as the day it is written as.
            $parsed = Yaml::parse($frontMatter, Yaml::PARSE_DATETIME);
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * One key of the front matter as a string, or nothing where it is not
     * written.
     *
     * @param array<string, mixed> $matter
     */
    public static function value(array $matter, string $key): string
    {
        $value = $matter[$key] ?? '';
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * One key as a list of names, whatever shape it was written in.
     *
     * @param array<string, mixed> $matter
     * @return list<string>
     */
    public static function names(array $matter, string $key): array
    {
        $value = $matter[$key] ?? [];
        if (is_string($value)) {
            $value = [$value];
        }

        return is_array($value)
            ? array_values(array_map(static fn(mixed $name): string => trim((string) $name), array_filter($value, is_scalar(...))))
            : [];
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
