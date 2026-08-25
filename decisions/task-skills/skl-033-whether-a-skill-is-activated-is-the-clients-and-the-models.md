---
id: D-SKL-033
title: Whether a skill is activated is the client's and the model's
date: 2026-08-11
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
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
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows, measured on
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

### 2026-08-24 — a session of the counted shape opened both skills

`feedback/2026-08-24-183420` and `feedback/2026-08-24-183447` are one session in
`/home/benji/projects/typo3-cms` on `claude-opus-5`, reviewing Gerrit change
91127. It activated `typo3-core-patch-checkout` and `typo3-core-patch-review`,
reports both as fitting, and names no user prompt and no interrupt — unlike the
2026-08-10 session this entry was written from, where the skill opened at call
14 after "warum fragst du das review tool nicht an?".

Six hours earlier in the same checkout, `feedback/2026-08-24-122413` reviewed
change 95179 on `claude-opus-5[1m]` and opened nothing. Same client, same day,
same task shape, and nothing this server publishes differed between the two.
What the record separates them by is the model variant, which is one pair of
sessions and not something to read a cause out of — the two tasks differ in
their briefs as well, and one of them was written in German.

The statement holds, and this is the first evidence for it that is not an
absence. What it corrects is how the benchmark above reads: eighty-two empty
rows are one arm on one model variant, and not a property of the request shape.

### 2026-08-24 — a second German brief on the same variant, and one word of match

[`feedback/2026-08-24-133515`](../../feedback/archive/2026-08-24-133515-a-full-core-patch-session-ran-green-with-zero.md)
is the same checkout again on `claude-opus-5[1m]`, opening in German with "bitte
schaue dir das hier an und baue eine patch mit tests dafür" and an artifact URL.
A core patch followed, green, with no skill opened and no call made. So the
pairing the section above records is two to one rather than one to one, and the
variant, the language and the brief still differ together — the confound is
where it was, and what would settle it is the same German brief on
`claude-opus-5`.

What this row adds is a limit on what the third **Wrong if** counts. It counts a
description delivered "in the request's own words", and this request shares one
content word with
[`typo3-core-patch-development`](../../skills/typo3-core-patch-development/SKILL.md)'s
— `patch` — where the 2026-08-10 brief shared two of the first four. It names no
subject at all: that this was TYPO3 core work was said by the checkout, which no
description match reads. So the row counts toward the shape more weakly than the
founding one, and nothing here changes.

The rest of that report is about the entry point rather than about a skill
listing, and it is judged at
[`D-SKL-062`](skl-062-the-workflow-question-is-asked-again-on-a-new-subject.md).

### 2026-08-24 — the shape arrives outside the core checkout, and the channel beside it did not fire

[`feedback/archive/2026-08-24-140120`](../../feedback/archive/2026-08-24-140120-four-skills-matched-the-opening-request-almost.md)
is the first row here that is not core work.
`/home/benji/projects/ext-usercentrics` on `claude-opus-5[1m]`, opening in
German with "diese extension ist in einem schweren zustand, wir wollen das
bereinigen, wir wollen compat zu typo3 v13 und v14 herstellen in einer version".
Four published skills cover what the session did over the whole task and none of
them opened: `typo3-extension-health`, `typo3-extension-upgrade`,
`typo3-extension-documentation` and `typo3-extension-patch-review`. Two others
opened late, and the session reports both as fitting.

What it adds to the count is a description carrying the entry point in the
brief's own shape and being passed over anyway.
[`typo3-extension-health`](../../skills/typo3-extension-health/SKILL.md) opens
on "Review a TYPO3 project, sitepackage or extension against its checkout and
active installation and put it right — 'look over my repository and fix it'",
against a brief whose first clause asks for the repository to be cleaned up. So
the wording lever this entry declined for `typo3-core-patch-review` has already
been pulled on another skill, written to the request rather than to the
activity, and that one did not fire either.

