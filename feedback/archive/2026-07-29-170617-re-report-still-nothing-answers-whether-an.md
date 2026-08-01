---
date: 2026-07-29T17:06:17+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: 3882b01
subject: "[TASK] Close the re-report that had already shipped"
tool: typo3_project_scope
directory: /home/benji/projects/site-new
---

# RE-REPORT: still nothing answers whether an extension is part of the core.

## Observation

RE-REPORT: still nothing answers whether an extension is part of the core.

Reported last round after two failures in one session, in opposite directions: I asserted that Content Blocks is core (it is not — the user corrected me), and I did not know that typo3/theme-camino is core since v14.1 until the user named it, which cost a full rework of the sitepackage's directory structure.

The knowledge side of the camino half has been fixed — sitepackage-layout now exists and even says camino is "absent from a Composer project that did not require it", which is the right framing. But the underlying question is still unanswerable: typo3_project_scope lists the extensions of the installation being read, typo3_extension_scope describes one installed extension, and neither says anything about an extension that is not installed. That is precisely the case where the answer gets taken from memory, and memory is what got both of these wrong.

The installation has the answer on disk: the core package's composer.json replaces every system extension, and vendor/typo3/cms-* is enumerable.</observation>
<parameter name="suggestion">Let typo3_project_scope — or a small lookup of its own — report the system extensions of the TYPO3 line being read, marking which of them this installation has active. Reading the replace list per major would also let each entry carry a "since" version, which is what makes an entry like theme_camino (14.1) useful rather than trivia.

## Query

re-report — no tool answers "is extension X part of the core, and since when"
