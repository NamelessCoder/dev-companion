---
date: 2026-08-18T11:33:27+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_schema_lookup, typo3_changelog_lookup
directory: /home/benji/projects/bootstrap_package
---

# how a core tt_content palette showitem differs between 13.4 and 14 was the whole bug, and a core ...

## Observation

Task: review GitHub PR #1627 against the bootstrap_package TYPO3 sitepackage, a one-line TCA change, and say whether it is correct.

The PR fixed a field disappearing from the content element form. Root cause: the extension appends to a core palette with `$GLOBALS['TCA']['tt_content']['palettes']['frames']['showitem'] .= '...'`. On 13.4 the core definition is a multi-line string whose last item is `space_after_class;LLL:EXT:frontend/.../locallang_ttc.xlf:space_after_class_formlabel,` — with a trailing comma — so the concatenation worked for years. On 14 the same palette is a single line, `'layout,frame_class,space_before_class,space_after_class'`, with the per-field label suffixes and the trailing comma both removed. The appended `\n    --linebreak--,` then fuses with the last item into one invalid item, `space_after_class\n    --linebreak--`, and both the field and the line break silently vanish from the form. No error is raised anywhere.

I established this by grepping typo3/sysext/frontend/Configuration/TCA/tt_content.php in a core checkout, then running `git log -L` over the palette block to see the two shapes side by side, then `git show origin/13.4:...` to confirm the older one — four round trips of archaeology before I could say anything about the patch. I never put it to this server, for the reason filed separately about name-only tool lists, so I cannot report that a lookup failed. I report that the knowledge the entire review turned on was the per-version shape of one core palette, and that a checkout was the only place I looked.

Two further facts came from the same checkout and belong to the same question: that `ExtensionManagementUtility::addFieldsToPalette()` is the safe way to do this because `removeDuplicatesForInsertion()` explicitly exempts `--linebreak--` from deduplication, and that a resulting double comma is harmless because both PaletteAndSingleContainer and TcaColumnsProcessShowitem parse with `GeneralUtility::trimExplode(',', ..., true)`. Those two decided the review's recommendation and its backward-compatibility verdict on 13.4.

## Query

"tt_content palettes frames showitem 13.4 vs 14" — never submitted to this server. Answered instead with grep over typo3/sysext/frontend/Configuration/TCA/tt_content.php, `git log -L` over the palette block, and `git show origin/13.4:` on the same file.

## Suggestion

One call returning a core TCA table's types and palettes as a given version defines them, and diffing one entry across two majors, would have replaced the archaeology and been the whole answer. For this case it would have had to return, for table tt_content, palette frames: the 13.4 showitem string, the 14 showitem string, and the observation that the trailing comma and the `;LLL:` label suffixes are gone in 14. The trailing comma is not cosmetic — every extension in the wild that appends to a core palette with `.=` breaks on exactly this, silently. If a changelog entry covers the 14 palette reshaping, typo3_changelog_lookup answering "tt_content palette showitem labels" would be the second-best route and worth checking exists.
