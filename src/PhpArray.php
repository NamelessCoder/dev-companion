<?php

declare(strict_types=1);

namespace Typo3CmsMcp;

/**
 * Reads the keys of the array a TYPO3 declaration file returns, without running
 * it.
 *
 * Configuration/Icons.php, Configuration/Backend/Modules.php and
 * Configuration/RequestMiddlewares.php all have the same shape: a file whose
 * whole content is `return [ 'identifier' => [...], ... ];`, and the identifiers
 * are what a caller asks for. Including such a file would load somebody else's
 * code into this process, which is the one thing this server never does — so the
 * file is tokenised and the keys at the wanted nesting level are taken from the
 * token stream.
 */
final class PhpArray
{
    /**
     * The array keys at $level of the array literal in $file.
     *
     * Level 1 is the outermost array — the icon identifiers, the module
     * identifiers — and level 2 the entries below each of them, which is where
     * a middleware identifier sits under its request scope.
     *
     * @return array<int, string>
     */
    public static function keys(string $file, int $level = 1): array
    {
        if (!is_file($file)) {
            return [];
        }

        $keys = [];
        $depth = 0;
        $tokens = @token_get_all((string) file_get_contents($file));
        foreach ($tokens as $index => $token) {
            if ($token === '[') {
                ++$depth;
                continue;
            }
            if ($token === ']') {
                --$depth;
                continue;
            }
            if ($depth !== $level || !is_array($token) || $token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            if (self::isKey($tokens, $index)) {
                $keys[] = trim($token[1], "'\"");
            }
        }

        return $keys;
    }

    /**
     * Whether the string at $index is a key: the next meaningful token is "=>".
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function isKey(array $tokens, int $index): bool
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            $following = $tokens[$next];
            if (is_array($following) && in_array($following[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return is_array($following) && $following[0] === T_DOUBLE_ARROW;
        }

        return false;
    }
}
