<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Tool;

use Typo3CmsMcp\Installation\Instance;
use Typo3CmsMcp\Knowledge\Catalog\TranslationDomain;
use Typo3CmsMcp\Result\Schema;
use Typo3CmsMcp\Result\ToolResult;

/**
 * The translation domain an XLF file resolves to, computed from its path.
 *
 * Nothing registers a domain: it follows from the path by the rules in the
 * core's own path-to-domain rules — the class holding them has been both
 * TranslationDomainMapper and TranslationDomainResolver. Computing it rather
 * than looking it up is what makes it answerable at all — for a file in any
 * extension, in any instance, and for one a patch is about to add, which is
 * exactly when it cannot be looked up anywhere.
 */
final class TranslationDomainLookup extends ReadOnlyTool
{
    /**
     * The first TYPO3 major that resolves translation domains.
     *
     * Verified against the core: 13.4 has no TranslationDomain* class at all,
     * 14 ships the mapper. A domain written into a label below this renders
     * nothing — the failure is silent and at runtime, which is why this is the
     * one version fact the code carries rather than the knowledge base.
     */
    private const SINCE = 14;

    public static function name(): string
    {
        return 'typo3_translation_domain_lookup';
    }

    public static function description(): string
    {
        return 'Compute the translation domain an XLF file resolves to, from its path. The domain is the canonical way to reference a label (backend.alt_doc:key) in TCA, LanguageService::sL() and f:translate, and it is registered nowhere — it follows from the path by the rules the core itself applies, which live in TranslationDomainMapper on one branch and TranslationDomainResolver on the next. Because it is computed, it also answers for a file outside the core and for one a patch is about to add. Where the installation being read is older than translation domains, it answers with the full LLL:EXT: reference instead: the domain form renders nothing there and fails at runtime rather than at build time.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'path' => ['type' => 'string', 'minLength' => 1, 'description' => 'The XLF file path, either as an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or relative to a core checkout ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").'],
            ],
            'required' => ['path'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'path' => Schema::string('The XLF path the domain was computed from.'),
            'domain' => Schema::nullableString('The translation domain it resolves to. Null when the path names no extension, and also when the installation being read is too old to resolve domains at all — there the full LLL:EXT: reference is the answer.'),
            'domainOnNewerVersions' => Schema::nullableString('Set only in that second case: what the domain would be on a version that has them. It is not usable on this installation.'),
        ], ['path', 'domain']);
    }

    public static function answer(array $args): ToolResult
    {
        $path = trim((string) ($args['path'] ?? ''));
        $domain = TranslationDomain::fromPath($path);

        if ($domain === null) {
            return ToolResult::create(
                sprintf(
                    "\"%s\" names no extension, so no translation domain follows from it.\n"
                    . 'Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") '
                    . 'or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").',
                    $path
                ),
                ['path' => $path, 'domain' => null],
            );
        }

        // The domain form is younger than the versions this is asked from. On
        // an installation that has no resolver for it, the domain string is
        // syntactically fine and resolves to nothing at runtime: every label it
        // is written into silently renders empty. That is the one answer here
        // that has to be withheld rather than qualified.
        $major = Instance::typo3Major();
        if ($major !== null && $major < self::SINCE) {
            $reference = str_starts_with($path, 'EXT:') ? $path : 'EXT:<key>/' . ltrim($path, '/');

            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        'The installation here is TYPO3 %s, which has no translation domains: the API that resolves '
                        . 'them arrived after it. Reference the file itself instead:',
                        Instance::typo3Version(),
                    ),
                    '',
                    '  LLL:' . $reference . ':<trans-unit id>',
                    '',
                    sprintf(
                        'For the record, the domain this path would resolve to on a version that has them is "%s". '
                        . 'Writing it into a label on this installation renders nothing, and fails at runtime rather '
                        . 'than at build time.',
                        $domain,
                    ),
                ]),
                ['path' => $path, 'domain' => null, 'domainOnNewerVersions' => $domain],
            );
        }

        return ToolResult::create(
            implode("\n", [
                sprintf('%s resolves to the translation domain:', $path),
                '',
                '  ' . $domain,
                '',
                'Reference a label in it as "' . $domain . ':<trans-unit id>" — in TCA, in LanguageService::sL(), '
                    . 'and in f:translate as separate domain and key attributes.',
                'Which trans-units the file actually holds is a property of your checkout: read the file, and remember '
                    . 'that an installation can override it through LANG/resourceOverrides.',
            ]),
            ['path' => $path, 'domain' => $domain, 'domainOnNewerVersions' => null],
        );
    }
}
