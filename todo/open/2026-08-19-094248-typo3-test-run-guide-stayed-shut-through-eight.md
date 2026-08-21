# The project answer names what a declared test command needs outside the core

**Serves:** feedback/2026-08-19-094248-typo3-test-run-guide-stayed-shut-through-eight.md, D-ANS-092
**Priority:** normal

Give `ProjectDescribe::suites()` its arm for the other kinds of instance: where
the repository is not a core checkout and the declared commands include a test
suite, the answer names `typo3_hint_lookup` with `id=project-extension-tests` as
what a functional run needs before its first assertion, the way the core arm
names `typo3_test_run_guide`. Read `project-extension-tests` first — it opens on
building a harness, and a caller whose harness exists has to reach the
credentials without reading past three hints that are not theirs, so the lead
and the `appliesTo` phrases are part of this step. The two facts the feedback
asks for that nothing here has established are established before they are
written: that `ddev exec` refuses from a git worktree while the project runs
from its own directory, and that the PHP a DDEV container runs and the PHP a
lockfile resolves PHPUnit for can disagree, with the port-mapped database as the
way out. `D-ANS-092` carries the reading behind all of it, including what was
rejected.
