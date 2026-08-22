---
id: D-DOC-016
title: 'An answer that reads no installation is derived and checked'
date: 2026-08-04
status: open
coveredBy:
  - CoreFixtureTest::theWrittenCheckoutIsReadAsOneAndSaysWhichTypo3ItIs
  - CoreFixtureTest::everyAnswerThatDoesNotMoveWithARootIsDerivedFromOne
  - ToolSurfaceTest::everyPageIsWhatTheRegistryDeclares
---

# D-DOC-016 — An answer that reads no installation is derived and checked

**A tool whose answers read nothing an installation contains has its
`## Answered` half derived by `bin/cli tools:index` and held by
`bin/cli tools:check`. The rest stay recorded.** It is derived against a core
checkout this repository writes, which declares its identity and holds no
content.

`D-DOC-006` made the whole of that half evidence because a filled answer needs
an installation. For part of the surface it does not, and what was being
recorded there was a derivation nobody checked.

## Evidence

- Measured on 2026-08-04 over the 43 calls `ToolCalls` drives that reach no
  host. Answered from `.checkouts/14.3` and from a root holding a
  `composer.json` declaring `"type": "typo3-cms-core"` and one `Typo3Version`
  class, eight tools came back byte-identical over their 20 calls and every
  other tool moved.
- The first pass compared against no installation at all, and only
  `typo3_commit_message_guide` held. Every difference was one of two values:
  `scope`, from `Instance::startedIn()`, and `targetVersion`, from the version
  read off the installation. Both are the root's identity rather than its
  content, which is what makes a root that declares only those two the right
  comparison and not a weaker one.
- `typo3_translation_domain_lookup` is the near miss. It is identical from a
  root declaring `14.3.6-dev` and different from one declaring `14.3.0`, because
  it prints the installation's exact version into its text — so a derived page
  would state a patch level nothing here has.
- Regenerating the eight pages moved six of them in the opening sentence alone.
  `typo3_hint_lookup` and `typo3_task_guide` moved further, and both were driven
  from the same two roots afterwards and came back identical: their recorded
  answers were from 2026-08-03 and `knowledge/` had moved since. That is
  `D-DOC-006`'s second **Wrong if** standing in the corpus rather than as a
  worry.
- What the option not taken costs, read off the pages as they stand:
  `changelog: hit` answers `"ext_tables.php in extensions"` from the checkout
  and `"ext_tables.php in the fixture extension"` from a fixture, and
  `extension` answers `"TYPO3 CMS Backend"` against
  `"The fixture installation's backend package."`

## Decided

- **The line runs between pages, never through one.** A page is wholly derived
  or wholly recorded, and its opening sentence says which. That is what
  `D-DOC-007` drew between the two halves of a page, kept: a section split into
  a checked part and an unchecked one is the shape both entries exist to
  prevent.
- **The set is measured, not declared.** `ToolCalls::derived()` names it and
  `CoreFixtureTest` holds it from both ends: a tool in it whose answers move is
  a checked page asserting one root's content, and a tool outside it whose
  answers do not move is a recording nobody needs.
- **`CoreFixture` holds nothing but the identity** — three files, no packages,
  no console, no changelog. Content in it would reach a derived answer, and a
  page claiming to be checked would then show this repository's fixture where a
  reader expects TYPO3.
- Rejected: the fixture as `tools:record`'s primary root, which is the answer
  this card was put back with on 2026-08-04. It buys the same check for the
  whole surface and pays with every real answer on it — the two calls above are
  what that reads like, and `D-DOC-012`'s first **Wrong if** is the entry that
  already names the cost. Put back to the maintainer with the measurement, the
  answer was to check what is derivable and keep the rest recorded.
- Rejected: reading the set off `answersFrom()`. `typo3_test_run_guide` and
  `typo3_script_lookup` declare `knowledge` alone and still moved with the root,
  because `Scope` asks the installation which repository the session is in. A
  declaration about where an answer comes from is not a statement about what
  moves it.
- The tools that reach a host stay recorded and `CoreFixtureTest` does not drive
  them. `D-DOC-008` is why they are in the table at all, and driving one here
  would put this suite's requests on somebody else's service twice per root.
- A tool answering from the installation is outside the question rather than
  outside the set: neither test root has a console, so the two agree for a
  reason that says nothing about a caller who has one.

## Assumed

- That kind and major exhaust what a caller's root contributes to these eight
  answers. It was measured on one day against one checkout, and the variant root
  in the test is what keeps measuring it.
- That a derived page is read as the answer a caller gets rather than as an
  example. Nothing measures that, and it is the same assumption `D-DOC-006`
  makes about a recorded one.

## Wrong if

- A derived tool starts reading the installation and the test stays green,
  because the variant root has nothing of the kind it reads. It carries a
  package, a stylesheet and a changelog for exactly that reason, and a new kind
  of read needs a new thing in it before the measurement means anything again.
- A caller on another major takes a derived page for their answer. Each one
  states the version it was derived at in its opening sentence and nothing else
  says it.
- `bin/cli tools:check` grows into what `tools:record` is. It writes a fixture
  and makes 20 calls today, none of which reaches a host, boots a container or
  needs `.checkouts/` — the properties that let it run in CI at all.
