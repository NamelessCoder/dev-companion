<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\BackendCss;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Cli;
use TYPO3\DevCompanion\Upkeep\CustomElements;
use TYPO3\DevCompanion\Upkeep\Json;
use TYPO3\DevCompanion\Upkeep\StyleguideListing;

/**
 * Where each of a component's classes sits, and which majors it holds on.
 *
 * Both come out of the compiled `backend.css`, which the core commits on every
 * branch, so the four covered majors are read at once and reading them is the
 * verification rather than a step after it. What stays curated is which
 * components are worth answering about; every fact about a class is written
 * here — `D-CAT-008`.
 */
#[AsCommand(
    name: 'components:derive',
    description: 'write where each component class sits and the majors it holds on, from .checkouts/',
)]
final class ComponentDerive
{
    private const CLASSES = '/knowledge/catalog/component-classes.json';

    private const ELEMENTS = '/knowledge/catalog/custom-elements.json';

    private const LISTING = '/knowledge/catalog/styleguide-listing.json';

    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();
        $styles = [];
        $elements = [];
        $listings = [];
        $missing = [];
        foreach (Versions::covered() as $version) {
            $checkout = $checkouts . '/' . $version['branch'];
            $css = BackendCss::of($checkout);
            if ($css === null) {
                $missing[] = $version['branch'];
                continue;
            }
            $styles[$version['major']] = $css;
            $elements[$version['major']] = CustomElements::of($checkout);
            $listing = StyleguideListing::of($checkout);
            if ($listing !== null) {
                $listings[$version['major']] = $listing;
            }
        }
        if ($missing !== []) {
            Cli::errors($output)->writeln(sprintf(
                'No compiled backend stylesheet below %s: run bin/cli checkouts:update.',
                implode(', ', $missing),
            ));

            return 2;
        }

        $components = Catalogs::read('components');
        $known = self::known($components, $elements);

        $classes = [];
        foreach ($components as $component) {
            $root = $component['rootClass'] ?? null;
            if (!is_string($root) || $root === '') {
                continue;
            }
            foreach (self::classesOf($component) as $class) {
                $classes[] = self::derive($class, $root, (string) $component['name'], $styles, $known);
            }
        }

        self::write(self::CLASSES, $classes);
        self::write(self::ELEMENTS, self::derivedElements($elements));
        self::write(self::LISTING, self::derivedListing($listings));

        $placed = count(array_filter($classes, static fn(array $c): bool => $c['positions'] !== []));
        $output->writeln(sprintf(
            '%d classes, %d of them placed, and %d custom elements, over %s',
            count($classes),
            $placed,
            count(self::derivedElements($elements)),
            implode(', ', array_column(Versions::covered(), 'branch')),
        ));
        $output->writeln(sprintf(
            '%d components listed by the styleguide, which %s ships',
            count(self::derivedListing($listings)),
            $listings === [] ? 'no covered major' : 'major ' . implode(', ', array_keys($listings)),
        ));

        return 0;
    }

    /**
     * @param array<string, mixed> $component
     * @return list<string>
     */
    private static function classesOf(array $component): array
    {
        $names = [];
        foreach (['variants', 'modifiers', 'subComponents'] as $field) {
            foreach ($component[$field] ?? [] as $name) {
                if (is_string($name) && $name !== '') {
                    $names[$name] = true;
                }
            }
        }

        return array_keys($names);
    }

    /**
     * @param array<int, array<string, mixed>> $components
     * @param array<int, array<string, string>> $elements
     * @return list<string>
     */
    private static function known(array $components, array $elements): array
    {
        $known = [];
        foreach ($components as $component) {
            if (isset($component['rootClass']) && is_string($component['rootClass'])) {
                $known['.' . $component['rootClass']] = true;
            }
            foreach (self::classesOf($component) as $class) {
                $known['.' . $class] = true;
            }
        }
        foreach ($elements as $tags) {
            foreach (array_keys($tags) as $tag) {
                $known[$tag] = true;
            }
        }
        ksort($known);

        return array_keys($known);
    }

    /**
     * @param array<int, BackendCss> $styles
     * @param list<string> $known
     * @return array<string, mixed>
     */
    private static function derive(string $class, string $root, string $component, array $styles, array $known): array
    {
        $present = [];
        $positions = [];
        $within = [];
        foreach ($styles as $major => $css) {
            if (!$css->carries($class)) {
                continue;
            }
            $present[] = $major;
            $position = $css->position($class, $root);
            if ($position !== null) {
                $positions[$position][] = $major;
            }
            foreach ($css->stylesWithin($class, $known) as $name) {
                $within[$name][] = $major;
            }
        }

        $entry = [
            'class' => $class,
            'component' => $component,
            'since' => $present === [] ? null : min($present),
            'positions' => self::ranged($positions, 'position'),
            'stylesWithin' => self::ranged($within, 'name'),
        ];
        $until = self::until($present);
        if ($until !== null) {
            $entry['until'] = $until;
        }

        return $entry;
    }

    /**
     * @param array<string, non-empty-list<int>> $byValue
     * @return list<array<string, mixed>>
     */
    private static function ranged(array $byValue, string $key): array
    {
        $out = [];
        foreach ($byValue as $value => $majors) {
            $entry = [$key => $value, 'since' => min($majors)];
            $until = self::until($majors);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /**
     * The last major a fact holds on, or null where it reaches the newest one.
     * An open range is written as an absent `until` throughout `knowledge/`, so
     * a fact that is still true says nothing rather than naming today's major.
     *
     * @param list<int> $majors
     */
    private static function until(array $majors): ?int
    {
        $covered = Versions::majors();
        if ($majors === [] || $covered === []) {
            return null;
        }

        return max($majors) < max($covered) ? max($majors) : null;
    }

    /**
     * What the styleguide of each major lists, which is the public API
     * boundary: a component it lists may be used and one it does not list may
     * not. Only the listing is written — whether a single class is
     * demonstrated cannot be read out of the templates, because a demo renders
     * its markup through a ViewHelper or a web component as often as it writes
     * it, and what is written is as often the styleguide's own page furniture.
     *
     * @param array<int, StyleguideListing> $listings
     * @return list<array<string, mixed>>
     */
    private static function derivedListing(array $listings): array
    {
        $seen = [];
        foreach ($listings as $major => $listing) {
            foreach ($listing->components() as $component) {
                $seen[$component][] = $major;
            }
        }
        ksort($seen);

        $out = [];
        foreach ($seen as $component => $majors) {
            $entry = ['component' => $component, 'since' => min($majors)];
            $until = self::until($majors);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /**
     * @param array<int, array<string, string>> $elements
     * @return list<array<string, mixed>>
     */
    private static function derivedElements(array $elements): array
    {
        $seen = [];
        foreach ($elements as $major => $tags) {
            foreach ($tags as $tag => $source) {
                $seen[$tag]['majors'][] = $major;
                $seen[$tag]['source'] = $source;
            }
        }
        ksort($seen);

        $out = [];
        foreach ($seen as $tag => $found) {
            $entry = ['tag' => $tag, 'source' => $found['source'], 'since' => min($found['majors'])];
            $until = self::until($found['majors']);
            if ($until !== null) {
                $entry['until'] = $until;
            }
            $out[] = $entry;
        }

        return $out;
    }

    /** @param list<array<string, mixed>> $records */
    private static function write(string $path, array $records): void
    {
        file_put_contents(
            Paths::root() . $path,
            Json::format((string) json_encode($records, JSON_THROW_ON_ERROR)),
        );
    }
}
