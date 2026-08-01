---
date: 2026-07-30T19:01:23+02:00
category: tool-gap
status: closed
closed: 2026-07-30
commit: 9ced27c
subject: "[FEATURE] Read a selected documentation page"
tool: typo3_documentation_lookup
---

# The documentation lookup finds the right page and cannot hand it over. It searches the official m...

## Observation

The documentation lookup finds the right page and cannot hand it over. It searches the official manual and answers with a title, a canonical URL and one paragraph of excerpt; there is no way to read the page it just found.

In a forward run of EXT-04 the session asked it well: four short English queries in one call, targetVersion 14, among them "backend module controller ModuleTemplate" and "module doc header buttons". It got back exactly the two pages that answer them, DocHeaderComponent and ModuleTemplate for 14.3. It never followed either URL. Instead it reconstructed the API with about twenty-five greps through vendor/typo3 - AbstractButton, InputButton, ShortcutButton, ModuleTemplateFactory, the label resolution in BaseModule - and still missed that the shortcut button had been replaced, which its own test caught afterwards.

That is the largest single cost in a 132-call session, and it is spent re-deriving what the manual states in full.

## Query

backend module registration Modules.php; backend module controller ModuleTemplate; module doc header buttons; extension configuration ext_conf_template

## Suggestion

Let a caller read what the search found. Documentation already fetches the index and the pages of the versioned manuals, so the missing half is a way in: a page of the manual, for the version that was searched, returned as text with its canonical URL.

What it is worth is not another lookup - it is the difference between an excerpt that has to be verified in the core sources and an answer that can be acted on. Weigh the reading against the installed sources: the checkout is the truth for that installation, the manual is the truth for the API, and the answer should say which of the two it is.
