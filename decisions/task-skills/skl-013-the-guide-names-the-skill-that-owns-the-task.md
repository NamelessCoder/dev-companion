---
id: D-SKL-013
title: The guide names the skill that owns the task
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aBriefNamesTheSkillThatOwnsTheWork
  - SkillTest::everySkillNamedInKnowledgeIsPublished
---

# D-SKL-013 — The guide names the skill that owns the task

**`typo3_task_guide` names the task skill that owns the work it recognized.**

Two of the four things that describe this server outward had said so for three
days while the answer named nothing. Which of the two to correct is the question
`todo/waiting/2026-07-31-192945` and `2026-08-01-003356` carried, and it was
answered by the person who queued them on 2026-08-03. Both cards are closed by
this entry.

## Evidence

- **One property from both sides, from inside a skill.**
  `feedback/2026-07-31-192945` recites the conformance skill's workflow without
  step 3 and reports nothing to drop; the same session's call log twenty seconds
  later has thirteen round trips and no `typo3_task_guide` among them.
  `feedback/2026-07-31-194826` is the same call from another model in the same
  project and reports that it restated the skill's own checklist. One session
  skipped the step and lost nothing, another ran it and gained nothing.
- **And the side that had no skill at all.** `feedback/2026-08-01-003356` built
  a content element with a custom backend preview in `site-new`, loaded no
  skill, and guessed at facts `typo3-content-element-development` covers in its
  own description. Re-run from there on 2026-08-02, the guide matched the
  content-element and test intents, answered with the two hints that session
  spent its evening guessing at, and named seven next lookups of which none was
  a skill — `D-SKL-001`.
- **The claim was already published twice.** `skills/base.md` has said since
  `66813e3` that step 3 returns "the workflow this task belongs to", and
  `18a371a` put "hands the parts that have their own workflow to the skill that
  owns them" into the `instructions` every client receives at initialize. What
  `src/Tool/TaskGuide.php` contained was no skill name at all.
- **What the two sides cost is not the same.** Saying in `skills/base.md` what
  the call is worth to a caller that arrived through a skill changes a file
  installed in somebody else's project and leaves a caller that arrived without
  one exactly where `003356` was. Only this side reaches both.
- **The other complaint against the same call had already landed.**
  [`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
  is `held`: the `audit` change type and the intent of the same name answer a
  review with a review's brief rather than a patch checklist. So what was left
  of `194826` was the route and not the checklist.
- **Measured in this checkout on 2026-08-03.** The testimonials task names
  `typo3-content-element-development`, the conformance audit of a site package
  names `typo3-extension-conformance`, the same words against a core path name
  `typo3-core-patch-review`, and a deprecation in `typo3/sysext/` names
  `typo3-core-patch-development`.

## Decided

- **The mapping is data.** `skill` and `skillCore` on an intent in
  `knowledge/task-intents.json`, read by `TaskIntents::skills()`. Two keys
  rather than one, because the same words name two workflows: an audit belongs
  to the conformance skill outside the core and to the patch review inside it,
  and each of those two descriptions hands the other side away in as many words.
  The core key is taken only where nothing in the call is outside-core work, so
  a path in an extension settles the side that the word "core" in a task text
  would otherwise claim.
- **Only a confirmed intent routes.** A weak match is a word that named the
  subject without naming the work, and a whole workflow loaded on one of those
  is the wrong answer rather than a partly wrong one — the same reason its
  checklist items carry their condition instead of being stated.
- **Five of the thirteen intents route, and that is the finished state rather
  than a first pass.** `labels`, `icons` and `backend-ui` name subsystems that
  no workflow owns; `changelog` spans three skills; `submission` matches
  "review", "push" and "backport", which span both core skills, so it names
  neither. `installation-setup` and `installation-operations` are owned by
  `typo3-development-installation`, which is drafted and not published — a name
  this server answers with before that is one nobody can load, so the route is
  the second half of publishing it and is written into that card.
- **Publication is public and is what a name is held to.** A skill exists for
  its readers once it is published —
  [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst) —
  and `SkillTest::everySkillNamedInKnowledgeIsPublished` holds every name in
  `knowledge/` to it. It was the Installer::SKILLS list when this was decided
  and is `Installer::skills()` since 2026-08-04, which is the same statement
  read out of the skills' own front matter; see **Since then**.
- **`skills/base.md` is unchanged.** That is the half of this answer that cost
  nothing: step 3 now says what the call does, and the growth `D-SKL-001`'s
  **Wrong if** watches did not happen.

## Assumed

- **That a deprecation or a breaking change outside the core is upgrade work.**
  The needles are the words a package deprecating its own API would use too, and
  no run has been through that route. The two audit routes and the
  content-element one are the ones a filed session measured.
- **That naming a skill the project has not installed costs nothing.** The line
  says "where this project has it installed" and the payload's own description
  says a name is not a promise, but nothing measures what a session does with a
  name it cannot load.
- **That the answer reaches a model the descriptions did not.** The route rides
  the same channel that already failed for `003356` — whether that client passes
  `instructions` and the skill listing to the model at all is unmeasured, which
  `D-SKL-001` records.

## Wrong if

- A build session in `site-new`, in the same client and model, reads the line
  and still loads no skill. Then the route was not the obstacle, and what is
  left to suspect is the channel — `D-SKL-006` wrote this one before the route
  existed.
- A brief names a skill for work it does not own and a session loads it. One
  such collision is already visible: the matcher matches a needle at a word
  boundary rather than whole, so "testimonials" matches the `tests` intent's
  `test` and that task names `typo3-extension-testing` first. It is queued as a
  todo of its own, and it was a false intent before it was a false route.
- The mapping grows to fill the table. Five intents route because five workflows
  own them and three published skills — documentation, release, backend module —
  are reached by no intent at all; a name invented for the sixth row is a route
  into a workflow nobody asked for.

## Since then

The second **Wrong if** happened again by the other route: a needle naming a
subject without naming the work routed a review to the workflow for writing the
change. The first fired in another project under the same client, where the
brief named the skill and the session went on without it. The third gained a row
that was not invented — an intent of its own routing to a workflow already
published, which is the opposite of a name in search of one.

The published list is gone with the same reading: `Installer::skills()` is the
directory minus every draft, where a list beside it was one fact in two places.
