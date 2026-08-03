# `typo3_hint_lookup`

Return hints for TYPO3 core paths or task topics, grouped by section. Where the
paths read as a project or third-party extension the hints still come back,
because the conventions transfer. The "Backend CSS" and "Backend TypeScript and
JavaScript" sections describe the TYPO3 backend interface and are withheld, with
the reason, where the task names the frontend.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# File paths related to the task, as they are in the repository they belong to.
# Each is placed on its own, so a core path and an extension path in one call
# are matched separately, and a statement is labelled where it obliges the other
# one.
paths: [string]  # optional
# Short task description or topic, in English. Matching is lexical against
# English text, so another language reaches only the loanwords.
task: string  # optional
# Ask for one hint by its id, for example language-files, instead of matching.
# Every answer that returns no hint lists the ids there are, so a subject that
# exists can be requested by name rather than guessed at.
id: string  # optional
# The TYPO3 version the answer has to hold for, for example "13.4" or "14".
# Statements that do not hold there are left out, including those the repository
# needs for another major it declares. Defaults to every major this repository
# declares typo3/cms-core for, or to the installation this server was started in
# where there is no declaration; where there is neither, nothing is filtered and
# every statement carries the versions it holds for.
targetVersion: string  # optional
# Maximum number of hints.
limit: integer  # optional
```

## Answers with

```yaml
task: string or null  # optional
paths: [string]
# Which kind of work each path is. Paths of different scope are matched
# separately, so a hint that came back for one of them is about that path.
scopes:
  - path: string
    # One of: core, uncertain, project, extension. Which kind of work this
    # answer is for: core, a patch to the TYPO3 core itself; project, the site
    # repository around an installation; extension, a package in it, whether a
    # sitepackage or a third-party one; or uncertain, which means nothing in the
    # call placed the work and what came back is the core's own.
    scope: string
# The TYPO3 major this repository runs — stated by the caller, or read from
# the installation. Null means nothing was filtered and every statement carries
# its own range. Where the repository serves several majors, targetVersions is
# what the answer holds for.
targetVersion: integer or null  # optional
# Every TYPO3 major the answer holds for. One entry is the ordinary case.
# Several mean this repository declares typo3/cms-core for more than one of
# them, so a statement was kept when it holds on any — and where two
# statements about the same subject differ, the difference is the constraint the
# code lives under rather than drift. Empty when nothing was filtered by
# version.
targetVersions: [integer]  # optional
# Hints outside these domains are not returned.
domains: [string]
# Categories that matched the domains but were left out because the task names
# the frontend. "Backend CSS" and "Backend TypeScript and JavaScript" describe
# the TYPO3 backend interface and are wrong advice for what a website renders;
# see docs.typo3.org for frontend theming.
withheldCategories: [string]
hints:
  - id: string
    title: string
    # PHP, TypeScript, JavaScript, CSS, or General.
    category: string
    # One of: core, project, extension, null. Which kind of work the whole hint
    # obliges. "core" means it is a condition of a patch to the TYPO3 core and a
    # convention anywhere else — the backend's own design system, the
    # changelog artifact, the paths of the mono repository. "project" and
    # "extension" are the mirror: what the repository around an installation, or
    # a package distributed on its own, has to do, and what is context rather
    # than a condition inside the core. Null, the ordinary case, means it holds
    # wherever TYPO3 is written: an API that throws throws in a sitepackage too.
    scope: string or null
    hints:
      - # The statement itself. It reads the same on every version it holds for;
        # the range is beside it, never inside it.
        text: string
        # First TYPO3 major this holds on. Null means as far back as this
        # knowledge base reaches.
        since: integer or null
        # Last TYPO3 major this holds on. Null means it still holds.
        until: integer or null
        # The same range as a sentence, empty when the statement is bound to
        # nothing.
        versions: string
        # One of: core, project, extension, null. Which kind of work this
        # statement obliges. "core" means it is a condition of a patch to the
        # TYPO3 core and a convention anywhere else — the backend's own design
        # system, the changelog artifact, the paths of the mono repository.
        # "project" and "extension" are the mirror: what the repository around
        # an installation, or a package distributed on its own, has to do, and
        # what is context rather than a condition inside the core. Null, the
        # ordinary case, means it holds wherever TYPO3 is written: an API that
        # throws throws in a sitepackage too.
        scope: string or null
