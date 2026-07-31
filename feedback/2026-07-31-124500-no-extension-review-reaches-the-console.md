---
date: 2026-07-31T12:45:00+02:00
category: idea
status: open
tool: typo3_icon_lookup, typo3_label_lookup, typo3_configuration_lookup
---

# No extension review has reached the console half of this server

## Observation

Trimmed on 2026-07-31 to the half nothing has taken on. What this note also
said — that three recorded `REVIEW-02` runs quoted the project's own commands
instead of running them, and said nowhere in their answers that they had — is
worked off: `skills/base.md` names the three things a finding can rest on and
requires the finding to say which, and `typo3_project_scope` now reports per
command whether running it hands the code back as it was, so the checks are run
and the rest is not (`R-SKL-5`, `R-PRJ-7`, `D-EVI-3`).

What is left is the evidence those same runs did not gather. DDEV was up and
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

Decide whether a review of an extension is supposed to reach the installation at
all. Every console-backed tool is a defect surface that only `E-SITE` exercises,
and the review scenario that runs twice as often is the one that never touches
it — so either `REVIEW-02` is the wrong place to expect it and something else
has to cover the console, or the extension review has a reason to ask the
installation a question and does not know it yet.
