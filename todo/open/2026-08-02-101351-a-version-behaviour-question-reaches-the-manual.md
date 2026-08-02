# A version-behaviour question reaches the manual from the order a review follows

**Serves:** feedback/2026-07-31-174524-typo3-v14-backend-layout-compatibility-gap-none.md
**Priority:** normal

Step 3 of the ladder, routing: `typo3_documentation_lookup` at the target
version answers "does pattern X still work in version N" in one call, and the
order a review follows never asks it — `skills/base.md` numbers the changelog
sweep and leaves the manual a conditional bullet, which is how two findings of
one session ended in "I had to read installed vendor core". `D-ANS-010` has the
readings and what would show the diagnosis wrong.

Next: settle against `skills/base.md` and the skills that copy it whether this
becomes a step of its own in the numbered order or a sentence on the existing
changelog step saying what its silence does not mean. That is a skill contract,
so read `documentation/clients/writing-a-skill.md` and the `SkillTest` cases
that hold the order before writing either.
