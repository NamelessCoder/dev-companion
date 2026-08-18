# The project answer covers a clone nobody has installed yet

**Serves:** feedback/2026-08-18-070333-typo3-project-describe-answers-nothing-on-a.md
**Priority:** normal

`D-ANS-085` judged this step 1b and decided the seam: `typo3_project_describe`
answers what the repository's own files declare wherever a project root is
found, and withholds only `typo3Version`, `corePhpConstraint` and `extensions`,
which are read out of the installed tree. The first step is the root rule,
because it is where the change can go wrong: establish what identifies a TYPO3
project root with no `vendor/composer/installed.json` — read the `t3g/blog`
shape the feedback names, the projects below `.environments/`, and what
`Instance::locate()` walks past today — and write it so the twelve-directory
walk does not report a project for every PHP repository above the caller.
`D-ANS-085` also says why the feedback's own shape is not the one to build, and
names `D-ANS-083` as the entry to read before the `unsupported` answer stops
being what this state returns.