# The hints that exist in the searched domains, returned when none matched.
# Empty on a hit.
availableHints:
  - # Ask for this hint outright by passing it as id.
    id: string
    title: string
    category: string
```

## Answered

The tool was called `typo3_architecture_lookup` when this was recorded, and
every call below names it by that spelling. Recorded on 2026-08-02 by
`bin/cli tools:record`. Answered against core-checkout, TYPO3 14.3.6-dev, the
14.3 core checkout below .checkouts/, whose console could not be reached:
<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3
exists. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### architecture: path

Called with:

```json
{
    "paths": [
        "typo3/sysext/core/Classes/DataHandling/DataHandler.php"
    ]
}
```

Text:

```
Paths:
- typo3/sysext/core/Classes/DataHandling/DataHandler.php
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Architecture hints:
### PHP

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s unit
- CI=true ./Build/Scripts/runTests.sh -s functional

## DataHandler and Persistence
Hints:
- DataHandler and persistence changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.
- Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.
- Writing records goes through a datamap: $dataMap[<table>][<uid or "NEW" plus a unique suffix>][<field>], handed to start($dataMap, $cmdMap) and then process_datamap(). Moving, copying and deleting go through the command map instead. A new record's real uid comes back in substNEWwithIDs, keyed by the placeholder.
- A new record is placed at the TOP of its page: the pid field is the positioning pid, and a page uid there means "first record on that page". A datamap written in reading order therefore comes out reversed — pages in a menu, content elements in a column.
- To place records in order, use the negative form: a pid of -<uid> means "directly after that record". A "NEW" placeholder may be used there as well, as -NEW..., and resolves once the record it names has been created in the same run.
- DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s functional
- CI=true ./Build/Scripts/runTests.sh -s phpstan
```

Data:

```json
{
    "task": null,
    "paths": [
        "typo3/sysext/core/Classes/DataHandling/DataHandler.php"
    ],
    "scopes": [
        {
            "path": "typo3/sysext/core/Classes/DataHandling/DataHandler.php",
            "scope": "core"
        }
    ],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "withheldCategories": [],
    "hints": [
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s unit",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        {
            "id": "datahandler-persistence",
            "title": "DataHandler and Persistence",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "DataHandler and persistence changes are high-impact and usually need functional tests.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Writing records goes through a datamap: $dataMap[<table>][<uid or \"NEW\" plus a unique suffix>][<field>], handed to start($dataMap, $cmdMap) and then process_datamap(). Moving, copying and deleting go through the command map instead. A new record's real uid comes back in substNEWwithIDs, keyed by the placeholder.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A new record is placed at the TOP of its page: the pid field is the positioning pid, and a page uid there means \"first record on that page\". A datamap written in reading order therefore comes out reversed — pages in a menu, content elements in a column.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "To place records in order, use the negative form: a pid of -<uid> means \"directly after that record\". A \"NEW\" placeholder may be used there as well, as -NEW..., and resolves once the record it names has been created in the same run.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "DataHandler acts as a backend user: pass one to start() or have one in $GLOBALS['BE_USER']. Permission checks, workspaces and the reference index all hang off it, which is what makes DataHandler the right way to seed and a direct INSERT the wrong one.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s functional",
                "CI=true ./Build/Scripts/runTests.sh -s phpstan"
            ]
        }
    ],
    "availableHints": []
}
```

### architecture: topic

Called with:

```json
{
    "task": "sass build"
}
```

Text:

```
Task: sass build
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: css (hints outside these domains are not shown)

