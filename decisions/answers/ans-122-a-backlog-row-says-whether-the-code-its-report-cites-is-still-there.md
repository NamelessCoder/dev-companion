---
id: D-ANS-122
title: 'A backlog row says whether the code its report cites is still there'
date: 2026-08-27
status: open
---

# D-ANS-122 — A backlog row says whether the code its report cites is still there

**`typo3_forge_lookup` names the classes, methods and files an issue's own text
cites and says which of them the installed packages still ship.**

A stale issue's status is untouched by definition, so the tracker cannot say
that a 2015 report is about code that is gone. Three of the five candidates one
session read in full were dead in exactly that way, and it found out by reading
the checkout.

## Evidence

- `feedback/2026-08-26-223257` counted the cost. Roughly 13 round trips of code
  reading rejected three candidates against roughly 5 that confirmed the one it
  patched, and of the session's ~85 calls 12 were this server and ~72 were Bash
  reading the checkout.
- The three it rejected are three different verdicts, and none of them is on the
  tracker: #71566 names `is_callable()` in an `ObjectAccess` that has since been
  rewritten onto Symfony PropertyAccess, #78546 asks for something
  `ClassSchema::reflectMethods()` cannot carry, and #78607 is already fixed
  because `Result::forProperty()` is typed `?string` today.
- Re-run on 2026-08-27 through `ForgeLookup::answer()`: issues 78607 and 71566
  both answer `answered` and their payload carries `id`, `subject`, `status`,
  `tracker`, `priority`, `assignedTo`, `targetVersion`, `typo3Version`,
  `phpVersion`, `createdOn`, `updatedOn`, `url`, `description`, `relations`,
  `attachments`, `reviews`, `noteCount`, `botNoteCount` and `notes`. Not one of
  those is about the code the report is about, so the feedback is not answered
  by the tool as it stands.
- `typo3Version` is the nearest field and says the opposite of what a triage
  needs. #78607 answers "6.2" and #71566 "7", which the answer itself calls what
  the reporter had rather than what it still reproduces on.
- The advice was already there and the reading was still by hand.
  `ForgeLookup::workflow()` has printed five readings under a page of `stale`
  and `oldest` since `e12ec2c9` on 2026-08-25 13:37, a day before this session,
  and two of them — where the symptom appears, how far the mechanism reaches —
  are the reading those 13 round trips were. So what is missing is performing it
  and not prescribing it.
- `bin/cli hints:probe "does the class this issue names still exist in the installed core"`
  matches nothing out of 109 candidate hints. This is not step 1a: no statement
  in the corpus would answer it and none should, because the answer is about one
  caller's own tree.
- The same session named the shape, as a strength rather than as a request.
  `feedback/2026-08-26-223414` credits the inline `reviews` field with deciding
  four candidates without a Gerrit call, and asks for this verdict in that shape
  — a small field on the row a sweep reads without a second call.
- The source is precedented. `Source::Packages` is the files the installed
  packages ship, read rather than executed, and `typo3_changelog_lookup` already
  declares `[Packages, Network]`, so a tracker answer that also reads the tree
  is not a new kind of tool.
- The page was read whole on 2026-08-27. `open="stale"`, `tracker="Bug"`,
  `updatedBefore="2020-01-01"`, `limit=25` answered 25 of 71, filed between 2010
  and 2018, and every description and every comment of those 25 was read.
  Thirteen notes is the most any of them carries, so the fifteen `Forge` returns
  never cut one.
- Fourteen of the 25 name a core class, a method or a core file somewhere in
  their text. The other eleven name a TCA key, a TypoScript path or a table
  column instead, and two of those carry no description at all.
- A `\TYPO3\CMS\…` namespace is in 8 of them: 5 inside a `<pre>` block, 4 in
  prose, written with the leading backslash, without it, and doubled inside a
  PHP string. `Class::method` is in 3 and only #81102 carries it in the subject.
  `Class->method()` is in one, `Class::class` in one, `Class::CONSTANT` in one.
