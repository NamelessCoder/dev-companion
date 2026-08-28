---
id: D-ANS-045
title: 'The Classes section reports every directory below it'
date: 2026-08-03
status: open
coveredBy:
  - ProjectTest::aFluidRootIsRenderedAsADirectory
  - ProjectTest::everyDirectoryBelowClassesIsInTheAnswer
---

# D-ANS-045 — The Classes section reports every directory below it

**`typo3_extension_describe` reports every directory below `Classes/` and every
PHP file under it, and a value it derives from a directory existing is not
presented as a registration.**

The answer promises "the shape of its Classes/ directory" and describes a
whitelist of thirteen names. What falls outside is dropped without a trace, and
a caller who trusts the promise never learns there was more to open.

## Evidence

- `feedback/2026-08-03-164651`, re-run on 2026-08-03 from
  `/home/benji/projects/ext-guidedtour` through this worktree's
  `bin/typo3-dev-companion`, server 0.3.0. It reproduces exactly:
  `classes: [{"kind": "EventListener", "files": 2}]`, while
  `find Classes -name '*.php'` gives three files in two directories.
  `Classes/Utility/` is under no kind and in no line of the answer.
- Extension::CLASS_KINDS is a closed list of thirteen names, and
  `Extension::classes()` iterates it and nothing else. A directory that is not
  on the list is dropped, and so is a PHP file lying directly in `Classes/`.
- Measured against `.checkouts/14.3`, the filter is not an edge case. `core` has
  seventy directories below `Classes/` and 1508 PHP files under it; thirteen of
  those directories and 106 of those files are on the list. `extbase` reports 27
  of 284, `frontend` 53 of 144, `install` 48 of 158.
- One instance is in this repository already.
  `documentation/server/tools/typo3_extension_describe.md` records
  `Classes: Command (8), Controller (90), … ViewHelpers (15)` for `backend`.
  Those counts sum to 343, against 671 PHP files below that extension's
  `Classes/`.
- Nothing records the whitelist as a boundary.
  [`D-ANS-014`](ans-014-the-extension-answer-enumerates-registrations-not-files.md)
  puts the file tree on `glob`'s side, and
  [`D-ANS-008`](ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md)
  deliberately puts this section on the other one: it "describes what an
  extension's `Classes/` holds rather than what it registers". A section on the
  file side that reads thirteen of seventy directories is a filter nobody chose.
- The `fluidRoots` half holds too. `Extension::fluidRoots()` is `is_dir()` over
  three fixed names, and the audited extension declares no Fluid root at all:
  `Classes/EventListener/LoginTourEventListener.php:46` appends
  `EXT:guidedtour/Resources/Private/Layouts` to `setLayoutRootPaths()` while the
  event runs.
- Where that reads as a registration is the text and the description, not the
  schema. The field says "Which of Resources/Private/Templates, Partials and
  Layouts exist", which is exact. The rendered line is
  `Fluid roots: Resources/Private/Layouts/` with no qualifier, and
  `ExtensionScope::description()` lists "Fluid roots" among the service tags and
  the middlewares.

## Decided

- The whitelist goes, and the boundary is that no directory below `Classes/` and
  no PHP file under it is absent from the answer. That is settled here because
  it is about this answer's shape rather than about TYPO3.
- Which shape carries it belongs to the todo. A row per directory, the
  feedback's `Other` bucket for a name that matches nothing known, a total
  beside the breakdown — all three satisfy the line above, and the tool is where
  they are weighed against each other.
- **Queued rather than closed on the spot.** Both halves touch `src/` and the
  declared `outputSchema`, which
  [judging.md](../../documentation/records/judging.rst) puts on the reviewed
  side of that line.
- The priority is `high` for the classes half, and this is what set it. The
  filter is silent, it holds for every extension this server answers for, and
  `skills/typo3-extension-conformance` routes an audit at exactly this section.
- The `fluidRoots` half is step 4, wording, and narrow: the rendered line and
  the tool description, not the schema field, which already says what it means.
  It is `normal`, and it is a second todo because it is a second step.
- The feedback stays open behind the two todos, and the card that asked for this
  judgement is deleted by the same commit.

## Assumed

- That a row per top-level directory stays readable where there are seventy of
  them. The rendered line joins the kinds with commas, so `core` would carry a
  long one, and the `Other` bucket is the cheaper shape.
- That the counts themselves are right. `Extension::countPhpFiles()` walks the
  subtree, `D-ANS-008` settled that it says so, and nothing here disputes a
  number.
- That `typoScript`, `files` and `artifacts` are not in the `fluidRoots`
  position. Each names a file that is there, which is nearer to a reading than
  to a guess, and none of them was checked here.

## Wrong if

- A feedback reports the widened list as noise. That is
  [`D-ANS-014`](ans-014-the-extension-answer-enumerates-registrations-not-files.md)'s
  third **Wrong if** arriving at this section, and the boundary would then be in
  the right place and drawn too far out.
- A caller checks the section after the change and still gets a number `find`
  disagrees with. What was missing would then be the shape rather than the
  coverage.
- `fluidRoots` is read as a registration by a session that had the qualified
  line in front of it. The field would then be the problem rather than its
  wording, and removing it would be the change.

## Since then

The shape left to the todo is settled and it is all three at once: a row per
directory, the files lying loose counted as their own row, and the total beside
the breakdown.

The bucket alone was rejected, because it names no directory and the name is
what the audit needed — it went past a directory without learning there was one
to open. The total is there because the check that found the gap was a count,
and summing forty-six rows by hand is not that check. The **Assumed** section's
fear is real and accepted: one extension's line names seventy-one things, and
the cheaper shape buys that back by dropping the names.

## Since then

Extension::CLASS_KINDS is gone and `Extension::classes()` reads the directory:
`Finder::create()->directories()->in($directory)->depth(0)` takes every
directory below `Classes/`, so the bullet above describes a closed list that no
longer decides anything. What it settled still holds — the section covers the
directory it names — and the mechanism under it is the opposite of the one
recorded, a reading rather than a list.
