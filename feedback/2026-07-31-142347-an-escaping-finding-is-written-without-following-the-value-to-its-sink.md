---
date: 2026-07-31T14:23:47+02:00
category: idea
status: open
tool: typo3_task_guide, typo3_architecture_lookup
---

# An escaping finding is written without following the value to its sink

## Observation

`REVIEW-02` reported an editor-supplied field rendered unescaped in an
extension's shipped templates as the one finding with "an active security
consequence", and put it third in the order it recommended acting on. Every
citation under it is correct: the template does wrap the value in
`<f:format.htmlentitiesDecode>`, the core ViewHelper does set
`$escapeChildren = false` and `$escapeOutput = false`, and the field is a plain
`type => input` in TCA with no RTE.

The output is escaped anyway. All six occurrences sit inside a ViewHelper of the
extension's own that emits nothing — it hands its rendered children to a
`PageTitleProvider`, core hands the resolved title to `PageRenderer::setTitle()`,
and `PageRenderer` wraps it in `<title>|</title>` through `htmlspecialchars()`.
The decode exists precisely to stop the title being double-encoded on the way
there. The workaround the finding condemned is the reason the result is correct.

The rule the answer applied — *Fluid escapes output by default; do not work
around it in the template* — is right, and applying it to a template line is the
obvious move. What was missing is the second half, and that half is written
since: an escaping finding is a claim about a **sink**, so it is not established
until the value has been followed to the thing that emits it. It stands in the
conformance checklist beside the finding gate it qualifies, held by
`SkillTest::anEscapingFindingIsNotEstablishedUntilItsSinkIs`.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

What is left is the case that would measure it. An extension whose escaping
opt-out is correct because its sink escapes is a small, checkable repository,
and "does the review follow the value or stop at the opt-out" is exactly the
kind of question a targeted contract case answers better than a forward run —
a forward review reaches this shape only when a checkout happens to contain it,
which is how the run above found it once and by accident.
