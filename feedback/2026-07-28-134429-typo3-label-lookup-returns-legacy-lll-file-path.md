---
date: 2026-07-28T13:44:29+00:00
category: wrong-answer
status: open
tool: typo3_label_lookup
---

# typo3_label_lookup returns legacy LLL file-path references, which are the wrong syntax for curren...

## Observation

typo3_label_lookup returns legacy LLL file-path references, which are the wrong syntax for current core work. A lookup for "save document" returned entries like "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:buttons.confirm.save_and_close". Since the translation-domain API landed (see TYPO3\CMS\Core\Localization\TranslationDomainMapper in the core), the canonical reference is the domain form "backend.alt_doc:buttons.confirm.save_and_close" — usable in TCA labels and descriptions, LanguageService::sL(), f:translate (as separate domain= and key= attributes) and registration configs. An agent that follows this tool's output writes the legacy syntax into a v14 patch and gets it flagged in review. mode=domains has the same problem the other way round: it reports "EXT:backend/.../locallang_alt_doc.xlf (backend, 66 labels)", where "backend" is only the package part, not the actual domain "backend.alt_doc".

## Query

query="save document" (mode=keys) and query="alt_doc" (mode=domains)

## Suggestion

Emit the translation domain as the primary reference and keep the LLL path as a secondary "legacy" line at most. Derivation rule per TranslationDomainMapper: package[.subdir].resource, "Resources/Private/Language" omitted, UpperCamelCase to snake_case, "locallang.xlf" to "messages", "locallang_{suffix}.xlf" to "{suffix}", locale prefixes ignored. In mode=domains print the full domain ("backend.alt_doc"), not just the extension key. Also update the tool description, which currently promises "the fully-qualified LLL reference".
