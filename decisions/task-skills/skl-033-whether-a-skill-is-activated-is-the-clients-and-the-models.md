---
id: D-SKL-033
title: Whether a skill is activated is the client's and the model's
date: 2026-08-11
status: open
---

# D-SKL-033 — Whether a skill is activated is the client's and the model's

**A skill's description is written for the listing it is read in, and nothing
here is built to force an activation the client and the model decide.**

A description opening with the request's own words was delivered whole and did
not fire, so the wording is not what is left to suspect.

## Evidence

- **The session.** `/home/benji/projects/typo3-cms` on 2026-08-10,
  `claude-opus-5` in Claude Code, transcript
  `d7b5b468-5ef2-4e9e-81df-27a4afbdce07`. The whole brief was "bitte review mir
  den patch [TASK] Keep the docheader navigation row sticky". Thirteen `Bash`
  calls followed — `git show`, four repository greps, a branch listing, four
  file windows and a failed CSS build — and `typo3-core-patch-review` was
  activated at call 14, after the user interrupted with "warum fragst du das
  review tool nicht an?".
  [`feedback/archive/2026-08-10-182404`](../../feedback/archive/2026-08-10-182404-a-review-request-quoting-the-skill-s-own.md)
  is that session's own report of it.
- **The listing arrived, described.** The `skill_listing` attachment carried
  9500 characters with a description on every entry, the twelve published here
  among them.
  [`typo3-core-patch-review`](../../skills/typo3-core-patch-review/SKILL.md)'s
  is byte-identical to the one in this checkout today, all 256 characters of it,
  and opens "Review a TYPO3 core patch". The German sentence's only content
  words are `review` and `patch`, which are two of those first four.
- **The instructions arrived, whole.** The `mcp_instructions_delta` opens with
  `Installer::NOTICE` — the project's published skills were stale — and then
  with "Start every task with typo3_project_describe", naming `typo3_task_guide`
  as what "hands the parts that have their own workflow to the skill that owns
  them". Neither tool was called before call 14.
- **What the twelve cost today.** 3537 characters of listing against the 3600
  `SkillTest::everyDescriptionIsWrittenToTheBudgetTheyShare` allows, measured on
  2026-08-11.

## Decided

- The description stays as it is. Its first four words are the request's own
  two, and no rewrite of it matches this brief harder.
- Against trigger words in another language, here and in the other eleven.
  Everything this server publishes is English and it says so three times — in
  the `instructions`, in `typo3_server_scope` and in the last section of
  [`skills/base.md`](../../skills/base.md) — so a German word in a description
  is a second vocabulary to keep true in twelve installed files, bought for a
  request whose matching words were English already.
- Against more request shapes. The two sides a review arrives from are in the
  description already, `R-SKL-010` is what put them there, and 63 characters is
  what the ratchet has left.
- What this run queues instead is step 3 of the order, which the same transcript
  shows skipped after the skill was active — `D-SKL-015`.
- **The channel that was not tried, named so nobody rediscovers it.** The
  installer writes `.mcp.json` and the published skills, and nothing into the
  project's own agent instruction file, which is the one place left that reaches
  a session before its first turn without a call being made. It is not taken on
  this evidence: the `instructions` reach a session that way already, they were
  in this one's context from the first turn, and they bought thirteen calls of
  nothing.

## Assumed

- That the listing this session received is the one other sessions receive. It
  was read in one client on one day, and 9500 characters is above the 6000
  `D-SKL-026` computes for a 200k session on this model generation — so either
  the window was larger or that arithmetic has moved, and which was not
  established.
- That a model reading `- typo3-core-patch-review: Review a TYPO3 core patch …`
  against "bitte review mir den patch" is choosing rather than failing to match.
  Nothing here can see the choice, which is why this entry stops at what was
  delivered.

## Wrong if

- A transcript shows the same request shape arriving where the description was
  dropped or truncated from the listing. Then the budget was the obstacle after
  all, and `D-SKL-026`'s arithmetic is what to act on rather than the wording.
