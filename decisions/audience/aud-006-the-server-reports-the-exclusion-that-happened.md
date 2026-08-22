---
id: D-AUD-006
title: 'The server reports the exclusion that happened'
date: 2026-08-04
status: open
coveredBy:
  - ExcludedToolsTest::neitherSurfaceCallsAToolExcludedThatIsInTheList
  - ExcludedToolsTest::theScopeNamesWhatTookNothingAwayAsIgnoredRatherThanAsExcluded
  - EntrypointTest::anExcludedNameThisServerOffersAnywayIsSaidOnStderrToo
  - InstallerTest::codexInstallKeepsTheLinesOfTheSectionItDoesNotOwn
  - InstallerTest::codexInstallRefusesASectionItCannotRewriteWithoutDropping
---

# D-AUD-006 — The server reports the exclusion that happened

**What the client is told is missing is what the offered list is actually
missing, and a name in `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` that took nothing
away is reported as that.**

`ExcludedTools::all()` was what the caller wrote, so both client-facing surfaces
described a capability the same server had just offered.

## Evidence

- Measured on 2026-08-04 in this checkout, over one stdio session per case.
  `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_project_describe, typo3_icon_lookup`:
  25 tools offered including `typo3_project_describe`, the initialize
  instructions opening "typo3_project_describe, typo3_icon_lookup are left out
  of your tool list" at 1940 characters, and `excludedTools.names` carrying
  both. `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_feedback_record`: 26 tools
  offered including it, the instructions opening "typo3_feedback_record is left
  out of your tool list" at 1920 characters, `excludedTools.names` carrying it,
  and nothing said on stderr, because that name is in the registry.
- The instructions are capped at 2048 characters by
  [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md),
  and the exclusion prefix is counted against it. So the false sentence was
  displacing a true one that a client would otherwise keep. After the trim the
  same two cases measure 1916 and 1818 characters.
- The three names that cannot shorten the list are
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md)'s,
  and two of them are outside `ExcludedTools` entirely: `Registry::offered()`
  appends the feedback tools past the filter under
  [`D-FBK-042`](../feedback/fbk-042-the-read-only-boundary-is-the-installation.md).
- The TOML half of the installer, measured the same day in a fixture project:
  `.codex/config.toml` carrying
  `env = { TYPO3_DEV_COMPANION_EXCLUDE_TOOLS = ... }` and
  `startup_timeout_sec = 30` in its section had both gone after
  `install --agent=codex`, with a `Configured` message and nothing else said.
  `installTomlConfiguration` matched the section with a regex and replaced the
  match whole. `453e439` had fixed the JSON path by writing back every field it
  does not own, and left this one carded rather than half-fixed.

## Decided

- `ExcludedTools::all()` answers what is really gone, and the report is derived
  rather than declared: the registry is asked twice, once with the filter and
  once with this class answering empty, and every name the caller wrote falls
  into one of three states by comparing the two lists. An exception added to
  `Registry::offered()` therefore arrives in the report without this class being
  told about it — which is the reason for asking twice rather than for listing
  the three protected names here.
- Nothing about `Registry::offered()` changes. The behaviour was settled by
  `D-FBK-042` and `R-SCO-009`; what was wrong was the description of it.
- The result is memoized against the raw variable. The trim asks the registry,
  which asks every tool for its schemas, and `all()` is read several times per
  answer — from a variable the tests change between two of them.
- The two reasons a name takes nothing away are said apart on stderr, because
  what somebody has to change differs: a name no tool answers to is corrected, a
  name this server offers anyway is dropped from the variable. They are one list
  in `typo3_server_scope`, under `excludedTools.ignored`, because to a client
  they are one fact — the tool is in the list it was handed. That answers
  `D-AUD-005`'s third **Wrong if**: the report is now in-band as well, where a
  client that discards stderr cannot lose it.
- `excludedTools.ignored` is not a required property. Absent means nothing to
  report, which is every session that set nothing.
- The TOML path keeps what it can read and refuses what it cannot. A section's
  lines are classified one at a time: blank, comment, or a whole `key = value`.
  `command` and `args` are rewritten where they stand, everything else is copied
  through, and a line whose value does not end on it fails the run with the file
  and the line number. Refusing there rather than guessing is the point — the
  outcome that must not survive is the silent deletion, and a value continued on
  the next line cannot be copied without the key that opens it.
- A full TOML parser was rejected. It would be a dependency, or a second
  implementation of one, for two clients and two keys; what this needs is to
  know where a line ends, which is a smaller question than what a value means.

## Assumed

- A section this package writes into is written one key per line. That is what
  every client's own documentation shows and what this package itself writes, so
  the refusal is a corner rather than the ordinary case.
- The offered list is what a client sees. The report is derived from
  `Registry::definitions()`, so a client shortened anywhere else — an SDK
  filter, a transport — would be described by a list it does not have.

## Wrong if

- A caller is told a tool is missing and finds it in `tools/list`, or told one
  was ignored and finds it gone. Either way the two lists that the report is
  derived from stopped being the ones the client is handed.
- The refusal fires on a file somebody actually keeps. Then one key per line is
  not how these sections are written, and preserving them needs a parser rather
  than a line classifier.
- An `install` or an `update` is found to have dropped a line of a section
  again. The JSON path and the TOML path now make the same promise, and it is
  the promise, not the format, that a caller relies on.
