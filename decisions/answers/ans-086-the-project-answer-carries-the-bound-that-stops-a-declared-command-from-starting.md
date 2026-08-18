---
id: D-ANS-086
date: 2026-08-19
status: open
---

# D-ANS-086 — The project answer carries the bound that stops a declared command from starting

**`typo3_project_describe` states the PHP bound Composer wrote into the vendor
tree it read, and says where the interpreter that would run the declared
commands stands against it.**

A command marked `check` is offered as one a task told not to change files may
run. Whether it starts is a second question, and the answer carried nothing that
could be read for it.

## Evidence

- `feedback/2026-08-18-113412` reports `composer cgl:ci` in a sitepackage
  aborting in `.build/vendor/composer/platform_check.php` — *requires a PHP
  version >= 8.4.0. You are running 8.3.23* — before the fixer ran. The session
  looked for `php8.4` and `php8.5`, found only 8.3, and deferred the check to
  GitHub Actions.
- The same mechanism twice more, in two other kinds of checkout.
  `feedback/archive/2026-08-07-125950`: the core's pre-commit hook died in
  `vendor/composer/platform_check.php` on host PHP and then printed a missing
  file header for every commit regardless of content. `D-KNW-036`: running
  `cglFixMyCommit.sh` on the host was rejected as the handed-over check because
  `main` pins 8.5 and the host has 8.3, so it stops at the same file.
- Three PHP numbers are already in that answer and none of them is this one.
  `phpConstraint` is what the root requires, `corePhpConstraint` what the
  installed core requires, `environment.php` what a DDEV container runs. The
  bound is the highest requirement over every package installed into the tree,
  so a dev-only fixer raises it and no manifest field states it: this checkout's
  own `vendor/composer/platform_check.php` says `>= 80200`, matching its
  `require.php` of `>=8.2`, while the reported tree said 8.4.0 against a
  sitepackage that declares neither.
- The reader exists and is one method. `Typo3Cli::installedPhpBound()` parses
  that file for the console, and `R-DIS-010` is the same distinction — reachable
  and ready are two questions — already drawn for the boot and not for the
  commands the same server hands over.
- The tree is the one this tool already reads. `typo3_project_describe` reports
  `installed` off the Composer metadata under the vendor directory the
  repository declares, which is where the reported abort came from.

## Decided

- The bound is a number of its own beside the three, and `runs` gains nothing.
  What a command does to the sources and whether it starts are two questions,
  and one enum answering both would make `check` mean two things — `R-PRJ-007`
  is about the first only.
- What is stated is the bound, the interpreter, and the environment this
  repository configures, in the shape `R-DIS-010` already requires: both steps
  that end the state, never one.
- Rejected: widening `typo3_test_run_guide`, which the feedback asks about
  first. Its suites are `Build/Scripts/runTests.sh` invocations, that script is
  in the core repository, and the tool already declines a project path rather
  than handing over commands that do not exist there. A repository's own
  declared commands are the project answer's subject.
- Rejected: recommending a container image. Which image runs a given fixer is
  read from nothing here, and a recipe this server cannot check is a guess with
  an answer's authority. The core case has a container to hand over because
  `runTests.sh` is in the checkout; a sitepackage has none, and saying so is the
  honest half.
- Rejected: judging whether deferring a check to CI hides something, which the
  feedback also asks for. That is a judgement about one change, and what this
  server can supply is the fact the judgement is made on.
- Where the repository configures no environment, the answer states the bound
  and names `php -v` as what settles it against. No interpreter is discovered:
  the shell a declared command runs in is the caller's own, and the process this
  answer is composed in is not it — a version read from here would be the wrong
  claim in exactly the case the bound is reported for. `Typo3Cli` does search
  for one, for a boot this server performs itself, which is the other question.
  It is also what `R-PRJ-001` asks, and the answer stays readable from files.

## Assumed

- That the bound in `platform_check.php` covers the tool the declared command
  invokes. It does where that tool is installed into the tree the project
  declares, which is what both reports had.
  `skills/typo3-extension-testing/references/static-quality.md` names the other
  layout — a second manifest below the build directory with a vendor directory
  of its own, where the solver refuses a check tool — and that tree has a bound
  this does not read.
- That Composer writes the file at all. It leaves it out where nothing requires
  a PHP version and deletes it where `platform-check` is off or the platform
  requirements were ignored, and absent has to read as no bound rather than as
  none found.

## Wrong if

- The bound equals the project's own floor in every repository somebody runs
  this against, which would mean the fourth number bought nothing and
  `phpConstraint` was saying it all along.
- A command reported as startable aborts anyway — its tool sits in a vendor tree
  this did not read, or the binary carries a requirement Composer never wrote
  into the check. The claim would then be one the file cannot carry, and what is
  left is naming the bound without saying which commands clear it.
- The repositories this happens in declare no PHP floor of their own. The bound
  is stated either way and the text relates it to the environment either way,
  but `phpRelation` is withheld whole where no floor was read (`R-PRJ-010`), so
  a client reading the data alone would get the number and not the standing.
  What is left then is relating the bound outside that object.
