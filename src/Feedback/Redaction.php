<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Feedback;

/**
 * Takes a value that looks like a credential out of text a session wrote, and
 * says what it took out.
 *
 * A feedback is moved from the project a session is standing in into this
 * checkout, which is committed and pushed. A key, a password or a token pasted
 * as evidence therefore leaves the repository that owns it for one where nobody
 * is looking for it and nobody can take it back, and no amount of asking the
 * session not to reaches the one that is busy proving what the live value is.
 * What the finding needed was the path and the shape — "the key at
 * `SYS/encryptionKey` is the active one, hardcoded in
 * `config/system/settings.php`" is the whole of it, and the 96 characters after
 * it establish nothing further.
 *
 * **Marked, never silently.** The archive keeps a session's report because the
 * report is the evidence, so a reader has to be able to see that something was
 * taken out and go and ask. Every removal leaves `[redacted: …]` naming the
 * shape of what stood there, which is also what makes the corpus checkable: the
 * marker is greppable and the value it replaced is not.
 *
 * **What counts, and why the thresholds are where they are.** Every rule below
 * was run over the 207 recorded feedback before it was written down, because a
 * rule that redacts a class name or a version string costs more than the leak
 * it prevents. Over that corpus all four together take out exactly one value:
 * the encryption key this was written for.
 *
 * - A hexadecimal run of 64 characters or more. The key was 96; a git revision
 *   is 7 to 40 and is quoted constantly in feedback about core patches, so 64
 *   clears the longest of those by 24 characters.
 * - A base64 run of 64 characters or more. The same threshold, for the same
 *   measured reason: at 40 the corpus loses `RemovedPublicMethodsRelated`…,
 *   `ImportSiteConfigurationsOnPackageInitialization` and six more changelog
 *   and class names, and at 64 it loses nothing.
 * - A value assigned to a name that says what it is — `password`, `secret`,
 *   `token`, an API key, an encryption key. This is the half a length rule
 *   cannot see: a short password is only recognisable by the name next to it.
 * - The password in a URL that carries one, `mysqli://user:…@host` — a database
 *   DSN read out of an installation, which is one string and not an assignment.
 *
 * **What it deliberately does not catch**, so that what holds is readable
 * without reading the code: a base64 value containing `/`, which the corpus
 * showed is indistinguishable from a class path; base64url and the JWTs made of
 * it, whose `-` and `_` run into every changelog identifier; and a short secret
 * standing on its own, with no name and no shape to know it by. A value the
 * session simply describes — "the key is the one in `settings.php`" — is not a
 * leak and is left alone.
 */
final class Redaction
{
    /** What is left where a value was, so a reader can see that something was. */
    private const MARKER = '[redacted: %s]';

    /** Names that say the value after them is a credential. */
    private const NAMES = 'password|passwd|pwd|secret|token|api[_-]?key|apikey|encryptionkey|credentials?';

    /**
     * A name of that kind, an assignment, and the value it assigns.
     *
     * The separator is where the false positives were. `:` alone matched
     * `install:password:set` — a console command quoted in a feedback about
     * setting an installation up — so a colon counts only where a value follows
     * it, after a space or a quote, which is what YAML, PHP arrays and prose
     * agree on. `=` and `=>` need no such help.
     */
    private const NAMED_VALUE = '~(?<name>(?<![\w-])[\w.$\[\]\'"-]*(?:' . self::NAMES . ')[\w.\[\]\'"-]*)'
        . '(?<separator>\s*(?:=>|=|:(?=\s|[\'"]))\s*)'
        . '(?<value>\'[^\']+\'|"[^"]+"|[^\s,;)\]}\[]+)~i';

    /** `scheme://user:password@host`, of which only the password is taken. */
    private const URL_CREDENTIALS = '~\b(?<prefix>[a-z][a-z0-9+.\-]*://[^\s/:@]+:)(?<secret>[^\s/@]+)@~i';

    /**
     * How long a run has to be before nothing anybody writes by hand reaches
     * it: longer than a git revision, longer than the longest changelog
     * identifier in the corpus, and shorter than the key this was written for.
     */
    private const RUN_LENGTH = 64;

    private const HEXADECIMAL_RUN = '~(?<![0-9a-f])[0-9a-f]{' . self::RUN_LENGTH . ',}(?![0-9a-f])~i';

    private const BASE64_RUN = '~(?<![A-Za-z0-9+])[A-Za-z0-9+]{' . self::RUN_LENGTH . ',}={0,2}(?![A-Za-z0-9+/=])~';

    /** Below this, a word after `password:` is prose rather than a value. */
    private const SHORTEST_VALUE = 6;

    /** @param array<int, string> $removed what was taken out, by shape */
    private function __construct(
        public readonly string $text,
        public readonly array $removed,
    ) {}

