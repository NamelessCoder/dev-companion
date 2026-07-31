---
date: 2026-07-31T12:45:00+02:00
category: idea
status: open
tool: typo3_project_scope, typo3_task_guide
---

# A review reads the checks it never runs, and does not say so

## Observation

Across all three recorded `REVIEW-02` runs — bootstrap_package at 02:55 and
08:15, syntax at 12:21, two different repositories — not one project-owned
command was executed. Not `composer phpstan`, not `composer test:php:unit`, not
`cgl:ci`, not `phplint`. `typo3_project_scope` returned ten commands in the
first repository and five in the second, and in both cases the commands were
quoted as subjects of findings rather than run.

That is defensible on its own: a review is asked not to change files, and a
command that fails tells you less than the configuration that would make it
fail. What is not defensible is that the answers do not say it. In the syntax
run, finding 1 asserts that CI "proves nothing beyond the files parse" and
finding 7 asserts a deprecation "fires on every v14 installation" — both read
out of configuration and both correct, but presented with the same confidence as
the eleven findings that carry a verified path and line. A reader cannot tell
which findings were established and which were derived.

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

`skills/base.md` already fixes the order a task starts in. Add to it the
distinction the answers are missing: a finding rests on a path that was read, on
a command that was run, or on a mechanism traced into an installed package, and
which of the three it is belongs in the finding. A review that runs nothing is a
legitimate review; one that does not say so is a review whose weakest finding
reads like its strongest.

Worth deciding separately, and not obviously yes: whether a review-only task
should run the project's own read-only checks at all. `cgl:ci` and `phplint`
change nothing and would have turned two of the syntax findings from derived
into established.
