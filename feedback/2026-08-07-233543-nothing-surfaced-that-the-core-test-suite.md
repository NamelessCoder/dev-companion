---
date: 2026-08-07T23:35:43+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# nothing surfaced that the core test suite already carries this bug as commented-out @todo asserti...

## Observation

Task: establish whether Forge issue 15984 still reproduces against a 15.0.0-dev core checkout.

The strongest single piece of evidence in this triage is that the core's own functional suite already documents the defect, as data-provider rows commented out with a reason:

    // ['https://acme.us/', 1100, 1521, 1500, 0, '<a href="/my-acme?pageId=1521"></a>'], // @todo Fails, not expanded to sub-pages

Three such rows sit in typo3/sysext/frontend/Tests/Functional/SiteHandling/SlugLinkGeneratorTest.php at lines 417, 421 and 428. The fixture behind them, Fixtures/SlugScenario.yaml, already contains the exact constellation the 2006 report describes: page 1520 "Forecasts" with visitorGroups 20 and extendToSubpages true, and page 1521 "Current Year" as its unrestricted child. The reproduction therefore needed no new test and no new fixture — it needed one comment removed. Uncommenting the guest row and running the suite produced the red I needed:

    - '<a href="/my-acme?pageId=1521" data-access-restricted="true">Current Year</a>'
    + '<a href="/my-acme/forecasts/current-year">Current Year</a>'

I reached that file in four steps of hand reading: grep for showAccessRestrictedPages, read the test's setUp for its scenario file, grep the fixture for the page ids, read the data provider. No lookup pointed at it.

typo3_test_run_guide did what it promises — which suites can fail on the paths I passed, the targeted invocation form, and the option list — and its notes are what told me to pass -b docker, since podman is the default and this machine has only docker. It does not, and does not claim to, know that the suite already contains a disabled assertion for the behaviour under triage.

This is a general capability rather than a one-off. The best possible outcome of a core triage is "the project already knows, here is the disabled assertion and here is the fixture", and it is cheap to detect: commented-out data-provider rows carrying @todo, plus markTestSkipped and markTestIncomplete, matched against the words of a report. It would convert a four-step manual hunt into one call, and it turns a triage directly into a patch-ready starting point — the failing test the skill asks a triage to produce had already been written and switched off.

## Query

Establish whether Forge 15984 still reproduces on 15.0.0-dev. The decisive artefact — typo3/sysext/frontend/Tests/Functional/SiteHandling/SlugLinkGeneratorTest.php lines 417, 421, 428 — was found by hand-reading the test file and its fixture, not from any lookup.

## Suggestion

Add a lookup over the core checkout's test suites for disabled or known-failing assertions — commented-out data-provider rows adjacent to @todo, plus markTestSkipped and markTestIncomplete — searchable by the words of an issue and by path. For issue 15984 it should return SlugLinkGeneratorTest.php lines 417, 421 and 428 with their @todo text, and the SlugScenario.yaml entries for pages 1520 and 1521 that already model the case.
