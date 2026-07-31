---
date: 2026-07-31T12:45:00+02:00
category: idea
status: open
tool: typo3_project_scope, typo3_task_guide
---

# A review reads the checks it never runs

## Observation

Across all three recorded `REVIEW-02` runs — bootstrap_package at 02:55 and
08:15, syntax at 12:21, two different repositories — not one project-owned
command was executed. Not `composer phpstan`, not `composer test:php:unit`, not
`cgl:ci`, not `phplint`. `typo3_project_scope` returned ten commands in the
first repository and five in the second, and in both cases the commands were
quoted as subjects of findings rather than run.

That the answers did not say so is settled: `skills/base.md` now names the three
things a finding can rest on and requires the finding to say which. What is not
settled is whether a review should have run those commands at all. `cgl:ci` and
`phplint` change nothing and would have turned two of the syntax findings from
derived into established — against that, a command that fails tells you less
than the configuration that would make it fail, and a review is asked not to
change files.

The same three runs leave a second kind of evidence ungathered. DDEV was up and
reachable in the extension checkout every time, and in three sessions no
question arose that needed it: of the 15 server calls in the last run, none
reached the installation. The console half of this server is exercised only in
`E-SITE`. That is how the stdin defect fixed in 88b7bb4 survived — the first
extension review to call `typo3_icon_lookup` and `typo3_label_lookup` is the one
that hit it.

## Query

Review this TYPO3 extension. Tell me the most important things that would
prevent us maintaining and supporting it confidently, in priority order. Do not
change files.

## Suggestion

Decide whether a review-only task runs the project's own read-only checks, and
if it does, which of the returned commands qualify — the answer is a property of
the command, not of the task, and nothing in `typo3_project_scope` says today
which of the ten it returns write anything. The other half of this note is
worked off: `skills/base.md` carries the distinction, held by
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn` and `R-SKL-5`.

Ungathered alongside it, and the same question one layer down: the console half
of this server, which no extension review has reached for.
