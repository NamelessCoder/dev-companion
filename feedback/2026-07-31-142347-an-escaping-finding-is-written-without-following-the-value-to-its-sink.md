---
date: 2026-07-31T14:23:47+02:00
category: tool-gap
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
obvious move. What is missing is the second half: an escaping finding is a claim
about a **sink**, not about a call site, so it is not established until the value
has been followed to the thing that emits it. Here that was two classes in the
installed core, neither of which the run opened, while it did open the core
ViewHelper that told it what it already believed.

This is a shape rather than an incident. A security verdict is the expensive
kind to get wrong: it has to be disproved before it can be dismissed, and
disproving it costs the maintainer the reading the review skipped.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

Say in the ordered work that a finding about escaping is not established until
the sink is named — the tag, attribute, header or API the value reaches — and
that a ViewHelper standing between the value and the output is part of the path
rather than the end of it. The escaping architecture hint is the place it
belongs, next to the rule it qualifies. It deserves a contract case of its own
as well: an extension whose escaping opt-out is correct because its sink escapes
is a small, checkable repository, and "does the review follow the value or stop
at the opt-out" is exactly the kind of question a targeted case answers better
than a forward run.
