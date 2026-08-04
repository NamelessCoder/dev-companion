---
id: R-SKL-005
status: held
restsOn: [D-EVI-003, D-SKL-003, D-SKL-004, D-SKL-015]
---

# R-SKL-005 — The order a task starts in is written once

**The order a task starts in is written once and carried into every published
skill.**

It is the installation and its commands, the extension and what it ships, the
workflow, the conventions of each subsystem in scope, the deprecations of the
installed core over what that extension ships — and only then the checkout.

Two of those steps carry a condition, and each one names what makes the step
already done or empty rather than optional. The workflow step is skipped where
the guide's own answer is what named the skill the session is in: the call has
been made and its brief is in the session. A skill reached from its own
description keeps the step, because the brief is built from the caller's paths
as well as the task text and no skill knows those paths — so a skip there costs
the hints and core checks they match, and the commit step the guide names for
every task that changes a file. The deprecation sweep is skipped where the
change touches no TYPO3 API, because a deprecation is a statement about API the
package calls and a change that calls none leaves the sweep empty before it is
run. Which side a change falls on is read off the files it touches rather than
the task it started as. Everywhere else both steps are run, because a
prescription that gets skipped teaches the next reader to skip the ones that
matter too.

A skill states what it adds to that order, never a second copy of the order
itself. The base also separates the two kinds of lookup, so a runtime answer is
not taken for a verdict, and says a returned rule is read against the code that
already exists as well as the code about to be written — in both directions: a
mechanism that costs something is not a defect for costing it, so what it is
there for is established from the repository's own statements first, and a
documented purpose makes it a trade-off to name with its cost rather than a
finding. Where no purpose can be established, the finding says that instead of
concluding there is none.

The deprecation sweep is part of that order rather than a step a finding
triggers, and it is bounded by the changelog's own axes: the type, each major
the package declares, and the index tag, with no query at all. What the
extension was reported to ship picks the tags rather than the words — the system
extensions it requires, renders through or registers into, and the surfaces its
files are — which is why the sweep still exists before a file is opened. Each
identifier it returns is verified in the checkout, and the `FullyScanned` /
`PartiallyScanned` tag reaches the answer because it says whether the Extension
Scanner finds the remaining call sites or the reader does.

That step also says what an empty result is worth, because a changelog records
change events and a pattern nothing changed has no entry: "does this still work
in version N" goes to `typo3_documentation_lookup` at that version, there and
whenever the reading raises it again. What that answers is a documented surface,
because the manual matches page titles and section paths and never the text of a
page: a PHP identifier has no page to be titled after, and goes to
`typo3_changelog_lookup` under its own name and then to the class. Where the
manual has no page for a surface either, that miss is a result rather than an
answer.

A behaviour question that survives all of them is read out of the installed
source — the class that implements it and the one it inherits from — as the step
after the lookups rather than in place of them, and what that replaces is
changing the code until it works. What it settles is what this installation does
and never what TYPO3 supports, so a finding says the question could not be
settled beyond the version installed and an answer built on the reading names
the version it holds for. Both dispositions are named, because a session that
has to produce working markup cannot write a finding.

It also names the three things a finding can rest on — a file that was read at
its path and line, a command that was run, or a mechanism traced into an
installed package — and requires the finding to say which of them it is, because
not saying so gives a derived finding the weight of an established one. And it
sends the session to the second of the three where the repository already
declares it: the commands `typo3_project_scope` marks as checks are run even by
a task told not to change files, the ones it marks as changes are not, and an
unknown is named as evidence that is available rather than run unasked.

## From

Three `REVIEW-01` runs (2026-07-31) and the divergence they exposed — the
conformance skill was repaired while the content-element, documentation and
testing skills still ordered reading the checkout ahead of the conventions
lookup, which is the arrangement those runs measured. Extended after `REVIEW-02`
(2026-07-31) reported five of six priorities against mechanisms the package
ships deliberately — a compile step a setting drives, a vendored copy that makes
a non-Composer install work, a font download that keeps the file on the site's
own host. Extended again after three recorded `REVIEW-02` runs in two
repositories (2026-07-31) executed no project-owned command of the ten and five
they were offered, and said so nowhere in their answers. What those runs should
have done about it was decided separately and afterwards, in `D-EVI-003`: two of
the fifteen were checks, and a check is run. Extended once more by the
`REVIEW-02` run in an extension declaring two majors against an installation a
major behind (2026-07-31), which called `typo3_changelog_lookup` four times and
never once with `type: deprecation`, reported the frontend surface as carrying
no superglobal access with 24 call sites in 11 files against a controller the
installed core marks deprecated, and named the one deprecated API it found
because a ViewHelper finding walked it there. Extended a last time by the
bootstrap_package conformance review (2026-07-31), which ended two findings in
"I had to read installed vendor core": both asked the changelog whether a
pattern still worked in 14 and read its silence as the answer, while
`typo3_documentation_lookup` at that version answered one of them in a single
call — `D-ANS-010`, re-run 2026-08-02. The sweep's own bound was replaced last,
by two models sweeping one sitepackage on the same day with word queries the
step told them to derive and getting nothing back from either
(`feedback/2026-07-31-194459`, `feedback/2026-07-31-194819`); `D-SKL-003`
carries the re-run and what the two bounds return. The step after the lookups
came from the other kind of session: `feedback/2026-08-01-003933` was building a
content element in `site-new`, guessed at the `f:if` branch contract and changed
the markup until the user corrected it, and the base's one sentence for an
exhausted question was addressed to a review it was not writing — `D-SKL-004`.
The two conditions came last, from one session adding a code style fixer to an
extension in `/home/benji/projects/ext-guidedtour` (2026-08-04). Routed to
`typo3-extension-testing`, it skipped steps 3 and 5 and reported both:
`feedback/2026-08-04-055741` warns that a prescription which gets skipped
teaches the next reader to skip the ones that matter too, and
`feedback/2026-08-04-055715` asks what the guide adds when a skill has already
routed the task. Which of the two readings to write is a question about what is
wanted, and the maintainer answered it on 2026-08-04 — the narrow one, in
`D-SKL-015`.

## Held by

- `SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`
- `SkillTest::theWorkflowStepIsSkippedOnlyWhereTheGuideNamedThisSkill`
- `SkillTest::theDeprecationSweepRunsFromTheExtensionsSurfaceAndIsReportedWhenItFindsNothing`
- `SkillTest::theDeprecationSweepIsSkippedOnlyWhereTheChangeTouchesNoTypo3Api`
- `SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks`
- `SkillTest::theInstalledSourceIsTheStepAfterTheLookupsAndItsAnswerIsVersionBound`
- `SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`
- `SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk`
- `InstallerTest::codexInstallAndUpdatePreserveConfigurationAndTrackTheirSkillsCentrally`
