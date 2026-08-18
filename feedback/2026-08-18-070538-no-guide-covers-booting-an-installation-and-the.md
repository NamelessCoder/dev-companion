---
date: 2026-08-18T07:05:38+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/blog
---

# no guide covers booting an installation, and the guides list is unreachable in the state where on...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository.

I read no guide document this session, and I could not have. Two reasons, and both are structural rather than my choice.

First, my client (Claude Code) never rendered an MCP resource list. I saw the typo3-dev-companion tool names in a deferred-tool listing and the server's instructions block at the top of the session; no typo3:// resources appeared anywhere. The server's own instructions anticipate this — "including the whole procedures served as typo3://guides resources, which your client may not list at all" — and point at typo3_server_scope as the way to see them. The skill I was following explicitly told me not to call typo3_server_scope ("The orientation tool is for a caller who does not know whether this server can answer at all, which is not this workflow's question"), and I followed that. So the two routes to the guides list were closed at once: the client did not show it, and the skill told me the tool that would have shown it was not for this workflow.

Second, the fallback route — the guides list that rides on typo3_project_describe — is empty in exactly the state where a boot procedure would be read. My first project_describe, on the fresh clone, returned the "unsupported: no-installation" block with no guides key at all. The 11 entries (any/testing/browser-check, project/testing/playwright, core/contribution/*, extension/testing/phpunit, extension/documentation/manual, …) only arrived on the second call, after the installation already existed and the work was done. A session that boots an installation therefore finishes before it learns the guides exist. This is a consequence of the project_describe gating I filed separately, but the effect on the guides is its own subject: the one document class the server says a client may never see is also gated behind the state the reader has not reached yet.

Third, and independent of both: none of the 11 is about installation. The closest is any/testing/browser-check, which is about looking at a change in a browser, not about bringing an installation up. So even had I seen the list at the right moment there was nothing to read, and I assembled the procedure from the skill plus two hint ids instead.

The page I wanted would have been called something like "project/installation/ddev-boot" — "Booting a DDEV Installation From a Clone" — and end to end it would have carried: ddev start, ddev composer install, the second ddev start and why (Typo3Version.php detection, additional.php, exception 1396795884), the unattended `ddev exec bash -c "TYPO3_… typo3 setup --no-interaction"` line, extension:setup, cache:flush, and the two-sided verification by requesting the frontend and the backend rather than reading a green start. Every one of those facts exists in the server already, spread across installation-boot, installation-setup, environment-runtime-readers and the operations checklist; what is missing is one document that runs them in order.

## Query

The "guides" key of typo3_project_describe, compared between the call on a fresh clone (absent) and the call after installation (11 entries). No typo3_rule_lookup call was made this session.

## Suggestion

Add an installation-boot guide under an id like project/installation/ddev-boot and list it among the guides, so the procedure exists as one ordered document rather than as four hints a caller must assemble. Independently, carry the guides list on the no-installation answer of typo3_project_describe too — it is read from the server's own checkout and does not depend on an installation existing, and the no-installation state is precisely when a boot guide would be opened. Consider also naming the guides in the server instructions block by id, since that block is the one thing a client with no resource list is guaranteed to render.
