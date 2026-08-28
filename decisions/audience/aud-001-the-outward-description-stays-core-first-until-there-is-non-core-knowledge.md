---
id: D-AUD-001
title: The outward description stays core-first until there is non-core knowledge
date: 2026-07-29
status: confirmed
---

# D-AUD-001 — The outward description stays core-first until there is non-core knowledge

**The outward description stays core-first until there is non-core knowledge to
describe, and the requirement is the record that it is meant to change.**

The server is to serve core contributors, extension authors and site developers
(R-AUD-001 to R-AUD-004). What it currently *contains* is core knowledge, and
what it says about itself matches that: `knowledge/server-scope.json` opens with
"a curated knowledge base for contributing to the TYPO3 core".

## Decided

- The outward description stays core-first until there is non-core knowledge to
  describe. A promise is made when it can be kept; the requirement is the record
  that it is meant to be.

## Assumed

- The boolean `outsideCore` cannot carry this. An audience has at least three
  values and an honest fourth — unknown — and the flag was written when "not
  core" was the only distinction that existed.
- The audience is not readable from the checkout alone, because extension
  development happens inside site installations. Any detection that keys on the
  installation kind alone will be wrong for that case, which is a common one
  rather than an edge.

## Wrong if

- A signal turns out to identify the audience reliably on its own — the presence
  of `typo3/sysext/` in the touched paths comes closest — which would make the
  combining logic unnecessary complexity.

## Confirmed on 2026-08-02

The combining stays. On the recorded runs the marker alone answers what the
combination answers, 38 of 38 — and it agreed by defaulting rather than by
reading the same evidence, since every path in those runs is outside-core work
and the marker read none of the nine decisions the installation and the task
text carried. The checkout is where it separates: of what a core checkout holds
outside `typo3/sysext/`, the marker calls eleven directories outside the core
and all eleven are core work, `Build/Scripts/runTests.sh` most sharply. What
would reopen this is a recorded run in `E-CORE`, the one environment the
measurement had to reach for the checkouts instead.
