<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

/**
 * The components the styleguide of one core checkout lists.
 *
 * What the styleguide lists is public API and what it does not list is not to
 * be used or suggested — the maintainer, 2026-08-24. So this is a boundary
 * rather than a demo, and it is read out of the checkout like everything else:
 * the controller offers one action per component, and that list is the whole of
 * it.
 *
 * Whether a single class is demonstrated is not read here and cannot be. A demo
 * renders its markup through a ViewHelper or a web component as often as it
 * writes it — `<be:avatar>` spells no avatar class at all — and what the
 * template does write is as often the styleguide's own page furniture, such as
 * the `indicators-grid` the status indicators are laid out in. Read on
 * 2026-08-24: that question belongs to a rendered styleguide.
 *
 * The styleguide is a system extension from 13.4 and an installable package
 * before it, so a checkout that ships none answers nothing here.
 */
final class StyleguideListing
{
    private const CONTROLLER = '/typo3/sysext/styleguide/Classes/Controller/ComponentsController.php';

    /** The action that lists the rest, which is the page and not a component. */
    private const OVERVIEW = 'componentsOverview';

    /** @var list<string> */
    private array $components;

    public function __construct(string $controller)
    {
        $this->components = self::listed($controller);
    }

    /** Null where the checkout ships no styleguide, which 12.4 and older do not. */
    public static function of(string $checkout): ?self
    {
        $path = rtrim($checkout, '/') . self::CONTROLLER;

        return is_file($path) ? new self((string) file_get_contents($path)) : null;
    }

    /** @return list<string> */
    public function components(): array
    {
        return $this->components;
    }

    public function lists(string $component): bool
    {
        return in_array($component, $this->components, true);
    }

    /** @return list<string> */
    private static function listed(string $controller): array
    {
        $matches = [];
        if (!preg_match('/\$allowedActions\s*=\s*\[(.*?)\];/s', $controller, $matches)) {
            return [];
        }
        $names = [];
        if (!preg_match_all("/'([a-zA-Z]+)'/", $matches[1], $names)) {
            return [];
        }

        return array_values(array_diff($names[1], [self::OVERVIEW]));
    }
}
