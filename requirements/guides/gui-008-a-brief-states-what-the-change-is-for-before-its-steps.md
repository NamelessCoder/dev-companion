---
id: R-GUI-008
status: held
restsOn: [D-GUI-005]
---

# R-GUI-008 — A brief states what the change is for before its steps

**Every task brief opens on the premise: content changes, so what is delivered
has to be the current version, and a defect is judged by that outcome.**

The steps under it are all about the code, and which of them matter is decided
by what the change does to the editor and the visitor. A brief that starts at
"confirm the branch" answers a bug report by whether the code was used
correctly, which is a question with its own consistent answers and not the one
the product asks.

## From

A core session on Forge #105403 that assessed the report as an API question —
whether the value passed to `f:image` is of the type the argument accepts — and
committed a clearer exception for it, where the defect was that an editor
replacing the image went on being served the old one. The same premise was
missing from three further turns the user corrected
(`feedback/2026-08-02-145043`, 2026-08-02).

## Held by

- `HintsTest::everyBriefOpensOnThePremiseADefectIsJudgedBy`
