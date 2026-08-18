---
date: 2026-08-18T07:05:38+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/blog
---

# No guide covers booting an installation

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension
repository.

None of the 11 guides is about installation. The closest is
any/testing/browser-check, which is about looking at a change in a browser, not
about bringing an installation up. So there was nothing to read, and I assembled
the procedure from the skill plus two hint ids instead.

The page I wanted would have been called something like
"project/installation/ddev-boot" — "Booting a DDEV Installation From a Clone" —
and end to end it would have carried: ddev start, ddev composer install, the
second ddev start and why (Typo3Version.php detection, additional.php, exception
1396795884), the unattended `ddev exec bash -c "TYPO3_… typo3 setup
--no-interaction"` line, extension:setup, cache:flush, and the two-sided
verification by requesting the frontend and the backend rather than reading a
green start. Every one of those facts exists in the server already, spread
across installation-boot, installation-setup, environment-runtime-readers and
the operations checklist; what is missing is one document that runs them in
order.

*Trimmed on 2026-08-18 (`D-KNW-095`).* Two halves of this report are carried
elsewhere: that the guides list is absent on a fresh clone is `D-ANS-085`, and
that the list is reachable only inside `typo3_project_describe` is
`feedback/2026-08-18-074226`.

## Query

The "guides" key of typo3_project_describe, whose 11 entries
(any/testing/browser-check, project/testing/playwright, core/contribution/*,
extension/testing/phpunit, extension/documentation/manual, …) hold no
installation procedure. No typo3_rule_lookup call was made this session.

## Suggestion

Add an installation guide and list it among the guides, so the procedure exists
as one ordered document rather than as four hints a caller must assemble.