Architecture hints:
### Backend CSS

## CSS Source and Build Boundaries
Hints:
- Treat Build/Sources/Sass/ as the source of truth when a Sass source exists.
- This is the core's asset pipeline. A project extension owns a separate build; see extension-asset-build.
- Do not hand-edit generated public CSS as the only change.
- Not every asset comes out of the Sass build. The CKEditor CSS is built through Build/rollup/ckeditor.js, so a change there is not picked up by a CSS build and looks like nothing happened.
- Verify generated assets are in sync when public assets are committed.
- Use lintScss for TYPO3's stylelint setup and npm -- run build-css for a focused CSS build while iterating.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s build
- CI=true ./Build/Scripts/runTests.sh -s lintScss
- CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css

## CSS Component Structure
Hints:
- The backend stylesheet is a set of bundles. Each top-level entry file under Build/Sources/Sass/ without a leading underscore — backend.scss, dashboard.scss, adminpanel.scss, form.scss, workspace.scss and a few more — compiles to one generated public CSS file. Everything else is a partial named _name.scss and reaches a bundle only through an @import.
- A partial is compiled only once something imports it: there is no glob and no index file. That is the step a new partial is forgotten at — the file exists, the Sass build passes, and none of it is in the output. Wire a foundational, reusable component into _minimal.scss and an app-specific one into backend.scss or whichever bundle owns the feature.
- The backend bundle has two layers. _minimal.scss is the base — the Bootstrap foundation plus TYPO3's own foundational partials, component/buttons, badges, panel, table, nav, modal and the scaffold/* layout. backend.scss is the application layer: it imports _minimal, then the backend-specific partials, the element/* custom-element styles and the typo3/* glue.
- The folders under Build/Sources/Sass/ each own a concern: component/ holds one partial per component, component/forms/ the form controls, component/scaffold/ the topbar, toolbar, module menu and sidebar, element/ the custom elements named after their host element, dashboard/ and module/ the area styles, variables/ and mixins/ the tokens and helpers. libs/ and typo3/ are third-party and legacy glue — no new component styles go there.
- Prefer focused component partials in the existing Sass structure.
- Name a partial after the class root it owns, _badges.scss for .badge. One partial owns one component; a component spread across partials is one nobody can find.
- Keep selectors close to the owning UI component.
- Use forms, scaffold, dashboard, and element folders for their owning concerns instead of creating broad global styles.
- Document a component's canonical markup in a // Markup: block at the top of its partial, and let the styleguide demo mirror that markup.
- The Sass layer uses @import, not the Dart Sass @use/@forward module system. Follow the existing import style and ordering rather than introducing the newer one in one file.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s build
- CI=true ./Build/Scripts/runTests.sh -s lintScss

## Styleguide Demos for CSS Components
Hints:
- All CSS components must be represented with demos in the styleguide extension.
- New CSS components need a matching styleguide demo.
- Changed CSS components should update an existing styleguide demo or add one when no demo exists.
- Backend component demos usually live below typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/.
- A demo covers what a reviewer would otherwise have to build themselves: the variants, the states, the sizes, with and without an icon, empty and disabled and mid-interaction, in both color schemes, and in RTL where the layout is direction-sensitive. A demo of the default state only shows the case nobody was worried about.
Worked example: typo3/sysext/styleguide — typo3_reference_list for what it demonstrates and where an installation has it.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s build
- CI=true ./Build/Scripts/runTests.sh -s lintScss
- CI=true ./Build/Scripts/runTests.sh -s functional

## Web Components and Element Styles
Hints:
- Styles for TYPO3 custom elements should start at the custom element host selector.
- Keep custom element Sass below Build/Sources/Sass/element/ when the style belongs to a web component.
- Use CSS custom properties, ::part(...), slots, and explicit host attributes as stable styling boundaries.
- Do not style arbitrary internal DOM depth when a host selector, part, slot, or custom property can express the contract.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s build
- CI=true ./Build/Scripts/runTests.sh -s lintScss
- CI=true ./Build/Scripts/runTests.sh -s lintTypescript
- CI=true ./Build/Scripts/runTests.sh -s unitJavascript

