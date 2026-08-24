---
date: 2026-08-24T14:01:30+00:00
category: missing-knowledge
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/ext-usercentrics
---

# typo3 setup --create-site also writes a site setup.typoscript that overrides every site set

## Observation

Task: build a DDEV development installation for an extension so its site set can be verified in a browser, TYPO3 14.3.6.

typo3_hint_lookup id=installation-setup says: "--create-site <url> and TYPO3_SETUP_CREATE_SITE create the root page and its site configuration under config/sites/." That is true and it is where the answer stops.

What it does not say is that the command also writes config/sites/<identifier>/setup.typoscript containing a complete welcome page — page = PAGE, page.10 = COA with the TYPO3 logo and a CONTENT object. Site TypoScript is applied after the site sets, so that file silently overrides any page object a set provides.

The failure looks like nothing: the page answers HTTP 200, the log stays empty, and the site set is demonstrably applied. I proved the set was loaded by injecting a marker (page.5 = TEXT) which rendered, while the set's page.10 = FLUIDTEMPLATE produced nothing and TYPO3's welcome content appeared instead. I found the cause by grepping the installed core for the literal "max-width: 800px" from the rendered markup, which landed in typo3/cms-install/Classes/Service/SetupService.php:326, method writeSiteSetupTypoScript().

That cost a full debugging cycle in a session that had already torn the installation down and rebuilt it twice.

The same hint's neighbour extension-test-site does carry the analogous warning for tests — "setUpFrontendRootPage() and site sets exclude each other ... a sys_template that clears resets the AST" — and that warning saved me from the identical mistake in the functional suite. The installation side has no equivalent sentence.

## Query

typo3_hint_lookup id=installation-setup, then id=project-configuration-files, against TYPO3 14.3.6. Task text: "Set up a DDEV based development installation for the ext-usercentrics extension so the site set can be verified in a real frontend".

## Suggestion

Add to installation-setup, next to the --create-site statement: the command writes config/sites/<identifier>/setup.typoscript with a welcome PAGE object, site TypoScript is applied after the site sets, so an installation whose rendering comes from a set has to replace or remove that file. Worth saying it fails silently with HTTP 200 and an empty log, because that is what makes it expensive — and worth naming the marker technique (add a trivial page.N = TEXT to the set) as the way to tell "set not applied" from "set applied but overridden", which are indistinguishable from the rendered page.
