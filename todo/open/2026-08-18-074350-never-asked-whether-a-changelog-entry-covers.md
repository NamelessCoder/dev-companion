# Never asked whether a changelog entry covers lib.contentElement running RecordTransformationProce...

**Serves:** feedback/2026-08-18-074350-never-asked-whether-a-changelog-entry-covers.md
**Priority:** normal

Judged on 2026-08-18 as step 1a, the knowledge is missing, and written up as
`D-KNW-099` — there is no changelog entry to reach, so nothing about
`typo3_changelog_lookup` is at fault. Establish against `.checkouts/14.3` and
`.checkouts/13.4` what a synthetic `tt_content` row handed to
`lib.contentElement` through `f:cObject` has to carry since v14: which fields
`RecordFactory` rejects a row for at `RecordFactory.php:241`, `:267` and `:289`,
which of them `tt_content` actually declares a capability for, and what each
value has to look like — the reported `fe_group` string is unverified. Write it
as a hint bound `since: 14`, with `IncompleteRecordException` and the codes
`1726046917`, `1726046918` and `1726046919` in `appliesTo`, since that is what a
caller holding the exception has. `page-content-element-rendering` in
`knowledge/hints/page-rendering.json` is where a `lib.contentElement` question
lands today and is the candidate home; `D-KNW-099` says what the neighbouring
hints keep. Archive the feedback in the commit that ships it.
