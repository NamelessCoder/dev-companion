---
id: R-SKL-012
title: 'A finding is attributed to the change under review'
status: held
restsOn: [D-SKL-007]
heldBy:
  - SkillTest::aFindingSaysWhetherThePatchIntroducedIt
---

# R-SKL-012 — A finding is attributed to the change under review

**Every finding says whether this patch introduced it, and a patch that is one
of a set is read against the state at the end of the set.**

A finding about a line the diff only moved past sends the author to repair
something they did not change, and nothing in the report says it was not meant
to. What the patch did not introduce stays in the review where it blocks
submission on its own and goes to the issue tracker otherwise. What a later
patch in the same set removes is not a defect of the set, and establishing that
is a reading of that patch rather than of what a message promises about it.

Reachability is the other half of the same question and moves in one direction
only: what a diff shows about who reaches a path may raise a rank and never
lower one, because reachability is what a diff establishes worst.

## From

The second recorded `REVIEW-03` run attributed twice without being asked to, and
both were load-bearing. It established that the parsing the patch adds already
exists one line up and is byte-identical on `origin/14.3`, which turns the
finding from "the patch parses TSConfig" into "the patch makes an already-parsed
flag take effect" — a different report to the author. And it marked the
empty-tab finding "cosmetic and new" rather than inflating it. Nothing in the
skill asked for either, so nothing makes the next run do it.

`D-SKL-007` records where the general form was read and what was rejected with
it.
