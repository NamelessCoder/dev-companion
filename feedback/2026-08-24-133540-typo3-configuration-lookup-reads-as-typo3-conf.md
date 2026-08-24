---
date: 2026-08-24T13:35:40+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_configuration_lookup, typo3_schema_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_configuration_lookup reads as TYPO3_CONF_VARS, so site config.yaml questions went to grep

## Observation

Task: write a TYPO3 Core patch with functional tests for a stale site-settings/TypoScript-cache bug, on a main (15.0-dev) checkout.

I needed a site object in a functional test that would take the code path guarded by Site::isTypoScriptRoot(), without registering a fixture set extension. That is a site-configuration question: which config.yaml keys feed which Site accessors. I never put it to this server, and the reason is the tool name.

typo3_configuration_lookup reads, to me, as TYPO3_CONF_VARS / config/system/settings.php / the install-tool configuration tree. Site configuration is YAML too, but it is a different subsystem and I did not expect it under that name. typo3_schema_lookup I read as TCA and the TcaSchema layer, which is what "schema" means in this codebase. So both tools that could plausibly hold the answer were read in the tool list and passed over, and I spent four Bash round trips reading Site.php and SiteSettings.php instead — three of them widening the same sed window because the first two ranges missed the constructor.

What I actually had to learn from those reads, and what an answer would have had to contain: (1) $this->sets = $configuration['dependencies'] ?? [] — the YAML key is "dependencies", not "sets"; (2) isTypoScriptRoot() is true when sets !== [] || typoscript !== null || tsConfig !== null, so passing a SiteTypoScript instance is enough and no set registry is needed; (3) the Site constructor's fourth to sixth arguments are ?SiteSettings, ?SiteTypoScript, ?SiteTSconfig, and a null SiteSettings falls back to createFromSettingsTree($configuration['settings']).

Point (1) is the one I would not have guessed. "dependencies" for what the UI, the docs and the rest of the code call sets is exactly the kind of spelling mismatch that defeats a lexical search: had I searched this server for "site sets configuration key" I would have got nothing useful, and had I searched the checkout for "sets" I would have drowned. I found it only by reading the constructor top to bottom.

I cannot report whether this server holds any of that, because I never asked. That is the point of the feedback: the assumption that it does not was never tested, and it was made on the tool names alone.

## Query

Never called. The question I had, and would have asked in these words: "What in config/sites/<id>/config.yaml makes Site::isTypoScriptRoot() return true, and which YAML key becomes Site::getSets()?" I answered it instead with: grep -n "isTypoScriptRoot|function getSets" typo3/sysext/core/Classes/Site/Entity/Site.php, then sed -n '240,270p;350,375p' on the same file, then sed -n '1,140p' on it, then cat of SiteSettings.php twice.

## Suggestion

Say in the tool description of typo3_configuration_lookup whether config/sites/<identifier>/config.yaml and settings.yaml are inside it or outside it. One clause either way settles it — "including site configuration" pulls the call in, "excluding site configuration, see X" sends it to the right place immediately. Today the name alone reads as global system configuration, and a caller with a site-YAML question routes around it.

If site configuration is in scope, the answer worth having for "what makes a site a TypoScript root" is the key-to-accessor mapping, not prose: dependencies -> Site::getSets(), settings -> Site::getSettings(), and the note that the entity-level typoscript/tsConfig come from constants.typoscript / setup.typoscript / page.tsconfig beside config.yaml rather than from a key in it. The mismatch between the key "dependencies" and the word "sets" everywhere else deserves to be spelled out as an alias, so a lexical search on "sets" reaches it.
