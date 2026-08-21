# the ViewHelpers an installation has are not answerable, only the namespaces they live in

**Serves:** feedback/2026-08-21-074351-the-viewhelpers-an-installation-has-are-not.md
**Priority:** normal
**Branch:** todo/the-viewhelpers-an-installation-has-are-not
**Claimed:** 2026-08-21

Judged into `D-ANS-003`: the tool it asked for is refused, and what is left is
two sentences on the `fluid-templates` hint, beside the two that already route
an icon and a backend component — that a core ViewHelper's arguments come back
from the Fluid ViewHelper Reference through `typo3_documentation_lookup`, and
that one outside the core is read from the class its identifier resolves to in
the installed package. The second sentence needs that resolution established
before it is written: `TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver` sits
in the engine and not in `.checkouts/`, where only the core subclass is, so it
takes an installation's `vendor/` or the manual. Normal rather than low because
the placement lands on the hint every Fluid template task already reaches, and
the writing is two sentences once the reading is done.
