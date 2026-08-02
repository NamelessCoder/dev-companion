---
name: typo3-core-patch-review
description: Review a TYPO3 core patch and say what is wrong, missing, or not ready for review, in priority order — your own change before you push it, or somebody else's patch set. Use for the current changes in a core checkout, a commit, a branch against the branch it targets, or a change fetched from Gerrit: what the diff removes or renames, whether the tests and the changelog entry it owes are there, whether the commit message, the issue reference and the target branch are right, and which of the project's own checks would have to pass. Reviewing an extension, a sitepackage or a site project is a different workflow and belongs to the conformance skill.
---

# TYPO3 Core Patch Review

Review one patch against the checkout it sits in, and report in priority order.
Keep this skill as routing and review method; the contribution rules, the
suites and the commit-message rules are lookups, and a copy of them here is one
that cannot be corrected.

## Establish the patch, then the rules it is judged by

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result: a rule fetched after the diff has been read confirms a reading
   instead of testing it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, and the severity rubric.
3. Establish the patch itself. The server does not read your working tree, and
   what it needs from you is exactly what a patch is: **the changed paths**,
   **the branch it targets**, **the commit message**, and **the issue it names**.
   One reading of the diff produces all four, and every lookup below takes one
   of them as its argument. A review that has not established the target branch
   is reviewing against the wrong conventions and cannot tell.

The changed paths are the argument, not the subject. Pass them to
`typo3_architecture_lookup` for the conventions of the subsystem the patch is
in, one call per subsystem, before forming a view of whether the code is right.

## What the patch owes, per finding

Ask the owner of each obligation rather than recalling it:

- `typo3_rule_lookup` for the contribution rules the diff makes relevant —
  what a breaking change owes, what a deprecation owes, what belongs in a
  changelog entry, and what review readiness means. Ask it per obligation and
  not once: the sections it returns are named by subject, and a query that
  names two reaches neither.
- Enumerate what the diff **removes or renames** before asking. A public class,
  method, property, constant, TCA field, TypoScript path or Fluid ViewHelper
  argument that disappears is the finding class this review exists for, and it
  is the one a reading of the new code does not surface — the evidence is in
  what is gone, not in what is there.
- `typo3_changelog_lookup` for the precedent, when the patch does something the
  core has done before. What an earlier entry required of the same kind of
  change is the strongest argument a review can make, and it is also the one
  that settles disagreement without an appeal to taste.

Every finding names the changed path it is about. A statement about the
subsystem that does not tie to a line in this diff belongs in the issue, not in
a review of a patch.

## Verification is the project's own, and it is narrowed by the diff

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change and the targeted invocation for each. That is the verification a
review proposes: the narrowest applicable suite first, the broader ones named
after it.

The core's suites are not among the commands `typo3_project_scope` declares —
that answer is about the repository's own composer scripts, and the test runner
is a script rather than one of them. Take the commands from
`typo3_test_run_guide` and `typo3_script_lookup`, never from memory and never
from the host's own PHP: a check run outside the project's runner is evidence
about your machine.

A review may run what cannot change the code, and says what it ran and what it
printed. Anything it did not run stays labelled as available rather than
passing. "The tests would presumably still pass" is not a review sentence.

## Commit shape and target branch

`typo3_commit_message_guide` with the message and the change type says whether
the message is submittable. Read its answer against the diff rather than on its
own: the subject that describes the wrong action, the missing issue reference,
the marker a breaking change needs and this one does not carry.

The branch the patch targets decides which conventions apply and which findings
matter, so a patch whose target is stated and whose diff does not fit it is a
finding of its own.

## Report

Order by what stops the patch, and say why each one stops it:

1. what blocks the patch from being submitted at all;
2. what a reviewer would send it back for;
3. what is worth changing and would not block it;
4. what was checked and is correct — briefly, so a silent surface and a verified
   one are not read alike.

Close on the checklist's surfaces with each one marked assessed, unassessed or
not applicable to this diff. A review that reports only findings cannot be told
apart from one that looked at less.

This skill owns the review of a core patch and the order its findings are
reported in. It does not change the patch: a review that rewrites what it
reviews has destroyed the evidence for its own findings, and the request was for
a reading. Where the answer is that the patch needs work, name it and stop —
`typo3-core-patch-development` owns making the change, the changelog entry, the
tests it needs and the push, and it takes the findings across as its work list.
Reviewing an extension, a sitepackage or a site project belongs to
`typo3-extension-conformance` and its checklist, which reads different surfaces
against different rules.
