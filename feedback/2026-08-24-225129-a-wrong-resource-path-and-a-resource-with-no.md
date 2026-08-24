---
date: 2026-08-24T22:51:29+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_label_lookup
directory: /home/benji/projects/typo3-cms
---

# A wrong resource path and a resource with no match both answer matchCount 0

## Observation

Task: adding a client-side error state to the localization wizard, I needed a heading label and wanted to reuse rather than add one.

First call, which worked and was the best answer of the session:
  typo3_label_lookup(query="error", resource="EXT:backend/Resources/Private/Language/Wizards/localization.xlf")
returned eight labels including `error.recordNotFound` — "Record with uid \"%s\" of type \"%s\" does not exist or is not accessible." That already carried "or is not accessible", i.e. exactly the permission case, and Build/types/labels/backend.wizards.localization.d.ts types it as [string, string] so it is callable from TypeScript with positional args. No new label, no translation cycle. Keep this.

Second call, which cost a round trip and a wrong inference:
  typo3_label_lookup(query="error title", resource="EXT:backend/Resources/Private/Language/Wizard.xlf")
returned matchCount 0 with terms [{error, 0}, {title, 0}].

I had guessed the resource path. There is no Wizard.xlf; the file is Wizards/general.xlf, and it does contain `wizard.step.error.title` = "Error", which is exactly what I was looking for. The empty answer reads as "this resource has no such label" when the truth was "there is no such resource", and those two call for opposite next moves: the first says stop looking and add a label, the second says fix the path. I read it the first way, then found the label by grepping the checkout for a string I already knew.

The terms breakdown makes this worse rather than better: reporting that "error" matches 0 and "title" matches 0 implies a resource was searched and came up empty. Nothing in the answer says the resource was never found.

Related, smaller: I never called typo3_translation_domain_lookup, which would have computed the domain from a path. It would not have helped here — my problem was the path itself, which is the input that tool takes.

## Query

typo3_label_lookup(query="error", resource="EXT:backend/Resources/Private/Language/Wizards/localization.xlf") -> 8 labels, good. typo3_label_lookup(query="error title", resource="EXT:backend/Resources/Private/Language/Wizard.xlf") -> matchCount 0, but the resource does not exist; the real file is EXT:backend/Resources/Private/Language/Wizards/general.xlf

## Suggestion

When `resource` names a path that resolves to no XLF the installation knows, say so instead of answering with an empty match set. A field like `resourceResolved: false` plus, ideally, the near misses under the same directory: "no such resource; EXT:backend/Resources/Private/Language/Wizards/ holds general.xlf, localization.xlf, ...".

The nearest-directory listing is what turns the failure into an answer, because a caller guessing a resource path is almost always one path segment or one pluralisation away — here Wizard.xlf vs Wizards/general.xlf.

Failing that, the shortest fix is one sentence in the answer when matchCount is 0: whether the resource was found at all.
