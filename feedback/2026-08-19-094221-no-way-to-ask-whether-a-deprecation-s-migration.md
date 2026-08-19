---
date: 2026-08-19T09:42:21+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_hint_lookup, typo3-extension-health
directory: /home/benji/projects/blog
---

# No way to ask whether a deprecation's migration target exists on the other declared major

## Observation

Task: full audit of the blog extension (t3g/blog) before its official TYPO3 v14 release. The package declares typo3/cms-core ^13.4.15 || ^14.3 || 15.*@dev; installation was 14.3.6. The user stressed mid-session: "wir haben v13 und v14 combat bitte vergiss das nicht".

The deprecation sweep worked and produced real findings. But for every single one, the question that decided the remediation was not "what is deprecated" — it was "does the replacement exist on 13.4 too". Nothing on this server answers that, and it is the question a dual-major package asks about every entry the sweep returns.

Concretely, the sweep returned these and I had to settle each myself:
- #109438 ext_tables.php -> migration target is allowedRecordTypes TCA (#108557), Feature entry in 14.2
- #107047 addPiFlexFormValue -> FlexForm as 7th arg of registerPlugin(), Feature in 14.0
- #107229 Extbase\Annotation -> Extbase\Attribute, 14.0
- #106947 Install\Updates\* -> Core\Upgrades\*, 14.0
- #108524 Fluid namespaces in TYPO3_CONF_VARS -> Configuration/Fluid/Namespaces.php, Feature in 14.1

All five migration targets are v14-only. That is the single most consequential fact in the whole audit and no lookup stated it. typo3_changelog_lookup answers per version, so asking it at 13.4 returns "no such entry", which is indistinguishable from "the feature is not there" and from "nothing changed about it".

What I did instead: shelled out over the installed core's changelog directory —
  for v in 13.0 13.1 13.2 13.3 13.4 14.0 14.1 14.2 14.3; do ls $v/*<issue>* ; done
— to find which version carries the Feature entry. That works only because a Composer installation ships the whole Changelog tree, it only finds changes that were written up as a Feature, and it says nothing about an API that was always there.

Later I needed the same answer for ExtbaseRequestParameters::getControllerName() and for associative FlexForm item keys. For the first I gave up on the server and installed TYPO3 13.4.34 into a git worktree (rm composer.lock; composer require typo3/cms-core:^13.4) — roughly five minutes and six round trips — to read the class and run the suite. For the second I finally guessed a query, typo3_changelog_lookup with query "TCA items associative array keys" and no version filter, and got Feature #99739 / 12.3 on the first try. That call cost one round trip and replaced the whole procedure. It worked because the feature happens to be titled after the words I picked; the five above are not.

The extension-health skill's base.md does say a "does this still work in version N" question goes to typo3_documentation_lookup at that version. I did not use it for this, because the question is not "does it work" but "since when does it exist", and the manual matches page titles rather than API names — the base itself says a PHP identifier has no page to be titled after. So the route the skill names does not reach this question either.

## Query

typo3_changelog_lookup version=14 type=deprecation tag=ext:core (and ext:extbase, ext:backend, ext:frontend, ext:fluid, ext:form, ext:install, TCA, TypoScript, TSConfig, Fluid) — then, for each returned entry, the unanswered follow-up: "is the migration path this entry names available on 13.4, which this package also declares?"

## Suggestion

Give the sweep a second axis: the majors the package declares. Either a parameter on typo3_changelog_lookup (declaredVersions: ["13.4","14"]) that annotates every returned deprecation with the version its named migration target became available in, or a dedicated call — typo3_migration_availability with an issue number or an identifier — answering "Configuration/Fluid/Namespaces.php: since 14.1; on 13.4 use $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']".

The answer a dual-major package needs per deprecation is three lines: what to stop calling, what to call instead, and the lowest declared major on which the replacement exists. Today the sweep gives the first two and leaves the third to a directory listing.

Where the target is not available on the lower major, saying so is enough — the caller can then decide between a version switch, a shim, or waiting. What costs the session is not knowing, because the only way to find out is to install the other major.
