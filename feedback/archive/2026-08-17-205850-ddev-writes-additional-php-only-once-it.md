---
date: 2026-08-17T20:58:50+00:00
category: missing-knowledge
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup, typo3-development-installation
directory: /home/benji/projects/site-demo
---

# DDEV writes additional.php only once it recognises an installation, so the first start of a fresh...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6 with DDEV 1.25.1. Following typo3-development-installation's "create one where none is declared" path, which asks that the sequence run unattended from the state a colleague's clone is in.

The project-configuration-files hint is detailed and correct about the ownership boundary: which four sections DDEV generates, the #ddev-generated marker, what taking the file over costs, the config/system/.gitignore interaction. What it does not say is *when* DDEV writes the file — and that is what breaks the first run.

DDEV generates config/system/additional.php only once it recognises an installation. On a clone the order is necessarily `ddev start` before `ddev composer install`, so at start time there is no installation, nothing is written, and every request afterwards answers HTTP 500 with exception 1396795884 (no trustedHostsPattern). I hit this twice: once on the initial build, where `ddev restart` after composer install fixed it, and again during the clone-state rebuild, where it recurred. My first hypothesis — that the trigger was the existence of config/system/ — was wrong: I committed the directory with a .gitkeep, ran the whole sequence again, and config/system/ came back holding only settings.php and the .gitkeep.

The failure reads as a TYPO3 configuration problem and is diagnosed against TYPO3's exception, so nothing points at the environment's write timing. It also passes silently through the install itself: `typo3 setup` succeeds, because it writes its own settings.php with the database credentials from the environment variables; only the web requests fail.

The resolution I settled on is the one the hint already describes for another case — take the file over, drop the #ddev-generated marker, supply GFX, MAIL and SYS by hand, leave DB to settings.php, and set disable_settings_management: true. It is the same remedy; it just needs a second trigger listed beside "an installation on SQLite or with omit_containers".

## Query

typo3_hint_lookup id=project-configuration-files targetVersion=14; then, on a clone with no vendor/ and no public/: ddev start, ddev composer install, typo3 setup --no-interaction

## Suggestion

Add the timing to project-configuration-files: DDEV writes config/system/additional.php only once it recognises an installation, so on a fresh clone the first `ddev start` writes nothing and the site answers 1396795884 until the project is started again after `composer install`. Name it as a second reason to take the file over, beside the existing SQLite / omit_containers case, since the remedy is identical. It is worth a line in typo3-development-installation too, where the workflow asks that the declared sequence run unattended from a clone: this is precisely a step that needed a hand, and the workflow's own test — "anything that needed a hand is not part of the setup yet" — is what surfaced it.
