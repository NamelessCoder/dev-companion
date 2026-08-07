# Claim the work that ends before a patch

**Serves:** feedback/2026-08-07-231236-typo3-task-guide-reads-as-being-about-writing-a.md, feedback/2026-08-07-231249-typo3-test-run-guide-was-passed-over-at-the.md
**Priority:** high

The instructions say "The coding agent writes the patch; this server supplies
the task knowledge and workflows around it", and it sits immediately after the
sentence routing a task to `typo3_task_guide`. A session asked whether a 2006
bug still reproduces read that as addressed to work it was deliberately not
doing — the user's words were "before I touch it" — skipped the guide, and says
it would make the same call again from the same wording. Name the work that ends
before a patch: backlog triage, establishing whether a report still reproduces,
and pricing what a fix would cost. All three are task shapes `scenarios/` holds
cases for, so this is coverage the server has and does not claim. Put it in the
instructions and in `typo3_task_guide`'s own description, and mind `R-ANS-013`,
which holds the instructions to a budget — this should replace framing rather
than add to it. In the same pass, `typo3_test_run_guide` opens with recommending
`runTests.sh` commands, which claims running a suite that is already set up; the
question a session actually holds at that moment is what this checkout needs
before a functional test can run at all and which interpreter it runs under,
which is why one reached for `ls` and `command -v` instead. `D-AUD-009` carries
both.
