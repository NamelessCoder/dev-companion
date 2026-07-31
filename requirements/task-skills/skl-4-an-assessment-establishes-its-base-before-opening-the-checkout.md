---
id: R-SKL-4
status: held
---

# R-SKL-4 — An assessment establishes its base before opening the checkout

**An assessment establishes its base — scope, the owning tools, and the list
of surfaces it will cover — before it opens the checkout, and derives that
list from the audit surfaces rather than from the file tree: a surface with no
files is invisible to a listing, and its absence is usually the finding.**

It asks the owner of a surface's conventions before forming a view of it, not
afterwards to confirm one, and does not mistake a runtime lookup for that
question — what is registered and what a path resolves to are facts about the
installation, never a verdict on it. What comes back is read against the
checkout in both directions: a file that has settled into the opposite of a
rule is a finding rather than a local style. A surface never asked about is
reported as unassessed, because a defect nobody looked for and a defect that is
not there are indistinguishable in a report that does not separate them.

**From:** the second `REVIEW-01` run (2026-07-31), which followed two of the
conformance skill's seven evidence steps, read the site package's three XLF
files without asking what governs them, and so missed the German
`source-language` that
[`R-KNW-33`](../knowledge/knw-33-a-new-label-names-its-source-language.md)
already covers and a run on 2026-07-30 had already found in the same checkout.
Sharpened by the third run the same day, which read the checklist, then listed
the file tree and spent five minutes reading it before calling
`typo3_task_guide` or any conventions lookup — and which confirmed the
translation domain with `typo3_translation_domain_lookup` and filed
translations under "assessed and clean" with the German `source-language`
header on screen, while the extension's absent `Documentation/` appeared
neither as a finding nor as unassessed.

**Held by:** `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened`,
`SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk`, `REVIEW-01`
