---
id: D-AUD-013
title: A competing route is corrected where it is written
date: 2026-08-25
status: revoked
revokedBy: D-AUD-014
coveredBy: []
---

# D-AUD-013 — A competing route is corrected where it is written

**A route a caller's own repository names is corrected in that repository; this
server's own surfaces state its routes and say nothing about another file.**

The core checkout ships an instruction file that names a `curl` recipe for
Forge, and this server has a tool that answers the same question better.

## Evidence

- [`feedback/2026-08-24-163321`](../../feedback/archive/2026-08-24-163321-the-repository-s-own-agents-md-routes-agents.md)
  worked a core patch with that file in its context and called
  `typo3_forge_lookup` anyway, naming what it got that the recipe could not
  give: the `open` enumeration with its filters, and `notes=people` dropping 12
  of the 14 comments on issue #104460. The worse route was offered and not
  taken, so no failure was observed — what the feedback reports is what a more
  obedient session would have done.
- `AGENTS.md` at the root of the core checkout, read on 2026-08-25, still
  carries the paragraph verbatim, and no line of the file names an MCP server.
- That file is tracked in the core repository — `git log` names
  `781c8525870 [TASK] Add AGENTS.md with repository guidelines for coding agents`
  — so it is changed by a patch through Gerrit like any other core file and not
  by an edit on somebody's machine.
- The same feedback's second point is the other side of the boundary. The
  session wrote its commit message from that `AGENTS.md` without calling
  `typo3_commit_message_guide`, and the message was accepted and survived an
  amend. Where the competing file is right, the tool is never reached and
  nothing is lost.
- Two open feedback report that file disagreeing with the guide rather than
  agreeing with it: `2026-08-24-183512` on `Signed-off-by` and
  `2026-08-24-205132` on the 72-character body wrap. Both carry their own cards
  and neither is judged here.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 3, routing, against a routing table this repository does not own. The
  feedback is **trimmed**: its changelog half is answered under
  [`D-KNW-111`](../knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md)
  and its Forge half stays open.
- The three surfaces this server states a route on are the `instructions` of
  `knowledge/server-scope.json`, each tool `description`, and the skills the
  installer publishes
  ([`D-DIS-017`](../discovery/dis-017-the-skills-reach-a-project-through-the-installer.md)).
  None of them changes for this, because none of them failed.
- A skill that says what another repository's `AGENTS.md` names was rejected. A
  skill lands in somebody else's project, where a sentence about a third
  repository's file goes stale and no release of this server reaches it.
- Whether to propose the core patch waits for the maintainer. Naming an
  experimental server in the core's own instructions is an outward claim, and
  the ladder says an outward claim is never made quietly.
- The priority is `normal`, set by what the file is rather than by how many
  sessions reported it: it is read into the context of every session in a core
  checkout.
- Nothing holds this. The lever is a patch in another repository, and the two
  surfaces it bears on are unchanged, so there is no assertion to write.

## Assumed

- That a session holding both the recipe and the tool takes the tool. This one
  did and said why, and no session has reported the other way.

## Wrong if

- A feedback reports a session that queried Forge with `curl` while
  `typo3_forge_lookup` was offered to it. Then the tool list does not win
  against the file, and a statement on this server's own surfaces is the lever
  after all.
- The core's `AGENTS.md` comes to name an MCP server. Then this server's absence
  from it is a placement rather than a boundary, and the paragraph is a patch to
  write rather than a question to ask.
- The maintainer answers that no core patch naming this server is to be
  proposed. Then the route is corrected nowhere, and what is left is the
  statement on this server's own surfaces that this entry declined to make.

## Since then

**The commit-message bullet above is bounded**, by a session in the same
checkout on the same day. `feedback/2026-08-25-105141` also wrote its core
commit message out of that `AGENTS.md` without calling
`typo3_commit_message_guide`, and the `Releases:` trailer it wrote that way was
wrong. So the reading holds for the subject and body conventions the file states
in full, and not for the branch set, which it states as a rule without the
value.

That bounds the evidence rather than what was decided. The lever is still a
patch in the other repository, and what this server says on its own surfaces is
what its tool settles rather than what another file leaves out: the index entry
[`D-AUD-011`](aud-011-the-instructions-index-the-question-each-tool-answers.md)
bought now names the branches, in the characters the old wording gave back.

**The first Wrong if is met** by a feedback standing on the board.
`feedback/2026-08-25-114714` searched and read Forge with three `curl` calls
while `typo3_forge_lookup` was offered to it, and names the documented recipe as
the reason it never considered the tool. It carries its own card and the
judgement is that card's; what is recorded here is that the event this entry
watches for has happened.

**That card was judged on 2026-08-27** and the reading is
[`D-AUD-014`](aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md):
the ladder's step 4, and the lever is the opening sentence of
`typo3_forge_lookup`'s description. So the bullet above saying none of the three
surfaces changes is bounded to the day it was written — one of them failed, and
the statement this entry declined to make is made there. The core patch waits as
before, on the question it always waited on: that corrects the route where it is
written, and the description says what this server's own route is worth.

## Revoked on 2026-08-27

**The maintainer answered that no core patch naming this server is to be
proposed: a change to another repository's instruction file is out of scope for
this one.** That is the third **Wrong if** verbatim, and it is the one that
retires the entry rather than qualifying it — this decision says a competing
route is corrected where it is written, and correcting it where it is written is
what will not happen.

The patch existed by then. It was written on 2026-08-27 and checked: five lines
replacing the first two of the Context list's issue-tracker bullet, naming
`typo3_forge_lookup` where the server is connected and keeping the `curl` recipe
as the fallback, with a message `typo3_commit_message_guide` had passed for
`workflow="core"`. What stopped it was neither its text nor Gerrit: this
repository does not change another repository, and the question of who carries
an outward claim was settled the other way.

What is left is what this entry declined to make and
[`D-AUD-014`](aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md)
made two days later: the statement on this server's own surfaces. The tool
description now opens with what the caller's own recipe cannot do, which is the
whole of the correction this route gets. The core's `AGENTS.md` keeps its
paragraph, and a session that reads it and never calls the tool is a cost this
server carries rather than one it can fix.
