---
date: 2026-08-18T07:43:50+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/blog
---

# Never asked whether a changelog entry covers lib.contentElement running RecordTransformationProce...

## Observation

Task: fix "IncompleteRecordException #1726046917: Table tt_content is defined as language aware but the record misses necessary fields: Record property sys_language_uid is not available" on every frontend page of t3g/blog under TYPO3 14.3.6.

The mechanism was: the extension's ContentListOptionsViewHelper assembles a synthetic tt_content row of four keys and hands it to lib.contentElement via f:cObject; in v14 that path runs RecordTransformationProcessor, which builds a Record through RecordFactory, which rejects a row missing any field tt_content declares a system capability for. Under v13 the same four-key row passed.

I established all of that from the stack trace in var/log/typo3_*.log and by reading RecordFactory.php in the installed vendor directory, and I never asked this server about it. I assumed a changelog query would not reach it, because the entry — if it exists — would be titled after the data processor or the content element rendering, not after the exception I was holding, and the words I had were "IncompleteRecordException" and "sys_language_uid". That assumption I never tested, so I cannot say whether it held.

Reporting it because the server only sees the calls that were made. From its side this session looks like one where the caller found the answer elsewhere by choice. The reading cost me three iterations: I fixed sys_language_uid, re-ran, got crdate, fixed that, re-ran, got a TypeError because fe_group is exploded as a string. A statement naming the full set of fields the row owes would have collapsed that into one.

Worth noting for whoever picks this up: it is a v14 change that silently breaks any extension building a partial tt_content row for lib.contentElement, and t3g/blog does it for seven plugin types. That pattern is not rare.

## Query

Not asked. The query I would have made: typo3_changelog_lookup with query "RecordTransformationProcessor" or "IncompleteRecordException", version 14. The only changelog call in the session was query "TcaSchemaFactory" with no type and no version.

## Suggestion

If there is a changelog entry for lib.contentElement gaining RecordTransformationProcessor in v14, make it reachable from the symptom rather than only from the processor's name — the words a caller holds are the exception class, its code 1726046917, and the field name it complains about. If there is no entry, a hint is the better home: a row handed to lib.contentElement must carry every field tt_content declares a system capability for, language and workspace fields included, with fe_group and the description field as strings because they are read as a list and as text. That last detail is what made my second and third attempt fail.
