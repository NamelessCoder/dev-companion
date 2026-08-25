---
id: D-AUD-013
title: A competing route is corrected where it is written
date: 2026-08-25
status: open
coveredBy: []
---

# D-AUD-013 — A competing route is corrected where it is written

**A route a caller's own repository names is corrected in that repository; this
server's own surfaces state its routes and say nothing about another file.**

The core checkout ships an instruction file that names a `curl` recipe for
Forge, and this server has a tool that answers the same question better.

## Evidence

- [`feedback/2026-08-24-163321`](../../feedback/2026-08-24-163321-the-repository-s-own-agents-md-routes-agents.md)
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
