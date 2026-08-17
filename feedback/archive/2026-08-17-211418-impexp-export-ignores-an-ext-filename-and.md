---
date: 2026-08-17T21:14:18+00:00
category: wrong-answer
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# impexp:export ignores an EXT: filename and writes to the default import-export folder, reporting ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. This is a second, separate incorrect claim in the impexp-artifact hint; I filed one earlier about --include-related=sys_file and this is a different sentence with a different fix.

The hint states: "A file path handed to the console is resolved by GeneralUtility::getFileAbsFileName(): a relative path is relative to the public directory, not to the project root. EXT:<key>/Initialisation/data.xml is the form that works from anywhere."

I followed it exactly, passing EXT:site_distribution/Initialisation/data as the filename argument so the artifact would land straight in the package that ships it. The command answered "[OK] Exporting to fileadmin/user_upload/_temp_/importexport/data.xml succeeded." The EXT: prefix was neither honoured nor rejected — the export went to the default import-export folder under fileadmin, and the success message named that path rather than the one I gave.

I only noticed because I went looking for the file in the package directory and found the Initialisation/ directory holding nothing but the Site/ subdirectory I had created by hand. Had I trusted the OK line, the distribution would have shipped with no data.xml at all and the failure would have surfaced on someone else's installation.

The workaround is trivial once known: pass a bare name, then move data.xml and its .files directory into the package. But the hint presents the EXT: form as the one "that works from anywhere", which is precisely the sentence a reader relies on to avoid a manual move, and it is the opposite of what happens.

I did not establish whether the argument is resolved differently from what the hint describes or ignored outright — the export succeeded either way, so nothing forced the question. What is certain is the observed behaviour and the reported destination.

## Query

typo3_hint_lookup id=impexp-artifact targetVersion=14, then: typo3 impexp:export --pid=1 --levels=999 --table=tt_content --save-files-outside-export-file EXT:site_distribution/Initialisation/data on TYPO3 14.3.6

## Suggestion

Correct or drop the EXT: sentence for the export direction, and say what the command actually does with the filename argument: it writes into the default import-export folder below fileadmin and reports that path in its success message, so a package artifact has to be moved into place afterwards. Worth stating the check alongside it, since the command reports success regardless — look at the path in the OK line rather than assuming the argument was honoured. If the EXT: form does hold for impexp:import but not for impexp:export, saying which direction it applies to would preserve the useful half.
