---
date: 2026-08-24T10:04:27+00:00
category: idea
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# task_guide returns extension- and project-scoped hints for a repository already established as a ...

## Observation

Task: review Gerrit change 95375 against the TYPO3 core checkout at /home/benji/projects/typo3-cms.

Call order in this session: typo3_project_describe first, which answered kind="core-checkout", extensions=[], typo3Version="15.0.0-dev". Then typo3_task_guide with changeType="audit", targetVersion="15" and the two changed paths, both under the core checkout.

task_guide correctly classified both paths as scope "core" — its own scopes array says so. It then returned four hints:

- extension-asset-build, scope "extension" — about a project extension owning its own build tool and package.json scripts
- public-assets, scope null — about Resources/Public publishing, _assets/<hash>/, SystemResourceFactory
- project-build-and-scripts, scope "project" — about Build/ at a project root, var/, document root, .gitignore for a Composer installation
- backend-typescript, scope "core" — the only one on target

Three of four were about repository kinds this is not. project-build-and-scripts explicitly opens "Build/ at the project root is for what runs before or around the site rather than in it" — in a core checkout Build/ is the core's own asset pipeline, which is the opposite thing, and the hint even says so in its last bullet ("For a patch to the TYPO3 backend itself, css-source-build-boundaries and backend-typescript describe the core's source trees"). So the payload contains a hint whose own text redirects to the hint sitting next to it.

Cost was low — I read them and used only backend-typescript — but it is three quarters of the hint payload, and it arrived after the server had already been told, in the previous call of the same session, what kind of repository this is. The scopes array in the answer proves the classification was available at the moment the hints were selected.

## Query

typo3_task_guide(task="Review a bugfix patch for the TYPO3 form manager new-form wizard: the settings step must pick the template matching the Blank/Predefined mode chosen in step 1", changeType="audit", targetVersion="15", paths=["Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts","typo3/sysext/form/Resources/Public/JavaScript/backend/form-wizard/steps/settings-step.js"]) — preceded in the same session by typo3_project_describe returning kind="core-checkout".

## Suggestion

Filter hints by their own scope field against the scope task_guide already computed per path. Where every path resolves to scope "core", drop hints declaring scope "extension" or "project"; where they resolve to extension or project, drop the core-only ones. Hints with scope null stay, since they are the ones that transfer.

If dropping them outright is too strong, return them under a separate key — the answer already has an omittedHints mechanism for hints it left out, and the same shape would work here: "hints that apply to other repository kinds", so a caller can ask for them rather than read past them.
