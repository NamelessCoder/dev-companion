# Let an entry say its demo shows the component nowhere copyable

**Serves:** decisions/

D-CAT-003's second **Wrong if** was confirmed on 2026-08-02 and half of it fixed.
Run over all 25 entries against `.checkouts/14.3` and `.checkouts/main`, five
demos hand back page scaffolding as the component's installed markup. `card` is
now curated with `demoSelector: card-title` and takes the canonical card instead
of the `<form>` of switches its demo opens with. The other four cannot be fixed
that way, and that is the finding rather than a gap in the work: `Input.fluid.html`
and `Buttons.fluid.html` name `form-control` and `btn-group` nowhere except
inside the styleguide's own `example-container` grid, `StatusIndicators.fluid.html`
wraps all nine of its examples in demo layout — the first six loop
`<f:for each="{states}">` over a variable only its controller sets, so what is
handed over does not render at all — and `Dropdown.fluid.html` has exactly one
example carrying `dropdown`, inside an inline-styled flex row. Selecting within
them only moves which scaffolding is handed over.

What they need is the state D-CAT-003 already **Assumed** for a template with no
`sg:example` at all — keep the bundled markup, label it a fallback, do not
pretend it was derived — reached by the index's judgment rather than by the
count being zero. That is a second curated field, not a second meaning for
`demoSelector`, which is why this is queued rather than folded in: one field
that both picks an example and suppresses derivation is two rules read off one
value, and the entry stops saying which was meant.

Settle first whether it is a field at all. `"demoSelector": false` and a
`demoDerives: false` both exist as shapes, and so does deriving nothing whenever
the selected example carries a known scaffolding class — which is the permissive
extractor D-CAT-003 rules out, so it is named here to be dismissed on the record
rather than rediscovered. Then curate the four, and check `dropdown` separately:
its single match is real dropdown markup inside a wrapper, which may be a
trimming question rather than a suppression one.

`InstalledComponents::derive()` already does the right thing with an empty
example list — `markupSource` stays `catalog` — so the change is the field, the
read in `Components::load()`, and `CatalogCheck::demoMarkup()`, which digests
what is handed over and will report each entry whose digest moves. What holds it
is `CatalogTest`: the fixture pattern is
`aCuratedSelectorDecidesWhichExampleIsTheComponent`.
