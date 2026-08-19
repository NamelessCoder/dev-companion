---
date: 2026-08-19T09:02:00+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3-extension-upgrade, typo3-extension-health, typo3-extension-testing
directory: /home/benji/projects/blog
---

# No skill covers the npm/webpack asset build of an extension

## Observation

Task: "gehe alle dependabot prs durch, update die dependencies im js bereich und schliesse sie anschliessend", later widened to "schau bitte das du uns alles auf die aktuellsten versionen hebst", in the TYPO3GmbH/blog extension checkout (EXT:blog, package t3g/blog). My transcript is complete — it does not begin at a summary — so this covers the whole session.

Not one tool of this server and not one of its skills was activated during the working portion. Zero calls. The only MCP call in the session is the ToolSearch that loaded typo3_feedback_record for this debrief.

The work was: five open Dependabot PRs (immutable, lodash, picomatch, postcss, serialize-javascript, all transitive), package.json raised to current for eleven direct packages, node-sass dropped as unreferenced by the build, DataTables carried 1.13 -> 3.0, the webpack entry and two SCSS files migrated to the new DataTables API and class names, assets rebuilt and committed, master fast-forwarded, five PRs closed.

I scanned the skill list at the start and none matched. The list offers content-element, backend-module, testing, documentation, upgrade, health, distribution-content, development-installation, patch-checkout/development/review, extension-patch-review, core-issue-triage. Read by their descriptions:
- typo3-extension-upgrade says "TYPO3 and PHP versions it declares" — this task touched neither.
- typo3-extension-health is a whole-repository audit that "reports first; nothing is changed before the list is agreed" — the user asked for one specific, already-agreed change.
- typo3-extension-testing names "Playwright browser and accessibility tests" but nothing about the asset build that produces what those tests load.

So an extension's frontend build — package.json/package-lock.json, webpack config, SCSS pipeline, the committed artefacts under Resources/Public/JavaScript and Resources/Public/Css, and the third-party JS libraries an extension vendors for its backend modules — is carried by no skill description here. This is not a marginal corner of extension work: the built assets are committed to the repository, are served through JavaScriptModules.php, and go stale silently. The blog extension's backend.scss still carried DataTables 1.x rules styling sort arrows with FontAwesome glyphs, dead since the TYPO3 backend moved to SVG icons, and nothing in the repository or in this server would have flagged that.

The concrete cost: I reconstructed the DataTables 1.x -> 3.x migration myself with eleven separate greps into node_modules/datatables.net/js/dataTables.js (legacyDom, Dom.classAdd/stringArrays, the ext.classes layout map, the order class map, the columns().header/nodes/every/search registrations, and the code that wraps header content in .dt-column-title/.dt-column-order), then wrote a jsdom smoke test from scratch to prove the migration. That part is genuinely third-party library knowledge and I do not claim this server should hold it. What it should hold is the TYPO3 side of the same task, and that is where the gap bit — see the separate feedback on the .table-fit backend class.

## Query

Session task (no tool call was made): "bitte gehe einmal alle dependabot prs durch, update die dependencies im js bereich und schliesse sie anschliessend" + "schau bitte das du uns alles auf die aktuellsten versionen hebst", against the TYPO3GmbH/blog extension checkout at /home/benji/projects/blog. Files worked on: package.json, package-lock.json, webpack.config.mjs, Resources/Private/JavaScript/backend/datatables.js, Resources/Private/Scss/backend/{backend,datatables}.scss, Resources/Public/{JavaScript,Css}/*, Configuration/JavaScriptModules.php.

## Suggestion

A skill whose description names the asset build of an extension explicitly — npm/package.json, webpack or vite config, SCSS/Dart Sass pipeline, the committed build artefacts under Resources/Public, vendored third-party backend JS, and how those reach the backend through Configuration/JavaScriptModules.php and the importmap. Words that would have made me open it from this task: "npm", "package.json", "dependency update", "Dependabot", "webpack", "build assets", "Resources/Public/JavaScript", "SCSS". It would carry: which TYPO3 majors the extension declares and what that constrains in the backend CSS it may rely on; whether jQuery may still be assumed present (TYPO3 ships it as a legacy importmap entry, but relying on it is a decision, not a default); that build artefacts are committed here and must be rebuilt in the same commit as the source; and that backend CSS classes an extension borrows from the core must be checked against the supported majors rather than carried over.
