---
name: typo3-core-patch-review
description: 'Review a TYPO3 core patch — your own before you push it, or somebody else''s patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/benjaminkott/typo3-dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Review

Review one patch against the checkout it sits in, and report in priority order.
Keep this skill as routing and review method; the contribution rules, the suites
and the commit-message rules are lookups, and a copy of them here is one that
cannot be corrected.

## Establish the patch, then the rules it is judged by

1. Work through [references/base.md](references/base.md). It fixes the order
   every task here starts in, and a review is where that order decides the
   result: a rule fetched after the diff has been read confirms a reading
   instead of testing it.
2. Read [references/checklist.md](references/checklist.md) for the review
   surfaces, what a finding owes, what a dropped candidate owes, and the
   severity rubric.
3. Establish the patch itself. The server does not read your working tree, and
   what it needs from you is exactly what a patch is: **the changed paths**,
   **the branch it targets**, **the commit message**, and **the issue it
   names**. One reading of the diff produces all four, and every lookup below
   takes one of them as its argument. A review that has not established the
   target branch is reviewing against the wrong conventions and cannot tell.

The changed paths are the argument, not the subject. Pass them to
`typo3_hint_lookup` for the conventions of the subsystem the patch is in, one
call per subsystem, before forming a view of whether the code is right.

## What the project already says about this patch

The message names two things the diff does not contain — the issue it resolves
and the change it is — and both are read before the code is read a second time.

- `typo3_forge_lookup` with the issue number. What the change is *for* belongs
  to the issue, and a review that takes it from the commit message has the
  author's account of the problem and of the solution. It is also where a series
  says it is one: an issue calling itself a part tells you the patch is not
  meant to stand alone, which decides every finding about what is missing from
  it.
- `typo3_gerrit_lookup` with the `Change-Id` the message carries. It answers
  whether the change is on the review server, at which patch set and with which
  commit, against which branch and in what state. A comment somebody left on an
  earlier patch set and nobody answered is a finding of its own, and it is the
  one this review would otherwise make a second time.

**Both arguments come out of the commit message, and that is what makes them
safe.** `Resolves:` is the Forge issue, `Change-Id:` is the change, and the
`Change-Id` still names it after an amend. A number carried in from elsewhere
does not fail: asked for under the other's name both lookups answer, with a real
change and a real issue belonging to neither this patch nor each other. So the
check is the subject — what comes back carries the subject of the commit under
review, or the number was wrong rather than the patch.

A patch that is not pushed yet has no change, and an answer of nothing is a
result: say so rather than leaving the surface silent. Where the commit in the
checkout and the change on the server differ, name which of the two was read —
reviewing an older patch set than the one that exists is the failure this step
is here for, and the checkout cannot report it. The answer carries the commit
the current patch set is: hold it against `git rev-parse HEAD`, and where the
two differ say which one the findings are about.

Reading is the whole of it. Voting, commenting and uploading stay with the
person doing the review.

## What the patch owes, per finding

Ask the owner of each obligation rather than recalling it:

- `typo3_rule_lookup` for the contribution rules the diff makes relevant — what
  a breaking change owes, what a deprecation owes, what belongs in a changelog
  entry, and what review readiness means. The sections are named by subject, so
  ask in the words of a subject rather than in a sentence about the patch.
  **Obligations that share a document are one call.**
  `breaking change changelog entry` returns both sections whole, and asking the
  two separately returns the same pair twice. Length is the limit rather than
  the count. Coverage is measured against the query's own words, so a query
  naming four obligations dropped the deprecation section that one subject alone
  returns. A genuinely different subject — testing, code style, the Gerrit
  workflow — is a call of its own.
- Enumerate what the diff **removes or renames** before asking. A public class,
  method, property, constant, TCA field, TypoScript path or Fluid ViewHelper
  argument that disappears is the finding class this review exists for, and it
  is the one a reading of the new code does not surface — the evidence is in
  what is gone, not in what is there.
- `typo3_changelog_lookup` for the precedent, when the patch does something the
  core has done before. What an earlier entry required of the same kind of
  change is the strongest argument a review can make, and it is also the one
  that settles disagreement without an appeal to taste.

  **Ask it in the words the entry is titled in, not in the identifier the diff
  removes.** What the enumeration above leaves you holding is a class and a
  method name, while a removal is titled after what was removed *about* — the
  subsystem, the kind of API — and carries the identifiers in a list inside the
  file. So a query naming one of them and coming back empty has established
  nothing, and neither has one narrowed to the branch this patch targets: a
  precedent is filed under the version it landed in, which is an earlier one by
  definition. Where the subject words miss as well, the precedent is still there
  and the checkout is what holds it — `Documentation/Changelog`, which this
  server does not read and you do. Say which of the two answered.