- A description rewritten for another reason starts activating on a shape it did
  not carry before. Then the wording carries more than this entry allows it, and
  the shapes are worth their characters.
- A third session reports a skill that was listed, described in the request's
  own words, and left shut. Then the project's own instruction file is the
  channel to weigh, and this entry is what recorded that it was untried.

## Since then

### 2026-08-14 — the second session, and one hypothesis fewer

[`feedback/archive/2026-08-12-092545`](../../feedback/archive/2026-08-12-092545-a-german-language-review-request-activated-no.md)
is the same request shape in the same checkout two days later: German, naming
Gerrit change 95169 by number and by review URL, and no skill activated at any
point. It is the second session on record of the shape the third **Wrong if**
counts to three, and it is not the third.

What it settles is the other half of the report this entry was written from.
That session named two things as plausible causes, the language and a request
naming a local commit rather than a change on the review server, and this one
carries the change number, the review URL and the description's own two words.
So the local commit is not what kept the skill shut, and the language and the
client's own choice are what the entry already stops at.

Everything **Decided** stands, including the German trigger words: the second
session changes how often the shape arrives and not what a description could
have matched. What it adds is a channel this entry did not weigh — the answer of
the tool the session did call, which is
[`D-SKL-038`](skl-038-the-change-answer-names-the-skill-that-owns-the-patch-it-describes.md).
The project's own instruction file is untried still, and stays what the third
**Wrong if** watches for.

### 2026-08-19 — the third Wrong if fired, and the channel it named is weighed

A benchmark outside this repository, read on 2026-08-19: an equipped arm ran
seventeen project tasks in Claude Code 2.1.234 on `claude-opus-5`, the client's
own init event lists all twelve published skills beside the connected server,
and `skills_used` is empty on every row of all four recorded sweeps — eighty-two
runs. So the shape this entry counts to three arrived eighty-two times over, and
what it counted is settled: the listing is delivered, described, and no skill is
loaded.

One of the seventeen called `typo3_task_guide`. It answered
`skills: ["typo3-content-element-development"]` and the line `D-SKL-013` put
above the payload, and the session went on through `typo3_schema_lookup`,
`typo3_changelog_lookup` and Bash without a `Skill` call. A name delivered in a
tool's own answer, in the turn that asked for it, is the shortest channel this
server has, and it did not fire either.

**The project's own agent instruction file is weighed here and not taken.** The
same run called `typo3_project_describe` eleven times against `typo3_task_guide`
once, so the `instructions` reached the model and its first imperative was acted
on eleven times: the channel delivers. What would go into the instruction file
is the sentence that rode after it in the same paragraph, written a second time
on a channel that is already carrying it — and the run that would show it
working is one where the instructions did not arrive at all, which is not this
one.

What the eleven to one names instead is the placement inside the `instructions`:
`typo3_project_describe` is the imperative the paragraph opens on and
`typo3_task_guide` follows it after a clause about declared commands. That is a
sentence this server can move, where the instruction file is a file it would
add, and it is queued rather than done here.

The **Decided** bullet naming the channel as untried stands as the record of why
it was untried, and this section is what it was waiting for. It stays untaken
for a different reason than it was untried: not that nothing had met the third
**Wrong if**, but that the run which met it also showed the channel beside it
working.

### 2026-08-19 — the placement was weighed, and the mood is what moved

What the section above queued is
[`D-AUD-012`](../audience/aud-012-the-second-call-of-the-entry-point-is-an-imperative.md).
The sentence stays where it is and becomes an imperative, because the position
is `skills/base.md`'s own order and the mood is the difference the eleven to one
holds everything else constant across. It cost two characters of the twenty-two
the budget had left, so nothing was displaced for it.

Nothing about this entry changes. The eleven to one is one run, a second of the
same shape is what would settle it, and `D-AUD-012` carries that as its first
**Wrong if**.

