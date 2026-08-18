---
date: 2026-08-18T07:46:06+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-development-installation, typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/blog
---

# No skill owns an installation that boots but answers wrong, and the one that came closest sent me...

## Observation

Task: "ich habe das blog setup ausgeführt, aber das frontend zeigt immer noch 404" on a DDEV TYPO3 14.3.6 installation of t3g/blog. Container up, backend 200, frontend 404.

typo3_task_guide named typo3-development-installation and that was the closest fit available, but the skill owns bringing an installation into existence and booting the one a repository declares. Mine was neither: it existed, it booted, it served both a backend and a frontend, and the frontend served a well-formed wrong answer. The skill has no shape for that. Its "Prove it" section is the nearest thing and it is written for a side that does not answer at all.

That mismatch produced a concrete wrong turn. The skill says to read the failure from what the installation wrote down rather than out of the page it rendered, and calls fetching the rendered error page "the detour this replaces — it costs the whole document through the context and still holds nothing where the message was withheld". I followed it: var/log/typo3_*.log was zero bytes for the 404, because a 404 is a response TYPO3 produces deliberately and writes nothing about. The only evidence in the entire installation was one line in the page I had been told not to fetch: "The page did not exist or was inaccessible. Reason: The requested page does not exist". That line is what established a site had been matched and the page lookup inside it had failed — which is the whole difference between a site-configuration problem and a slug problem, and it decided the diagnosis.

So the instruction is right for an uncaught exception and wrong for a deliberate 404, and the skill does not distinguish the two. Later in the same session it was right and paid off: when the frontend went to 500, the log held the IncompleteRecordException with class, code and full stack, and I never fetched that page.

Beyond that one instruction there is a gap with no owner. Nothing covers repairing a site configuration in a running installation: two sites colliding, a site pointing at a deleted root page, a root page left hidden, a base that is a sub-path. Those were the whole task. typo3-extension-conformance audits code, typo3-development-installation creates and boots, and the space between them — an installation that is up and misconfigured — has no skill.

## Query

Skill typo3-development-installation activated with args "Blog-Setup wurde ausgeführt, Frontend liefert weiterhin 404". Its "Prove it" section routes a side that does not answer to typo3_hint_lookup id=installation-exception-output and says fetching the rendered error page is the detour that replaces.

## Suggestion

Split the "Prove it" instruction by what the failure is: an uncaught exception is read from the log and the rendered page is a detour, but a status code TYPO3 returns on purpose — 404, 403, a redirect — writes nothing to the log, and its rendered page carries the one line that says which stage produced it. Say that the log being empty is itself the finding that separates the two. Beyond that, consider a skill for an installation that is up and answering wrong, covering the site configuration as its own subject: which site a request matches, a root page that is deleted or hidden, a base that is a path where another site holds the host, and how to establish each against the running installation rather than against the files.
