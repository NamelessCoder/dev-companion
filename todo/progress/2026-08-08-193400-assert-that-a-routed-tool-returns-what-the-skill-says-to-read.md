# Assert that a routed tool returns what the skill tells the session to read

**Serves:** requirements/task-skills/
**Priority:** normal
**Branch:** todo/assert-that-a-routed-tool-returns-what-the-skill-says-to-read
**Claimed:** 2026-08-08

Every rule the authoring contract holds is read off the file, and
[writing-a-skill.md](../../documentation/clients/writing-a-skill.md) says so
itself: the wording is present and a reorganisation can leave it present while
the behaviour goes. There is one gap in that proxy narrower than behaviour and
still checkable — a skill does not only name a tool, it tells the session what
to read out of the answer. `base.md` step 1 has the session read whether a
command is check, change or unknown; the extension steps have it read the test
layers below `Tests/` and the source language each XLF declares. If a tool stops
reporting one of those, nothing fails. The skill still names the tool, the
assertion still passes, and the session is told to read a key that is not there.

`ROUTING_SKILLS` in `SkillTest` already records which tools each skill routes
through and in what order, so the list exists and needs no second copy. What is
missing is the other half of the pair: for each routed tool, call it and assert
the shape the skill's prose depends on.

The step is to take the four calls `base.md` fixes, since they are the ones
every skill inherits and therefore the ones whose drift costs the most, and
assert for each the keys the base tells the session to read.
`typo3_project_describe` marking commands check, change or unknown is the
concrete first one and is named at `skills/base.md:180`. Where a tool needs an
installation to answer, the fixtures under `.fixtures/` are what it is asked
against; where it reaches outside this process, assert the shape of a recorded
answer rather than making the suite depend on a host being up.

This is the one mechanism worth taking from `WordPress/agent-skills`, whose
`eval/harness/run.mjs` runs what a skill routes to and checks the shape it
depends on. The rest of their harness is not worth copying — it validates
frontmatter in 133 lines, its 41 scenario files have no consumer in the
repository, and their own `docs/ai-authorship.md` says no formal evaluation
system exists. `SkillTest` is already past it everywhere except here.
