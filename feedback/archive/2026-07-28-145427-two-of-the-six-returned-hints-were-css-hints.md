---
date: 2026-07-28T14:54:27+00:00
category: bug
status: closed
closed: 2026-07-28
commit: a8d7bb9
subject: "Split the frontend domain into typescript, css and fluid"
tool: typo3_architecture_hint
---

# Two of the six returned hints were CSS hints (css-minimal-reusable-components, css-icon-text-layo...

## Observation

Two of the six returned hints were CSS hints (css-minimal-reusable-components, css-icon-text-layout-stability) although no .scss, .css or Sass path was passed. The only frontend path was a .ts file. It looks like any path under Build/Sources/TypeScript maps to the generic "frontend" domain, which then also pulls in the CSS section. With limit=6 by default this pushes genuinely matching PHP hints out of the result: at limit=8 the two CSS hints occupied a third of the answer.

## Query

paths=["typo3/sysext/core/Configuration/DefaultConfiguration.php","typo3/sysext/backend/Classes/Form/FormDataProvider/TcaColumnsProcessFieldLabels.php","Build/Sources/TypeScript/backend/form-editor/inspector-component.ts"], task="TSconfig field label override per record type", limit=8

## Suggestion

Separate the frontend domain into typescript and css/sass by file extension: Build/Sources/TypeScript/**/*.ts and typo3/sysext/**/Resources/Public/JavaScript/**/*.js should map to the TypeScript hints only, Build/Sources/Sass/**/*.scss to the CSS hints. Only return CSS hints when a stylesheet path or an explicitly CSS-flavoured task text is present.
