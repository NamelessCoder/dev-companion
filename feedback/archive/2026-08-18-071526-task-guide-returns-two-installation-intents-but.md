---
date: 2026-08-18T07:15:26+00:00
category: tool-gap
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-development-installation
directory: /home/benji/projects/blog
---

# task_guide returns two installation intents but only one skill exists, and the operations half ha...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository. This reports a gap the server itself pointed at in its own answer, which I noticed only on review.

typo3_task_guide returned two intents, both at strong confidence: installation-setup ("Setting an installation up") and installation-operations ("Bringing an installation up and running it"). It returned one skill: typo3-development-installation. That skill's own closing section draws the same line from the other side — "It does not own hosting, deployment or backups, and it does not own what runs against the installation once it answers."

So the server distinguishes two intents, names both, and has a published skill for only one of them. Everything after the installation answers is unowned. In this session that showed up as three separate things I had to decide without routing:

1. How the extension gets content into the fresh instance — filed separately; the answer was a backend wizard I found by grepping .rst.
2. What to do with a non-empty deprecation log on first boot. var/log/typo3_deprecations_*.log reached 63 KB on the first request, entirely from the extension's own TCA (fields on tx_blog_domain_model_author and tx_blog_domain_model_tag considered searchable by TCA type but absent from ctrl.searchFields). I read it, reported it, and stopped, because no workflow I was in owned it. The covering skills for the fix — typo3-extension-conformance or typo3-extension-upgrade — exist and are well named, but nothing in the operations path hands over to them, and by the time the log is readable the installation workflow is finished.
3. The ordinary operational verbs of a dev instance once it stands: flushing caches after a code change, resetting a backend password, importing a colleague's database dump, adding an editor user, adding a site set to an existing site. The hints for several of these exist (installation-boot covers cache:flush and backend:resetpassword; installation-upgrade exists as a hint id). They are hints without a workflow.

The asymmetry is visible in the skill list itself. Development, testing, conformance, cleanup, documentation, upgrade, triage, patch checkout, patch development, patch review — the authoring side is covered thoroughly and thoughtfully. The running side has exactly one skill, and it is scoped to creation.

To answer the question I was asked directly: for the task I had, no skill was missing — typo3-development-installation fitted and I activated it. For the task the user is most likely to hand me next in the same installation, one is.

## Query

typo3_task_guide task="Boot the local DDEV development installation for the blog extension repository…", changeType="operations" — its own answer: "intents":[{"id":"installation-setup","title":"Setting an installation up","confidence":"strong"},{"id":"installation-operations","title":"Bringing an installation up and running it","confidence":"strong"}] against "skills":["typo3-development-installation"].

## Suggestion

Publish a skill owning installation-operations, the intent task_guide already names — running an installation that exists rather than creating one. Its scope is the verbs after the boot: flushing the right caches for a given change, resetting a backend or install-tool password, importing and reconciling a database dump against current code (extension:setup, the cache tables that ride in the dump, the sys_file rows with no files behind them — all of which installation-boot already knows), adding a user, putting a site set onto an existing site, and reading what a failing request wrote down. Much of the knowledge is already there in installation-boot, installation-exception-output and installation-upgrade; what is missing is a workflow that owns them and an explicit hand-off at the end of typo3-development-installation, which currently stops at "the site answers" and points nowhere. Add to that hand-off the deprecation log: a first boot that produces one is the normal case, and the route from there into typo3-extension-conformance is not drawn today.
