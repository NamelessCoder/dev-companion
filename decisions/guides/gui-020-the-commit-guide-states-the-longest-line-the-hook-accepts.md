---
id: D-GUI-020
title: The commit guide states the longest line the hook accepts
date: 2026-08-26
status: open
coveredBy: []
---

# D-GUI-020 — The commit guide states the longest line the hook accepts

**`typo3_commit_message_guide` names the longest line the commit hook accepts
and the first it refuses, so a caller holding a stricter written rule can tell
which one runs.**

The width is stated today as a property of the draft — "the body is wrapped at
72 characters" — and the hook is named only by `body-line-too-long`, which fires
when the draft is already over. A caller whose draft is clean is told nothing
about the boundary at all.

## Evidence

- `checkForLineLength()` in `.checkouts/main/Build/git-hooks/commit-msg` is
  `grep -q -E '^[^#].{72}'`. Run against files of 71, 72 and 73 characters on
  2026-08-26 it accepted the first two and refused the third: `^[^#]` consumes
  one character and `.{72}` needs 72 more, so 73 is the shortest line that
  matches. It is the hook's only length gate, beside the commit type, the
  `Resolves:` line and the `Releases:` line.
- The check is byte-identical in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  The only difference between the hook on the oldest covered branch and on
  `main` is that 12.4 also accepts `Fixes:` where `main` takes `Resolves:`
  alone.
- The guide does emit lines of exactly 72. A body of eleven five-letter words
  and one six-letter word came back as a 72-character line with no
  `body-line-too-long` check, and the hook's own regex accepts that draft
  unchanged. So the report's primary claim — that every draft hitting the wrap
  width is one character over — does not hold, and `wrapParagraph()` admitting a
  word at `<= BODY_WIDTH` is right as written.
- The core checkout's `AGENTS.md` says "**No line of the message may reach 72
  characters** — the hook rejects the commit, footer trailers included". That is
  one character stricter than the hook it names in the same sentence, and it is
  what the reporting session took the guide to be violating.
- Three sessions in that checkout worked from the same imprecise statement.
  `feedback/2026-08-24-205132` hand-wrapped three messages to 66–68 characters
  and used no returned draft. `feedback/2026-08-25-114605` read the regex
  itself, measured every line at 68 or less, and still reported `AGENTS.md` as
  the repository's rule on length. `feedback/2026-08-25-105141` names "the
  72-character rule" among what made `AGENTS.md` look sufficient.
- The first of those three also reported a defect in the user's existing commit
  message on the strength of the stricter reading — two body lines of exactly 72
  characters, which the hook accepts.

## Decided

- The guide states the boundary rather than the width: the longest line the hook
  accepts, and the first one it refuses. Where a caller has a stricter rule in
  front of them, that sentence is what settles which one the commit runs
  through.
- It is stated on a clean draft too. A check that only fires on a defect cannot
  tell a caller that the draft in their hand is already within the rule, which
  is the moment all three sessions were in.
- `knowledge/documents/core/contribution/commit-messages.md` says it as well, in
  the same run: it is prose about the core, it was measured here rather than
  recalled, and it is what a caller reaching the document rather than the tool
  reads. `D-FBK-052` is why that half is not queued.
- Rejected: wrapping at 71 and failing at 72, which the report suggested. It
  spends a column of every line of every core commit message to satisfy a
  checkout file that is stricter than the hook it cites, and it makes the guide
  report a defect the commit does not have.
- Rejected: reporting a line of exactly 72 as a warning. Same objection, one
  step quieter — a caller who acts on it shortens a line nothing refused.
- `coveredBy` is empty because what is decided here is a sentence in an answer,
  and the boundary it states is read from a checkout no unit test may reach.
  What a test can hold is the width the guide wraps to, which
  `CommitMessageGuideTest` already covers for `D-GUI-003`.

## Assumed

- The hook a contributor has installed is `Build/git-hooks/commit-msg` from the
  checkout. `D-GUI-003` carries that assumption and why nothing here can read
  the installed copy; this rests on it unchanged.
- The hook is the only thing that measures a line. Gerrit's own server-side
  validation was not read, and nothing in the checkout describes it.

## Wrong if

- A caller commits a draft the guide returned with no length check and the hook
  refuses it. Then the boundary stated is not the one that runs, and it has to
  be read out of the hook rather than restated.
- Gerrit refuses a 72-character line the hook accepted. Then the hook is not the
  only gate, the boundary has a second source, and the stricter reading in the
  checkout's `AGENTS.md` is describing that one.
- A session reads the stated boundary and still hand-measures to a margin. Then
  what stopped the three sessions was not the missing sentence, and the lever is
  somewhere else on the ladder.
