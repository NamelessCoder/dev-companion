---
date: 2026-08-07T23:35:01+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_hint_lookup answered three core frontend link-building paths with sitepackage authoring hin...

## Observation

Task: establish whether Forge issue 15984 — links to pages restricted only through an ancestor's extendToSubpages are not replaced by the login-page link — still reproduces on a 15.0.0-dev core checkout.

All three paths were classified scope "core" correctly. The four hints returned were persistence-reading, frontend-records, page-variables-and-processors and system-extension-boundaries.

Only persistence-reading was adjacent, and usefully so: its lines on FrontendRestrictionContainer carrying FrontendGroupRestriction, and that "access groups and workspaces are conditions there and nowhere else", are real context for an fe_group question. frontend-records ("a table of your own is a TCA file and nothing else", dataProcessing, record-transformation) and page-variables-and-processors (PAGEVIEW, what a page template is handed, menu processor excludeDoktypes) are sitepackage and TypoScript authoring hints. Neither bears on PageLinkBuilder, and the paths I passed were core PHP classes, not templates or site sets. system-extension-boundaries is generic. One partial hit in four.

The larger finding is in availableHints, which is long and contains nothing about frontend access restriction. No hint covers fe_group evaluation, extendToSubpages, the split between RecordAccessVoter::groupAccessGranted and RecordAccessVoter::accessGrantedForPageInRootLine, or config.typolinkLinkAccessRestrictedPages.

That split is the entire substance of this defect and of a family of related ones: the frontend enforces an inherited restriction when serving a page (PageInformationFactory.php:485 calls accessGrantedForPageInRootLine, which honours extendToSubpages) and ignores it when building a link to that same page (PageLinkBuilder.php:486 calls groupAccessGranted, which reads only the record's own fe_group and returns true when it is empty — RecordAccessVoter.php:120). TYPO3 therefore emits a link it will then refuse to serve with "Subsection was found and not accessible". I established this by reading RecordAccessVoter.php whole and grepping both methods for callers.

I would still make this call — base.md orders it and persistence-reading paid for it — but the path matching pulled in template-authoring material that a core frontend PHP task cannot use.

## Query

task: "frontend link building and access restriction for menus", targetVersion: "15", paths: ["typo3/sysext/frontend/Classes/Typolink/PageLinkBuilder.php", "typo3/sysext/frontend/Classes/ContentObject/Menu/AbstractMenuContentObject.php", "typo3/sysext/core/Classes/Domain/Access/RecordAccessVoter.php"]

## Suggestion

Add a hint for frontend access restriction covering: that RecordAccessVoter::groupAccessGranted reads only the record's own fe_group and never walks the rootline; that accessGrantedForPageInRootLine is the rootline-aware form that honours extendToSubpages; where each is consumed (PageInformationFactory and PageRepository::getDescendantPageIdsRecursive for the first, PageLinkBuilder for the second); and that config.typolinkLinkAccessRestrictedPages and menu showAccessRestrictedPages both funnel into the single PageLinkBuilder call site. Weight path matching so core Classes/ PHP paths do not draw hints whose content is Fluid templates, site sets and dataProcessing.