## CSS Class Naming
Hints:
- Use existing component naming conventions from nearby Sass files.
- TYPO3 backend component classes usually use a short component root plus hyphenated elements or variants, for example .panel-heading, .toolbar-item, or .module-docheader.
- Variants should customize the base component through custom properties whenever possible.
- Do not introduce a new naming system such as BEM-style block__element--modifier unless the surrounding component already uses it.
- Variants and sizes append a suffix to the root — .btn-sm, .card-size-large, .table-fit — and should mainly set the custom properties the base component consumes rather than duplicating a full rule set per variant.
- State classes are explicit and consistent: .active, .disabled, .selected, and the .is-* and .has-* forms.
- Avoid a generic name that can collide globally. There is one stylesheet and no scoping, so a .header or a .content in a component partial is a name taken from everybody.
- Use t3js-* classes only as JavaScript hooks and keep them separate from visual styling selectors.
Relevant checks:
- CI=true ./Build/Scripts/runTests.sh -s build
- CI=true ./Build/Scripts/runTests.sh -s lintScss

### General

## Building Assets in a Project Extension
Binding for work outside the TYPO3 core — a project repository or a distributed extension. In the core it is context for what such a repository has to do, and no condition of a patch.
Hints:
- An extension owns its asset source, build tool and generated output; installing it into TYPO3 does not attach its Sass or TypeScript to the core's Build/Sources pipelines. Put only browser-consumable output below Resources/Public/ and keep the source where the extension's own package scripts name it.
- Decide whether generated assets are committed. If they are, source and output change together; if they are not, the project deployment has to run the build. The extension's package.json and CI are the executable record of that decision.
- The public-assets hint covers how Resources/Public files are published and referenced. The extension-files hint covers Configuration/JavaScriptModules.php for backend JavaScript import maps; neither implies a particular bundler.
- For a patch to the TYPO3 backend itself, css-source-build-boundaries and backend-typescript describe the core's source trees and generated pairs; those paths and commands do not transfer to an extension.
```

Data:

```json
{
    "task": "sass build",
    "paths": [],
    "scopes": [],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "css"
    ],
    "withheldCategories": [],
    "hints": [
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "General",
            "scope": "extension",
            "hints": [
                {
                    "text": "An extension owns its asset source, build tool and generated output; installing it into TYPO3 does not attach its Sass or TypeScript to the core's Build/Sources pipelines. Put only browser-consumable output below Resources/Public/ and keep the source where the extension's own package scripts name it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Decide whether generated assets are committed. If they are, source and output change together; if they are not, the project deployment has to run the build. The extension's package.json and CI are the executable record of that decision.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The public-assets hint covers how Resources/Public files are published and referenced. The extension-files hint covers Configuration/JavaScriptModules.php for backend JavaScript import maps; neither implies a particular bundler.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "For a patch to the TYPO3 backend itself, css-source-build-boundaries and backend-typescript describe the core's source trees and generated pairs; those paths and commands do not transfer to an extension.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": []
        },
        {
            "id": "css-source-build-boundaries",
            "title": "CSS Source and Build Boundaries",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Treat Build/Sources/Sass/ as the source of truth when a Sass source exists.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "This is the core's asset pipeline. A project extension owns a separate build; see extension-asset-build.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Do not hand-edit generated public CSS as the only change.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Not every asset comes out of the Sass build. The CKEditor CSS is built through Build/rollup/ckeditor.js, so a change there is not picked up by a CSS build and looks like nothing happened.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Verify generated assets are in sync when public assets are committed.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use lintScss for TYPO3's stylelint setup and npm -- run build-css for a focused CSS build while iterating.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss",
                "CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css"
            ]
        },
        {
            "id": "css-components",
            "title": "CSS Component Structure",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "The backend stylesheet is a set of bundles. Each top-level entry file under Build/Sources/Sass/ without a leading underscore — backend.scss, dashboard.scss, adminpanel.scss, form.scss, workspace.scss and a few more — compiles to one generated public CSS file. Everything else is a partial named _name.scss and reaches a bundle only through an @import.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A partial is compiled only once something imports it: there is no glob and no index file. That is the step a new partial is forgotten at — the file exists, the Sass build passes, and none of it is in the output. Wire a foundational, reusable component into _minimal.scss and an app-specific one into backend.scss or whichever bundle owns the feature.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The backend bundle has two layers. _minimal.scss is the base — the Bootstrap foundation plus TYPO3's own foundational partials, component/buttons, badges, panel, table, nav, modal and the scaffold/* layout. backend.scss is the application layer: it imports _minimal, then the backend-specific partials, the element/* custom-element styles and the typo3/* glue.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The folders under Build/Sources/Sass/ each own a concern: component/ holds one partial per component, component/forms/ the form controls, component/scaffold/ the topbar, toolbar, module menu and sidebar, element/ the custom elements named after their host element, dashboard/ and module/ the area styles, variables/ and mixins/ the tokens and helpers. libs/ and typo3/ are third-party and legacy glue — no new component styles go there.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Prefer focused component partials in the existing Sass structure.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Name a partial after the class root it owns, _badges.scss for .badge. One partial owns one component; a component spread across partials is one nobody can find.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep selectors close to the owning UI component.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use forms, scaffold, dashboard, and element folders for their owning concerns instead of creating broad global styles.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Document a component's canonical markup in a // Markup: block at the top of its partial, and let the styleguide demo mirror that markup.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The Sass layer uses @import, not the Dart Sass @use/@forward module system. Follow the existing import style and ordering rather than introducing the newer one in one file.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss"
            ]
        },
        {
            "id": "css-styleguide-demos",
            "title": "Styleguide Demos for CSS Components",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "All CSS components must be represented with demos in the styleguide extension.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "New CSS components need a matching styleguide demo.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Changed CSS components should update an existing styleguide demo or add one when no demo exists.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Backend component demos usually live below typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A demo covers what a reviewer would otherwise have to build themselves: the variants, the states, the sizes, with and without an icon, empty and disabled and mid-interaction, in both color schemes, and in RTL where the layout is direction-sensitive. A demo of the default state only shows the case nobody was worried about.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss",
                "CI=true ./Build/Scripts/runTests.sh -s functional"
            ]
        },
        {
            "id": "css-web-components",
            "title": "Web Components and Element Styles",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Styles for TYPO3 custom elements should start at the custom element host selector.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep custom element Sass below Build/Sources/Sass/element/ when the style belongs to a web component.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use CSS custom properties, ::part(...), slots, and explicit host attributes as stable styling boundaries.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Do not style arbitrary internal DOM depth when a host selector, part, slot, or custom property can express the contract.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss",
                "CI=true ./Build/Scripts/runTests.sh -s lintTypescript",
                "CI=true ./Build/Scripts/runTests.sh -s unitJavascript"
            ]
        },
        {
            "id": "css-class-naming",
            "title": "CSS Class Naming",
            "category": "Backend CSS",
            "scope": "core",
            "hints": [
                {
                    "text": "Use existing component naming conventions from nearby Sass files.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "TYPO3 backend component classes usually use a short component root plus hyphenated elements or variants, for example .panel-heading, .toolbar-item, or .module-docheader.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Variants should customize the base component through custom properties whenever possible.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Do not introduce a new naming system such as BEM-style block__element--modifier unless the surrounding component already uses it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Variants and sizes append a suffix to the root — .btn-sm, .card-size-large, .table-fit — and should mainly set the custom properties the base component consumes rather than duplicating a full rule set per variant.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "State classes are explicit and consistent: .active, .disabled, .selected, and the .is-* and .has-* forms.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Avoid a generic name that can collide globally. There is one stylesheet and no scoping, so a .header or a .content in a component partial is a name taken from everybody.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Use t3js-* classes only as JavaScript hooks and keep them separate from visual styling selectors.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ],
            "checks": [
                "CI=true ./Build/Scripts/runTests.sh -s build",
                "CI=true ./Build/Scripts/runTests.sh -s lintScss"
            ]
        }
    ],
    "availableHints": []
}
```

### architecture: miss

Called with:

```json
{
    "task": "quantumflux"
}
```

Text:

```
Task: quantumflux
Answered for TYPO3 v14: statements that do not hold there are left out.
Domains: php (hints outside these domains are not shown)

