---
id: D-KNW-125
title: A core commit message carries four trailers and the hook's Change-Id
date: 2026-08-25
status: open
revokes: [D-KNW-110]
coveredBy:
  - CommitMessageTest::aCoreDraftAsksForTheSignOffItCannotWrite
  - CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet
  - KnowledgeTest::theTrailerAnswerStatesTheRuleAndWhatLeavesItUnenforced
---

# D-KNW-125 — A core commit message carries four trailers and the hook's Change-Id

**A core commit message carries `Resolves:`, `Related:`, `Releases:`,
`Signed-off-by:` and the `Change-Id:` the hook writes, and
`typo3_commit_message_guide` refuses every other trailer and asks for a missing
sign-off.**

`D-KNW-110` said the same about the first three and struck the fourth. It is
revoked whole rather than amended: which trailers a core commit carries is one
rule, and a reader who has to assemble it from an entry and its correction has
two places to be wrong.

## Evidence

- The three that did not move were never in dispute. The official Contribution
  Guide's appendix lists them, and `D-KNW-110` measured a message carrying all
  three answering nothing under `workflow="core"`.
- The TYPO3 Association board's statement on GPL and AI-generated code, dated
  2026-07-20, recommends "a lightweight per-commit sign-off in the form of a
  Developer Certificate of Origin". It gives the reason as making "the
  contributor's provenance representation explicit and auditable", in the cases
  where "contaminated third-party material may have entered a contribution
  unnoticed".
- The board calls it "a recommendation to consider, not a precondition". The
  maintainer decided on 2026-08-25 that this project sets it always, which is
  the step from the board's recommendation to the rule stated here.
- The same statement settles what signing claims: a contribution "may be
  published under GPL v2 and … does not violate the rights of third parties", an
  obligation that "is indivisible and is not diminished by the use of an AI
  tool".
- The core's own `AGENTS.md` asks for it: "Sign off every commit —
  `git commit -s` appends the `Signed-off-by:` trailer, or set
  `git config format.signOff true` to get it automatically", certifying the
  right to submit under the project's licence. `D-KNW-110` was written against
  that file; the rule now agrees with it, so the conflict two sessions reported
  is gone rather than resolved.
- The same file says "Do not credit tooling or assistants in commit messages",
  which is the core's own ground for the two trailers that stay refused. The
  maintainer's ruling of 2026-08-24 had been the only source for it.
- Read at `fc0b542e52` of 2026-08-21, and identical on `13.4` and `14.3`. The
  file does not exist on `12.4`, so the core states this to an agent on the
  maintained lines and not on the oldest one — which changes nothing about the
  rule, because the rule is the maintainer's rather than that file's.
- The core's `AGENTS.md` states one half of what the hook does with the line:
  "The hook preserves the trailer and only ignores it when computing the
  `Change-Id`." What it leaves out is the half below, which is what this corpus
  adds rather than repeats.
- `Build/git-hooks/commit-msg` builds `clean_message` by removing any diff,
  every `^Signed-off-by:` line and every comment line, and returns without
  writing a `Change-Id` where that leaves nothing. So a message that is only a
  sign-off is an empty one and gets no id, which is the reason the line is
  removed at all. Identical on all four covered branches, read on 2026-08-25.
- The same `clean_message` is what `_gen_ChangeIdInput` hashes, so the trailer
  never enters the id, and the hook returns as soon as it finds a `Change-Id:`
  line. Amending the sign-off in or out therefore leaves an existing id standing
  and the patch set stays valid.
- The maintainer ruled on 2026-08-24 that `Co-Authored-By:` and the session
  trailer an agent writes about itself do not belong in a core commit message.
  The board's statement is about provenance and reaches neither, so that ruling
  stands.

## Decided

- One rule in `core/contribution/commit-messages`, naming the five and what each
  of them is for. The sources stay under it as why nothing enforces the rule,
  which is what a caller needs when a reviewer strikes a line no check rejected.
- The sign-off is required, so its check is an `error` and the draft carries
  `Signed-off-by: YOUR_NAME <YOUR_EMAIL>`. That is the shape `Resolves:` already
  has here: a placeholder in the draft, an error beside it, and the placeholder
  refused when it is read back, so the one moment somebody checks the message
  they are about to commit is not the moment it reports clean.
- A placeholder rather than an identity this server could guess. The certificate
  is an attestation about provenance, and a draft that signed it on somebody's
  behalf would make that claim for them — which is the one thing the board's
  statement asks a contributor to make deliberately.
- The message names `git commit -s` and what signing claims. A caller told only
  that a line is missing writes it by hand and learns nothing about what it
  says.
- A refused trailer is an `error` and comes off the draft. The draft this tool
  returns is committed as it stands, so a refused trailer left in it would be
  the answer contradicting its own check.
- Core only. `workflow="project"` keeps every trailer it is handed and is asked
  for no sign-off, because this repository's own commits carry `Co-Authored-By:`
  and a session link, and nothing about them belongs to the core's rules.
- A change to the rule is the maintainer's to make. A session that believes a
  trailer is owed asks rather than deriving an answer from whichever file it is
  holding — `R-KNW-075`, which this entry is the first exercise of.

## Assumed

- That the four names are the list. An agent attribution trailer under a fifth
  name passes the check, and only the sentence in the document says it does not
  belong.
- That the board's recommendation stays. It is a recommendation, so the core
  could adopt something else and this rule would then be the maintainer's alone
  rather than the maintainer's on the board's reading.
- That the placeholder is not committed. Nothing here can see whether a caller
  replaced it, and a patch carrying `YOUR_NAME` is a worse failure than a patch
  carrying no trailer at all, because it looks like an attestation.

## Wrong if

- A core patch is struck for a trailer this list does not name. Then the list is
  the wrong shape and the rule belongs on what a trailer is rather than on which
  five.
- A core patch is struck for carrying the sign-off. Then the practice on Gerrit
  did not follow the board, and the rule belongs on what the reviewers do rather
  than on what the board recommended.
- A patch appears carrying `Signed-off-by: YOUR_NAME <YOUR_EMAIL>`. Then the
  placeholder is read as a value rather than as a question, and what it costs is
  worse than the error it was written to raise.
- The core adds a check that rejects a message without the trailer. Then
  "nothing in the checkout enforces the rule" stops being true and the page
  states a reviewer's habit where a gate has taken over.
- Somebody drafts a core commit under `workflow="project"` and the checks never
  run. Then the rule sits behind an argument the caller chooses, which is the
  half no test can hold.