- A bare class name is in 10, and in 5 of those it is the only handle the report
  gives: `ObjectStorage` (#79571), `RequestBuilder` (#82033), `ReferenceIndex`
  (#24299), `DatabaseConnection` (#72962), and `BackendUtility::setUpdateSignal`
  (#81102), which is a subject line and no namespace.
- A core file path is in one. #84998 pastes a stack trace naming
  `…/Classes/DataHandling/Localization/DataMapProcessor.php` at line 631. Five
  further issues name a `.php` file and not one of the five is a core file.
- A method is as often an English word as a call. "the readMM-method" (#75145),
  "Triggering setUpdateSignal" (#81102) and `_loadDefaultValues_` (#82033) carry
  neither parentheses nor a class. Redmine's own markup is glued to the name in
  `@PropertyMappingConfiguration@`, `_TcaColumnsOverrides_` and
  `_\TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRecordTypeValue_`.
- Placed against `.checkouts/main` and `.checkouts/12.4` by deriving the
  extension key from the namespace's second segment, all 16 core namespaces
  resolved to a file, and the two placeholder ones — `\Vendor\Ext\…` in #78546
  and `MyComp\SomeModule\…` in #63810 — matched no package at all.
- The verdicts are real and they move with the version.
  `TypoScriptFrontendController` (#63810) is gone on `main` and there on `12.4`,
  `DatabaseConnection` (#63810, #72962) is gone on both, and
  `ExtensionManagementUtility::makeCategorizable` (#61923) is gone on both while
  the class it sat on stands.
- A bare name matched by file basename lands on the wrong package. `Example`,
  which #78714 names as `Domain\Model\Example.php` of an example extension,
  matches two core test fixtures on `main`; `PropertyMappingConfiguration`
  matches `form` and `extbase`, and `ActionController` matches `extbase` and
  `extensionmanager`.
- Redmine's index answer carries `description` and no journal. A field on an
  enumerated row therefore costs no second read, and it sees what 9 of the 25
  cite in their subject or description — one more, #63810, cites only in a
  comment.

## Decided

- It is built. The measure is `D-FBK-027`'s: the question costs its caller a
  round trip per cited symbol, it is asked once per candidate on a page of
  candidates, and both counts are in the report.
- It is a field on `typo3_forge_lookup` and not a tool of its own. The question
  is never asked apart from an issue, a second tool would be a second call on
  every row, and the six verbs have nothing this would be the seventh of.
- The field sits on a row of an enumeration as well as on an issue read whole.
  The sweep is where it pays, which is what `reviews` already demonstrates.
- It reads `Source::Packages` and nothing else — no boot, no console, no
  `Typo3Cli`. `.checkouts/` is this repository's own evidence and never a
  caller's tree, so it is not read here either.
- What it says is where a symbol stands, not whether the defect reproduces.
  Ranking candidates is what the report asked for, and it is what an extraction
  out of ten-year-old prose can carry.
- A name it cannot place is reported as unplaced and never as gone. A wrong
  "gone" discards a valid candidate unread, which costs more than the hand
  reading it replaces, and it is the one failure this may not have.
- A bare class name is extracted and placed too. The namespace alone answers 8
  of the 25 and would report "no code cited" for the 5 whose only handle is a
  bare name, which is a wrong answer in the shape of an empty one.
- A bare name is looked up by file basename below each package's `Classes/`, and
  one matching two packages names both. Three of the ten read match two, and
  picking one of them is where a right-looking verdict lands on the wrong class.
- A bare name no installed package ships is reported as not shipped rather than
  as gone. That is true of `DatabaseConnection` (#72962), which core removed,
  and of a name belonging to an extension the caller never installed, and
  nothing in the text tells the two apart.

## Assumed

- One session, which reported the cost twice — once as the gap and once as the
  shape to build it in. The corpus holds no second triage and a session files
  nothing unprompted, so that is one report rather than a rate.
- That a name a report gives bare means the class an installed package ships
  under it. The page read on 2026-08-27 holds the counter-case already:
  `Example` belongs to an example extension and matches two core test fixtures.
- That a caller triaging the core backlog is standing in a tree that holds the
  core — as a checkout for a contributor, under `vendor/` for everybody else.
  Both are `Source::Packages`.

## Wrong if

- A session reports discarding a candidate on a "gone" that turns out wrong,
  which would say the extraction claims more than it can place.
- A page of stale Bugs answers "nothing cited" for more than the eleven rows of
  25 the reading counted, which would say the extraction is narrower than the
  forms those reports are written in.
- A row places a bare name on a package the report was never about, which the
  reading found once before the field exists.
- A session reads the field and goes to the checkout for the same candidates
  anyway, which would say what decides a candidate is not where its symbols are.
