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
     * The file name an entry takes, which is its id and its title.
     *
     * One implementation, because the check that holds a name and whatever
     * writes one have to agree on every character of it: an apostrophe or a
     * backtick is dropped rather than turned into a separator, so "a package's
     * build" reads `a-packages-build`, and every other run of what is neither a
     * letter nor a figure is one dash.
     */
    public static function fileName(string $id, string $title): string
    {
        $slug = strtolower(str_replace(["\u{2019}", "\u{2018}", '`', "'"], '', $title));
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', $slug), '-');

        return strtolower(substr($id, 2)) . '-' . $slug . '.md';
    }

    /**
     * The entries a test holds, which is what a failing one prints.
     *
     * Read from the generated list, which the test's own `#[Decision]` or
     * `#[Requirement]` attributes are what wrote — so this answers with the
     * entries that test declared, and a session standing in a red test is sent
     * to them.
     *
     * A whole class counts for every method in it: an entry naming
     * `VersionsTest` rests on all of it.
     *
     * @param array<string, array{id: string, title: string, group: string, file: string, tests: list<string>}> $entries
     * @return list<array{id: string, title: string, file: string}>
     */
    public static function restingOn(array $entries, string $corpus, string $class, string $method): array
    {
        $held = [];
        foreach ($entries as $entry) {
            if (!in_array($class . '::' . $method, $entry['tests'], true) && !in_array($class, $entry['tests'], true)) {
                continue;
            }
            $held[] = [
                'id' => $entry['id'],
                'title' => $entry['title'],
                'file' => $corpus . '/' . $entry['group'] . '/' . $entry['file'],
            ];
        }

        return $held;
    }

    /**
     * The entry's file as the generated list of names would write it.
     *
     * The `#[Decision]` and `#[Requirement]` attributes are the source and the
     * front matter is the copy, which is why the whole file comes back rather
     * than the list: `decisions:cover` and `requirements:cover` write what this
     * returns and the checks compare against it, so one implementation decides
     * what the front matter says.
     *
     * An entry no test names keeps an empty list it was written with — that
     * says nothing holds the entry, where the absence of the key says nobody
     * has asked.
     *
     * @param list<string> $names
     */
    public static function withNames(string $contents, string $key, array $names): string
    {
        $written = $names === []
            ? $key . ": []\n"
            : $key . ":\n" . implode('', array_map(static fn(string $name): string => '  - ' . $name . "\n", $names));

        // A callback rather than a replacement string: a `$` in one would be
        // read as a group reference, and what is written here is a name.
        $carries = '/^' . $key . ':(?: \[])?\R(?:  - .*\R)*/m';
        if (preg_match($carries, $contents) === 1) {
            return (string) preg_replace_callback($carries, static fn(): string => $written, $contents, 1);
        }
        if ($names === []) {
            return $contents;
        }

        // The key is new, and goes at the foot of the front matter: the first
        // `---` on a line of its own, the opening one standing at the very
        // start of the file.
        return (string) preg_replace_callback('/\R---\R/', static fn(): string => "\n" . $written . "---\n", $contents, 1);
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
