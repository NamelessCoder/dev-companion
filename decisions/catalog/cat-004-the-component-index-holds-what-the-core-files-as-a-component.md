---
id: D-CAT-004
title: 'The component index holds what the core files as a component'
date: 2026-08-11
status: open
---

# D-CAT-004 — The component index holds what the core files as a component

**The index's candidate set is what the core itself files as a component: the
partials under `Build/Sources/Sass/component/` and the custom elements under
`element/`.**

A miss therefore means uncurated, and the backend module chrome is a miss of
that kind rather than a subject the index declines.

A session reworking Gerrit 95163 read the boundary the other way round. It took
`typo3_component_lookup` for a catalogue of discrete widgets — badge, card,
panel, input-group, the examples its own schema names — concluded that the
chrome around every backend module was not in it, and never called the tool
while authoring `.module-docheader-wrapper` and four `--module-docheader-*`
custom properties. It established that from the styleguide templates in the
checkout instead, and said so in
`feedback/2026-08-10-182511-component-lookup-was-passed-over-while.md`.

## Evidence

- Re-run on 2026-08-11 against the bundled snapshot (TYPO3 15.0 @
  `4c8b38b2dd07`, verified 2026-07-28): `docheader`, `module` and
  `module docheader`, each with `targetVersion` 15.0, all return the miss text.
  The session's conclusion was right and its route to it was not.
- `.checkouts/main` at `c71b2bdb2f`: `Build/Sources/Sass/component/_module.scss`
  defines `.module`, `.module-docheader`, `.module-docheader-navigation`,
  `.module-docheader-buttons`, `.module-body` and some twenty `--module-*`
  custom properties. Every one of the 25 catalogued entries carries a `sassPath`
  into that same directory, `_card.scss` and `_panel.scss` among them.
- `--module-docheader-scroll-offset` has been in that file since `859318a83d`
  (2025-03-18). The feedback lists it among the properties its patch authored,
  which is the round trip a call would have taken off it.
- Nothing a caller reads *before* calling states the curation rule. The
  description names badge, card, search box and input-group; the rule itself is
  in the miss text and in `typo3_catalog_scope`, both of them one call further
  in than the decision to skip.
- The `css-components` hint already carries the core's directory vocabulary —
  `component/` one partial per component, `component/scaffold/` the topbar and
  module menu, `module/` the area styles. That reached the session. The class
  contract of the partial it was editing did not.

## Decided

- The index is a curated subset of the core's own filing, and the filing is what
  says whether something is a candidate. Module chrome is one, so it is curated
  rather than declined.
- What that boundary is gets stated where a caller reads it before calling: the
  tool description, the `routing` entry in `knowledge/server-scope.json`, and
  `scope.components` in `knowledge/catalog/meta.json`.
- Rejected: narrowing the routing line to discrete widgets. That draws a line
  the core does not draw, and every backend CSS task outside it is sent to grep
  by an instruction that sounds authoritative.
- Rejected: a tool of its own for backend CSS classes. The caller here is a core
  contributor with the checkout open, so the answer costs one read rather than
  the four round trips and a trap that earn a tool — `D-FBK-027`.

## Assumed

- Chrome markup is worth carrying in an entry. `EXT:backend` holds it in
  `Resources/Private/Layouts/Module.fluid.html` and
  `Resources/Private/Partials/DocHeader.fluid.html`, and `EXT:info` ships a
  `Module.fluid.html` of its own, so a second package writing that markup is
  practice rather than a hypothetical.
- What the core files under `component/` is a set somebody can curate against.

## Wrong if

- The chrome entry hands out markup nobody should author by hand, because
  `ModuleTemplate` renders it for every backend module. That surfaces as an
  extension whose template carries copied docheader markup, or as a feedback
  reporting the entry as advice it had to ignore.
- Curating from the core's filing turns out to mean nothing, because that same
  directory also holds `_utility.scss`, `_type.scss` and `_root.scss` — partials
  no caller would look for as a component. Then the candidate set is not the
  directory, and the rule has to name what inside it is one.

## Since then

On 2026-08-14, a second session decided against the tool before calling it, and
a re-run says the skip cost it two hits. `feedback/2026-08-13-214927` reviewed a
Playwright diff asserting on six backend selectors — `.wizard-loader`,
`.form-check-type-card`, `.node-name`, `#tracker-details`, `[role="treeitem"]`
and `.wizard-actions button` — and grepped `Build/Sources/TypeScript` for each
of them. Its reasoning: the index answers what the right class to use is, and a
diff asks whether the class exists in the element the spec drives, which is a
source question.

Re-run on 2026-08-14 against the bundled snapshot (TYPO3 15.0 @ `4c8b38b2dd07`,
verified 2026-07-28), each with `targetVersion` 15.0: `wizard-loader` and
`wizard-actions` miss, `form-check-type-card` returns `form-check`, which
carries it as a variant, and `node-name` returns `tree`, which carries it as a
sub-component. Two of the six were curated by that name, and nothing the session
held before calling told it which two.

That is what its suggestion would cost. It asks the description to say the tool
answers what is registered rather than whether a selector exists, and to name
grepping the checkout for the second question. As a rule that is the narrowing
this entry rejected, arriving by question shape rather than by subject: a caller
cannot sort its own selectors into the two questions before it asks, so a line
sending the *does it exist* shape to grep sends the curated hit there too.

Both halves already reach a caller in the order it needs them. The description
says a miss means uncurated rather than outside the subject, and the miss text
names what settles it — "verify against the checkout before concluding a class
does not exist". So the statement before the call says the miss is not
authoritative, and the one after it says where the answer is.

Considered and not taken: routing a review of backend markup to the lookup the
way the instructions route authoring to it. Here that would have reported one
asserted class as a documented variant, which is worth a call and is not a
finding.
