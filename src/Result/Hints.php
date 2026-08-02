<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Knowledge\ArchitectureHints;
use Typo3CmsMcp\Knowledge\Catalog\References;
use Typo3CmsMcp\Knowledge\Scope;
use Typo3CmsMcp\Knowledge\Versions;

/**
 * Matched architecture hints, as an answer.
 *
 * The architecture lookup returns them as the answer and the task guide carries
 * them inside a larger one, so the range beside a statement, the notice above a
 * core-scoped hint and the worked example under it are written once.
 */
final class Hints
{
    /**
     * Matched architecture hints as data, without the internal match patterns.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    public static function records(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'id' => (string) $hint['id'],
            'title' => (string) $hint['title'],
            'category' => (string) $hint['category'],
            'scope' => ($hint['scope'] ?? null)?->value,
            'hints' => array_map(static fn(array $statement): array => [
                'text' => $statement['text'],
                'since' => $statement['since'],
                'until' => $statement['until'],
                'versions' => Versions::label($statement['since'], $statement['until']),
                'scope' => ($statement['scope'] ?? null)?->value,
            ], $hint['hints']),
            'checks' => array_map('strval', $hint['checks']),
        ], array_values($hints));
    }

    /**
     * One statement as a line, with the versions it holds for where that is not
     * all of them.
     *
     * The range is rendered beside the sentence rather than inside it: the
     * sentence is the same sentence on every version it holds for, and a reader
     * filtering by version must not have to parse prose to do it. What it is
     * binding for is rendered the same way, and only where it is not this
     * caller's obligation — inside the core everything listed applies, so the
     * marker would be on every line and say nothing.
     *
     * @param array{text: string, since: ?int, until: ?int, scope: ?Scope} $statement
     */
    public static function statement(array $statement, Scope $of = Scope::Uncertain): string
    {
        $labels = array_filter([
            Versions::label($statement['since'], $statement['until']),
            self::obligation($statement['scope'] ?? null, $of),
        ]);

        return $labels === [] ? $statement['text'] : $statement['text'] . ' [' . implode('; ', $labels) . ']';
    }

    /**
     * What one statement obliges, where that is not this caller.
     *
     * Two directions and one rule: a statement declares whose it is, and it is
     * labelled where the answer is for somebody else. Inside its own scope the
     * label would be on every line and say nothing, and where nothing placed
     * the work there is nobody to contrast it with.
     */
    private static function obligation(?Scope $declared, Scope $of): string
    {
        if ($declared === null || $of === Scope::Uncertain) {
            return '';
        }
        if ($declared === Scope::Core && $of->isOutsideTheCore()) {
            return 'binding for a core patch, a convention here';
        }
        if ($declared->isOutsideTheCore() && $of === Scope::Core) {
            return 'binding outside the core, context here';
        }

        return '';
    }

    /**
     * What a whole hint obliges, where that is not this caller.
     *
     * The backend's design system is the case this exists for: every rule in it
     * is a condition of a core patch and none of it is a condition of anything
     * in a project — which does not make it useless there, because a project
     * building a backend module wants exactly those rules. So the answer keeps
     * them and says which of the two it is handing over.
     *
     * @param array<string, mixed> $hint
     */
    public static function scopeNotice(array $hint, Scope $of): ?string
    {
        $declared = $hint['scope'] ?? null;
        if ($declared === null || $of === Scope::Uncertain) {
            return null;
        }
        if ($declared === Scope::Core && $of->isOutsideTheCore()) {
            return 'Binding for a patch to the TYPO3 core. Here they are conventions you may adopt — worth having '
                . 'where this repository builds the same thing, and no condition of anything in it.';
        }
        if ($declared->isOutsideTheCore() && $of === Scope::Core) {
            return 'Binding for work outside the TYPO3 core — a project repository or a distributed extension. '
                . 'In the core it is context for what such a repository has to do, and no condition of a patch.';
        }

        return null;
    }

    /**
     * The core's own worked example per hint id, as one line for the answer.
     *
     * A hint is a summary of something that exists in full and passing; naming
     * it beside the summary is what makes "read it" available at the moment the
     * summary turns out to be thin, rather than in a document read once.
     *
     * @return array<string, string>
     */
    public static function examples(?int $target): array
    {
        $lines = [];
        foreach (References::on($target) as $entry) {
            if ($entry['hint'] !== null) {
                $lines[$entry['hint']] = 'Worked example: ' . $entry['path']
                    . ' — typo3_reference_list for what it demonstrates and where an installation has it.';
            }
        }

        return $lines;
    }

    /**
     * The matched hints as text: one section per category, one block per hint.
     *
     * `$of` is the scope of the paths this block was matched for, so a
     * statement declaring whose it is can be labelled where the answer is for
     * somebody else.
     *
     * @param array<int, array<string, mixed>> $hints
     */
    public static function sections(array $hints, Scope $of, ?int $target): string
    {
        $examples = self::examples($target);
        $sectionTexts = [];
        foreach (ArchitectureHints::groupByCategory($hints) as $section) {
            $hintTexts = [];
            foreach ($section['hints'] as $hint) {
                $block = ['## ' . $hint['title']];
                $notice = self::scopeNotice($hint, $of);
                if ($notice !== null) {
                    $block[] = $notice;
                }
                $block[] = 'Hints:';
                foreach ($hint['hints'] as $entry) {
                    $block[] = '- ' . self::statement($entry, $of);
                }
                if (isset($examples[$hint['id']])) {
                    $block[] = $examples[$hint['id']];
                }
                if ($hint['checks'] !== []) {
                    $block[] = 'Relevant checks:';
                    foreach ($hint['checks'] as $check) {
                        $block[] = '- ' . $check;
                    }
                }
                $hintTexts[] = implode("\n", $block);
            }
            $sectionTexts[] = '### ' . $section['category'] . "\n\n" . implode("\n\n", $hintTexts);
        }

        return implode("\n\n", $sectionTexts);
    }

    /**
     * What the groups of one call found, as the one answer the payload is.
     *
     * The hints keep the checks of the group they were matched for, so a hint
     * that matched on both sides carries them once, for the paths that can run
     * them — which is why the answer names those paths beside them.
     *
     * @param array<int, array{scope: Scope, paths: array<int, string>, result: array<string, mixed>}> $found
     * @return array{matchedHints: array<int, array<string, mixed>>, availableHints: array<int, array<string, mixed>>, domains: array<int, string>, withheldCategories: array<int, string>}
     */
    public static function merged(array $found): array
    {
        $matched = [];
        $available = [];
        $domains = [];
        $withheld = [];
        foreach ($found as $group) {
            foreach ($group['result']['matchedHints'] as $hint) {
                $matched[$hint['id']] ??= $hint;
            }
            foreach ($group['result']['availableHints'] as $entry) {
                $available[$entry['id']] ??= $entry;
            }
            $domains = array_merge($domains, $group['result']['domains']);
            $withheld = array_merge($withheld, $group['result']['withheldCategories']);
        }

        return [
            'matchedHints' => array_values($matched),
            'availableHints' => array_values($available),
            'domains' => array_values(array_unique($domains)),
            'withheldCategories' => array_values(array_unique($withheld)),
        ];
    }
}
