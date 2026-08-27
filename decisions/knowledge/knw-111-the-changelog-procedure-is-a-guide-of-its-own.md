---
id: D-KNW-111
title: The changelog procedure is a guide of its own
date: 2026-08-24
status: open
coveredBy:
  - KnowledgeTest::aChangelogQuestionIsToldWhichTypeTheChangeOwes
  - KnowledgeTest::aCommitMessageQueryIsAnsweredWithTheObligationAndNotOnlyThePage
  - KnowledgeTest::aQueryForTheChangelogObligationReachesTheSectionThatStatesIt
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - KnowledgeTest::theChangelogProcedureIsFoundUnderItsOwnName
  - KnowledgeTest::theMovesTheCommitRulesStopAreStillStated
---

# D-KNW-111 — The changelog procedure is a guide of its own

**The changelog rules move out of `core/contribution/commit-messages` into a
`core/contribution/changelog` document, because the guides list is read by its
names rather than searched.**

Two sessions assembled the conventions from the core checkout while the rules
sat in a page named for a different subject.

## Evidence

- [`feedback/2026-08-24-122249`](../../feedback/archive/2026-08-24-122249-nothing-answers-where-a-changelog-rst-goes-when.md)
  reviewed a Gerrit change whose substance was an `Important` entry for a
  backport. It read `Documentation/Changelog/Howto.rst` in two ranges and two
  neighbouring entries by hand, and called none of `typo3_task_guide`,
  `typo3_hint_lookup` or `typo3_rule_lookup`. What it did see was the `guides`
  list of `typo3_project_describe`, and it says why it opened nothing from it:
  "None is named for the changelog".
- The answer was here three times over, and each says where a backport goes: the
  `documentation-changelog` hint, the `changelog` intent of
  `knowledge/task-intents.json`, and `## Changelog Files` of
  `knowledge/documents/core/contribution/commit-messages.md`. So this is not
  step 1a — `bin/cli hints:probe` reaches the hint on the feedback's own
  question.
- The placement rule is correct. `Howto.rst` in `.checkouts/main`, read on
  2026-08-24, works it through by example: a change backported to the stable LTS
  goes into that LTS's `.x` directory on `main` and into the same directory on
  the branch, and one reaching two LTS lines is duplicated into both.
- [`feedback/2026-08-24-173211`](../../feedback/archive/2026-08-24-173211-the-guides-list-was-returned-and-never-pulled.md)
  is the second session, on another task shape — an old Forge issue worked off —
  and it spent six calls on the same conventions with the guides list in front
  of it. It asks for the same page.
- What neither corpus carries is the byte level: the spacing of the `include`
  directive, how long the title's fence has to be, which `.. index::` tags
  exist, and that `Index.rst` needs no entry because it globs.
- Neither report may be copied into `knowledge/` for it. The 15.0 entries of
  `.checkouts/main` open with both spellings of the directive in equal number,
  which is what one feedback observed and the other contradicts; and the fence
  the first one patched to the byte is demanded by nothing, since `Howto.rst`
  states no length and `Build/Scripts/validateRstFiles.php` asks only for
  "multiple === above and below".

## Decided

- The judgement is **step 2, delivery**, and it is **taken on**. The rule was
  here, it was right, and the one surface the session read did not name it.
