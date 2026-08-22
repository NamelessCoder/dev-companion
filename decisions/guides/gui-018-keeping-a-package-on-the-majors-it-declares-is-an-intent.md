---
id: D-GUI-018
title: 'Keeping a package on the majors it declares is an intent'
date: 2026-08-21
status: open
coveredBy:
  - KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther
  - KnowledgeTest::anInstallationSayingWhichMajorItIsOnIsNotCompatibilityWork
  - KnowledgeTest::everyGuideAnIntentNamesIsADocument
  - ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout
---

# D-GUI-018 — Keeping a package on the majors it declares is an intent

**`typo3_task_guide` recognizes what a package owes the TYPO3 majors it declares
as work of its own, routes `typo3-extension-upgrade` and names
`extension/compatibility/running-on-a-declared-major-that-is-not-installed`.**
The workflow and both pages were already here and no task text reached either.

## Evidence

- Measured in this worktree on 2026-08-21, before the change. "Make the
  extension compatible with v14", "prove the package runs on TYPO3 13 and 14"
  and "does this still work on v13?" matched no intent at all, so the brief
  named no skill and no guide.
- `EXT-01` is the contract case written about this work — "our extension
  supports TYPO3 12 and 13, the next major is out and I want to add support for
  it without dropping 13" — and it matched no intent either. Of the cases
  written for the three audiences it was the only one the catalog reached on
  nothing, weak matches included.
- `typo3-extension-upgrade` owns the work, names the running page in its own
  step 3, and was reached by `deprecation` and `breaking` alone — the two
  intents whose subject is what the core did, not what the package now owes the
  range it declares.
- `installation-upgrade` is the nearest intent and is other work. Its needles
  are `upgrade`, `maintain`, `composer update` and `new major`, its condition
  says it holds for the installation rather than the code in it, and it routes
  no skill — so a compatibility task landing there would be recognized and still
  handed nowhere.
- The other neighbour is settled and stays settled. `D-GUI-012`'s entry of the
  same day put both `extension/compatibility/` pages in
  `typo3-extension-health`'s audit order, because the condition the feedback
  asked for — a declared major that is not installed — is the checkout's state
  and a skill already holds it.

## Decided

- **An intent rather than `installation-upgrade` widened.** That intent's
  checklist is the order an installation is carried up in — dump the database,
  run the wizards, flush the caches — and none of it is what a package owes a
  range it declares.
- **The subject is in the needle.** `package runs on` and `extension runs on`,
  with their tenses, rather than `runs on`: measured before it was written, a
  strong `runs on typo3` took "this site runs on TYPO3 12 and we need to be on
  13 before support ends", a text `installation-upgrade` matches on nothing, so
  the compatibility brief was the whole answer to a site upgrade rather than a
  second one beside it.
- **The ordinary words are `matchWeak` under the condition, not left out.**
  `compatible`, `runs on`, `run on` and `support for` are what "keep the change
  backwards compatible" and "add support for a second image in the teaser"
  carry. A conditional match is the right answer to both: the checklist arrives
  marked with what it holds under, and the skill and the guide do not.
- **`skillCore` and `guideCore` are empty.** A major the core declares is the
  core's own release process and not this work, and the core's side of a package
  breaking is `deprecation` and `breaking`, which already carry it.
- **The guide is the running page and not its sibling.** It is the page the
  reporting session in `D-GUI-012` improvised, and its second paragraph hands
  the reader on to `a-declared-major-that-is-not-installed` for the question it
  does not answer — so naming it reaches both, and naming the reading page would
  reach one.
- **`changesNothing` is not set.** This work produces the change; judging a
  package without changing it is `audit`, and a brief that changes nothing
  routes neither the skill nor the guide (`D-SKL-039`).
- **No `rulesQuery`.** The three documents a brief searches are the core's
  contribution process, and a package crossing a major has nothing to take from
  them.

## Assumed

- That a caller who means the package says so, in the subject or in the word
  compatibility. "Make it run on 14" names neither and is a conditional match,
  which is the cost the bullet above was chosen with rather than one nobody saw.
- That the guide arriving with the work is read where the same page listed at
  orientation was not. That is `D-GUI-012`'s own assumption and nothing here
  measures it either.

## Wrong if

- A brief about carrying an installation up a major names this guide. Then the
  needles reach the version a thing is on rather than the range a package
  declares, and the subject in the needle is not the separator it was measured
  to be.
- A session reports it described this work and was routed nowhere. Then the
  strong needles are too narrow, and what discriminates has to move out of the
  needle — the scope of the paths is where `installation-upgrade` would look.
- A session gets the running page and needed the sibling. Then the two pages are
  two pointers rather than one hand-off, and the intent owns a `guide` it cannot
  fill with one id.
