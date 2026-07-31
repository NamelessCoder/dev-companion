---
date: 2026-07-31T17:45:26+00:00
category: tool-gap
status: open
model: big-pickle
tool: typo3_extension_scope
directory: /home/benji/projects/bootstrap_package
---

# Stale-registration detection gap: ext_localconf.php registers $GLOBALS['TYPO3_CONF_VARS']['FE']['...

## Observation

Stale-registration detection gap: ext_localconf.php registers $GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates'] entries pointing at 'bootstrappackage/Configuration/TypoScript/' and '.../TypoScript/ContentElement/', but that directory no longer exists (site sets replaced it). No lookup covers whether such a registration is still consumed in the active version: I had to read installed vendor core (SysTemplateTreeBuilder.php / TreeFromLineStreamBuilder.php) to confirm the entries are inert magic keys that only matter if a defaultTypoScript_* source is registered under them. typo3_extension_scope reports what an extension registers but not whether a registration is dead in the installed major.

## Query

bootstrap_package

## Suggestion

typo3_extension_scope (or the architecture lookup) could flag registrations that are no longer consumed in the installed version — magic-key-only globals, deprecated registration patterns, paths that do not exist — with the version range in which they are inert, so a review does not need to read the core's include-tree code to tell stale config from harmful config.
