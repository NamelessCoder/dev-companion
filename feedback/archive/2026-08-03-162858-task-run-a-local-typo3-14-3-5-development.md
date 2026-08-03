---
date: 2026-08-03T16:28:58+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_configuration_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: run a local TYPO3 14.3.5 development instance under DDEV (v1.25.1) for an extension, later ...

## Observation

Task: run a local TYPO3 14.3.5 development instance under DDEV (v1.25.1) for an extension, later on SQLite with no database container. DDEV's TYPO3 integration is undocumented on this server, and both directions of getting it wrong cost a full debugging cycle.

What DDEV writes, unprompted, for project type typo3 in Composer mode is config/system/additional.php, guarded by getenv('IS_DDEV_PROJECT') and applied with array_replace_recursive over $GLOBALS['TYPO3_CONF_VARS']. It contains the database connection, GFX with the container's ImageMagick 6 paths, MAIL pointed at Mailpit on localhost:1025, and SYS with trustedHostsPattern, devIPmask and displayErrors. It carries a "#ddev-generated" marker and is rewritten on every start unless that marker line is removed — the documented per-file opt-out.

Mistake in the first direction: setting disable_settings_management: true and hand-writing the same file. The immediate symptom of writing nothing is UnexpectedValueException 1396795884 "The current host header value does not match the configured trusted hosts pattern", because the DDEV router forwards a host name that TYPO3's default SERVER_NAME-based pattern rejects — which is exactly one of the settings DDEV would have supplied. Everything hand-written there was redundant.

Mistake in the second direction: assuming that DDEV then adapts. It does not. With database omitted via omit_containers: [db] and TYPO3 installed on SQLite, DDEV still writes driver mysqli / host db into that file, and because it merges with array_replace_recursive it overrides the SQLite connection that "typo3 setup" wrote into config/system/settings.php — the instance cannot connect at all. DDEV's generator only knows its own database container; there is no SQLite-aware variant. The way out is the marker: write config/system/additional.php once during the install, without the "#ddev-generated" line, keeping GFX, MAIL and SYS and simply omitting the DB section so that settings.php governs the connection.

Two smaller ones from the same setup. DDEV's default fail_on_hook_fail is false, so a post-start hook that installs TYPO3 can fail completely while "ddev start" still reports success — for an install hook it belongs set to true. And a "ddev config" invocation rewrites config.yaml wholesale, dropping keys that were set to their default value, so a hand-edited config.yaml does not survive one.

## Query

Running a TYPO3 14.3 development instance under DDEV: what DDEV's settings management writes into config/system/additional.php, and how it collides with SQLite and omit_containers: [db].

## Suggestion

Carry DDEV as the default local environment for TYPO3 work and describe its settings management: that it generates config/system/additional.php with the database connection, GFX, MAIL and SYS (trustedHostsPattern above all), that hand-writing those is redundant, and that removing the "#ddev-generated" marker is the supported way to take the file over. State the limit explicitly: the generator assumes its own database container, so SQLite or omit_containers: [db] requires taking the file over, otherwise the merged mysqli connection silently defeats whatever "typo3 setup" configured. Add that an install hook should set fail_on_hook_fail: true, since a failed setup otherwise leaves a green "ddev start" behind.
