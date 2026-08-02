---
id: R-SKL-4
status: held
---

# R-SKL-4 — An assessment establishes its base before opening the checkout

**An assessment establishes its base — scope, the owning tools, and the list of
surfaces it will cover — before it opens the checkout.**

That list comes from the audit surfaces rather than from the file tree: a
surface with no files is invisible to a listing, and its absence is usually the
finding.

A surface stated as what the repository declares is the file tree in another
form and fails the same way, so a surface that is a layer of checks names what
a complete one covers and is answered entry by entry.

It asks the owner of a surface's conventions before forming a view of it, not
afterwards to confirm one, and does not mistake a runtime lookup for that
question — what is registered and what a path resolves to are facts about the
installation, never a verdict on it. What comes back is read against the
checkout in both directions: a file that has settled into the opposite of a
rule is a finding rather than a local style. A surface never asked about is
reported as unassessed, because a defect nobody looked for and a defect that is
not there are indistinguishable in a report that does not separate them — which
is why the deprecation sweep is reported when it comes back empty as well, with
the majors it covered.

A finding about a user-controlled value is held to the same distinction one
surface further in: it is a claim about the sink, so it is not established until
the tag, attribute, header, statement or process the value ends up in is named
and read, and an opt-out or a quoting helper on the way there is part of the
path rather than the end of it. Escaping and injection are that same claim about
different sinks, so the gate is written once for both and the sinks themselves
are the architecture hints' to answer.

## From

The second `REVIEW-01` run (2026-07-31), which followed two of the conformance
skill's seven evidence steps, read the site package's three XLF files without
asking what governs them, and so missed the German `source-language` that
[`R-KNW-33`](../knowledge/knw-33-a-new-label-names-its-source-language.md)
already covers and a run on 2026-07-30 had already found in the same checkout.
Sharpened by the third run the same day, which read the checklist, then listed
the file tree and spent five minutes reading it before calling
`typo3_task_guide` or any conventions lookup — and which confirmed the
translation domain with `typo3_translation_domain_lookup` and filed
translations under "assessed and clean" with the German `source-language`
header on screen, while the extension's absent `Documentation/` appeared
neither as a finding nor as unassessed. Extended by two `REVIEW-02` runs in the
same extension checkout on 2026-07-31, at 12:21 and at 13:32, neither of which
produced a finding about static analysis in a repository with no analyser, no
analysis step and no baseline — the second having run both declared checks and
reported their ceiling instead, because the quality surface asked what the
repository declares rather than what a complete check layer covers. Extended
again by the `REVIEW-02` run in an extension a major behind the world
(2026-07-31), whose one finding with an active security consequence was an
escaping opt-out in a template: every citation under it correct, and the output
escaped anyway, because the six call sites sit in a ViewHelper that emits
nothing and the core wraps the resolved title in `htmlspecialchars()` two
classes further on. The run opened neither of them, and did open the core
ViewHelper that confirmed what it already believed.

## Held by

- `SkillTest::theBaseIsEstablishedBeforeTheCheckoutIsOpened`
- `SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk`
- `SkillTest::aSecurityFindingIsNotEstablishedUntilItsSinkIs`
- `HintsTest::bothSidesOfAnInjectionQuestionReachTheSinkMethod`
- `SkillTest::theDeprecationSweepRunsFromTheExtensionsSurfaceAndIsReportedWhenItFindsNothing`
- `SkillTest::theCheckLayerIsMeasuredAgainstACompleteOneRatherThanWhatIsDeclared`,
- `REVIEW-01`, `REVIEW-02`, `SKILL-09` — the last of which is what measures the
  escaping half, because the test beside it reads a sentence rather than a
  review.
