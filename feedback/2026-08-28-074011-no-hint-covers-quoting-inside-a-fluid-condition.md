---
date: 2026-08-28T07:40:11+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/bootstrap_package
---

# no hint covers quoting inside a Fluid condition attribute, the exact defect under review

## Observation

Task: review pull request #1613 against bootstrap_package. Its entire content is one changed line in Resources/Private/Partials/ContentElements/Table/Columns.html:

  -<f:if condition="{cell}">
  +<f:if condition="{cell}!="">

That is not a working condition, it is a parse error: the attribute value ends at the second double quote, the tag then matches no ViewHelper tag pattern, and the closing `</f:if>` raises TYPO3Fluid\Fluid\Core\Parser\Exception 1224485838 ("You closed a templating tag which you never opened!"), reported at the closing tag's line rather than at the condition's. I verified this against typo3fluid/fluid 5.3.1 (TYPO3 14.3) and 4.6.1 (TYPO3 13.4). The partial is rendered by both the `table` and the `csv` content element, so the blast radius is every page carrying either.

I went to typo3_hint_lookup with id=fluid-conditions-and-arrays — title "Fluid Conditions, Escaping and Array Literals", the closest-named hint in the corpus and the one typo3_task_guide had already returned for these paths. It carries four statements: default output escaping; the rule that an `<f:if>` with an `<f:else>` needs an explicit `<f:then>`; array literals needing at least one key-value pair; and the inline `f:if` form for an array argument that has to be absent. None of them is about the syntax of the condition attribute itself.

So the corpus has a hint titled after Fluid conditions that does not state how to write one containing a comparison. I established the failure by running the template through both Fluid versions, not by lookup.

The second half of the same gap is the original bug. `<f:if condition="{cell}">` takes the else branch for every value PHP reads as falsy — "0", 0, "00", "0.0" — so a table cell an editor typed a zero into rendered as `&nbsp;`. That is the behaviour the contributor reported, and nothing in the hints states it either.

Neither half is exotic. Comparing a value against empty inside a condition attribute is what a contributor reaches for the moment they hit the falsy-zero problem, and reaching for it with double quotes is the mistake that follows.

## Query

typo3_hint_lookup id="fluid-conditions-and-arrays" availableHints=true

Earlier, via typo3_task_guide task="Review an incoming pull request that changes an f:if condition in a Fluid partial of the table content element", changeType="audit", paths=["Resources/Private/Partials/ContentElements/Table/Columns.html"], which returned the same hint id.

## Suggestion

Add to fluid-conditions-and-arrays a statement on quoting inside a condition, with the three parts a reader needs:

- The working form: a comparison inside a double-quoted attribute uses single quotes, `condition="{cell} != ''"`. The same holds for any attribute value carrying a string literal.
- The failing form and what it looks like: `condition="{cell}!=""` does not parse, and the error names the closing tag and its line rather than the condition, so the reported line number points away from the defect. That mismatch is why the mistake survives a glance at the stack trace.
- The behaviour underneath: a bare `condition="{value}"` is falsy for 0, "0", "00" and "0.0" as well as for empty and null, so a value an editor legitimately entered takes the else branch. Where the else branch is a placeholder, the value silently disappears.

A worked pair would carry it: the bare condition beside the compared one, with a table of what each renders for "0", "", null and "text". I built exactly that table by hand this session, against both Fluid 4.6.1 and 5.3.1, and it is what settled the review.
