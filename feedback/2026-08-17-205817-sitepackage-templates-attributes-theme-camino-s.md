---
date: 2026-08-17T20:58:17+00:00
category: wrong-answer
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# sitepackage-templates attributes theme_camino's templateName configuration to lib.contentElement ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. Deciding how six custom content elements resolve their Fluid templates.

The sitepackage-templates hint states: "The template of a content element is derived from its CType rather than configured per element: lib.contentElement carries templateName.ifEmpty.cObject with case = uppercamelcase, so my_ext_text_teaser renders Content/MyExtTextTeaser.fluid.html. That is what makes a snake_case CType a requirement rather than a habit."

On this installation lib.contentElement carries no such thing. vendor/typo3/cms-fluid-styled-content/Configuration/TypoScript/Helper/ContentElement.typoscript sets `templateName = Default` as a plain value, and each shipped element overrides it explicitly — Configuration/TypoScript/ContentElement/Text.typoscript is `tt_content.text =< lib.contentElement` followed by `templateName = Text`. The uppercamelcase derivation is theme_camino's own configuration: its Configuration/Sets/camino/TypoScript/content.typoscript resets `templateName >` and then adds `templateName.ifEmpty.cObject` with `case = uppercamelcase`.

So the mechanism the hint describes is real, but it belongs to a package a project may not have installed, and the hint presents it as a property of lib.contentElement. I only caught it because I read the installed fluid_styled_content source before writing my TypoScript. Had I trusted the hint, six elements would have rendered fluid_styled_content's Default.fluid.html — with no error, because Default exists.

The consequence stated in the hint inherits the error: a snake_case CType is not "a requirement" under fluid_styled_content alone, since nothing derives a file name from it there.

## Query

typo3_hint_lookup id=sitepackage-templates targetVersion=14, then read vendor/typo3/cms-fluid-styled-content/Configuration/TypoScript/Helper/ContentElement.typoscript on TYPO3 14.3.6

## Suggestion

Attribute the derivation to where it lives: theme_camino configures lib.contentElement that way; fluid_styled_content ships `templateName = Default` and each of its elements sets templateName explicitly in its own TypoScript file. Then say which of the two a project sitepackage is choosing between — inheriting camino's convention means reproducing that configuration, and the alternative (one TypoScript file per element with an explicit templateName, which is what fluid_styled_content itself does) is what the content-element checklist already asks for elsewhere. The snake_case-CType consequence should move with it, since it only holds under the camino-style configuration.
