---
id: D-AUD-014
title: A description opens with what the caller's own route cannot do
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::theBriefOpensWithWhatTheCheckoutsOwnConventionsCannotSay
---

# D-AUD-014 — A description opens with what the caller's own route cannot do

**Where the caller's own repository hands out a route to the same source, the
tool's description opens with what that route cannot do rather than with what
the tool reads.**

[`D-AUD-013`](aud-013-a-competing-route-is-corrected-where-it-is-written.md)
changed no surface of this server, because the one session on record had held
both routes and taken the tool. Its first **Wrong if** has since happened.

## Evidence

- [`feedback/2026-08-25-114714`](../../feedback/archive/2026-08-25-114714-typo3-forge-lookup-lost-to-the-raw-curl-recipe.md)
  queried Forge with three `curl` calls and two inline Python parsers while
  `typo3_forge_lookup` sat in its tool list, and it names the recipe in the
  core's `AGENTS.md` as the reason it never considered the tool. One of those
  calls bought nothing but the knowledge that the endpoint answered at all.
- The description that session saw is the one `f1e047d1` left, and it opens
  "Read the TYPO3 issue tracker at forge.typo3.org before writing a patch" —
  which is what the recipe already promises.
- The clause the feedback asks for was in it. "An issue that does not exist is
  answered as such, and so is a tracker that could not be reached" is the last
  sentence of that description, past every parameter it takes. So this is
  placement and not absence.
- The bot protection appears nowhere in the description.
  `ForgeLookup::UNREACHABLE` names it, which a caller reads once the tracker has
  already refused them.
- `.checkouts/main/AGENTS.md` lines 11 to 15, read on 2026-08-27, still carry
  the recipe and its Anubis warning verbatim. Forge is the only entry of that
  Context list handing out a working route; Gerrit is named as two URLs.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 4, wording. The tool was delivered, and its description did not take
  against a recipe promising the same thing.
- The feedback is **queued** rather than closed on the spot, because the change
  is in `src/` —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  keeps that half of the line whatever the judging run holds.
- The surface is the description. The feedback's other option, declaring the
  `curl` route the intended one under `doesNotCover`, was rejected: the tool
  passes the bot protection and tells a miss from a refusal, so a boundary drawn
  there would be a false one.
- The statement goes at the opening. A caller decides whether to call from the
  first sentence, and everything this tool does that `curl` cannot was already
  written further down.
- The priority is `normal`, which is what `D-AUD-013` set the other half at on
  what the file is. This is the same file with a measured failure behind it
  rather than a predicted one.
- Nothing holds it, so no requirement is written. What would have to be asserted
  is that one sentence of one description says what a file in another repository
  leaves out, and a check reading for that is a keyword the next rewrite moves.

## Assumed

- That the opening is what a caller weighs a tool against a route it already
  has. The session says the tool's name alone did not beat the recipe, and it
  does not say how far into the description it read.

## Wrong if

- A session reports going around `typo3_forge_lookup` again once the opening
  says what `curl` cannot do. Then what wins is the file arriving in the context
  rather than anything the tool list carries, and the lever is the core patch
  [`todo/open/T-260824-b4af.md`](../../todo/open/T-260824-b4af.md) carries.
- A feedback reports the opening as noise: a caller with no competing recipe,
  reading a caveat where the tool's subject belongs.
- The core's `AGENTS.md` paragraph comes to name this tool. Then the opening
  answers something no caller is told the opposite of any more, and it is a
  sentence to withdraw.

## Since then

### 2026-08-27 — the same file beats a second tool, and the tool is the entry point

[`feedback/2026-08-26-223325`](../../feedback/archive/2026-08-26-223325-the-stated-entry-point-and-both-fitting-skills.md)
worked a core patch in the same checkout and never called `typo3_task_guide`,
whose schema it had loaded in its first `ToolSearch` call. It names the reason
in the same shape this entry was written from: the description opens "Build a
task checklist enriched with matching hints and relevant core checks", it held
the core's `AGENTS.md` with the test-first rule, the `runTests.sh` invocations
and the commit conventions in context already, and a checklist was therefore not
a gap it felt.

The statement above transfers, and the tool it transfers to is the one the
`instructions` name second. What the session then had to guess is what that file
does not carry — the branches a `Releases:` trailer takes, and whether a bugfix
of that shape owes a changelog entry — and neither is in the description's
opening, which spends its first sentence on the artefact rather than on that.

So `typo3_forge_lookup` is not a single case, and what the two share is a file
read into every session in a core checkout. The rewrite is queued on
`T-260826-4194`, beside the routing half
[`D-SKL-081`](../task-skills/skl-081-a-brief-spanning-triage-and-the-patch-it-leads-to-carries-both.md).
The **Assumed** above is unchanged and is now carried by two tools: neither
session says how far into a description it read.

### 2026-08-27 — the second opening is written, and one of the two guesses was answered nowhere

`typo3_task_guide` now opens on what one change owes against a file that states
its rules once for every task, and names the two the session guessed at: whether
this fix owes a changelog entry, and the branches a `Releases:` trailer takes.

The second was already answered here and the first was not. `.checkouts/main`'s
`AGENTS.md` says bugfixes are backported to the maintained release branches and
never which those are, and `typo3_commit_message_guide` names them with the rule
for which of them a fix goes on — a call the brief has always routed to. The
changelog obligation was in `knowledge/documents/core/contribution/changelog.md`
and reached no bugfix brief: that document is not among `TaskIntents`' three
rule documents, and a plain bugfix confirms no intent that would pull it. So the
`bugfix` change type gained one item, stating the rule and naming the page it is
read whole from. An opening that had claimed the answer without it would have
been the lie this repository's own rule about outward surfaces names.
