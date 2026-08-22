---
id: D-SKL-040
title: 'A skill whose product is a report says it is a file'
date: 2026-08-14
status: revoked
revokedBy: D-SKL-042
---

# D-SKL-040 — A skill whose product is a report says it is a file

**A skill whose product is a report says that the report is a file, what it is
named and where it goes.**

The answer then carries a short summary and that path. Three skills specify a
report exhaustively — its bands, what each finding owes, the surfaces it closes
on — and none of them says that what it specifies is a document, so the session
does the one thing nothing told it not to and prints it into the chat.

## Evidence

- `feedback/2026-08-13-214811-the-review-skill-specifies-the-order-of.md`: a
  review of Gerrit change 93319 patch set 21 under `typo3-core-patch-review` ran
  to a couple of hundred lines with a table and went into the chat. The user
  corrected it twice — "reviews sollten immer im markdown format ausgegeben
  werden", then "damit es kopierbar ist" — and the session wrote
  `review-93319-ps21.md` afterwards.
- Read again on 2026-08-14: `## Report` in
  `skills/typo3-core-patch-review/SKILL.md` fixes five severity bands and closes
  on the checklist's surfaces, `## Report` in
  `skills/typo3-extension-conformance/SKILL.md` orders findings and closes on
  coverage, and *Say what the triage found* in
  `skills/typo3-core-issue-triage/SKILL.md` routes to the verdicts. None of the
  three names a form, a file or a path.
- The skill is what makes the report long. Five bands, the surface list and the
  dropped candidates cannot be short, so the deliverable was specified and its
  form was left to whatever the session did by default.
- `skills/typo3-core-patch-review/references/checklist.md` reports what is
  modified or untracked beside the commit as a review surface. So the checkout
  under review is the one place the file may not be written, and the feedback's
  second point holds as read.
- `bin/cli feedback:list` on 2026-08-14: 12 open, 10 of them out of one core
  checkout. This is the only feedback there — open or archived — that reports
  the form of a report rather than its content.

## Decided

- Queued at step 4 of the ladder. The section that specifies the report was read
  and followed, and it does not say that what it specifies is a document; the
  fix is a rewrite of that section rather than anything missing from
  `knowledge/`.
- Not closed on the spot. Three published skill bodies move, and a body is a
  copy in somebody else's project that no release of this server corrects.
- `normal` rather than `low`. One session reported it, and the correction came
  from the user twice on a deliverable this skill's own design makes long. Not
  `high`: what it cost was one answer given again.
- What the three skills say is the todo's: that the report is a file, named
  after its subject including whatever separates two reports of the same one,
  and written where the assessed checkout is not.
- Rejected: a statement in `knowledge/`. No caller asks for it, and it is a rule
  about how the skills here are written.

## Assumed

- All three of those workflows end in a document somebody carries elsewhere.
  Which skills those are is read off the bodies rather than off a field, as with
  the sides a description names.
- The session can write a file at all. Where a client cannot, the report stays
  in the answer, and the path is the caller's to name in any case.

## Wrong if

- A review under the rewritten skill still prints its report into the chat. Then
  the gap was behaviour rather than wording and the rung was the wrong one.
- A user asks for the report in the answer rather than at a path, which would
  make summary-plus-path the cost instead of the fix.
- One of the three turns out to produce something short enough that a file is
  ceremony. A triage of a single issue is the candidate.

## Revoked on 2026-08-14

Asked the same day it was written, the maintainer answered that the report may
stay in the chat: what it has to be is copyable, and formatted HTML is what
cannot be transferred. The second **Wrong if** above is the one that held, and
it held within the hour. So the statement names one way of being copyable as the
requirement, and `D-SKL-042` carries the property instead.

What this entry read stands — the three sections specify a report exhaustively
and name no form — and it is the reading `D-SKL-042` is built on.
