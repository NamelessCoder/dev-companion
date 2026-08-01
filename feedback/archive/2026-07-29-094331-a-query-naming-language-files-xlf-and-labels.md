---
date: 2026-07-29T09:43:31+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 878c879
subject: "Let a query about language files find the hint about language files"
tool: typo3_architecture_lookup
---

# A query naming language files, XLF and labels explicitly returned only the tca-formengine hint an...

## Observation

A query naming language files, XLF and labels explicitly returned only the tca-formengine hint and nothing about language files. The knowledge base clearly holds that material: typo3_rule_lookup lists "Language Files" as a topic of typo3-core-architecture, typo3_server_scope advertises "language files" among the covered subsystems, and typo3-core-rules has an "XLIFF Label Lifecycle" topic. None of it surfaced for the query that most directly asks for it, so the retrieval matched on "TCA" and stopped. The same query is what a caller would use before touching an XLF file, and it is the one place where the answer would have connected to typo3_translation_domain_lookup and typo3_label_lookup. By contrast the same tool did very well on other topics — "TypoScript site sets" returned the settings.definitions.yaml hint including the point that it replaces constants.typoscript, which is directly actionable in this project, and the ViewHelper and icon-usage hints were the strongest content the server produced. The gap is retrieval, not content.

## Query

typo3_architecture_lookup{task:"Language files, XLF labels and how to reference them in TCA"}

## Suggestion

Check why the language-files hint does not match a query containing "language files", "XLF", "XLIFF" and "labels" — most likely the hint lacks those keywords in whatever field the matcher scores, or the tca-formengine hint outscores it and the limit truncates. Adding xlf/xliff/locallang/label/translation as keywords on that hint should fix it. It would also help to expose the hint ids as a listable index, the way typo3_rule_lookup returns its documents and topics on a miss, so a caller can see that a "Language Files" hint exists and request it directly instead of guessing at phrasings until one matches.
