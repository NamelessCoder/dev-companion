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

**Read on 2026-08-05, and neither case was run.** A session in a checkout of
this repository is not one of the two environments: `E-CORE` is a core checkout
with this server installed, and the tools a case measures are reached for there
rather than here. What the reading found instead is that half of `SKILL-12`
already has evidence, from a session that was never given the case. On
2026-08-05 a triage of the RTE backlog ran the skill end to end and filed seven
feedback about it. Its step 1 could not be executed at all — the skill named
`open`, `category` and `tracker`, and the tool had none of them until an hour
later — so `met 1` was reached by leaving the server. `met 3` and `met 4` were
met, and the checklist is what the session credits for the first: it filed two
verdicts where one issue number held two defects, rather than reporting "could
not reproduce" as the behaviour being gone. `met 4` was met and answered
falsely, which is `D-ANS-055`.

So what is left is narrower than the card was written for. `SKILL-12` needs the
run for `met 2`, `met 5` and `met 6` and for step 1 as it now stands; `SKILL-13`
has no evidence of any kind, and its stopping rules are still the part nobody
has hit a conflict against. Do not read the feedback as the run — no scenario
was recorded, the prompt was the user's own, and a case is measured by what the
session did with the words in it.
