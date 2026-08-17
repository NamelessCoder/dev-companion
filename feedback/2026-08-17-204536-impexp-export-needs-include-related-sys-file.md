---
date: 2026-08-17T20:45:36+00:00
category: wrong-answer
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# impexp:export needs --include-related=sys_file; the hint says images come along on their own

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6.

The impexp-artifact hint states: "Images come along on their own — a sys_file_reference pulls its sys_file record in as a relation and the bytes follow — but by default they are base64 inside the export file". On the CLI at 14.3.6 they do not. Exporting exactly as the hint prescribes, with --table=sys_file_reference named explicitly and --save-files-outside-export-file given, produced an export whose header declared the relations (elements of the form sys_file:1 under a sys_file_reference record's rels) but whose body contained no sys_file table, no files_fal section, and a data.xml.files directory that was created and left empty. Nothing was reported: the command answered "[OK] Exporting to ... succeeded."

Adding --include-related=sys_file to the same invocation fixed it in one step: the body then carried a sys_file table, a files_fal section appeared as a third top-level element beside header and records, and data.xml.files held the nine image files each under its sha1. Export size went from 55318 to 76253 bytes.

The failure mode is the expensive one the hint itself warns about elsewhere: the export looks complete, the import succeeds, and the sys_file_reference rows on the target point at whatever sys_file uids happen to exist there.

## Query

typo3_hint_lookup id=impexp-artifact targetVersion=14; then typo3 impexp:export --pid=1 --levels=999 --table=tt_content --table=tx_sitepackage_teaser_card --table=tx_sitepackage_accordion_item --table=sys_file_reference --save-files-outside-export-file --dependency=site_package data

## Suggestion

The hint should say that on the console the related sys_file records are not pulled in by naming sys_file_reference alone: --include-related=sys_file is required, and --save-files-outside-export-file only decides where the bytes go once they are included at all. Worth stating the check too, since the command reports success either way: a correct export has a files_fal section beside header and records, and its .files directory is non-empty.
