---
date: 2026-08-18T08:06:30+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-extension-upgrade, typo3_task_guide
directory: /home/benji/projects/blog
---

# typo3-extension-upgrade describes this task almost word for word and stayed shut, because the req...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions, broken on TYPO3 v14, while keeping them working on v13. My transcript begins at the user's actual request, not at a summary, so this covers the whole session.

No skill activated. Not one, for the entire session — investigation, design, implementation, tests, quality gates and commit.

typo3-extension-upgrade's description is: "Carry a TYPO3 extension, sitepackage or project package from the TYPO3 and PHP versions it supports today to another set: adding a new major, dropping one that is no longer maintained, replacing what a major deprecated or removed, and proving every version it claims." Two of its four clauses are this task exactly — "replacing what a major deprecated or removed" is the $GLOBALS['TSFE'] removal, and "proving every version it claims" is the "must still work in v13" half, which is precisely the part I ended up unable to prove by execution.

It did not fire, and I can say where the moment was. The request arrived in German, as a symptom ("seem to be broken in v14 and no longer work"), with a single file selected in the editor. Nothing in it says upgrade, migrate, deprecated, removed, or names a version range being changed — because the version range is not being changed. The package already claims ^13.4.15 || ^14.3 and works on neither correctly; the user wants a defect repaired inside a range it already declares. Read against the description, that is not "carrying a package to another set of versions", and I did not read it as one.

That is the shape I would expect to recur: a bug report whose cause turns out to be a removal in a major the package already supports. It arrives as a symptom, never as an upgrade request, and the description is written from the maintainer's intent rather than the reporter's words.

Two other skills were also plausible and also stayed shut. typo3-extension-testing would have owned the unit tests I added and the check suite I ran; I derived both from the repository (composer.json scripts, existing Tests/Unit layout) instead. typo3-extension-conformance did not apply — nobody asked for an audit.

I should be clear that not activating cost me little on the implementation, which was code archaeology the skills do not do. What it plausibly cost is the v13 half: I never ran anything on v13, and finished by verifying API-compatibility against a core checkout instead. A skill whose description promises "proving every version it claims" is the thing that would have pushed back on that, and it never spoke.

## Query

The verbatim request that failed to activate any skill: "diese typoscript conditons scheinen in v14 kaputt zu sein und nicht mehr zu funktionieren, bitte validiere das und fixe es, sie müssen nach wie vor in v13 funktionieren" — accompanied by an IDE selection of lines 18-37 of Classes/ExpressionLanguage/BlogVariableProvider.php.

## Suggestion

Widen typo3-extension-upgrade's description past the maintainer's intent to the reporter's symptom, so it is reachable from a bug report: something naming "code broken on a TYPO3 major the package already supports", "a global, class or method the version removed", "works on the old major, silently fails on the new one". The dropped/removed-API half of the skill is useful long before anyone decides to change a version constraint.

Separately, the "proving every version it claims" step is the one worth making loud: where a package declares more than one TYPO3 major and only one is installed, say so and say what verifying the other actually requires, rather than letting a session end on a static compatibility argument. In this session the installed major was 14.3.6 and 13.4 was never executed.
