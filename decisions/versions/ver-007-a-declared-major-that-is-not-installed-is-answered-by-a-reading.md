---
id: D-VER-007
title: 'A declared major that is not installed is answered by a reading'
date: 2026-08-18
status: open
coveredBy:
  - KnowledgeTest::aQuestionAboutADeclaredMajorReachesTheReadingThatSettlesIt
  - KnowledgeTest::theCrossMajorPageHandsOverTheReadingAndNoSignature
  - ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope
---

# D-VER-007 — A declared major that is not installed is answered by a reading

**Where a package declares a TYPO3 major that is not installed, this server says
how the API question is settled and reads no core source of its own.**

A repository declaring two majors has one of them on disk, and the shape of a
class on the other is the question that decides whether the code fatals there.
Nothing here says how that is settled, so a session that needs it invents a way.

## Evidence

- `feedback/2026-08-18-074124`. `t3g/blog` declares
  `^13.4.15 || ^14.3 || 15.*.*@dev` with 14.3.6 installed. The session wrote
  against `TcaSchemaFactory`, `TcaSchemaCapability::getSystemCapabilities()`,
  `LanguageAwareSchemaCapability::getTranslationSourceField()` and
  `RecordFactory::createResolvedRecordFromDatabaseRow()`, and settled the 13.4
  half by curling five files from raw.githubusercontent.com and then by building
  a second installation with `composer require typo3/cms-core:^13.4`.
- The changelog reached that question and cannot answer it. A core package
  carries every changelog down to 7.0 — `.checkouts/14.3` ships 55 version
  directories, `13.2/Feature-104002-SchemaAPI.rst` among them — so the entry the
  session got back was on disk and the downward direction was never the gap. A
  changelog records change events: it says the Schema API arrived before 13.4,
  and what its shape is there is a different kind of statement.
- The reading itself is one call per file where the branch is at hand. Re-run in
  `.checkouts/` on 2026-08-18: `getSystemCapabilities(): array` stands in
  `Schema/Capability/TcaSchemaCapability.php` over a `SYSTEM_CAPABILITIES` const
  on 13.4 and on 14.3, and `getTranslationSourceField(): ?FieldTypeInterface` in
  `Schema/Capability/LanguageAwareSchemaCapability.php` on both. The five curls
  bought the answer that the two branches agree.
- The same session filed the same boundary as a strength half an hour later
  (`feedback/2026-08-18-080743`). The `doesNotCover` entry *PHP source as code:
  a method signature, whether a class or member is @internal or public API* with
  its *instead: Read the class* "describes almost exactly what the session
  consisted of", and what it asks for is that nothing softens it: "a scope
  document that says plainly 'this is not mine' is worth more than a lookup that
  would have half-answered it, because the half-answer is the one that gets
  believed".
- A third report of that day is the session that made no calls at all, because a
  core checkout carrying `origin/13.4`, `origin/14.1` and `main` answered every
  cross-major source question at one `git show origin/13.4:<path>` per file
  (`feedback/2026-08-18-080710`). What it asks for is this: the invocation named
  in the answer, so a checkout beside the installation is a reason to call
  rather than a reason not to.
- `.checkouts/` is this repository's own — four branches, gitignored, kept by
  `bin/cli checkouts:update`. It is the reading this repository does for itself
  and ships to nobody.

## Decided

- No tool reads core source for a version. The `doesNotCover` entry on PHP
  source as code stands as written, and one session reporting it as a cost and
  as a strength is what that costs and what it buys, in one place.
- What is missing is the procedure, and it is written where the caller was: a
  `knowledge/documents/` page in the extension scope, on settling whether an API
  holds on a declared major that is not installed.
- Its boundary. It names the constraint to read, says what the changelog settles
  and what it does not, names the invocation that settles the shape, and says
  what a static compatibility argument owes where nothing can be run. It states
  no signature and no per-version identifier list: that is the refused half, and
  a bundled one is wrong from the next release on.
- The page is taken on rather than written in the judging run, because what it
  says about reading a TYPO3 branch comes from `.checkouts/` rather than from
  this repository — `documentation/records/judging.rst`.
- `R-DIS-016` is the same situation answered for statements, and this is the
  half it does not reach: the hint lookups already answer for every declared
  major, and no answer says how the source question is put.

## Assumed

- That a caller who needs the other major can obtain a checkout of it. Where it
  cannot, the procedure's honest end is a static argument declared as unproven,
  which is what `feedback/2026-08-18-081129` asks for from the execution side.
- That the situation is ordinary rather than rare. `R-DIS-016` models it and the
  feedback claims nearly every extension repository declares more than one
  major; nothing here has counted them.

## Wrong if

- A session follows the procedure and still cannot answer. That would mean the
  reading needs something this server has and withholds, and the refusal above
  is what to reopen.
- A feedback reports the page read as an offer to answer the question, so a
  caller stops at the document where the branch is what settles it. That is the
  half-answer `feedback/2026-08-18-080743` warns about, arriving as prose
  instead of as a lookup.
- The procedure is followed and the curl remains cheaper, because assembling the
  other branch costs more than five files do.

## Since then

Written on 2026-08-18 as
`extension/compatibility/a-declared-major-that-is-not-installed`, its
invocations run against this repository's own checkouts. The third **Wrong if**
is left standing rather than answered: a blobless single-branch clone of the
local mirror did not finish in two minutes here, so the claim that cloning is
the small step is unmade, and the page says two or three files are read as files
instead. A day later a dual-major audit asked the page's question of every
deprecation a sweep returned and never reached it: none of the three routes
covers a session that is reading rather than writing, so the first **Wrong if**
is not what happened — the procedure was not delivered rather than followed and
failed. `D-VER-009` carries that.
