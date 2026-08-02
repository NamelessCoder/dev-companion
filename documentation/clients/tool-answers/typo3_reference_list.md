# What `typo3_reference_list` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## references

Called with:

```json
{}
```

Text:

```
Worked examples in the TYPO3 core, as TYPO3 v14 has them.
Paths are relative to a core checkout. Where none is at hand, they are also the paths in github.com/TYPO3/typo3 on the matching branch.

- typo3/sysext/theme_camino — TYPO3 v14 and newer
  A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.
  Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.
  In an installation: vendor/typo3/theme-camino/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="sitepackage-layout"
- typo3/sysext/styleguide — TYPO3 v13 and newer
  Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.
  In an installation: vendor/typo3/cms-styleguide/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="css-styleguide-demos"
- typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example
  An Extbase extension the core keeps green: domain models with their TCA, repositories, controllers and validators — and, in the functional tests around it, how a repository is exercised at all, request and configuration manager included.
  In an installation: vendor/typo3/cms-extbase/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="extbase"
- typo3/sysext/fluid_styled_content
  The content elements every installation has: how a record type is registered, the TypoScript that renders it on lib.contentElement, the templates a sitepackage overrides, and the backend previews beside them.
  In an installation: vendor/typo3/cms-fluid-styled-content/, below the same path with the typo3/sysext/<key>/ prefix removed.
  Conventions: typo3_architecture_lookup id="content-elements"
- Build/tests/playwright/e2e — TYPO3 v14 and newer
  A browser suite that runs: specs one directory per module here, the page objects they compose themselves from in ../fixtures/, the login setup every project depends on in ../helper/, and the accessibility scan among them. Build/playwright.config.ts is where the projects and their dependencies are declared.
  Only in the core repository — no Composer package ships it.
  Conventions: typo3_architecture_lookup id="browser-tests"
- Build/phpstan
  The PHPStan setup of a large TYPO3 codebase: the configuration a project copies, the baseline that makes adopting it possible at all, and the custom rules in src/.
  Only in the core repository — no Composer package ships it.
- Build/php-cs-fixer
  The PHP-CS-Fixer configuration the core's own CGL check runs, with the file header the core requires as a rule of its own.
  The header comment is the core's licence block; a project keeps the rule set and replaces that.
  Only in the core repository — no Composer package ships it.
```

Data:

```json
{
    "targetVersion": 14,
    "matchCount": 7,
    "references": [
        {
            "id": "sitepackage",
            "path": "typo3/sysext/theme_camino",
            "package": "typo3/theme-camino",
            "reference": "A sitepackage worked out in full: one template root with Pages/, Content/ and ContentPreviews/, a site set carrying its TypoScript, page TSconfig, settings and labels, backend layouts, and its content elements registered as record types of tt_content.",
            "caveat": "Read it, do not depend on it: it is the theme of one release line and is announced to move out of the core into a repository of its own.",
            "hint": "sitepackage-layout",
            "since": 14,
            "until": null,
            "existsOn": "TYPO3 v14 and newer"
        },
        {
            "id": "backend-components",
            "path": "typo3/sysext/styleguide",
            "package": "typo3/cms-styleguide",
            "reference": "Every backend component as rendered markup and every TCA field type as a record you can open: the demo pages the component catalog names, and a TCA corpus that answers what a column configuration looks like when it works.",
            "caveat": null,
            "hint": "css-styleguide-demos",
            "since": 13,
            "until": null,
            "existsOn": "TYPO3 v13 and newer"
        },
        {
            "id": "extbase-plugin",
            "path": "typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example",
            "package": "typo3/cms-extbase",
            "reference": "An Extbase extension the core keeps green: domain models with their TCA, repositories, controllers and validators — and, in the functional tests around it, how a repository is exercised at all, request and configuration manager included.",
            "caveat": null,
            "hint": "extbase",
            "since": null,
            "until": null,
            "existsOn": ""
        },
        {
            "id": "content-elements",
            "path": "typo3/sysext/fluid_styled_content",
            "package": "typo3/cms-fluid-styled-content",
            "reference": "The content elements every installation has: how a record type is registered, the TypoScript that renders it on lib.contentElement, the templates a sitepackage overrides, and the backend previews beside them.",
            "caveat": null,
            "hint": "content-elements",
            "since": null,
            "until": null,
            "existsOn": ""
        },
        {
            "id": "browser-tests",
            "path": "Build/tests/playwright/e2e",
            "package": null,
            "reference": "A browser suite that runs: specs one directory per module here, the page objects they compose themselves from in ../fixtures/, the login setup every project depends on in ../helper/, and the accessibility scan among them. Build/playwright.config.ts is where the projects and their dependencies are declared.",
            "caveat": null,
            "hint": "browser-tests",
            "since": 14,
            "until": null,
            "existsOn": "TYPO3 v14 and newer"
        },
        {
            "id": "static-analysis",
            "path": "Build/phpstan",
            "package": null,
            "reference": "The PHPStan setup of a large TYPO3 codebase: the configuration a project copies, the baseline that makes adopting it possible at all, and the custom rules in src/.",
            "caveat": null,
            "hint": null,
            "since": null,
            "until": null,
            "existsOn": ""
        },
        {
            "id": "coding-standards",
            "path": "Build/php-cs-fixer",
            "package": null,
            "reference": "The PHP-CS-Fixer configuration the core's own CGL check runs, with the file header the core requires as a rule of its own.",
            "caveat": "The header comment is the core's licence block; a project keeps the rule set and replaces that.",
            "hint": null,
            "since": null,
            "until": null,
            "existsOn": ""
        }
    ],
    "coveredVersions": [
        12,
        13,
        14,
        15
    ]
}
```