- `## Changelog Files` becomes
  `knowledge/documents/core/contribution/changelog.md`, declaring what it is and
  when to reach for it as
  [`D-KNW-057`](knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  requires. The commit-message document keeps what a message owes and names the
  new page for the entry the message announces.
- The split of
  [`D-KNW-039`](knw-039-the-type-a-changelog-entry-owes-is-stated-in-prose.md)
  stands: the prose says which type a change owes and where the file goes, the
  hint carries the skeleton. This moves the prose half into its own page rather
  than duplicating either.
  [`D-KNW-061`](knw-061-the-manual-scaffold-is-a-document-and-the-hint-keeps-the-policy.md)
  is the same shape for an extension's manual.
- The byte-level conventions go to the hint, which is what a session already
  editing the file asks, and they are read from `.checkouts/` rather than from
  the reports.
- The priority is `normal`, set by two sessions from two task shapes.
- The sibling card is not taken over. What it reports is that a guide named in
  the list is not pulled at the point of need, which reaches past the changelog;
  only its content half is answered here.
- Nothing holds this yet. The test that follows the moved section declares the
  id in the commit that moves it.

## Assumed

- That a session skipping every lookup opens a document named for its subject.
  Both sessions read the list and named what was missing from it, which is not
  the same as either having opened one.

## Wrong if

- A session with `core/contribution/changelog` in its guides list still
  assembles the conventions from `Howto.rst` and the neighbouring entries. Then
  the name was not what kept them out, and the pull the sibling feedback asks
  for is the whole lever.
- The byte-level conventions differ between the covered branches. They are then
  hint statements carrying a binding rather than a section of the document.
- A changelog question answers worse after the split than before, because the
  section no longer sits in the page a commit-message query returns.
  `KnowledgeTest::aChangelogQuestionIsToldWhichTypeTheChangeOwes` is what would
  show it.

## Since then

The split was made on 2026-08-24 and the third **Wrong if** happened
immediately. Moved under headings named for what each states — "Which Change
Owes an Entry" — the page was unreachable for `changelog file`, the query that
used to return the section by its heading, and
`aChangelogQuestionIsToldWhichTypeTheChangeOwes` went red. The headings name
their subject as well as their claim now, and the test declares this entry
beside `D-KNW-039`.

The byte level was read in `.checkouts/` the same day, and it is the core's own
template rather than a convention to be inferred from neighbouring entries:
`Build/rstTemplates/rstTemplate<Type>.rst`, one per type, substituted by
`runTests.sh -s watchRst core interactive` with the issue, the title and a
`date +%s` timestamp. The four templates are byte-identical on `12.4`, `13.4`,
`14.3` and `main`, as is `Build/Scripts/validateRstFiles.php`, so the second
**Wrong if** did not happen and none of it carries a binding.

Both reports were wrong about the two facts they had guessed at, which is why
neither was copied. The include directive takes one or two spaces — the
validator's own regex is `\.\. {1,2}include::` — and the `15.0` directory of
`main` carried 17 files with one space against 18 with two, where one report
said every file used two. The title fence is not measured against the title: the
validator asks for two or more `=` above and below, the shipped templates carry
a 68-character row whatever the title is, and 12 of the 494 entries in the eight
newest directories on `main` are shorter than their own title and are merged and
rendered. The other report patched its fence to the byte on a rule nothing
states.

A third session is on record, and it was judged on 2026-08-25.
[`feedback/2026-08-24-163321`](../../feedback/2026-08-24-163321-the-repository-s-own-agents-md-routes-agents.md)
was filed at 16:33 and the split landed at 21:02, so it had no such page. It
built its `Important` file from a neighbouring entry in `14.3/`, naming the
`.. _important-<issue>-<timestamp>:` label as the part it would not have
guessed, and it wrote the fence at a length it corrected in a second pass — 72
against 71. That is the second independent session to spend a round on a length
nothing demands, which is what the hint now tells a caller not to do.

It also says why it never called `typo3_changelog_lookup`: the description read
as consuming changelogs rather than authoring one. The same commit answered
that, and the description now names the authoring direction and the
`core/contribution/changelog` documentId. The lookup was re-run on 2026-08-25
and returns the page.

The first **Wrong if** stays unanswered by all three: none of them had the guide
in its list.

### 2026-08-25 — the content half is re-run, and the third Wrong if is checked where the brief answers

**The second session's feedback was judged on 2026-08-25, and the content half
this entry took on is answered.**
[`feedback/2026-08-24-173211`](../../feedback/archive/2026-08-24-173211-the-guides-list-was-returned-and-never-pulled.md)
counted six calls to establish the conventions, and each of them now lands in
one.

`typo3_rule_lookup` with that session's own subject as the query — *changelog
entry conventions for a bugfix that changes rendered output* — returns one match
and it is `core/contribution/changelog`, with the `documentation-changelog` hint
named beside it. Its question was which type a bug fix that changes rendered
output owes, and the matched section states it: an `Important`, on the same
reading that a fix changing nothing an installation renders owes none. The
byte-level items it listed are in the hint, the anchor timestamp included, which
it said it had inferred from three neighbouring files.

Its include-directive claim is refuted where it was made. Read in
`.checkouts/main` on 2026-08-25, `validateRstFiles.php` matches
`#^\.\. {1,2}include:: /Includes\.rst\.txt#m`, so the one space it wrote
validated and the pass that rewrote the file to two bought nothing. That is the
same reading recorded above, made again from the checkout rather than carried
over.

**The brief answers a changelog question no worse after the split than before.**
That is the third **Wrong if**, checked where its test does not read.
`typo3_task_guide` is the other place such a question is answered, and
`TaskIntents::rules()` searches three documents at two sections per intent — so
the page this entry created is outside what a brief can return, whatever it
says. Measured in this worktree for a bugfix task naming a rendered change, the
two sections are `core/contribution/rules#Documentation` and
`core/contribution/commit-messages#Breaking Changes`. Against the corpus as it
stood at `b9e29643^` they are the same two, so the split took nothing out of the
brief: the changelog prose never won a slot there. Adding the page to
`RULE_DOCUMENTS` leaves them unchanged as well, which is the repair that
suggests itself and does nothing. What the brief carries instead is the first of
its `nextTools`, naming the page by `documentId` and what it holds.
