---
id: D-KNW-109
title: Whether a core commit owes a sign-off is a subject this server owns
date: 2026-08-24
status: revoked
revokedBy: D-KNW-110
---

# D-KNW-109 — Whether a core commit owes a sign-off is a subject this server owns

**`core/contribution/commit-messages` states which of the core's own sources
asks for the `Signed-off-by:` trailer, because its agent instructions demand it
and one merged commit in a hundred carries it.**

The page lists the trailers through `Change-Id:` and stops. A reviewer holding a
patch without the trailer has to decide whether that blocks submission, and the
three sources that answer disagree with each other.

## Evidence

- `bin/cli hints:probe "signed-off-by"` reaches nothing: 101 hints were
  candidates and none matched. A search of `knowledge/` and `skills/` for the
  trailer, the DCO and `git commit -s` returns hits in `feedback/` and `todo/`
  and nowhere else.
- Read in `.checkouts/main` at `9dd4e1bfd7`, dated 2026-08-23 and fetched for
  this judgement because the worktree stood at 2026-08-18 and `AGENTS.md` had
  landed in between. The core's own `AGENTS.md` says "Sign off every commit —
  `git commit -s` appends the `Signed-off-by:` trailer, or set
  `git config format.signOff true`", certifies it against the Developer
  Certificate of Origin, and its footer example carries the trailer between
  `Releases:` and `Change-Id:`.
- `Build/git-hooks/commit-msg` is the other half of that sentence. Line 44 is
  `/^Signed-off-by:/d`, over the copy the `Change-Id` is hashed from, so the
  trailer neither disturbs an existing `Change-Id` nor is checked by anything
  the hook validates.
- `CONTRIBUTING.md` at the same revision says nothing about signing.
- 5 of the last 500 commits on `origin/main` carry the trailer — one in a
  hundred.
- Three sessions from `/home/benji/projects/typo3-cms` on 2026-08-24 arrived at
  it. `feedback/2026-08-24-110851` settled it from the checkout by hand to rank
  a missing trailer as worth changing rather than blocking;
  `feedback/2026-08-24-133602` had two drafts carrying the trailer struck by the
  user; `feedback/2026-08-24-133515` names those same two strikes among the
  costs of a patch session that called this server for nothing.

## Decided

- Written, and into `core/contribution/commit-messages` beside the trailers it
  already lists rather than as a hint. The question arrives while a footer is
  being written, and that page is what answers a footer.
- What it states is which source asks for it rather than one rule. The core's
  `AGENTS.md` demands it, the hook checks it against nothing, and the merged
  history barely carries it; all three hold at once, and a caller holding only
  the first emits a trailer a maintainer strikes.
- `git commit -s` and `format.signOff` are named with the hook's own treatment
  of the `Change-Id`, because that is the fear that keeps a contributor from
  adding the trailer to an amend.
- The reading above is the section's evidence, so the work verifies rather than
  establishes. What is still owed is the official Contribution Guide, which was
  not read here.
- Not what `typo3_commit_message_guide` returns. `feedback/2026-08-24-133602`
  asks for that, its lever is the guide's footer answer rather than this page,
  and it is unjudged with its own card standing.

## Assumed

- That the core's `AGENTS.md` stays. It was days old at this reading, and a
  statement about it is a statement about a file one commit removes.
- That the share stays low. One in a hundred over 500 commits is what makes "not
  enforced" the honest reading, and a project that starts enforcing it turns the
  section round.

## Wrong if

- The section is written from `AGENTS.md` alone and a caller emits a trailer a
  reviewer strikes. That is `feedback/2026-08-24-133602` happening again with
  this server's answer behind it.
- A caller reads the section as leave for omitting the trailer and a core
  maintainer asks for it. Then the practice is not what the merged history
  counts.
- Gerrit or the hook starts requiring it. Then it is a rule, and the section is
  a paragraph about a conflict between sources that no longer exists.
- The trailer turns out to be asked for outside the two files read here — the
  Contribution Guide, a review convention nobody wrote down. Then the section
  names a smaller set of sources than the question has.

## Since then

The section is written, and the Contribution Guide was read first because that
is what **Decided** left owed. It asks for nothing. Its commit message appendix
lists `Resolves:`, `Related:`, `Releases:`, `Depends:` and `Change-Id:` and
stops there; the Git setup page names every config a contributor sets and not
`format.signOff`; the commit hook appendix describes the hook without the
trailer; the page that walks through writing a patch commits with
`git commit -a`; and the guide read whole as one document on 2026-08-24 carries
none of "Signed-off-by", "sign off", "commit -s", "format.signOff", "Developer
Certificate of Origin" or "DCO". So the first half of the last **Wrong if** is
answered and the second is not: a review convention nobody wrote down is still
what could turn this round, and only a maintainer asking for the trailer would
show it.

`typo3_documentation_lookup` is not what read it, though the todo asked for it.
That tool indexes four manuals — TYPO3 Explained, TypoScript Explained, the TCA
Reference and the Fluid ViewHelper Reference — and the Contribution Guide is
none of them, so the guide was fetched page by page instead.

The **Evidence** above was verified again at `9dd4e1bfd7` in the bare repository
below `.checkouts/`, and every line of it holds: the `AGENTS.md` sentence and
its footer example, the silence of `CONTRIBUTING.md`, `/^Signed-off-by:/d` on
line 44 of `Build/git-hooks/commit-msg`, and 5 of the last 500 commits on
`main`. The entry stays open on its two assumptions, which are about what the
core does next rather than about what it does.

## Revoked on 2026-08-24

The maintainer settled the question the same day: the sign-off is not set on a
core patch, and neither is an agent's own attribution trailer. So the statement
above describes a section that no longer exists — what the page states is the
rule, and the sources this entry weighed stand under it as why nothing enforces
it. `D-KNW-110` is what a reader builds on, and the evidence here is what it
rests on.

The first **Wrong if** fired in the direction it did not name. It watched for a
section written from `AGENTS.md` alone; what happened is that naming every
source left the caller to choose, and the choice is what the two struck drafts
were.
