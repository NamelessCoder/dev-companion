---
id: R-KNW-057
title: 'The push a session cannot take back is answered in full'
status: held
restsOn: [D-SKL-005]
heldBy:
  - KnowledgeTest::theUnlistedPushIsAnsweredBesideTheOneThatPublishes
  - KnowledgeTest::theWriteDirectionIsAnsweredAroundThePushItself
---

# R-KNW-057 — The push a session cannot take back is answered in full

**The corpus answers the push itself: which form it takes, where the checkout
sends it, and what the issue behind it has to be.**

Which form it takes is a visibility no second push undoes, so the answer says
who can then see the change rather than only how the option is typed. A git
worktree changes none of the three, and the corpus says so, because that is the
doubt a session raised at the moment of pushing.

Everything before the push is local and reversible. The push is neither, and it
is the one step of a core patch where what a session has to choose is a
visibility it cannot undo by trying again. `typo3-core-patch-development` makes
the choice mandatory and routes it here — *"`typo3_rule_lookup` for the Gerrit
workflow has both forms"* — so an answer carrying only the form that publishes
is worse than no answer: the caller was told the other one is in it.

The other three are the same step read from the other side. Where a push lands
is a property of the checkout rather than of the project's name, and a session
can only read it; whether the refspec still holds from a worktree is the doubt a
user raised outright at the moment of pushing; and a change hanging off a closed
report asks reviewers to reverse a decision nothing in the change mentions.

## From

A core patch session on Forge #105403, pushing to Gerrit as a private change and
establishing every mechanical fact of the push from the checkout or from its own
knowledge — `feedback/2026-08-02-144848`, which
[`D-SKL-005`](../../decisions/task-skills/skl-005-core-contribution-earns-two-task-skills.md)
trimmed to these four on 2026-08-03 after re-running the rest against the server
(2026-08-02).