- **Sweep the checkout for the call sites before proposing an alternative.** A
  recommendation to a core reviewer needs precedent rather than taste, and
  whether an idiom is established in the core is precedent this server does not
  hold. The base's step after the lookups starts at the class that implements a
  behaviour; this question has none, and PHP source as code is outside what this
  server reads. The checkout answers it, and the answer is the call sites at
  their paths and lines. Say how many there are — one is a coincidence and a
  spread across system extensions is a convention. A review that proposes an
  alternative and names none has argued from taste.

Every finding names the changed path it is about. A statement about the
subsystem that does not tie to a line in this diff belongs in the issue, not in
a review of a patch.

Where the patch is one of a set, a finding is read against the state at the end
of the set before it is reported. What a later patch in the same set removes is
not a defect of the set, and establishing that is a reading of that patch rather
than of what a message promises about it. It is still reported where each patch
has to stand on its own, and it names the later one that settles it.

## Verification is the project's own, and it is narrowed by the diff

`typo3_test_run_guide` with the changed paths returns the suites that can fail
on this change and the targeted invocation for each. That is the verification a
review proposes: the narrowest applicable suite first, the broader ones named
after it.

The core's suites are not among the commands `typo3_project_describe` declares —
that answer is about the repository's own composer scripts, and the test runner
is a script rather than one of them. Take the commands from
`typo3_test_run_guide` and `typo3_script_lookup`, never from memory and never
from the host's own PHP: a check run outside the project's runner is evidence
about your machine.

A review may run what cannot change the code, and says what it ran and what it
printed. **It then writes out, by name, the suites on that list it did not
run.** Leaving them out is what makes four green suites read as a finished
verification, and that is the claim a review is least able to support. "The
tests would presumably still pass" is not a review sentence, and an unnamed
suite is the same sentence with the words taken out.

**A scratch probe is one of the things it may run.** Add a temporary fixture
column, a model property or a test of your own, run a targeted suite against it,
read what it prints, and put the tree back — `git checkout --` on what you
touched, then `git status` to confirm it is clean. The patch under review is not
edited, which is the boundary that matters; a probe writes files and restores
them, and the restoration is verified rather than assumed. This is what turns
"this would presumably throw" into a pasted error, and dropped-candidate
findings are where it earns most: what disproves a path is what makes it
impossible, and a probe is often the only thing that can.

## Commit shape and target branch

`typo3_commit_message_guide` with `workflow="core"`, the message and the change
type says whether the message is submittable. Without that argument it checks
the message as a repository of its own and asks for no Forge issue. Read its
answer against the diff rather than on its own: the subject that describes the
wrong action, the missing issue reference, the marker a breaking change needs
and this one does not carry.

The branch the patch targets decides which conventions apply and which findings
matter, so a patch whose target is stated and whose diff does not fit it is a
finding of its own.

## Report

Order by what stops the patch, and say why each one stops it:

1. what blocks the patch from being submitted at all;
2. what a reviewer would send it back for;
3. what is worth changing and would not block it;
4. what was checked and is correct — briefly, so a silent surface and a verified
   one are not read alike;
5. what was raised while reading and dropped, with what dropped it.

Close on the checklist's surfaces with each one marked assessed, unassessed or
not applicable to this diff. A review that reports only findings cannot be told
apart from one that looked at less.

## Where the review ends and the rework begins

The reader accepting your findings and asking for the change is the end of this
skill's work, and it looks like nothing at all from the inside — a sentence in a
conversation, in the middle of a session that is going well.

**At that sentence, invoke `typo3-core-patch-development` and work from it.**
That includes the amend and the push. A session that carries on under review
rules is holding "it does not change the patch" while changing the patch, and
one did: it edited `ColumnMap.php`, added a fixture column, wrote a functional
test, ran seven suites and amended the commit, all still inside this skill.
Nothing broke and the tree stayed clean, which is why nothing marked the
crossing.

Until that sentence, the rule above stands whole: a review that rewrites what it
reviews has destroyed the evidence for its own findings. Where the answer is
that the patch needs work, name it and stop.

This skill owns the review of a core patch and the order its findings are
reported in. Reviewing an extension, a sitepackage or a site project belongs to
`typo3-extension-conformance` and its checklist, which reads different surfaces
against different rules.
