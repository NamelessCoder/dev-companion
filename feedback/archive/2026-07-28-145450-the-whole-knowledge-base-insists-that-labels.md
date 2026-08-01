---
date: 2026-07-28T14:54:50+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: fedb5bd
subject: "Derive the translation domain instead of only looking it up"
tool: typo3_label_lookup
---

# The whole knowledge base insists that labels are referenced by translation domain (backend.alt_do...

## Observation

The whole knowledge base insists that labels are referenced by translation domain (backend.alt_doc:key) instead of the EXT: file path, but nowhere is it documented how the domain name of an XLF file is derived. typo3_script_help for "register a new translation domain for an XLF file" returned the unrelated "Invoking runTests.sh" section, and typo3_label_lookup mode=domains only lists domains of the ~118 files inside the snapshot — so for a newly added XLF file, or any file outside the covered subset, there is no way to get the answer from this server. The rules are non-obvious and are documented in typo3/sysext/core/Classes/Localization/TranslationDomainMapper.php: format package[.subdomain].resource; Resources/Private/Language is omitted; subdirectories become dot-separated parts; UpperCamelCase becomes snake_case (SudoMode.xlf -> backend.sudo_mode, Database.xlf -> form.database); locallang.xlf becomes "messages"; locallang_<suffix>.xlf becomes "<suffix>" keeping underscores; Configuration/Sets/<set>/labels.xlf becomes sets.<set>; locale prefixes such as de.locallang.xlf are ignored; the package part is the extension key, and files without the locallang prefix win when two files map to the same domain. Because the derivation is unstated, an agent that adds a new XLF file guesses the domain, and a wrong guess fails only at runtime.

## Query

typo3_script_help task="register a new translation domain for an XLF file"; typo3_label_lookup mode=domains

## Suggestion

Add these derivation rules as a knowledge section (Language Files / translation domains) so they are reachable from typo3_architecture_hint and typo3_rule_lookup, and ideally expose a derivation in typo3_label_lookup: given an XLF path, return the domain it maps to, computed rather than looked up, so it also answers for files outside the snapshot and for files a patch is about to add. Mentioning TranslationDomainMapper as the authority would let a contributor verify against the checkout.
