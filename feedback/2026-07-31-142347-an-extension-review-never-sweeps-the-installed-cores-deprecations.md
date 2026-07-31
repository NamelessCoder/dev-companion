---
date: 2026-07-31T14:23:47+02:00
category: tool-gap
status: open
tool: typo3_task_guide, typo3_changelog_lookup, typo3_extension_scope
---

# An extension review never sweeps the installed core's deprecations

## Observation

`REVIEW-02` in an extension declaring `^12.4.37 || ^13.4.15` against an
installed 13.4.33 reported the check layer, the registration debris, the
architecture and the documentation, and reported the extension's frontend code
as carrying **no superglobal access at all** — with 24 `$GLOBALS['TSFE']` call
sites across 11 files in `Classes/`, and `@deprecated since TYPO3 v13, will
vanish during v14 development` in the installed `TypoScriptFrontendController`.
The deprecation that decides whether the package survives the next major was
absent from the priority order, and the surface it lives on was asserted clean
rather than left unassessed.

The tool answers when it is asked. A run of the same prompt in the same
repository, one branch apart, called `typo3_changelog_lookup` with
`{"query": "TypoScriptFrontendController", "type": "deprecation", "version":
"13"}`, got Deprecation-105230 tagged `FullyScanned`, matched it against every
call site and ranked it High. The second run called `typo3_changelog_lookup`
four times and never once with `type: deprecation` — it found `renderStatic()`
(Deprecation-104789) only because a ViewHelper finding walked it there.

So this is not a knowledge gap and not a tool gap in the narrow sense. What is
missing is the step that asks: nothing in the ordered work says *take the
symbols this extension actually uses and sweep them against the deprecations of
the installed core*, so the sweep happens when a finding happens to lead there
and not otherwise. `typo3_extension_scope` already knows the extension's
surface; nothing turns that into the query set.

The same gap is what `EXT-01` names as its first requirement — "a procedure
that works on any branch, not a list from one" — which is why the note that
scenario already carries and this one are the same subject seen from the review
side and from the upgrade side.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

Give the evidence order a deprecation sweep that runs from what the checkout
contains rather than from what a finding stumbles into: the symbols and
registration shapes `typo3_extension_scope` reports, queried against
`typo3_changelog_lookup` with `type: deprecation` at each declared major, with
the `FullyScanned` / `PartiallyScanned` tag carried into the answer because it
decides whether the Extension Scanner can be trusted to find the rest. It
belongs wherever the ordered work lives — `skills/base.md` if it is evidence
order for every task, the conformance checklist if it is the review's own — and
a review that finds nothing should say the sweep ran and came back empty, which
is what would have made this run's false clean impossible to write.