**The channel the 2026-08-19 reading leaned on did not deliver here.** That
section left the project's own agent instruction file untaken because the run
which met the third **Wrong if** showed the `instructions` working beside it —
eleven `typo3_project_describe` calls against one `typo3_task_guide`. This
session made neither at its opening. Its first calls of any kind were
`typo3_changelog_lookup` and `typo3_ter_lookup`, several turns in, and
`typo3_project_describe` was reached only from inside a skill that named it. The
reason for leaving that file untaken rests on one benchmark arm, and this is one
session against it.

It stays untaken, on the count. One session is not the eleven to one
re-measured, and this one names why it started where it did: the brief opened as
a reading of the checkout, which is what the last sentence of the `instructions`
assigns to the caller. What would settle it is a run counting the two calls on a
brief of that shape, which is
[`D-ANS-091`](../answers/ans-091-the-project-answer-leaves-the-second-call-to-the-instructions.md)'s
first **Wrong if** and is not what this row is.

The session's other proposal is a sentence in the `instructions` naming
`typo3-extension-health` and `typo3-extension-upgrade` for a repository-wide
request. Measured in this checkout on 2026-08-24, the longest assembly is 2028
characters of the 2048 `D-AUD-011` holds. Twenty characters are free and the
sentence names two skills and three request shapes, so it is unaffordable before
it is weighed — and whether 2048 is still the boundary to design against is what
[`todo/waiting/2026-08-19-090401`](../../todo/waiting/2026-08-19-090401-tools-arrived-as-bare-names-with-no-schemas-and.md)
is waiting on.

### 2026-08-25 — one session, one description that matched and one that said nothing

[`feedback/2026-08-24-163220`](../../feedback/archive/2026-08-24-163220-both-skills-matching-this-task-stayed-shut-for.md)
is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]` again, opening in
German with "bitte suche forge issues im asset renderer bereich" and then "ok
suche einen den man einfach fixen kann und es mit tests belegen kann". It found
an issue, wrote the patch, the tests and the changelog entry, and opened no
skill. Neither `typo3_project_describe` nor `typo3_task_guide` was called, which
is the eleven-to-one of the 2026-08-19 reading measured the other way once more.

The row counts for the patch half alone.
[`typo3-core-patch-development`](../../skills/typo3-core-patch-development/SKILL.md)'s
description names the changelog entry, the checks and the push to Gerrit — three
of the things the session went on to reconstruct by hand — and it stayed shut.
That is this entry's shape, and the session's proposal for it is the lever this
entry declines: a trigger for being about to change a file in the core checkout
is another request shape, and the 2026-08-24-140120 row is one already written
that way and passed over.

The triage half is not this entry's, and it is why the report is judged
elsewhere. `typo3-core-issue-triage`'s description says nothing about picking an
issue out of the backlog, which is the first sentence of this brief and the
first section of that skill's body. A description that never names the job is
not a description the model chose against, so the wording is what is left to
suspect there rather than what has been ruled out —
[`D-SKL-076`](skl-076-a-description-names-both-jobs-a-skills-body-owns.md).

### 2026-08-25 — the first row that names its own cause, and it is outside anything published here

Every row above reads a skill's silence off an absence. This one is read off the
session's own account.
[`feedback/2026-08-24-173236`](../../feedback/2026-08-24-173236-task-guide-schema-was-fetched-and-never-called.md)
is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, sent to work off
another old Forge issue. `typo3-core-issue-triage` and
`typo3-core-patch-development` were both in its listing, it names the first as
describing "verbatim the work I did four times over by hand", and it invoked
`Skill` at no point. Why, in its own words: its operating instructions for that
session carried "Do not call the AgentTool unless the user requested it. Do not
use workflows or deep-research unless the user requested it", and it generalised
that caution to skills, which the instruction does not cover.

**Nothing this server publishes reaches that.** A description competes for the
choice; this session reports a rule it read as forbidding the call before any
description was weighed. So the row counts toward the third **Wrong if** and
carries a cause the entry could not see from the outside, which is the reading
the **Assumed** above stops at: a model passing over a matching description may
be choosing, failing to match, or held off the tool by its own client.

What it does not license is reading the earlier rows this way. One session's
reconstruction of its own reasoning is what it believed afterwards, and the
eighty-two empty rows of the benchmark were an arm nobody debriefed. Everything
**Decided** stands.

