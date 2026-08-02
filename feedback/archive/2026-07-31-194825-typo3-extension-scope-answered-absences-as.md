---
date: 2026-07-31T19:48:25+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3_extension_scope
directory: /home/benji/projects/site-new
---

# An extension that ships no translations is the one absence the text does not state

## Observation

Trimmed on 2026-08-02 to the half the praise does not cover. Three of the four
things this report credits are held and reproduce: the `answeredBy` attribution
is `R-ANS-001`, the commands `typo3_project_scope` lists are `R-PRJ-007`, and the
manual, README and test layers are answered present or absent in one `Ships:`
line. Re-run from `/home/benji/projects/site-new` against
`printworks_sitepackage`, that line is `Ships: manual none, readme none, tests
Functional+Unit`, with the three XLF files below it at `source-language de`.

The property the report names — an absence answered rather than left to be
found — holds for three of the four artifacts and not for the fourth. The
language files are rendered only where there are some. Run against
`rte_ckeditor` in `.checkouts/14.3`, which ships none, the data carries
`languageFiles: []` and the text goes from the `Ships:` line straight to the
boundary paragraph. Nothing in the primary answer says the extension ships no
translations, which is the one reading the report was praising.

`R-PRJ-006` names the XLF files among the four whose absence is answered, and
the test holding it asserts the present case in both halves of the answer and
the absent case in the data alone. `D-FBK-018` has the readings.

## Query

typo3_extension_scope extension="printworks_sitepackage"; typo3_project_scope

## Suggestion

Say the absence of a translation in the text the way the absence of a manual is
already said, so a caller that renders the prose can tell "ships none" from "not
part of this answer".
