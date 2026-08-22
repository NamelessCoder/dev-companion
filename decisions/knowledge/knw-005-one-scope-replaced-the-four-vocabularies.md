---
id: D-KNW-005
title: One `Scope` replaced the four vocabularies
date: 2026-08-02
status: confirmed
---

# D-KNW-005 — One `Scope` replaced the four vocabularies

**`binding`, `provenance`, `audience` and the `outsideCore` boolean are one
backed enum, `Knowledge\Scope`, with the cases `core`, `project`, `extension`,
`any` and `uncertain`.**

Four fields asked which kind of work an answer is for and none of them could be
compared with another. `D-KNW-003` kept two of them apart deliberately and named
what would show that wrong: a value that reads naturally on both axes. Naming
the three audiences of `R-AUD-001` outright is that value.

## Evidence

- The four, as they stood. `binding` on 28 hint and intent entries, one value,
  `core`. `provenance` on the 16 covered topics, three values: four `core-only`,
  eight `transferable`, four `installation`. `audience` in five tool output
  schemas, three values, one of them spelled as a negation — `outside-core`.
  `outsideCore`, a required boolean beside it in the same five schemas, saying
  the same thing again with less room.
- `R-AUD-001` has named three audiences since 2026-07-29 and the code
  implemented two of them. An extension author and a site developer both read
  `outside-core`, so neither could be answered for, and the corpus had already
  worked around it in prose: `project-repository-layout` and
  `extension-repository-layout` are two hints, `core-tests` and
  `project-extension-tests` are two more, and what separates each pair is a
  sentence rather than a field.

## Decided

- The word is `scope`, not `audience`. `audience` stays the idea the repository
  is organised around — `requirements/audience/` and R-AUD-001 through R-AUD-006
  — and `scope` is what the code and the payloads say, because one word beats a
  second one that means the same thing.
- An enum rather than string constants, so a value that is not one of the five
  cannot be written, passed or returned. `Scope::from()` on the corpus is what
  turns a typo in a JSON file into a failure at load.
- `installation` is not a scope and is gone as a value. It said where an answer
  is read from, which is what `source` on the same entry already says, and
  holding a slot for it was what kept an installation-backed topic from saying
  who it is for. The four topics that had it are now `any`, and their `source`
  names the installation.
- `outsideCore` is removed from the five output schemas rather than kept beside
  the enum. It is `scope !== core`, every tool that carried it already carried
  the scope, and `D-SCO-006` predicted the rename in 2026-07-29. This is a
  breaking change to those schemas.
- `Knowledge\Scope` was the class holding the server-scope map, and that moved
  to `Knowledge\Coverage`. The name goes to the vocabulary because the
  vocabulary is what the rest of the server says; the map is read by one tool
  and one resource index.
- `any` and `uncertain` belong to one side each rather than to both. A statement
  can hold wherever TYPO3 is written and a path cannot, because a path is one
  piece of work; a path can be one nothing placed, and a statement nobody could
  place is one nobody should have written. `Scope::ofPaths()` and
  `Scope::ofKnowledge()` are the two sets.

## Assumed

- `project` and `extension` can be told apart from structure. `PROJECT_WORK` is
  the evidence — `config/sites/`, `config/system/`, `public/`, `var/`, `.ddev/`
  — read after the extension containers, so a sitepackage's own `Configuration/`
  is not mistaken for the site's. Where nothing structural says which, a
  Composer project falls to `project`, which is the repository the session is in
  rather than a package inside it.
- Answering `project` where the old code answered `outside-core` costs nothing,
  because both are outside the core and R-SCO-002 draws its line there. Eleven
  of the paths in `theSysextSignalAloneAnsweredEveryDecisionTheRecordedRunsMade`
  now answer `project` rather than `extension`, and that test was narrowed to
  the claim the recorded runs actually made.

## Wrong if

- A caller is handed extension advice for site-configuration work or the reverse
  — `config/sites/` answered as a package, or a path under `packages/` answered
  as the project around it. The two are one repository in most sessions, which
  is what made one value survive this long, and it is what will make a wrong
  split hard to notice.
- A fourth kind of work arrives that is none of the five — a distribution, or a
  TYPO3 fork maintained downstream. Then the enum is the thing that has to
  change rather than a string somewhere, which is the cost this entry accepted
  for the guarantee above.

## Confirmed on 2026-08-22

Five cases, and the case the second **Wrong if** names has arrived without
asking for a sixth. `skills/typo3-distribution-content/` is published and a
session has built a distribution extension carrying a site's content, which the
enum answers as `extension` because that is what it is by structure. No fork
maintained downstream has been reported.

The first **Wrong if** is unreported in the shape it names. Nothing in the
archive says a caller was handed extension advice for site-configuration work or
the reverse. What did arrive is the other failure — `feedback/2026-08-18-070358`
got `uncertain` for `.ddev/config.yaml`, `composer.json` and an extension key in
a standalone extension repository, and core checks behind it. That is the
signals failing to place the work rather than the vocabulary putting it in the
wrong case, and the answer said `uncertain` where it could not tell, which is
the case this entry added.

`installation` has stayed gone as a value and `outsideCore` has not come back to
any schema.