    /**
     * The text as it may be written down, and what was taken out of it.
     *
     * The order is the point: a named value is redacted before the length rules
     * see it, so a key assigned to its own name is reported as "the value of
     * `encryptionKey`" rather than as an anonymous run of hexadecimal. The
     * marker each rule leaves carries none of the shapes the later rules look
     * for, so nothing is redacted twice.
     */
    public static function of(string $text): self
    {
        $removed = [];
        $text = self::urlCredentials($text, $removed);
        $text = self::namedValues($text, $removed);
        $text = self::hexadecimalRuns($text, $removed);
        $text = self::base64Runs($text, $removed);

        return new self($text, $removed);
    }

    /** @param array<int, string> $removed */
    private static function urlCredentials(string $text, array &$removed): string
    {
        return (string) preg_replace_callback(
            self::URL_CREDENTIALS,
            static function (array $match) use (&$removed): string {
                $what = 'the password in a URL';
                $removed[] = $what;

                return $match['prefix'] . sprintf(self::MARKER, $what) . '@';
            },
            $text,
        );
    }

    /** @param array<int, string> $removed */
    private static function namedValues(string $text, array &$removed): string
    {
        return (string) preg_replace_callback(
            self::NAMED_VALUE,
            static function (array $match) use (&$removed): string {
                $quote = str_starts_with($match['value'], "'") || str_starts_with($match['value'], '"')
                    ? substr($match['value'], 0, 1)
                    : '';
                $value = $quote === '' ? $match['value'] : substr($match['value'], 1, -1);
                if (!self::isAValue($value)) {
                    return $match[0];
                }

                $what = 'the value of ' . self::readableName($match['name']);
                $removed[] = $what;

                return $match['name'] . $match['separator'] . $quote . sprintf(self::MARKER, $what) . $quote;
            },
            $text,
        );
    }

    /**
     * A run of hexadecimal longer than any revision anybody quotes.
     *
     * @param array<int, string> $removed
     */
    private static function hexadecimalRuns(string $text, array &$removed): string
    {
        return (string) preg_replace_callback(
            self::HEXADECIMAL_RUN,
            static function (array $match) use (&$removed): string {
                return self::isMixed($match[0], '~[a-f]~i') ? self::markRun($match[0], 'hexadecimal', $removed) : $match[0];
            },
            $text,
        );
    }

    /**
     * The same length of base64, where it is bytes rather than one long word.
     *
     * @param array<int, string> $removed
     */
    private static function base64Runs(string $text, array &$removed): string
    {
        return (string) preg_replace_callback(
            self::BASE64_RUN,
            static function (array $match) use (&$removed): string {
                return self::isMixed($match[0], '~[a-z]~') && self::isMixed($match[0], '~[A-Z]~')
                    ? self::markRun($match[0], 'base64', $removed)
                    : $match[0];
            },
            $text,
        );
    }

    /** @param array<int, string> $removed */
    private static function markRun(string $run, string $shape, array &$removed): string
    {
        $what = sprintf('a %d-character %s value', strlen($run), $shape);
        $removed[] = $what;

        return sprintf(self::MARKER, $what);
    }

    /**
     * Whether what follows a name that says "credential" is a value at all.
     *
     * `the install tool password: it never prints` is a sentence, and the word
     * after the colon is `it`. What tells the two apart without a dictionary is
     * that a value carries something prose does not: a digit, a symbol, or a
     * capital letter that is not the first one. A password of six lowercase
     * letters walks past this, and that is the side to be wrong on — the other
     * one redacts an English sentence out of a report.
     */
    private static function isAValue(string $value): bool
    {
        if (strlen($value) < self::SHORTEST_VALUE) {
            return false;
        }

        return preg_match('~[0-9]~', $value) === 1
            || preg_match('~[^A-Za-z0-9]~', $value) === 1
            || preg_match('~.[A-Z]~', $value) === 1;
    }

    /**
     * Whether a long run is bytes rather than one word or one character over
     * and over: it has to carry a digit, and whatever else the shape needs.
     *
     * An encoded value carries every class of character it has at this length
     * as a matter of arithmetic — a 96-character key without one digit in it is
     * a coin landing the same way 96 times. What does not is what would
     * otherwise be caught by the alphabet alone: a 64-character camel-cased
     * identifier, which the corpus is full of, and a run of the same character
     * padding a line out, which is hexadecimal by charset and a value by
     * nothing else.
     */
    private static function isMixed(string $run, string $letters): bool
    {
        return preg_match('~[0-9]~', $run) === 1 && preg_match($letters, $run) === 1;
    }

    /**
     * The part of a name a reader needs: `encryptionKey` out of
     * `$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']`.
     *
     * The path is what the finding was about and it stays in the report — this
     * is only what the marker itself says, where the whole left-hand side would
     * be a sentence of brackets.
     */
    private static function readableName(string $name): string
    {
        if (preg_match_all('~[\w.-]*(?:' . self::NAMES . ')[\w.-]*~i', $name, $matches) === 0) {
            return 'a value named as a credential';
        }

        $segments = array_values(array_filter($matches[0], static fn(string $segment): bool => $segment !== ''));

        return $segments === [] ? 'a value named as a credential' : '`' . end($segments) . '`';
    }
}
