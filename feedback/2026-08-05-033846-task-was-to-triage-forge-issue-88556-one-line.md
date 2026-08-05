---
date: 2026-08-05T03:38:46+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Task was to triage Forge issue #88556 ("One line break in DB field causes 3 rendered p-tags in CK...

## Observation

Task was to triage Forge issue #88556 ("One line break in DB field causes 3 rendered p-tags in CKEditor") against a core checkout on main, and then to write the patch.

typo3_forge_lookup {issue: "88556"} returned a good, complete read of the text of the issue — description, all 11 notes with authors and dates, relations, current status and target version. What it did not return is that the issue carries seven attachments, and on this issue the attachments were where the decisive evidence sat. The notes are unusable without them: David Menzel's 2023 comment is literally "A bodytext database field has the following text: !db_field_value.jpg! The RTE ckeditor looks like this: !rte_view.jpg! ..." — the Redmine inline-image syntax comes through as bare filenames, so the tool hands back a comment whose entire content is references to files it does not mention exist.

I fetched the raw Redmine JSON with include=attachments, downloaded two of them and read them as images, and both changed the outcome:

- ckeditor-3-p-tags.png (2019) showed the CKEditor source view: <p>&nbsp;</p>, <p>Hello World</p>, <p>&nbsp;</p>, <p>&nbsp;</p>, <ul>. That confirmed the reported symptom matched the nested-<p> markup I had reproduced out of RteHtmlParser, and it is what let me say the "3 p-tags" of the title are what CKEditor makes of the nesting rather than something the PHP layer emits.
- db_field_value.jpg (2023) showed the second reporter's literal database content, including a <pre> with a <code class="language-html"> inside it. Running exactly that through RteHtmlParser on main produced no extra paragraphs at all, in either direction. That is what let me split the issue into two verdicts — "still happens" for the 2019 report and "not reproducible at this layer" for the 2023 comment — instead of filing one wrong verdict for both.

Without the attachments I would have taken the second reporter's "problem still exists in TYPO3 12.4.27" at face value and reported the whole issue as one confirmed defect. The image content is the evidence, and a text-only read of this issue is actively misleading rather than merely incomplete.

## Query

typo3_forge_lookup {issue: "88556"} — returned description, notes, relations, status, target version, typo3Version, phpVersion, but no attachments. Had to fall back to https://forge.typo3.org/issues/88556.json?include=journals,relations,attachments and then download the files from their content_url.

## Suggestion

Return the attachment list for a single-issue read: filename, content_type, filesize, and the content_url, so a caller can decide whether to fetch and read one. The URLs answer unauthenticated. Where a note body contains Redmine's !filename! inline-image syntax, it would help further to say so explicitly rather than leaving a bare filename in prose — a comment that is nothing but image references reads as an empty comment otherwise. No need to fetch or transcribe the images; naming them is enough, the caller can read them.
