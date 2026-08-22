# SKILL-08 — Establish static quality beside the checks that exist

**Environment:** `E-EXT`, in an extension that runs a code style fixer and a
syntax lint but has no static analyser, no baseline and no test suite ·
**Contract:** `held`
**Held by:**
`SkillTest::extensionTestingKeepsCheckingApartFromFixing`,
`SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand`

> Set up PHPStan and CGL for this TYPO3 extension, fix the findings in the
> changed code, and make the same checks run locally and in CI without replacing
> the quality commands we already have.

**What has to come out of it**

- The answer establishes the missing analysis rather than declining it because
  the project does not run one yet, and it names what a complete check surface
  covers before saying what is missing here.
- The existing fixer, lint step and their configuration survive and are run
  before anything is changed; what is added extends them instead of becoming a
  parallel set of commands.
- Development dependencies are resolved against this package's declared TYPO3
  and PHP range and accepted by the solver before a constraint is written.
- Each check gets one project-owned command, the command that reports is
  separate from the command that writes, and CI calls the commands that passed
  locally.
- Findings in the changed code are fixed. A baseline, if one is created at all,
  holds what was already there and is named as a work list with a horizon.
- Automatic formatting stays inside the extension's own files, and the answer
  reports which files the fixer changed.

**How it fails**

- Static analysis is reported as out of scope, or handed back as a testing
  question, because the extension does not use it yet.
- The analyser is pointed at the installed core, the vendor tree or generated
  output, so its report is about code the extension does not own.
- New findings are written into a baseline, or the analysis level is set where
  the report is empty.
- One command both checks and rewrites, so CI cannot call it and the working
  tree changes during a review.
- The fixer runs over vendored, generated or third-party files, and the answer
  does not say which files it touched.
