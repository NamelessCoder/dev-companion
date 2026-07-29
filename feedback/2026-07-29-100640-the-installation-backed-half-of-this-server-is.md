---
date: 2026-07-29T10:06:40+00:00
category: idea
status: open
tool: typo3_server_scope
---

# The installation-backed half of this server is already a good site-developer tool and is mis-scop...

## Observation

The installation-backed half of this server is already a good site-developer tool and is mis-scoped as core-only. typo3_icon_lookup, typo3_backend_module_lookup, typo3_fluid_namespace_list, typo3_configuration_lookup and typo3_translation_domain_lookup all answered correctly and usefully against this v14 site: the module list included the project packages, the icon concept mapping resolved warning to actions-exclamation-triangle, FE/cacheHash returned the assembled runtime value rather than the shipped default, and the domain resolver computed the right domain for a labels.xlf that does not exist yet -- exactly what you need when adding labels to a sitepackage. None of that is core-specific; it is a property of an installation, and the scope text says so. Meanwhile the curated half is genuinely core-only, and the two are presented as one server, so a site developer either trusts all of it (and gets runTests.sh commands for a project with no Build/Scripts) or none of it (and loses the five tools that do work). One curated section did transfer verbatim and was the single most valuable answer of the whole session: the Site Sets hints from typo3_architecture_lookup, which named settings.definitions.yaml as the replacement for TypoScript constants -- a real gap in the site I am maintaining, which ships settings.yaml with no definitions file.

## Query

overall evaluation from the perspective of a site developer maintaining a TYPO3 v14 project

## Suggestion

Split the surface by provenance rather than by audience. Mark each tool as installation-backed or core-catalog-backed in typo3_server_scope and in the tool descriptions, and let the installation-backed ones answer without the core framing. Consider a TYPO3_MCP_PROFILE or similar so a project can present the five installation tools plus the transferable architecture domains (site sets, Fluid, console commands, DI, TCA, DataHandler) without the Gerrit, changelog and runTests.sh material. That is a packaging change, not new knowledge -- most of the value is already there.
