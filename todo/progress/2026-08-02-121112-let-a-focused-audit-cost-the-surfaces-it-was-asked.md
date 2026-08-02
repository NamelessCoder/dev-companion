# Let a focused audit cost the surfaces it was asked about

**Serves:** feedback/2026-07-31-183648-the-typo3-extension-conformance-skill-provided.md
**Priority:** normal
**Branch:** todo/let-a-focused-audit-cost-the-surfaces-it-was-asked
**Claimed:** 2026-08-02

Step 4 of the ladder, judged in `D-SKL-002`, which carries the evidence and what
it rejected: `references/checklist.md` line 3 permits a scoped review
and the two operative steps of `SKILL.md` — line 20, which narrows the surface
list by the kind of checkout and not by the request, and line 98, which closes
on every entry of that list — never mention it, so a security-only review is
told to write and answer the whole list anyway. Settle first whether the reading
can be cut to the requested surfaces while the list stays whole, with the rest
reported as out of scope rather than dropped: read `R-SKL-004` and the runs
behind it, then the `REVIEW-02` transcripts under `scenarios/runs/`, and decide
against them whether an out-of-scope entry stays distinguishable from an
unassessed one in a report. Only then reword the two steps, and note that
`SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened` asserts line 20 as a
literal string in an ordered block, so the test moves with the wording.
