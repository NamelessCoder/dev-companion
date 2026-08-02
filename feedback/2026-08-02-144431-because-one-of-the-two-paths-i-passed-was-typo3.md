---
date: 2026-08-02T14:44:31+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403).

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403).

Because one of the two paths I passed was typo3/sysext/extbase/Classes/Service/ImageService.php, the answer included an architectureHints block id "extbase" titled "Extbase Plugins" with thirteen hints: registerPlugin vs configurePlugin, the plugin signature and the CType column, FlexForm registration, Configuration/Extbase/Persistence/Classes.php model mapping, QueryResultPaginator vs QueryBuilderPaginator vs ArrayPaginator, cacheHash excludedParameters and the ^ ~ indicators, property mapping allowProperties, non-persisted objects dropped from generated links, paginator page clamping, route ordering for paginated plugins, orderings being property names.

Not one of them bears on ImageService, which is a file-resolution and URL helper with no plugin, no controller, no model, no query and no route anywhere near it. It was the largest single block in the response and I skipped all of it. The match appears to be on the path segment sysext/extbase rather than on what the file is, so any task touching anything under ext:extbase gets the plugin briefing.

This is worth reporting less as a defect than as a cost: the useful hints in the same response — ViewHelpers get functional tests not unit tests, a changed ViewHelper argument list needs a changelog entry — were two lines competing with thirteen irrelevant ones, and a smaller model or a more hurried session is exactly where that ratio starts costing correctness.

## Query

task="Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource", changeType=bugfix, area=fluid, paths=["typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php","typo3/sysext/extbase/Classes/Service/ImageService.php"], targetVersion=15.0

## Suggestion

Narrow the extbase hint block from "any path under sysext/extbase" to the paths it actually describes — Classes/Mvc/, Classes/Domain/, Classes/Persistence/, Configuration/Extbase/, and the registration entry points — or split it into "Extbase Plugins" and a smaller "Extbase Services" set so that Classes/Service/ does not pull in the plugin briefing. More generally: where a path matches an area only by its extension prefix rather than by its role in that extension, returning nothing for it would be a better answer than returning the extension's headline topic.
