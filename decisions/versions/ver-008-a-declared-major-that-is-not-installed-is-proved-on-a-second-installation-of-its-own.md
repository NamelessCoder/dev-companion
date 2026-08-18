---
id: D-VER-008
date: 2026-08-18
status: open
---

# D-VER-008 — A declared major that is not installed is proved on a second installation of its own

**Where a package declares a TYPO3 major the installation does not have, this
server says how a run against it is stood up beside that installation.**

The upgrade workflow ends in a proof per declared combination and names no way
to make a cell exist. One session paid a full core download to find out how, and
the next declined the same step three hours later because nothing said what it
would overwrite.

## Evidence

- `feedback/2026-08-18-081129`. `t3g/blog` declares
  `^13.4.15 || ^14.3 || 15.*.*@dev` with 14.3.6 installed, and the user asked
  for a TypoScript condition fix that keeps working on 13. The session proved
  the 14 half by rendering the frontend before and after, and the 13.4 half by
  argument. What stopped it was operational rather than technical: a pin inside
  the DDEV project rewrites `.build/` and the installed state, nothing says what
  that costs or how to get back, and an unrecoverable installation looked worse
  than an unproven claim.
- `feedback/archive/2026-08-18-074124`, the same checkout two and a half hours
  earlier, ran it: it "built a second installation under `var/v13` with
  `composer require typo3/cms-core:^13.4` and ran the suite there, which is the
  right proof but costs a full core download". Two sessions, one question,
  opposite answers — and the one that ran it put the other major in a directory
  of its own rather than repinning the installation, which is the fact the
  second session was missing.
- `typo3-extension-upgrade` promises the proof and stops short of it. Its
  description offers "proving every version it claims", and **Prove it on every
  version it claims** says to build the matrix from the declaration, resolve
  each cell, run the repository's own commands per cell, and name an unrun cell
  as unrun. Neither it nor `typo3-extension-testing` says how a cell that is not
  the installed one comes to exist; that skill reaches a matrix once, in CI.
- `bin/cli hints:probe "install a second TYPO3 major beside the installed one to run the suite against it"`
  matched nothing on 2026-08-18, out of 98 candidates. The feedback's own query
  reaches `project-extension-tests`, `project-configuration-files` and
  `browser-tests-outside-core`, none of which is about a major other than the
  installed one.
- The neighbours are taken and none of them is this. `installation-upgrade`
  moves the developer's own installation to a new major for good — the dump
  before the first write, the wizards, "everything after it is not revertible" —
  and `typo3-development-installation` puts it outside itself as "a project of
  its own rather than a verb of the one somebody develops in".
  `typo3_test_run_guide` answers for a core checkout's `runTests.sh`.
  `extension/compatibility/a-declared-major-that-is-not-installed` ends where
  this starts: reading settles the shape, and "an installation on the other
  major with the package's own suite against it is what says so".

## Decided

- **Step 1a, and taken on.** What is missing is operational fact about Composer,
  the environment and the database that no session can recall, and it is a
  procedure rather than a statement — so it lands as a `knowledge/documents/`
  page in the extension scope rather than as a hint (`D-FBK-043`).
- **Where the boundary runs.** Inside: whether the repository's own CI already
  covers the cell, which is asked before anything is installed; standing the
  other major up in a directory of its own rather than repinning the
  installation the developer works in; what that writes and what it leaves
  untouched; whether the database survives; which checks are worth re-running
  there and which are version-independent; and what is left behind afterwards.
  Outside, unchanged: upgrading the developer's own installation, which
  `installation-upgrade` carries; the API question, which `D-VER-007`'s page
  settles by reading; and declaring the matrix in CI, which
  `typo3-extension-testing` owns.
- **The fallback half is answered and trimmed rather than built twice.** The
  report asks that where the second major cannot be run, the static argument be
  declared unproven and say what it covers.
  `extension/compatibility/a-declared-major-that-is-not-installed` was written
  the same day and its closing section is that: the symbols, the branch and the
  revision it was at, what the reading left uncovered, and that it is unproven
  outright. The execution order the report also names is what "left uncovered"
  already asks for, and a second clause for it would be one policy in two
  places.
- **`typo3_project_describe` does not grow a reading of CI.** The report asks it
  to report the workflow matrix beside `coreConstraint`, so a caller sees at
  once whether a claimed major is covered by anything. What earns a tool is the
  round trips it takes off the caller (`D-FBK-027`), and `.github/workflows/` is
  a fact the caller reads once from its own checkout with nothing in the way —
  which `AGENTS.md` names as what does not earn one. The page tells the session
  to read it, as its first question rather than a later one.
- **Priority `normal`, set by arrival rather than by weight.** Two sessions from
  one checkout reached this within three hours and one of them shipped an
  unproven half, which is not `low`. One repository and one session series are
  not `high`.
- **The feedback stays open behind the card**, which is what `D-FBK-017` asks of
  a judgement that turns a feedback into work.

## Assumed

- That a second installation of its own is what the procedure will recommend. It
  is read off the one session that ran it rather than off a run made here, and
  what a pin inside the working project actually rewrites is the reading the
  card starts with.
- That the database is the expensive half of the question. Both reports name it
  and neither measured it.
- That the situation is ordinary rather than rare. `D-VER-007` rests on the same
  assumption and nothing here has counted the repositories that declare two
  majors.

## Wrong if

- The reading finds that the CI of such a repository answers nearly every case,
  so the page is one paragraph saying to push it and a procedure nobody runs.
- The pin inside the working project turns out to be revertible in one command.
  Then the second installation is a cost this entry wrote into the corpus for
  nothing, and the page has it the wrong way round.
- A session follows the page, stands the other major up, and cannot run the
  suite there because the harness the package declares needs the installation
  the page told it to leave alone. That would put the boundary between this page
  and `typo3-extension-testing` in the wrong place.
- A feedback reports the page read as permission to rebuild the installation the
  developer works in. That is the failure the second session avoided by
  delivering an unproven half, arriving from the other side.
