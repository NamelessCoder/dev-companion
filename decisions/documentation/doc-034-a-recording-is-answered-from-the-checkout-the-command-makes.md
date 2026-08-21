---
id: D-DOC-034
date: 2026-08-18
status: open
---

# D-DOC-034 — A recording is answered from the checkout the command makes

**`bin/cli tools:record` refuses a core checkout below `.checkouts/` that
carries anything `bin/cli checkouts:update` did not put there, rather than
recording what such a tree happens to answer.**

The first root is a checkout because that is the one installation this
repository can recreate, which is what makes the recording repeatable
(`D-DOC-006`). Nothing held the tree to what the command makes, so a
`composer install` in it changed what was recorded and no reader could tell.

## Evidence

- All four checkouts on this machine carry an installed `vendor/`, which nothing
  in this repository writes: three of them from 2026-08-14 and `main` from
  2026-08-03. What that leaves is six entries in `.checkouts/14.3` as of
  2026-08-18 — `.cache/`, `bin/`, `index.php`, `typo3/sysext/core/bin/`,
  `typo3temp/`, `vendor/` — every one of them ignored by the core's own
  `.gitignore`, so `git status --porcelain` calls the tree clean and `--ignored`
  is what reports them.
- `bin/typo3` is one of the two paths `Typo3Cli` probes, so the checkout has a
  console again. The installation-backed tools then record a Doctrine exception
  about a missing database in place of the shapes the recording exists for: five
  answering `answeredBy: "packages"` and two answering `unsupported` with
  `installation-not-answering`.
- The recording committed in `73cff0ab` was made against a 14.3 tree rebuilt
  from that checkout's index, by hand and outside any command here. That is the
  cost this settles: it is the only way the pages could be produced at all, and
  nothing said so.
- `git status --porcelain --ignored` collapses an ignored directory to one entry
  and takes 0.095s on that checkout, so asking before every recording costs
  nothing.

## Decided

- The command that has the requirement states it, at the moment it matters.
  `tools:record` asks git what the root carries beyond its index and exits 2
  naming what it found, the `git clean` that takes the checkout back, and the
  other way out — an installation of the caller's own.
- Only a root below `.checkouts/` is asked. Those are this repository's own and
  are made by one command; a root somebody named is theirs, and whether a
  recording from it is reproducible is a question for whoever commits it.
- Anything beyond the index refuses, not just an installed console. A recording
  is reproducible from `checkouts:update` or it is not, and an edited tracked
  file reaches an answer exactly as an installed one does — which of them a tool
  happens to read is not knowable from here.
- Rejected: `checkouts:update` taking such a checkout back to what it makes. Its
  stated job is to create and fetch, and cleaning would delete a
  `composer install` somebody made to run the core's own tests — 203 MB and
  several minutes in `.checkouts/14.3` — as a side effect of updating a branch.
  It also fixes the state at a moment nobody records at: install again
  afterwards and the recording is wrong with nothing to say so.
- Rejected: a third written root, a pristine tree this repository produces per
  run the way it writes `Fixture` and `CoreFixture`. Those two are written
  because they are small; a core checkout is 181 MB without its packages, and
  `checkouts:update` already makes exactly this tree. A second copy to defend
  the first against being installed into is a concept where a sentence does.

## Assumed

- A checkout below `.checkouts/` is read and nothing else, so a refusal there is
  never a false one. An editor writing `.idea/` into a checkout somebody opened
  to read core sources would refuse a recording that would have been correct,
  and the remedy is stated rather than automatic.

## Wrong if

- The refusal is met by a session that cleans the checkout to get past it, and
  the `composer install` it removed was somebody's work. Then what a checkout is
  for is contested and this is the wrong end to have settled it at.
- A recording is wanted on a machine where the checkout has to stay installed —
  the same person verifying knowledge by running the core's tests and recording
  the tool surface. Then the two uses need two trees and the rejected third root
  comes back.

## Covered by

- `CheckoutsTest::everyEntryGitReportsIsCarried`
- `CheckoutsTest::bothKindsOfChangeAreCarried`
- `CheckoutsTest::aGitThatCannotAnswerReportsNoDifference`

## Since then

- 2026-08-21: the recording's own sentence said what the uninstalled checkout
  costs but not why, and it read as a property of core checkouts. `.checkouts`
  is a worktree nothing was installed in, and the core monorepo declares
  `bin-dir: bin` — so the two paths the reason listed as absent are exactly the
  ones `composer install` would write. `Typo3Cli::reason()` now says the
  dependencies are not installed and names the missing autoloader wherever the
  console is absent and no autoloader stands beside it, which the preamble of
  every recorded page carries. Held by
  `Typo3CliTest::aCheckoutThatWasNeverInstalledSaysThatRatherThanNamingEmptyPaths`
  and its counterpart for an installed root whose console sits elsewhere.
