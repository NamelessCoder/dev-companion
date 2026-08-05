# Publish the two draft core skills once they have been read

**Serves:** SKILL-12, SKILL-13
**Priority:** normal

`typo3-core-issue-triage` and `typo3-core-patch-checkout` are written, held by
the directory-wide assertions, and carry `status: draft`. Publishing is deleting
that one line from each front matter, and it is held back on the one step
nothing here can do:
[documentation/clients/writing-a-skill.md](../../documentation/clients/writing-a-skill.md)
requires the person who asked for a skill to read `SKILL.md` and every reference
whole, and to be asked by name what is missing and what is wrong. Neither draft
has had that.

What to ask about, because a general question gets agreement rather than review:

- The triage verdicts in `references/checklist.md`. Are those the outcomes a
  maintainer actually writes, and is the one that ends the work early — "cannot
  be settled here" — one they would accept.
- The stopping rules in the checkout skill's checklist. The rule is that a
  conflict is resolvable only where the change itself decides it, and the list
  of what that covers was reasoned rather than measured against conflicts
  anybody has hit.
- Whether the triage skill stopping short of the tracker is right. It writes a
  verdict and comments, closes and reassigns nothing, on the reading that those
  are the maintainer's act.
- Whether `typo3-core-patch-checkout` is the right name, given the pair it joins
  is `typo3-core-patch-development` and `typo3-core-patch-review`.
- The restore order at the foot of the checkout skill. Whether bringing the
  installed dependencies back in step belongs there at all, and whether a
  fast-forward is what a maintainer actually does to a local branch tracking the
  core.

Publishing takes three more edits per skill, and `SkillTest` and `ScopeTest`
fail until each is made:

- `knowledge/server-scope.json` has to name the workflow among what the server
  covers, or `ScopeTest::everyPublishedSkillIsAnnouncedByTheScope` fails.
- `knowledge/task-intents.json` may then route to it, in the same commit and
  never before (`D-SKL-013`).
- Run the installer in the checkout that plays the environment before any run
  meant to measure it; the published skills are a copy and nothing reports that
  they are older than the server.

`SKILL-12` and `SKILL-13` in `scenarios/contracts/task-skills/` are what measure
the behaviour no assertion reads off the file. Running either is worth doing
before publishing rather than after, because what a run finds is what the draft
is still allowed to change.
