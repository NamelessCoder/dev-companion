---
id: D-KNW-110
title: A core commit message carries three trailers and the hook's Change-Id
date: 2026-08-24
status: open
coveredBy:
  - CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet
  - KnowledgeTest::theTrailerAnswerStatesTheRuleAndWhatLeavesItUnenforced
---

# D-KNW-110 — A core commit message carries three trailers and the hook's Change-Id

**`Resolves:`, `Related:`, `Releases:` and the `Change-Id:` the hook writes are
what a core commit message carries, and `typo3_commit_message_guide` takes any
other trailer off a `workflow="core"` draft.**

`D-KNW-109` named which source asks for the `Signed-off-by:` trailer and left
the caller to weigh them. The weighing is what produced the two struck drafts
that `feedback/2026-08-24-133602` reports, so the answer this server owes is the
rule rather than the conflict behind it.

## Evidence

- The maintainer ruled it on 2026-08-24, in the session that recorded this: the
  sign-off is against the commit rules this project works by and is not to be
  set, and `Co-Authored-By:` and the session trailer an agent writes about
  itself do not belong in a core commit message either. Nothing in a checkout
  states this, which is why it is written down here.
- Every reading in `D-KNW-109` holds and now reads one way.
  `Build/git-hooks/commit-msg` deletes `^Signed-off-by:` and checks for nothing,
  the official Contribution Guide asks for none of the three, `CONTRIBUTING.md`
  is silent, and about one merged commit in a hundred on `main` carries the
  sign-off.
- The core's own `AGENTS.md` is the one source that demands it, on the Developer
  Certificate of Origin. The ruling stands against that file, which is the whole
  of what this entry adds to the reading.
- `feedback/2026-08-24-133602` had two drafts struck for the trailer by the same
  person, before this server said anything about it.
- Measured after the change on 2026-08-24: a message carrying all three trailers
  answers three `refused-trailer` errors under `workflow="core"` and comes back
  as a draft carrying neither, while `workflow="project"` keeps all three and
  reports nothing.

## Decided

- One rule in `core/contribution/commit-messages`, in place of the section that
  named the sources. The sources stay under it as why nothing enforces the rule,
  which is what a caller needs when a reviewer strikes a line no check rejected.
- The refusal is an `error` and the line comes off the draft. The draft this
  tool returns is committed as it stands, so a refused trailer left in it would
  be the answer contradicting its own check.
- Core only. `workflow="project"` keeps every trailer it is handed, because this
  repository's own commits carry `Co-Authored-By:` and a session link, and
  nothing about them belongs to the core's rules.
- A change to the rule is the maintainer's to make. A session that believes a
  trailer is owed asks rather than deriving an answer from whichever file it is
  holding — `R-KNW-075`.

## Assumed

- That the three names are the list. An agent attribution trailer under a fourth
  name passes the check, and only the sentence in the document says it does not
  belong.
- That the ruling outlives the core's `AGENTS.md` demand. Both are about the
  same trailer and they disagree, so a caller reading only one of them acts on
  it.

## Wrong if

- A core patch is struck for a trailer this list does not name. Then the list is
  the wrong shape and the rule belongs on what a trailer is rather than on which
  three.
- The maintainer sets a sign-off on a core patch. Then the ruling was narrower
  than what is written here.
- Somebody drafts a core commit under `workflow="project"` and the refusal never
  runs. Then the check sits behind an argument the caller chooses, which is the
  half no test can hold.
- The core's `AGENTS.md` drops the demand. Then the conflict this entry is
  written against is gone, and what stays is a list nobody disputes.

## Since then

The core's `AGENTS.md` was measured on 2026-08-24 rather than taken from
`D-KNW-109`, because two sessions read it differently on the same day. It landed
in `781c852587` on 2026-07-28 and stands at the bare repository's `main`,
`9dd4e1bfd7`. So the file is a month old and deliberately added, not the
days-old arrival `D-KNW-109` assumed, and the conflict this entry rules on is
the durable kind rather than one a revert would end.

The other reading was an absence that is not there: a session working the
changelog guide reported no `AGENTS.md` in `origin/main`, which is a ref no
worktree below `.checkouts/` carries — `git ls-tree main -- AGENTS.md` in
`.checkouts/typo3.git` answers the file. It withheld a sentence rather than
writing a wrong one, so nothing false reached the corpus.

### 2026-08-25 — reported twice, and an amend is the case the ruling misses

**Two sessions met the refusal after it landed and no `Wrong if` above fired:
what they report is the price this entry named, plus one case it does not
reach.**

`feedback/2026-08-24-183512` and `feedback/2026-08-25-114636` come from
`/home/benji/projects/typo3-cms` on two unrelated tasks. Both quote the
`refused-trailer` error verbatim, both grant that the practice is what this
entry says, and both went to `git log` to establish that for themselves — 2 of
the last 200 commits and 3 of the last 200. Every fact they came back with is
already in `core/contribution/commit-messages`, verified here and ruled on.
Neither session read the page, because the check message states the rule and
names none of what stands behind it: it overrules the core's `AGENTS.md` as
"whatever the checkout you are working in says", so a session holding that file
cannot tell whether the server knows about it. That half of the cost is this
server's to take back, and the card serving both feedback carries it.

The case the ruling does not reach is somebody else's patch set. What the
maintainer ruled is that the sign-off is not **set**, and both entries weighed
what a core commit message is written to carry. Measured again on 2026-08-25: a
core message handed in with a `Signed-off-by:` and a `Change-Id:` comes back
carrying the `Change-Id:` and not the sign-off, whoever wrote either. So a
session amending another contributor's patch set is told to strike that person's
Developer Certificate of Origin attestation, which is a different act from
declining to write one's own. `feedback/2026-08-24-183512` stood exactly there
on Gerrit change 91127, where the trailer had survived eight patch sets, and it
kept the line and said why. Whether the rule reaches an amend is `R-KNW-075`'s
question and the maintainer's to answer.

