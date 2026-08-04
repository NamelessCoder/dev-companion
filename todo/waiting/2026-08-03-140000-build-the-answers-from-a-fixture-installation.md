# Build the installation answers from a fixture

**Serves:** documentation/tools/
**Priority:** normal
**Waiting on:** put to the maintainer on 2026-08-04 with the three answers
    priced, and deferred rather than left unread — so a session reaching this
    card is not the first to look at it. Nothing is broken while it waits: the
    derived half of every page is checked today and the recorded half is what
    goes unheld. What would make it worth answering is the checkout becoming a
    problem somebody has rather than one the reading found — a CI run that needs
    the answered half, or `typo3_component_lookup` gaining a source that does
    not need an installed backend. The question as it stands: may the tool pages
    be built against a second fixture standing in for the core checkout, when
    `typo3_component_lookup` then answers from the bundled catalog rather than
    from an installed backend? Three answers below, and the reading behind them
    is here.

Half of it is done. `Upkeep\Fixture` writes a Composer project below
`.fixtures/` whose console answers and whose container boots full, and
`tools:record` records the installation-backed tools against it rather than
against the made `E-SITE`. All nine carry a second answer now, on every machine,
the three that had none included. `D-DOC-012` has what that cost per page, what
it bought, and what it gave up — the `E-SITE` reached its console through
`ddev exec` and nothing exercises that transport any more.
`todo/open/2026-08-03-120000-record-the-tool-answers-again-with-an-e-site.md`
asked for those three pages and is answered by this; it is another session's
file, so it was left where it is.

What is left is the sentence this todo opened with: `tools:check` holding the
whole page rather than its upper half. It does not follow from the fixture, and
what the reading found is why.

- Every page carries the primary root's answers, and that root is a core
  checkout below `.checkouts/` — fetched over the network, absent in CI. Nothing
  below `## Answered` is derivable anywhere until the primary root is too, so
  this is one fixture more rather than a check over the one that exists.
- Two of the tools that turn on it need only that `Knowledge\Scope` reads the
  installation as `core-checkout`, which a fixture supplies by declaring
  `"type": "typo3-cms-core"`: `typo3_test_run_guide` and `typo3_script_lookup`
  are otherwise answered from `knowledge/`.
- `typo3_component_lookup` is the one that does not.
  `Knowledge\Catalog\InstalledComponents` derives every class and custom
  property name from the installed `EXT:backend`'s `backend.css` and takes the
  styleguide's markup where that package is installed. A written stylesheet puts
  a fabricated class list on the page; no stylesheet at all falls back to the
  bundled catalog, which is the answer a caller with no installation gets.
- The three that reach outside stay recorded whichever way it goes —
  `D-DOC-008`.

The three answers, so the question is a choice rather than an opening: a second
fixture shaped as a core checkout, and `typo3_component_lookup` loses what an
installed backend shows; the primary root stays a real checkout, and
`tools:check` goes on holding the derived half alone; or the answered half is
checked for the nine installation-backed tools only, which splits one
`## Answered` section into a checked part and an unchecked one and runs against
the line `D-DOC-007` drew between the two halves of a page.
