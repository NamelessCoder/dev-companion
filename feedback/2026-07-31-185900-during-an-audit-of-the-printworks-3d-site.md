---
date: 2026-07-31T18:59:00+00:00
category: tool-gap
status: open
model: unknown
tool: typo3extensionconformance, typo3_project_scope, typo3_architecture_lookup, typo3_configuration_lookup, typo3_documentation_lookup, typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# An install writes the client entry and never says a callable tool is one step further on

## Observation

Partially addressed: the skill no longer runs against nothing. `skills/base.md`
gained the precondition on 2026-07-31 at 19:07 UTC, eight minutes after this was
filed — no `typo3_` tool in the session means stop and say so, never answer from
what the model knows instead — and the installer publishes it into every skill
as `references/base.md`. Nothing was missing on the server's side either: it
serves 24 tools over stdio, all five named above among them.

What remains is the step before that. The `.mcp.json` in this project is correct
and was written by this installer, and two sessions there still had no tool they
could call — this one on 2026-07-31 and the 2026-07-29 evaluation, which reached
the server through a hand-written stdio wrapper for the same reason. Writing the
entry is not registering the server: a client that scopes project servers behind
an approval has not been asked yet, and a session already open when the file was
written is running against what it started with. `install` reports nine
successes and says nothing about either.

## Query

Activated the typo3-extension-conformance skill. It instructed: 1) typo3_project_scope for installation metadata. 2) typo3_extension_scope per extension. 3) typo3_task_guide for the workflow. 4) typo3_architecture_lookup per subsystem. 5) typo3_changelog_lookup for deprecations. None of these were callable.

## Suggestion

Have `install` and `update` say, per client, what that client still needs before
a tool in the entry they just wrote can be called — in their own output, not
only in the installation manual.
