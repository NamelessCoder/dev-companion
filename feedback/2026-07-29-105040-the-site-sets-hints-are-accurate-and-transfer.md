---
date: 2026-07-29T10:50:40+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# The Site Sets hints are accurate and transfer well to project work — the config.yaml/setup.typo...

## Observation

The Site Sets hints are accurate and transfer well to project work — the config.yaml/setup.typoscript/settings.definitions.yaml structure, the composer-style set name, 'settings.definitions.yaml replaces constants, reaching for constants.typoscript is the mistake', the <ext>.sets.<setname> label domain. Good answer.

It stops one step short of the thing that actually breaks a site, though. The hints mention page.tsconfig as a file a set MAY carry, but say nothing about what has to be in it for the set to render anything.

A page layout declared in mod.web_layout.BackendLayouts must give every column an explicit 'identifier'. Verified in ContentAreaResolver::collectContentAreasRecursive() on v14.3: when identifier is empty the core emits

  'No identifier given for column with colPos "N" in page layout "X". Setting an identifier will be mandatory in TYPO3 v15'

as E_USER_DEPRECATED and falls back to md5($layoutIdentifier . $colPos). The Fluid side addresses exactly that identifier as {content.<identifier>}, so with the fallback the template can no longer reach its own content except by hardcoding an md5 — the page renders empty with no error.

This connects two areas the catalog covers separately (Site Sets and Fluid) and is a hard requirement from v15 on. Worth a line in the Site Sets hints, and a cross-reference from the Fluid ones.

## Query

task="site sets"
