---
id: D-AUD-001
date: 2026-07-29
status: confirmed
---

# D-AUD-001 — The outward description stays core-first until there is non-core knowledge

**The outward description stays core-first until there is non-core knowledge to
describe, and the requirement is the record that it is meant to change.**

The server is to serve core contributors, extension authors and site developers
(R-AUD-001 to R-AUD-004). What it currently *contains* is core knowledge, and what
it says about itself matches that: `knowledge/server-scope.json` opens with "a
curated knowledge base for contributing to the TYPO3 core".

## Decided

- The outward description stays core-first until there is non-core knowledge to
  describe. A promise is made when it can be kept; the requirement is the
  record that it is meant to be.

## Assumed

- The boolean `outsideCore` cannot carry this. An audience has at least three
  values and an honest fourth — unknown — and the flag was written when "not
  core" was the only distinction that existed.
- The audience is not readable from the checkout alone, because extension
  development happens inside site installations. Any detection that keys on the
  installation kind alone will be wrong for that case, which is a common one
  rather than an edge.

## Wrong if

- A signal turns out to identify the audience reliably on its own — the
  presence of `typo3/sysext/` in the touched paths comes closest — which would
  make the combining logic unnecessary complexity.

## Confirmed on 2026-08-02

It does not, and the combining stays. Both halves were measured against the
same signal, defined as the claim states it: the marker is present and the
answer is the core, or it is absent and the answer is not.

On the recorded runs the signal answers what the combination answers, 38
decisions out of 38. That is worth less than it reads. Every path handed to a
deciding tool by `REVIEW-01` (`E-SITE`, server `66813e3`) and `REVIEW-02`
(`E-EXT`, `b5555cb`) is outside-core work, and outside-core is what the marker
check returns for every path that does not carry it — which is all of them. It
agreed by defaulting, not by reading the same evidence: 27 of those decisions
were carried by a path marker or the extension layout, 8 by the installation
and one by the task text, and the marker check read none of the last nine. No
forward run has ever been recorded in `E-CORE`, so the run corpus has one side
of this question in it and cannot settle it either way.

The checkout is where it separates. Of what `.checkouts/14.3` holds outside
`typo3/sysext/` — its nine other root entries, plus `Build/Scripts/` and
`Build/Sources/` — the signal calls all eleven outside the core, and all eleven
are core work. `Build/Scripts/runTests.sh` is the sharpest: it is the script
every suite in `typo3_test_run_guide` invokes, so the contributor standing in
the repository that has it would be told those suites are not theirs to run.
Three calls that name no path — a fix pushed for review, a state on a backend
list row, a deprecation — go the same way, and that is the commoner shape,
because a brief is asked for before there is a file to name.

What carries those fourteen is not the combining in general but two of the
members: the core's own `Build/` layout where the manifest allows the
repository to be the core, and the installation. The third value is a second
reason the signal cannot stand alone — it has two values, and `uncertain` is
the one `R-AUD-002` asks for. The table is
`ScopeTest::theSysextSignalAloneAnsweredEveryDecisionTheRecordedRunsMade` and
`::theSysextSignalAloneAnsweredNothingACoreCheckoutDecides`, and collapsing
`audienceOf` to the marker fails the second one fourteen times. What would
reopen this is a recorded run in `E-CORE`: it is the one environment the
measurement had to reach for the checkouts instead.