Architecture hints:
No architecture hint matched. Name a path or a more specific topic, or ask for one of the ids below.

Hints that exist in these domains, requestable by id:
- language-files — Language Files (General)
- site-label-language — Core Labels on a Non-English Site (General)
- configuration-reach — Configuration Belongs to Its Reach (General)
- icon-usage — Rendering and Registering Icons (General)
- sitepackage-layout — How a Sitepackage Is Laid Out (General)
- project-repository-layout — How a TYPO3 Project Repository Is Laid Out (General)
- extension-repository-layout — How a Distributed Extension Repository Is Laid Out (General)
- form-framework — EXT:form Configuration and Runtime (General)
- frontend-records — Records in the Frontend Without Extbase (General)
- content-elements — Registering a Content Element (General)
- extension-documentation — Documenting a Project Extension (General)
- extension-asset-build — Building Assets in a Project Extension (General)
- documentation-changelog — Documentation and Changelog (General)
- deprecated-apis — Deprecated APIs (General)
- public-assets — Public Assets and the Publish Step (General)
- installation-upgrade — Upgrading an Installation (General)
- browser-tests — Browser Tests with Playwright (General)
- security-sinks — Following a Value to Its Sink (General)
- extension-static-analysis — Setting Up PHPStan for an Extension (PHP)
- system-extension-boundaries — System Extension Boundaries (PHP)
- dependency-injection-services — Dependency Injection and Services (PHP)
- events-extension-points — Events and Extension Points (PHP)
- tca-formengine — TCA, FormEngine, and Backend Forms (PHP)
- formdata-providers — FormEngine Data Providers (PHP)
- core-tests — Writing Core Tests (PHP)
- project-extension-tests — Testing a Project Extension (PHP)
- backend-modules — Backend Module and Route Registration (PHP)
- console-commands — Console Commands (PHP)
- extension-files — Extension Registration Files (PHP)
- tca-schema-api — TCA Schema API (PHP)
- datahandler-persistence — DataHandler and Persistence (PHP)
- routing-request-handling — Routing, Middleware, and Request Handling (PHP)
- caching — Caches (PHP)
- file-abstraction-layer — FAL: Storages, Files, and Drivers (PHP)
- authentication-permissions — Authentication and Permissions (PHP)
- upgrade-wizards — Upgrade Wizards (PHP)
- frontend-dataprocessors — Frontend DataProcessors (PHP)
- extbase — Extbase Plugins (PHP)
- sitepackage-initial-content — Shipping Initial Content with an Extension (PHP)
```

Data:

```json
{
    "task": "quantumflux",
    "paths": [],
    "scopes": [],
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "withheldCategories": [],
    "hints": [],
    "availableHints": [
        {
            "id": "language-files",
            "title": "Language Files",
            "category": "General"
        },
        {
            "id": "site-label-language",
            "title": "Core Labels on a Non-English Site",
            "category": "General"
        },
        {
            "id": "configuration-reach",
            "title": "Configuration Belongs to Its Reach",
            "category": "General"
        },
        {
            "id": "icon-usage",
            "title": "Rendering and Registering Icons",
            "category": "General"
        },
        {
            "id": "sitepackage-layout",
            "title": "How a Sitepackage Is Laid Out",
            "category": "General"
        },
        {
            "id": "project-repository-layout",
            "title": "How a TYPO3 Project Repository Is Laid Out",
            "category": "General"
        },
        {
            "id": "extension-repository-layout",
            "title": "How a Distributed Extension Repository Is Laid Out",
            "category": "General"
        },
        {
            "id": "form-framework",
            "title": "EXT:form Configuration and Runtime",
            "category": "General"
        },
        {
            "id": "frontend-records",
            "title": "Records in the Frontend Without Extbase",
            "category": "General"
        },
        {
            "id": "content-elements",
            "title": "Registering a Content Element",
            "category": "General"
        },
        {
            "id": "extension-documentation",
            "title": "Documenting a Project Extension",
            "category": "General"
        },
        {
            "id": "extension-asset-build",
            "title": "Building Assets in a Project Extension",
            "category": "General"
        },
        {
            "id": "documentation-changelog",
            "title": "Documentation and Changelog",
            "category": "General"
        },
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs",
            "category": "General"
        },
        {
            "id": "public-assets",
            "title": "Public Assets and the Publish Step",
            "category": "General"
        },
        {
            "id": "installation-upgrade",
            "title": "Upgrading an Installation",
            "category": "General"
        },
        {
            "id": "browser-tests",
            "title": "Browser Tests with Playwright",
            "category": "General"
        },
        {
            "id": "security-sinks",
            "title": "Following a Value to Its Sink",
            "category": "General"
        },
        {
            "id": "extension-static-analysis",
            "title": "Setting Up PHPStan for an Extension",
            "category": "PHP"
        },
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP"
        },
        {
            "id": "dependency-injection-services",
            "title": "Dependency Injection and Services",
            "category": "PHP"
        },
        {
            "id": "events-extension-points",
            "title": "Events and Extension Points",
            "category": "PHP"
        },
        {
            "id": "tca-formengine",
            "title": "TCA, FormEngine, and Backend Forms",
            "category": "PHP"
        },
        {
            "id": "formdata-providers",
            "title": "FormEngine Data Providers",
            "category": "PHP"
        },
        {
            "id": "core-tests",
            "title": "Writing Core Tests",
            "category": "PHP"
        },
        {
            "id": "project-extension-tests",
            "title": "Testing a Project Extension",
            "category": "PHP"
        },
        {
            "id": "backend-modules",
            "title": "Backend Module and Route Registration",
            "category": "PHP"
        },
        {
            "id": "console-commands",
            "title": "Console Commands",
            "category": "PHP"
        },
        {
            "id": "extension-files",
            "title": "Extension Registration Files",
            "category": "PHP"
        },
        {
            "id": "tca-schema-api",
            "title": "TCA Schema API",
            "category": "PHP"
        },
        {
            "id": "datahandler-persistence",
            "title": "DataHandler and Persistence",
            "category": "PHP"
        },
        {
            "id": "routing-request-handling",
            "title": "Routing, Middleware, and Request Handling",
            "category": "PHP"
        },
        {
            "id": "caching",
            "title": "Caches",
            "category": "PHP"
        },
        {
            "id": "file-abstraction-layer",
            "title": "FAL: Storages, Files, and Drivers",
            "category": "PHP"
        },
        {
            "id": "authentication-permissions",
            "title": "Authentication and Permissions",
            "category": "PHP"
        },
        {
            "id": "upgrade-wizards",
            "title": "Upgrade Wizards",
            "category": "PHP"
        },
        {
            "id": "frontend-dataprocessors",
            "title": "Frontend DataProcessors",
            "category": "PHP"
        },
        {
            "id": "extbase",
            "title": "Extbase Plugins",
            "category": "PHP"
        },
        {
            "id": "sitepackage-initial-content",
            "title": "Shipping Initial Content with an Extension",
            "category": "PHP"
        }
    ]
}
```
