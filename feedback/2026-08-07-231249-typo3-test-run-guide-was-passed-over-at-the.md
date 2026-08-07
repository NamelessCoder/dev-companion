---
date: 2026-08-07T23:12:49+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_test_run_guide was passed over at the exact point the session needed it

## Observation

Task: establish whether issue 15984 still reproduces in a 15.0.0-dev core checkout.

The strongest available proof was to enable one of the six data rows in typo3/sysext/frontend/Tests/Functional/SiteHandling/SlugLinkGeneratorTest.php that the core keeps commented out with "// @todo Fails, not expanded to sub-pages", and watch it fail. At that exact moment I ran a Bash one-liner checking for Build/Scripts/runTests.sh, docker and vendor/bin/phpunit, found no vendor directory, and told the user I had verified by reading code and the core's own disabled tests but had not executed the suite, offering to set it up.

typo3_test_run_guide was in my tool list, named for precisely that, and I never loaded its schema. The reason: I already had a conclusive answer from the disabled test rows, so running the suite became an optional extra rather than a step — and an optional extra does not feel like it needs a procedure. I reached for `ls` and `command -v` instead of asking how this repository expects functional tests to be run. That is the wrong instinct for a core checkout with a container-based runner, where the shell's PHP is not the interpreter the tests run under, and where "no vendor/bin/phpunit" means considerably less than it appears to.

Reporting this because a tool that was read and passed over is invisible to the server otherwise: it sees the calls that were made and nothing else.

## Query

End of session. I ran a Bash one-liner checking for Build/Scripts/runTests.sh, docker and vendor/bin/phpunit, then reported the functional suite as not executed. typo3_test_run_guide was present in the deferred tool list and its schema was never loaded.

## Suggestion

Nothing here says the tool is wrong; it is evidence about when it fails to get reached. If its description leads with running a suite that is already set up, consider having it also claim the earlier question — what this repository needs before a functional test can run at all, and which interpreter it runs under — because that is the question an agent actually holds at the moment it starts poking at runTests.sh with ls and command -v.
