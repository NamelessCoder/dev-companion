---
date: 2026-08-03T16:47:34+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation; the extension ships a fork of the core backend login Fluid layout.

I asked typo3_documentation_lookup for the v14 Fluid template file-extension convention (the core renamed its templates to *.fluid.html in v14) and for how a layout root path override on the login screen is meant to work. Three queries returned six results, none on either subject:

- "Multi-language Fluid templates" (ExtensionArchitecture/HowTo/Localization/Fluid.html)
- "Naming" (extension key / vendor naming conventions)
- "be.pagePath" (ViewHelper reference)
- "Rootline / Breadcrumb" (TypoScript menu processing)
- "Override service registration" (DI service configuration)
- "Backend layout" (page backend layouts, an unrelated concept that merely shares the word)

The `matched` field returned with each result shows the mechanism: "fluid." and "extens" matched against the URL **path**, "templa" against the title, and for the third query "layout"/"root"/"overri" matched three unrelated titles in three different manuals. So the ranking is substring matching over URL path segments and titles, and a query whose words happen to occur as substrings of unrelated URL segments outranks the page that answers it. "layout root path" reliably collides with "backend layout" and "rootline", which are different subjects that share tokens.

The cost is not the wasted call. Six confidently formatted, version-stamped, canonically-URL'd results read as "the official manual has nothing on this", when what actually happened is that the query never reached any page text. For an audit that is the expensive kind of wrong answer: it invites recording a surface as documented-nowhere when it may be documented well. I ended up settling the question by reading typo3fluid/fluid's TemplatePaths::resolveFileInPaths() in the installed vendor tree instead.

## Query

typo3_documentation_lookup {queries: ["fluid.html file extension templates", "Fluid template file naming convention v14", "layout root paths login screen override"], targetVersion: "14"}

## Suggestion

Match against page body text, not only URL path segments and titles — or at minimum weight a title or body hit far above a path-substring hit, and drop path-only matches below a relevance threshold. Where nothing clears the threshold, return that fact rather than the best six substring collisions: typo3_changelog_lookup already does the honest version of this by naming the largest part of a query that did reach entries, and the same convention here would let a caller distinguish "the manual does not cover this" from "your query did not reach the manual".
