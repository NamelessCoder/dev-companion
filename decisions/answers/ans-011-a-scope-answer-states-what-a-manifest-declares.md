---
id: D-ANS-011
date: 2026-08-02
status: open
---

# D-ANS-011 — A scope answer states what a manifest declares, and the comparison is the audit's

**`typo3_project_scope` and `typo3_extension_scope` each state what one manifest
declares; comparing two of them is the audit's work, and no tool here judges
whether they agree.**

A conformance review reports a version mismatch, and both numbers come from
answers this server already gives. What it does not give is the verdict that the
two disagree.

## Evidence

- `feedback/2026-07-31-190653`, re-run on 2026-08-02 through
  `bin/typo3-cms-mcp` from `/home/benji/projects/site-new`, the directory it was
  written in. `typo3_project_scope` opens with "composer-project, TYPO3 14.3.5,
  PHP ^8.4". `typo3_extension_scope` with `printworks_sitepackage` carries
  "Requires: php ^8.3, typo3/cms-core ^14.3" and, on its own line, "Ships:
  manual none, readme none, tests Functional+Unit".
- Both things the feedback records as established elsewhere were in answers it
  says it already had. It lists both calls as made, then reports reading
  composer.json for the PHP constraint and being surprised by the absent manual
  and README.
- Neither field arrived since. `requires` is in the extension answer from
  `9e06675` (2026-07-29 16:51), `artifacts` from `fc80db8` (2026-07-31 02:08),
  and `main` stood at `77cd0e7` (18:42) when the report was written at 21:06
  local. Both are ancestors of `main`, and `.mcp.json` in that project names
  `/home/benji/projects/typo3-cms-mcp/bin/typo3-cms-mcp`.
- What no answer states is that `^8.3` and `^8.4` disagree.
  `feedback/2026-07-31-193611` is that boundary from the other side: same
  directory, half an hour later, it compared the extension's declared constraint
  against the host's PHP 8.3.23 and reported "PHP version mismatch blocks all
  tests", which the DDEV container the suite runs in makes false.
- The comparison is already somebody's. `skills/typo3-extension-conformance/references/checklist.md`
  opens its surfaces with "Package: identity, Composer constraints, autoloading,
  extension metadata, and supported TYPO3/PHP range", and the skill states that
  it owns assessment and prioritization.

## Decided

- The feedback is closed by this commit. Both costs it reports are answered by
  the server as it stands and as it stood, so there is nothing to queue and
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes that the close answer rather than a special case.
- The strength half is read as evidence about a boundary, not as a confirmation
  of the conformance skill, which is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
  Nothing was added to `D-SKL-001` or `D-SKL-002`.
- The boundary stays where it is. A tool that reported two declarations as
  disagreeing would be judging rather than answering, `validate` is not one of
  the five verbs in [AGENTS.md](../../AGENTS.md), and the checklist surface above
  is where that judgement is already owed.
- The runtime half is named and not filled here. What PHP the container actually
  runs is what `feedback/2026-07-31-193611` asks for, it has a card of its own,
  and answering it from this run would be the copy-down
  [judging.md](../../documentation/feedback/judging.md) warns produces a guess
  with a reading's authority.

## Assumed

- That the session called the server this checkout builds. The two commits are
  on `main` and predate the report, and nothing records what that working tree
  held at 21:06.
- That one session wrote it. The report credits the server with the PHP finding
  and reports the same finding as read from a file, which is one account rather
  than two runs — and which of the two happened is what
  [judging.md](../../documentation/feedback/judging.md) declines to assess.

## Wrong if

- A session holding both answers still reports a mismatch it read out of a file.
  The two lines would then be delivered and not taken, which is step 4 of the
  ladder and a rewrite rather than a close.
- A recorded run of the conformance skill reaches the Package surface and
  produces no comparison of the declared ranges. The surface would then own the
  judgement in prose only.
- `feedback/2026-07-31-193611` is judged and lands somewhere other than declared
  against effective. The pairing above would then be a reading of two files
  rather than a property of these answers.

## Since then

`feedback/2026-07-31-193109` was judged on 2026-08-02 and is a second sighting of
the same pairing from the same directory 25 minutes later. Its fourth cost
credits `typo3_project_scope` with `^8.4` and reports 8.3.23 as read by bash, so
what it compares is a declaration against an effective runtime rather than two
declarations — which is where the third **Wrong if** expects the other feedback
to land, arrived at independently. Nothing here changed: the runtime half stays
with `feedback/2026-07-31-193611`, and answering it from this reading would be
the copy-down that entry already declines. The rest of that feedback is
[`D-ANS-015`](ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md).
