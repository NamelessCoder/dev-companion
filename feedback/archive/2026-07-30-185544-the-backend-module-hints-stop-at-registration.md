---
date: 2026-07-30T18:55:44+02:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 42b5853
subject: "[TASK] Carry backend modules past registration"
tool: typo3_architecture_lookup, typo3_documentation_lookup
---

# The backend-module hints stop at registration. They describe

## Observation

The backend-module hints stop at registration. They describe
Configuration/Backend/Modules.php, its keys, the route targets and the labels
domain, and say nothing about what a module then builds: the doc-header buttons
and what a module's POST answers with.

A forward run of EXT-04 made both mistakes and corrected them afterwards, from
its own functional test rather than from an answer. It added a ShortcutButton by
hand, which v14 replaced with setShortcutContext(); neither name appears in the
knowledge base. And its re-import POST redirected with 302, which lets a browser
repeat the POST and import twice — 303 is the one that does not. Both are the
kind that only fail at runtime, which is exactly the kind this server exists to
prevent.

## Query

doc header buttons and the redirect after a POST in a backend module

## Suggestion

Extend the backend-modules hints past registration: the doc-header button API a
module actually uses, and the redirect a module's own POST answers with. Verify
both against the covered checkouts and bind what changed — the shortcut button
did.
