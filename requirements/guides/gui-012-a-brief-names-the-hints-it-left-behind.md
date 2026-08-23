---
id: R-GUI-012
title: 'A brief names the hints it left behind'
status: held
restsOn: [D-GUI-007]
heldBy:
  - HintsTest::aBriefNamesTheHintsItLeftBehind
---

# R-GUI-012 — A brief names the hints it left behind

**Where `typo3_task_guide` carries fewer hints than `typo3_hint_lookup` holds
for the same paths, the brief names the ones it left.**

A sentence that states the count and not the subjects leaves a caller reading
four hint bodies unable to tell whether the fifth is a variation of one of them
or the one subsystem the work is really in. "Call it for the rest" is then a
pointer with nothing behind it, and the cheapest way to find out what the rest
is remains the call the pointer was standing in for. The ids are what turns it
into a reason: three names under the four bodies is one line, and a subsystem
the brief did not reach is visible before a file is opened.

## From

`feedback/2026-08-03-144410`, the debrief of a review of core commit
`9f6c6eb9093` (2026-08-03). It read the brief's four hints, made no separate
`typo3_hint_lookup` call, and established `#[Autowire(lazy: true)]` for the
patch's new service dependency by grepping three call sites out of the checkout.
`dependency-injection` is the seventh hint the lookup holds for the five paths
that brief was composed for.
