---
id: D-KNW-023
title: 'Which page may hold a record is a subject this server owns'
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::thePlacementAnswerSaysWhichPageMayHoldTheRecord
---

# D-KNW-023 — Which page may hold a record is a subject this server owns

**Where a record is allowed to live is inside this server's boundary and missing
from it, so the feedback is trimmed to that half and queued.**

The corpus says where a new record is *positioned* on a page. Nothing says which
page may *hold* it, which is the question a seeding session asks first and the
one this one guessed at.

## Evidence

- The positioning half is answered, in the words the feedback's own query is
  asked in. Called over stdio with that query as `task` and
  `targetVersion: "14.3"`, `typo3_hint_lookup` returns `datahandler-persistence`
  and `frontend-records`. The first carries "A new record is placed at the TOP
  of its page: the pid field is the positioning pid" and the negative form
  `-<uid>` for placing records in order.
- That answers the sorting half too. A seeding run positions with the pid rather
  than writing `sorting`, so the values this session guessed at are ones it
  should not have set.
- Those statements are older than the session that missed them. They landed on
  2026-07-29 in `2d0c533`, and the feedback is dated 2026-08-01. This is not a
  version of the server that no longer exists.
- The other half is in nothing here. "sysfolder", "doktype", "storage folder"
  and `storagePid` occur below `knowledge/` in one sentence, on the Fluid hint
  `frontend-page-rendering`, and from the opposite angle: `excludeDoktypes`
  keeping storage folders *out* of a menu.
- That sentence is also unreachable from this task.
  `bin/cli hints:probe "doktype 254 sysfolder storagePid page tree"` matches
  nothing at all: the query is classified `php`, and the one place the words
  occur is Fluid.
- The feedback's claim about TYPO3 holds, and the mechanism is one method. On
  `.checkouts/14.3`, `DataHandler::isTableAllowedForThisPage()` decides an
  insert on two things — the TCA root-level capability where the pid is 0, and
  `PageDoktypeRegistry::isRecordTypeAllowedForDoktype()` against the target
  page's doktype everywhere else.
- The storage folder is the doktype that allows any table.
  `Configuration/TCA/ pages.php` gives `DOKTYPE_SYSFOLDER` an
  `allowedRecordTypes` of `['*']`, above the comment "a general purpose storage
  folder for whatever you like. In CMS context it's NOT a viewable page."
- A standard page is the other side of it. `DOKTYPE_DEFAULT` declares no
  `allowedRecordTypes`, so `getAllowedTypesForDoktype()` falls back to `pages`,
  `sys_category`, `sys_file_reference` and `sys_file_collection`. A table of
  one's own is refused there and allowed in a folder, which makes the folder an
  enforced rule rather than an editorial habit.
- An admin does not get past it either. `hasPageContextPermission()` returns
  early for an admin and for `bypassAccessCheckForRecords`, and
  `hasPermissionToInsert()` calls `isTableAllowedForThisPage()` after it, so the
  doktype is checked on the run a seeding script makes.
- Where that list is declared moved between majors. On 12.4 and 13.4 it is
  hardcoded in `PageDoktypeRegistry` as `allowedTables => '*'`; on 14.3 it is
  TCA, and both `add()` and `addAllowedRecordTypes()` are deprecated for removal
  in v15. So the statement needs a range read across three checkouts, and this
  run may not write it.

## Decided

- Step 1a of the ladder on the "which page may hold this record" half, and
  queued rather than closed on the spot. What lands is a corpus statement about
  TYPO3, read across three majors.
- The feedback is trimmed rather than archived. Its pid and sorting half is
  answered, and the todo is what is left.
- Not step 2 and not step 3 for the answered half. That this session called
  nothing until the user demanded it is
  `feedback/2026-08-01-003356-did-not-consult-the-mcp-knowledge-server-or.md`,
  judged on its own card; walking it again here gives one gap a second entry.
- The DataHandler-instead-of-SQL and inline-relation halves of the same debrief
  are
  `feedback/2026-08-01-003216-lacked-datahandler-knowledge-and-worked-around.md`
  and
  [`D-KNW-018`](knw-018-what-a-datamap-does-to-a-relation-field-is-a-subject-this-server-owns.md).
  This entry adds only where a record may be put.

## Assumed

- The statement belongs on `datahandler-persistence`, beside the positioning
  pid. The question is asked while writing records, not while declaring the TCA
  that describes them.
- The reading side is a different question and stays where it is.
  `frontend-records` already names `pidInList`, and what an Extbase
  `persistence.storagePid` does is asked from the Extbase hint.

## Wrong if

- The restriction is not on the path a seeding datamap takes. A console run
  without a real backend user is refused before it, or reaches the insert by
  another route, and the statement then describes a check the session never met.
- The three majors differ in more than where the allowed list is declared. The
  hint then gains a statement per major where the todo planned one with a range.

## Confirmed on 2026-08-03

Both **Wrong if** were read and neither holds. The check is on the path a
seeding datamap takes: `process_datamap()` calls `hasPermissionToInsert()` on
`$theRealPid` — the pid after a negative positioning value has been resolved —
on 13.4, 14.3 and `main`, and 12.4 calls `checkRecordInsertAccess()` in the same
place. A refusal is a `log()` entry followed by `continue`, so the call returns
without an error and the record is absent, which is the shape the feedback
reported.

The three majors also differ in nothing but where the allowed list is declared.
A folder allows every table and a standard page allows `pages`, `sys_category`,
`sys_file_reference`, `sys_file_collection` plus every table declaring
`ctrl.security.ignorePageTypeRestriction` on 12.4, 13.4, 14.3 and `main` alike;
`pid = 0` is decided by `ctrl.rootLevel` with admin or
`ctrl.security.ignoreRootLevelRestriction` on top, on all four. Only the
declaration site moved, so one statement carries `since: 14` for TCA
`allowedRecordTypes` and one carries `until: 13` for `PageDoktypeRegistry`. On
`main` the registry's `add()` and `addAllowedRecordTypes()` are gone, which is
what that boundary predicted.

What the entry assumed about where the statement lands no longer describes the
corpus. `datahandler-persistence` was split by `D-KNW-030`, and the positioning
pid now sits on `datahandler-placement` — which is where the statement went, the
hint being widened from ordering to which page may hold the record at all
(`R-KNW-058`).
