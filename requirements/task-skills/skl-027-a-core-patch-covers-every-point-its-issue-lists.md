---
id: R-SKL-027
title: A core patch covers every point its issue lists
status: held
restsOn: [D-SKL-075]
heldBy:
  - SkillTest::aPatchCoversEveryPointItsIssueLists
---

# R-SKL-027 — A core patch covers every point its issue lists

**A core patch covers every point its issue lists, or each point it leaves is
given an issue of its own before the code is written.**

The points are enumerated while the issue is assessed, because a comment
regularly names more of them than the subject does. The split is decided there
too, since each part needs a number of its own — the `Resolves:` trailer and the
changelog file name both take one, and neither can be supplied to a patch that
is already written.

Dropping a point instead is invisible from outside the session: the trailer
closes the issue on every point it names, and nobody reopens a closed issue. So
a point that is riskier to change is an argument for giving it its own issue
rather than for leaving it out.

This is
[R-SKL-016](skl-016-the-assessment-before-a-core-patch-reads-the-issue-and-the-review-server.md)
read one step further into the same answer. That one has the notes read for the
status, the relations and the maintainer's reason; the fourth thing they carry
is what the issue requires.

## From

`feedback/2026-08-24-162543` (2026-08-24), a session on Forge #106584 with the
skill active. The subject names two ViewHelpers and a comment names three, the
session read all three and shipped two, and it reported the third as a follow-up
needing its own issue. The user corrected it, which is the only place the rule
came from.
