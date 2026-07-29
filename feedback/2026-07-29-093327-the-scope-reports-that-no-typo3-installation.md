---
date: 2026-07-29T09:33:27+00:00
category: bug
status: open
tool: typo3_server_scope
---

# The scope reports that no TYPO3 installation was found, although this extension checkout has .bui...

## Observation

The scope reports that no TYPO3 installation was found, although this extension checkout has .build/bin/typo3 and Composer-installed TYPO3 13/14 dependencies. Consequently typo3_icon_lookup and typo3_label_lookup return answeredBy=nothing and cannot validate the extension own registered icon content-bootstrappackage-accordion or labels. This makes the installation-aware tools unavailable in a common extension-development layout.

## Query

Run from /home/benji/projects/bootstrap_package, a Composer TYPO3 extension checkout with .build/bin/typo3 and installed TYPO3 packages

## Suggestion

Teach Instance discovery to recognize Composer extension development checkouts with a configured bin-dir/vendor-dir, for example .build/bin/typo3 and .build/vendor, or allow an explicit installation root/console path in MCP configuration. Scope output should list searched candidates and rejection reasons so discovery failures are diagnosable.
