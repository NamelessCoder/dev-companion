---
date: 2026-08-18T07:06:32+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/blog
---

# installation-setup tags the --create-site statement "up to v13", which reads as removed in v14 wh...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository, which meant deciding whether to pass --create-site to `typo3 setup` on TYPO3 14.3.6.

The installation-setup hint carries these two statements next to each other:

- "--create-site <url> and TYPO3_SETUP_CREATE_SITE create the root page and its site configuration under config/sites/, and nothing suppresses them." — since: null, until: 13, versions: "up to TYPO3 v13"
- "--distribution names a package whose content is imported instead. … Both options are read only where no distribution is active yet … In Composer mode every required package is active, so requiring such a package makes both inert" — since: 14, versions: "TYPO3 v14 and newer"

Read at speed, the version tags say: --create-site works up to v13, and on v14 you get the --distribution statement instead. That is not what is true. On v14 --create-site is still honoured whenever no required package ships an Initialisation/ data file, which is the common case. The correct reading is only recoverable from the second statement's clause "Both options are read only where no distribution is active yet", which is doing a lot of work in the middle of a paragraph that is otherwise about --distribution.

I paused on this and reasoned it out, then verified empirically: nothing in .build/vendor ships an Initialisation/ directory (checked with find), I passed TYPO3_SETUP_CREATE_SITE=https://blog.ddev.site/ on 14.3.6, and config/sites/main/config.yaml was written with base https://blog.ddev.site/ and rootPageId 1. The frontend answers 200. So the knowledge is correct; it is the version tagging that misleads.

The cost of getting it wrong is real in both directions. Believing --create-site is gone on v14 leaves an installation with no site configuration and, as the same hint says elsewhere, "no way to be told its own URL" — a 404 at its own root with no obvious cause. Believing it always works where a distribution is active produces the doubled root page the operations checklist separately warns about.

This is the one place in the session where I had to reconcile two of the server's own statements against each other rather than read one. Everything else in installation-setup and installation-boot was directly usable.

## Query

typo3_hint_lookup id=installation-setup, returned inside typo3_task_guide changeType="operations". The two adjacent statements about --create-site (until: 13) and --distribution (since: 14), read against an actual TYPO3 14.3.6 install where --create-site was used and did work.

## Suggestion

The general risk is worth a look beyond this entry: an until: N tag on a statement that was narrowed rather than removed at N+1 reads as removal, and the reader who trusts the tags rather than the prose gets the opposite of the intended answer.

*Trimmed on 2026-08-18. The installation-setup entry itself was corrected — what
--create-site writes is unbound and the condition that arrived at v14 is a
statement of its own. Verified against .checkouts/13.4, 14.3 and main; the rule
is D-VER-006. What stays open is the sweep of the rest of the corpus.*
