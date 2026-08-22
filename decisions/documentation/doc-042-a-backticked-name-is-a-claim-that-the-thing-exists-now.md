---
id: D-DOC-042
date: 2026-08-22
status: open
restsOn: [D-DOC-040]
---

# D-DOC-042 — A backticked name is a claim that the thing exists now

**A name in backticks is one a reader can reach, a name the sentence is about is
written plainly, and a member of our own classes is held to that.**

Four guards said it of four kinds and none of the fifth, so 24 entries claimed
in the present tense that a member exists which does not.

## Evidence

- Of 1673 `Class::member` references the records make to classes below `src/`
  and `tests/`, 1648 resolved on 2026-08-22 and 25 did not.
  `CommandRunner::class` is `::class` and not one of them, and all 24 that
  remained sat in `decisions/` — `documentation/`, `requirements/`, `skills/`,
  `knowledge/` and `todo/` were clean. The same split the tool names had in
  `D-DOC-040`.
- Half were a claim about now whose member had moved or been renamed.
  Scope::INSTRUCTIONS_BUDGET is `Coverage::INSTRUCTIONS_BUDGET`, Scope::offered
  and Scope::read are `Coverage::offered()` and `Coverage::read()`,
  Typo3Cli::installedPhpBound is `Instance::installedPhpBound()`,
  TaskIntents::sections is `TaskIntents::rules()`,
  Schema::architectureHintRecord is `Schema::hintRecord()`, Installer::SKILLS is
  `Installer::skills()`.
- Half were the entry that removed the thing, saying so in as many words:
  "ResourceHandler::DOCUMENT_PREFIX is gone rather than kept as an alias",
  "Site::FRONT and Site::MAP_PAGE are gone", "Domains::JAVASCRIPT is deleted
  rather than kept for a hint that might want it". Those want no backticks and
  never did.
- Two were not our names at all. Scope::from is a backed enum's generated reader
  and is declared nowhere, and the Site::__construct of `D-KNW-097` is TYPO3's
  `Site` rather than the one below `src/Upkeep/`.
- One was stale in substance rather than in name. `D-ANS-045` recorded a closed
  list of thirteen directory names that `Extension::classes()` iterated, and
  that method reads every directory below `Classes/` through the finder today.
  Its **Since then** says so; renaming a constant would have hidden it.
- `D-SCO-007` was `open` while the call its statement is about was replaced
  twice. `D-SCO-008` opens by saying that call is gone, so the entry is revoked
  against it.

## Decided

- `RecordsTest::everyMemberTheRecordsNameInBackticksExists` over the six corpora
  where a name is a claim about today. Five were clean, so it holds a boundary
  for them and the corrections for `decisions/`.
- Magic methods and a backed enum's `from`, `tryFrom` and `cases` are language
  members and are skipped. They are the two the corpus proved necessary, not a
  list kept against future need.
- The members are read from the files rather than through reflection, because
  the records name a private member as readily as a public one.
- Nothing was decided from a likeness between two identifiers. Each of the 24
  was read against the class it names, which is what told a rename from an entry
  recording a removal, and what found the two that are not ours.
- Rejected: a sixth test. This is a fifth guard of one rule and the four before
  it are where they are, so a class named for the rule is where the next kind
  goes rather than a sixth place to look.

## Assumed

- That a class name matching one of ours is ours. `D-KNW-097` names TYPO3's
  `Site` and is only quiet because the member is `__construct`; a shared class
  named with an ordinary member would fail here and be right.
- That the corpus spells a member reference one way. What is matched is a fully
  backticked `Class::member`, so a member named in prose without backticks is
  neither read nor held — which is the escape and also the hole.

## Wrong if

- A sentence corrected here turns out to have been about the old name. Twelve
  were read as claims about now and twelve as accounts of a removal, and a
  thirteenth of either kind would show up as an entry that stopped making sense.
- The escape becomes the answer. Unbackticking a false claim leaves it false,
  which is why `D-ANS-045` got a **Since then** and not a plain spelling.
- A class this repository shares a name with grows a member the records name,
  and the guard reports a miss that is nobody's mistake.

## Covered by

- `RecordsTest::everyMemberTheRecordsNameInBackticksExists`
