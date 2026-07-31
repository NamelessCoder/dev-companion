---
id: R-SKL-5
status: held
---

# R-SKL-5 — The order a task starts in is written once

**The order a task starts in is written once and carried into every published
skill: the installation and its commands, the extension and what it ships, the
workflow, the conventions of each subsystem in scope — and only then the
checkout.**

A skill states what it adds to that order, never a second copy of the order
itself. The base also separates the two kinds of lookup, so a runtime answer is
not taken for a verdict, and says a returned rule is read against the code that
already exists as well as the code about to be written — in both directions:
a mechanism that costs something is not a defect for costing it, so what it is
there for is established from the repository's own statements first, and a
documented purpose makes it a trade-off to name with its cost rather than a
finding. Where no purpose can be established, the finding says that instead of
concluding there is none.

It also names the three things a finding can rest on — a file that was read at
its path and line, a command that was run, or a mechanism traced into an
installed package — and requires the finding to say which of them it is.
Reading rather than running is a legitimate way to work; not saying so is what
gives a derived finding the weight of an established one.

**From:** three `REVIEW-01` runs (2026-07-31) and the divergence they exposed
— the conformance skill was repaired while the content-element, documentation
and testing skills still ordered reading the checkout ahead of the conventions
lookup, which is the arrangement those runs measured. Extended after
`REVIEW-02` (2026-07-31) reported five of six priorities against mechanisms the
package ships deliberately — a compile step a setting drives, a vendored copy
that makes a non-Composer install work, a font download that keeps the file on
the site's own host. Extended again after three recorded `REVIEW-02` runs in two
repositories (2026-07-31) executed no project-owned command of the ten and five
they were offered, and said so nowhere in their answers.

**Held by:** `SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`,
`SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`,
`InstallerTest::codexInstallAndUpdatePreserveConfigurationAndTrackTheirSkillsCentrally`
