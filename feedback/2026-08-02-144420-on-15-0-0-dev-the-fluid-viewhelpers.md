---
date: 2026-08-02T14:44:20+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_architecture_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403) on 15.0.0-dev.

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403) on 15.0.0-dev.

The fluid-viewhelpers architecture hints returned by typo3_task_guide are all about the ViewHelper class shape: final class, initializeArguments/registerArgument, typed render, renderStatic being on its way out, escapeOutput/escapeChildren, argument type strictness, functional-not-unit tests, changelog for public API, the SYS/fluid configuration array. All correct, and the last two were genuinely useful.

None of them mention the System Resource API, which is what actually changed about this area in v15 and what the task turned out to be entirely about. On main, TYPO3\CMS\Core\SystemResource\ now carries SystemResourceFactory, SystemResourcePublisherInterface, UriGenerationOptions, PublicResourceInterface and CacheBustingUri; f:uri.resource and f:resource are built on it; Core\Resource\File implements PublicResourceInterface; ResourceFactory::retrieveFileOrFolderObject routes EXT: paths through the publisher and carries two @todo notes that the fallback storage is to be removed. The commits behind it are visible in the log as "[!!!][FEATURE] Introduce System Resource API to resolve files/assets" and "[!!!][FEATURE] Implement relative URIs as system resources".

I established all of that by grepping and reading the checkout — roughly a dozen Bash and Read calls before I could reason about the fix at all. The nextTools block did suggest typo3_changelog_lookup "for what 15 changed about this area", and not calling it was my omission; but the hints that were returned describe a Fluid ViewHelper world in which resource URIs are still a solved, unremarkable detail, which is precisely the assumption this task destroyed.

## Query

task="Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource", changeType=bugfix, area=fluid, targetVersion=15.0 — architectureHints id "fluid-viewhelpers"

## Suggestion

Add hints for the v15 System Resource API under the fluid-viewhelpers and resource-handling areas: which ViewHelpers are built on it (f:resource, f:uri.resource), that PublicResourceInterface is declared public API and is implemented by both Core\Resource\File and PublicPackageFile so it spans FAL and package resources, that cache busting for resource URIs lives in CacheBustingUri and is applied by the publisher rather than by the ViewHelper, and that the storage-0 fallback used to resolve EXT: paths through FAL is marked for removal in the source. Marking the hints since:15 would keep them out of answers for 13.4 and 14. Where an area has had a structural change in the target version, saying so in the hints themselves would be worth more than routing to typo3_changelog_lookup, which a session under time pressure will skip.
