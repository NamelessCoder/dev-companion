# Run the two core workflow cases against what was published

**Serves:** SKILL-12, SKILL-13
**Priority:** normal

`typo3-core-issue-triage` and `typo3-core-patch-checkout` were published on
2026-08-05, after the maintainer read them and had the core suites and the DDEV
project named at the step that reproduces. What has not happened is either
contract case being run, so what holds them is still only what `SkillTest` reads
off the file — the routing order, the checklist beside the body, the crossing.

Both cases say so in their own **Held by** line, and both name the behaviour no
assertion reaches:

- `SKILL-12` — that a session separates what the reporter saw from what the
  reporter believed caused it, and does not report "could not reproduce" as the
  behaviour being gone.
- `SKILL-13` — that a session stops at a conflict the change does not decide,
  rather than resolving its way to something that compiles, and puts the
  checkout back afterwards.

`bin/cli scenarios:contract <id>` prints the prompt to paste verbatim. Run them
in a core checkout, and record what comes back rather than what was expected:
the part of either draft that was reasoned rather than measured is the stopping
rules in `typo3-core-patch-checkout/references/checklist.md`, which were written
against no conflict anybody has hit.

A finding here is a change to a published skill, which is the expensive kind —
the copy in somebody else's project is not corrected by the next release. That
is the argument for running them soon rather than eventually.
