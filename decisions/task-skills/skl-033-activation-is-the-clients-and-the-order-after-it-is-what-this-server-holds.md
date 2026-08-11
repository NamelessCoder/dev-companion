---
id: D-SKL-033
date: 2026-08-11
status: open
---

# D-SKL-033 — Activation is the client's, and the order after it is what this server holds

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
