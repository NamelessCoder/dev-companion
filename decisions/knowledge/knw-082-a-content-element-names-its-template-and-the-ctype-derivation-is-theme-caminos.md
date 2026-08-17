---
id: D-KNW-082
date: 2026-08-17
status: open
---

# D-KNW-082 — A content element names its template, and the CType derivation is theme_camino's

**The corpus states that `fluid_styled_content` sets `templateName` per element
and attributes the `uppercamelcase` derivation to `theme_camino`, which
configures it.**

`sitepackage-templates` carried the derivation as a property of
`lib.contentElement`, which is a package a project may not have installed, and a
sitepackage that trusted it would have named no template at all.

## Evidence

- `feedback/2026-08-17-205817`. A session building a v14 demo site read the
  installed `fluid_styled_content` before writing its TypoScript for six custom
  elements, found the derivation absent there, and reports it would otherwise
  have configured nothing.
- The claim holds, read in `.checkouts/14.3` at `627949e9dd`.
  `typo3/sysext/fluid_styled_content/Configuration/TypoScript/Helper/ContentElement.typoscript`
  sets `templateName = Default` as a plain value, and
  `Configuration/TypoScript/ContentElement/Text.typoscript` is
  `tt_content.text =< lib.contentElement` followed by `templateName = Text` —
  one such file per element, each imported by `setup.typoscript`.
- It holds on the branches below as well: both files are the same in
  `.checkouts/12.4` and `.checkouts/13.4`, so that half of the statement carries
  no version boundary.
- The derivation is `theme_camino`'s and nothing else's.
  `Configuration/Sets/camino/TypoScript/content.typoscript` unsets
  `templateName >` and sets `templateName.ifEmpty.cObject` to a `TEXT` with
  `field = CType` and `case = uppercamelcase`; the theme's own
  `camino_textmedia_teaser_grid`, registered in
  `Configuration/TCA/Overrides/20_tt_content_textmedia_teaser_grid.php`, is
  rendered by `Resources/Private/Templates/Content/CaminoTextmediaTeaserGrid.fluid.html`.
  The set declares no dependency on `fluid_styled_content`.
- The same file is where `tt_content = CASE` gets a `default` of
  `lib.contentElement`. The core's own default, in
  `typo3/sysext/frontend/ext_localconf.php`, is the yellow "has no rendering
  definition" `TEXT`, so under the theme the derivation answers for every CType
  and under `fluid_styled_content` alone an unnamed one is a visible error.
- What the feedback expects of the failure is not what the tree says.
  `fluid_styled_content` ships no `Default` template on any covered branch —
  `Resources/Private/Templates/` holds `Text`, `Textmedia`, `Generic` and the
  rest, and `Default` is a *layout*, `Resources/Private/Layouts/Default.html`
  on 12.4 and 13.4, `Default.fluid.html` on 14.3. So the copied `Default`
  resolves to no file rather than to a frame, and the statement says what was
  read rather than what was predicted.
- Nothing else in the corpus rested on the derivation. `templateName`,
  `lib.contentElement` and `uppercamelcase` across `knowledge/` and `skills/`
  reach `content-element-preview`, which pointed at this statement for how the
  name follows from the CType, and four statements that only name
  `lib.contentElement` as the object content elements render on.

## Decided

- Rewritten as two statements: what `fluid_styled_content` does, unbound, and
  what `theme_camino` configures, `since: 14`. The consequence moved with it —
  a `snake_case` CType is a requirement under the derivation and a habit without
  it.
- Closed in this run rather than queued. `documentation/records/judging.rst`
  puts a feedback that needs a TYPO3 lookup on the todo side of the line because
  the judging run has read nothing but this repository; the reading that
  disqualifies it is the one the ladder owes any feedback claiming something
  about TYPO3, and it was done here, in `.checkouts/`, on all three covered
  majors and named by file above.
- The ladder has no rung for a statement that was delivered, taken and wrong.
  This is step 1a by what was missing — the corpus never said what
  `fluid_styled_content` does — and step 4 by what the repair costs. One
  feedback is not evidence for adding a rung, so the ladder is left as it is.
- The feedback's second ask, which of the two configurations a sitepackage
  should choose, is answered by stating both and by naming what inheriting the
  theme's convention costs: reproducing that block. Which one a project wants is
  its own decision, and a recommendation here would be one this run has no
  evidence for.
- The pointer in `content-element-preview` is corrected in the same commit. It
  said the template name follows from the CType, which is the same error one
  hint further on.

## Assumed

- That a project sitepackage is the reader of both halves. The hint's other
  statements are written for one, and the reporting session was building one.
- That the derivation is worth stating at all now that it is somebody else's.
  It is the convention the core's own theme is read for, and the sitepackage
  layout hint already sends a reader there.

## Wrong if

- A session reports reproducing the theme's block where per-element
  `templateName` was what its project wanted, or the other way round. Then
  naming both configurations without saying which fits what is the gap, and the
  choice is what the statement owes.
- `fluid_styled_content` ships a `Default` template on a later branch. Then an
  element copied off `lib.contentElement` renders a frame instead of failing,
  and the trap named here is the wrong one.
- `theme_camino` moves out of the core, which `sitepackage-layout` says is
  announced. The path named in the statement stops resolving in a checkout, and
  the sentence has to say where the theme lives instead.
- A judging run cites this entry to close a false statement without reading the
  checkout. The exception is the reading, not the closing.

## Covered by

- `HintsTest::theCTypeTemplateDerivationIsAttributedToTheThemeThatConfigures`
