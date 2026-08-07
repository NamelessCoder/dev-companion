# Run the two core workflow cases against what was published

**Serves:** SKILL-12, SKILL-13
**Priority:** normal

`E-CORE` is ready and the run is what is left. `.checkouts/main` is a core
checkout at 15.0.0-dev with its dependencies installed, this server is in its
`.mcp.json` and the skills are published beside it; started from that directory
the server answers `kind: core-checkout`. So the remaining step is not setup: it
is a session that is handed the prompt and nothing else.
`bin/cli scenarios:contract SKILL-12` prints what to paste, verbatim, and the
same for `SKILL-13`. Record what came back rather than what was expected.

**Whoever runs it may not have read the case.** The criteria are what a session
does — whether it separates what the reporter saw from what the reporter
believed caused it, whether it asks the review server before opening the
checkout, whether it reports "could not reproduce" as one of three things rather
than as the behaviour being gone. A session that has read `met 1` to `met 6`
measures its own ability to satisfy them and is indistinguishable from a real
run afterwards. That is why the session working this repository cannot be the
subject, and the whole of what `scenarios/readme.md` opens with.

What each case still needs is narrower than the whole of it. `SKILL-12` has
evidence for `met 3` and `met 4` from a triage of the RTE backlog on 2026-08-05
that was never given the case — it filed two verdicts where one issue number
held two defects. Do not read that as the run: no scenario was recorded, the
prompt was the user's own, and `met 4` was answered falsely, which is
`D-ANS-055`. The run is owed for `met 2`, `met 5`, `met 6` and step 1 as it now
stands. `SKILL-13` has no evidence of any kind, and its stopping rules in
`typo3-core-patch-checkout/references/checklist.md` are still written against no
conflict anybody has hit, so it needs a change that really conflicts rather than
one arranged to.

A finding here is a change to a published skill, which is the expensive kind —
the copy in somebody else's project is not corrected by the next release. All
four core skills were rewritten on 2026-08-07 for `D-SKL-022` and `R-SKL-018`,
so what `SkillTest` reads off the file has moved and what no assertion reaches
has not been measured since.
