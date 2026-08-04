---
date: 2026-08-04T17:58:04+00:00
category: bug
status: open
model: claude-opus-5
tool: typo3_documentation_lookup, typo3_hint_lookup
directory: /home/benji/projects/site-new
---

# Task: add the TYPO3-recommended manual to a project sitepackage that had none (typo3_extension_de...

## Observation

Task: add the TYPO3-recommended manual to a project sitepackage that had none (typo3_extension_describe reported manual: null, readme: null).

Both lookups gave me the policy and neither gave me the scaffold. The hint `extension-documentation` says a manual lives in Documentation/ with Index.rst as entry point and guides.xml as renderer configuration, and that it ships with the package rather than with the project. The documentation page ExtensionArchitecture/HowTo/Documentation.html says Documentation/ is recommended over a single README and why. Neither says what has to be in the directory.

I got the rest by reading installed packages: vendor/typo3fluid/fluid/Documentation/guides.xml and vendor/typo3/cms-dashboard/Documentation/guides.xml for the file shape (xmlns https://www.phpdoc.org/guides, xsi:schemaLocation pointing at guides-cli's schema, the \T3Docs\Typo3DocsTheme\DependencyInjection\Typo3DocsThemeExtension element, the project element with title/release/version/copyright), plus Includes.rst.txt and the Index.rst header block with the toctree. That is four extra file reads to learn a fixed scaffold.

I also had to find the renderer myself: ghcr.io/typo3-documentation/render-guides, and that --fail-on-log is what turns a warning into a non-zero exit. Without that flag a broken reference renders quietly.

## Query

typo3_documentation_lookup queries=["extension documentation guides.xml","rendering documentation locally","Documentation Index.rst structure"] targetVersion=14; then page=".../ExtensionArchitecture/HowTo/Documentation.html"; then typo3_hint_lookup id=extension-documentation targetVersion=14.3

## Suggestion

Extend `extension-documentation` — or add a neighbour id such as extension-documentation-scaffold — with the file inventory (guides.xml, Includes.rst.txt, Index.rst plus one directory per chapter), a minimal guides.xml an extension can copy, and the render command with --fail-on-log. The policy half is already good; what is missing is the half that lets a session produce the directory without reading three vendor packages to reverse-engineer it.
