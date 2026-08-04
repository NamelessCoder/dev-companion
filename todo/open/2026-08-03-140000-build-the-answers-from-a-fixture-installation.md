# Build the installation answers from a fixture

**Serves:** documentation/tools/
**Priority:** normal

Write a second fixture below `.fixtures/` shaped as a core checkout — a Composer
project declaring `"type": "typo3-cms-core"` at its root, which is what
`Instance` reads as `KIND_CORE_CHECKOUT` and what `Knowledge\Scope` places a
path by — and make it `ToolRecord`'s primary root in place of
`self::newestCheckout()`, so the `## Answered` half of every page below
`documentation/tools/` is derivable with no network and `tools:check` holds the
whole page rather than its upper half. Ship no `backend.css` in it:
`Knowledge\Catalog\InstalledComponents` derives every class and custom property
name from the installed `EXT:backend`'s stylesheet, so a written one puts a
fabricated class list on the page while no stylesheet at all falls back to the
bundled catalog — which is the answer a caller with no installation gets, and it
is what `typo3_component_lookup` loses here. `typo3_test_run_guide` and
`typo3_script_lookup` need only the `core-checkout` scope and are otherwise
answered from `knowledge/`, and the three tools that reach outside stay recorded
whichever way it goes (`D-DOC-008`).

## What was already established

The first of the three answers this card was put back with, chosen by the
maintainer on 2026-08-04: the primary root stops being a real checkout, and
`typo3_component_lookup` answers from the bundled catalog rather than from an
installed backend.

Half of the card is already built. `Upkeep\Fixture` writes a Composer project
below `.fixtures/` whose console answers and whose container boots full, and
`tools:record` records the installation-backed tools against it rather than
against the made `E-SITE`. All nine carry a second answer now, on every machine,
the three that had none included. `D-DOC-012` has what that cost per page, what
it bought, and what it gave up — the `E-SITE` reached its console through
`ddev exec` and nothing exercises that transport any more.

What is left is a second fixture rather than a check over the one that exists,
because every page carries the primary root's answers and that root is a core
checkout below `.checkouts/` — fetched over the network, absent in CI.

The two answers not taken: leave `tools:check` holding the derived half alone,
or check the answered half for the nine installation-backed tools only — which
splits one `## Answered` section into a checked part and an unchecked one, and
runs against the line `D-DOC-007` drew between the two halves of a page.
