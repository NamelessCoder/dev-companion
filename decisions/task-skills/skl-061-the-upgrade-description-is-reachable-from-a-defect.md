---
id: D-SKL-061
title: 'The upgrade description is reachable from a defect'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::aDefectInsideTheDeclaredRangeMatchesTheRemovalSkill
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-061 — The upgrade description is reachable from a defect

**`typo3-extension-upgrade` names the removed and deprecated surface of a major
as a case of its own, so a defect inside a range the package already declares
reaches it.**

Its description opens on carrying a package from the versions it supports today
to another set, and the four shapes after the colon are read under that clause.
A bug report whose cause is a removal changes no range, so the clause that
carries the activation states a premise the task does not meet.

## Evidence

- **The session.** `/home/benji/projects/blog` on 2026-08-18,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-18-080630`](../../feedback/archive/2026-08-18-080630-typo3-extension-upgrade-describes-this-task.md).
  The request was a German symptom report — these TypoScript conditions seem to
  be broken in v14, they must still work in v13 — with one file selected in the
  editor. No skill activated at any point of the session, investigation through
  commit.
- **The words were there.** The session read the description back and reports
  two of its four clauses as the task exactly:
  `replacing what a major deprecated or removed` is the removal it was chasing,
  and `proving every version it claims` is the v13 half it could not prove. What
  it names as the obstacle is the opening clause, under which both read as steps
  of changing a declared range.
- **The body says the same thing.** `SKILL.md` opens "Cross a package from the
  range it declares to the range it is meant to declare", and its third step
  resolves the range the package may declare. A description rewritten alone
  would leave the file disagreeing with itself in somebody else's project, which
  is what `R-SKL-010` was written about.
- **The shape is real.** `.checkouts/14.3` carries
  `Breaking-107473-TypoScriptConditionFunctionGetTSFERemoved.rst` and
  `Breaking-107831-RemovedTypoScriptFrontendController.rst` under
  `Changelog/14.0/`. The package declares `^13.4.15 || ^14.3` and is broken by a
  removal inside that range, so the request arrives as a defect rather than as
  an upgrade.
- **The other route is shut too.** The two intents naming this skill in
  `knowledge/task-intents.json` match on `deprecat`, `breaking`,
  `remove public`, `drop support`, `@internal` and `public api`, none of which a
  symptom report carries, and both checklists are the core author's — a
  changelog file, an extension scanner matcher — rather than the consumer's. The
  `routing` entry of the same shape in `knowledge/server-scope.json` fires on
  starting work on a major or planning an upgrade, and routes to
  `typo3_changelog_lookup`.
- **Not step 1a.**
  `bin/cli hints:probe "extension broken on a TYPO3 major it already supports, removed global"`
  matched nothing and returned its 96 candidates as the index. What is missing
  is not a statement.
- **What the listing has left.** 3572 characters of the 3600 ceiling on
  2026-08-18, measured over `Installer::skills()` the way
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` measures it, with
  the upgrade description at 267 of them.

## Decided

- The ladder's step 3, on the skill's own trigger. It is the second sighting of
  the mechanism `D-SKL-024` was confirmed by on 2026-08-09: a clause that reads
  as one way of doing the job **narrows** what the description names, and the
  task that does it another way reads as actively not its case. There the clause
  was a list of steps and the excluded shape was a worktree; here it is the
  premise the shapes are listed under.
- Queued rather than rewritten in this run. A description is installed into
  somebody else's project, which is why `D-AUD-003` queued the backend preview
  rewrite rather than making it, and the body moves in the same commit.
- The rewrite trades words and does not buy a shape. `D-SKL-033` decided against
  paying listing characters for more request shapes, and this is not that
  request: the shape is already written down and the opening clause is what
  excludes it. 28 characters is what the ceiling has left, so a rewrite that
  only adds is the other trade and `D-SKL-026`'s arithmetic decides it.
- At `normal`, because two sessions on two skills have now reported the same
  mechanism, and because the words that would have matched were already in the
  file.
- Against reading this as a missing owner. What the session actually did — find
  the removal, find what replaces it, prove both majors — is this skill's
  workflow with the range decision taken out, so the skill owns the case and
  what failed is the way in.
- The second half of the feedback goes to the card that already carries it.
  [`feedback/2026-08-18-081129`](../../feedback/archive/2026-08-18-081129-nothing-says-how-to-execute-the-other-typo3.md)
  reports the same gap — nothing says how to execute the other major a package
  claims — from the same session with the procedure it would have needed, so
  this feedback is trimmed to the description and that card stays the owner.

## Assumed

- That the description was read and chosen against, rather than dropped from the
  listing or never consulted. Nothing here can see the choice, which is the same
  limit `D-SKL-033` stops at.
- That the opening clause is what governed the reading. The session says so and
  its report is the only account of it; the sentence also parses the other way,
  as four independent shapes after a colon.

## Wrong if

- A run against the rewritten description leaves the skill shut on a symptom
  report of this shape. Then the wording was not the obstacle for this skill
  either, and `D-SKL-033`'s position covers it as well.
- A session that activates on such a report is carried through the range steps
  and reports them as noise. Then the case belongs to an owner that does not
  exist yet, and the trigger was the smaller half of what is missing.
- The premise can only be widened by adding characters. Then this is a budget
  question rather than a wording one, and which of the twelve descriptions pays
  is what has to be decided first.

## Since then

The rewrite landed on 2026-08-18. The description opens on keeping a package
working on the versions it declares **or** carrying it to another set, so the
shapes after the colon are read under a premise a defect meets; the body opens
on the same two cases, and its third step reads the declared range where nothing
is being crossed rather than resolving one. `R-SKL-007` was widened with it, and
what the skill says it owns is no longer the crossing alone.

What the trade actually had to fit is smaller than this entry says. Measured the
way `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` measures — which
counts the description as the front matter writes it, quotes included — the
listing stood at 3597 of the 3600 the ratchet allows before this change and at
3595 after, and the upgrade description went from 269 characters to 266. So the
room was 3 characters and not 28, and the next rewrite of any of the twelve
reads the number off that test rather than off this entry.
