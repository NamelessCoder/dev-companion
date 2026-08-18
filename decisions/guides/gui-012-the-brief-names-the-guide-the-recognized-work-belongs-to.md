---
id: D-GUI-012
date: 2026-08-18
status: open
---

# D-GUI-012 — The brief names the guide the recognized work belongs to

**`typo3_task_guide` names the knowledge document the work it recognized belongs
to, beside the skill it already names.** The brief searches three
core-contribution documents, so the answer that knows what the task is hands a
session outside the core no guide at all.

## Evidence

- `feedback/2026-08-18-074226`. The session learned the guides exist from one
  place, the `guides` key of `typo3_project_describe`, while reading that answer
  for the version and the sites, and made no `typo3_rule_lookup` call in the
  whole session. It then verified a rendering change in a browser without
  `any/testing/browser-check` and added functional tests without
  `extension/testing/phpunit`.
- Measured in this worktree on 2026-08-18. `typo3_task_guide` for "add unit and
  functional tests for a ViewHelper in the blog extension", with an extension
  path, names the skill `typo3-extension-testing` and one document,
  `core/contribution/rules`. For "prove a rendering change in the browser after
  fixing a frontend crash" it names no document at all.
- `TaskIntents::RULE_DOCUMENTS` is why. A brief searches
  `core/contribution/rules`, `core/contribution/commit-messages` and
  `core/contribution/gerrit-workflow`, so every document under `any/`,
  `extension/` and `project/` is unreachable from the tool that recognized the
  task.
- The delivery already stands in the same answer. `Prose::sections()` names each
  page the excerpts were cut from and the `typo3_rule_lookup` call with
  `documentId` that reads it whole, which is `D-ANS-070`, and a named document
  arrives as a call rather than as an address, which is `D-ANS-061`.
- Two more feedback out of the same checkout report the shape from the skill
  side: `feedback/2026-08-18-074245` and `feedback/2026-08-18-081159`. Each is a
  pointer offered once, at orientation, and needed at a moment the session
  reached hours later.

## Decided

- **The ladder's step 2, delivery.** The corpus is here, the client rendered no
  resource list, and the only answer that names a guide is the call a session
  makes before it knows what the work is. What is missing is placement rather
  than a document.
- **The placement is `typo3_task_guide`.** It is the one answer that has already
  recognized what the work is, it names the skill that owns it, and the guide
  belongs on the same line.
- **Queued rather than made on the spot.** It moves
  `TaskIntents::RULE_DOCUMENTS` and the brief's `rules`, which is a declared
  output schema, and both are what `documentation/records/judging.rst` keeps off
  the spot.
- **The guide is named as the call that reads it**, the way the matched sections
  in the same answer already are. Not as a `typo3://guides` address, and not by
  inlining a page into a brief that already carries hints, rules, checks and a
  checklist.
- **What is named is the guide of the recognized work**, not every document
  whose words match the task text. A brief that searches the whole corpus is
  `typo3_rule_lookup` run a second time, and the intent is what tells the two
  apart.
- **Which of two shapes carries it is the todo's first step**: widening the
  searched documents per scope, or a guide named on the intent beside `skill`
  and `skillCore`. Both are read against what each intent owns today rather than
  chosen here.

## Assumed

- A guide named mid-task is read where the same list at orientation was not.
  Nothing here measures that, and the reporting session says it cannot tell
  whether either guide would have changed what it did.
- The work a brief recognizes is the work the session is in. That holds for a
  call made when the task changed shape and not for the one made at the start,
  which is the half `feedback/2026-08-18-081159` reports.

## Wrong if

- A brief names a guide that does not fit the work — testing recognized from a
  word rather than from the task — which would say the naming is a search and
  belongs in `typo3_rule_lookup`.
- A session reports that the guide arrived in the brief and was still not read,
  which would say the placement is not the lever and the page has to be handed
  over whole.
- A named guide repeats what the skill on the same line carries, which would say
  the two are one pointer rather than two.

## Covered by

- `R-GUI-013`

## Since then

### 2026-08-18 — the shape is the intent, measured against the other one

**The guide is named on the intent, `guide` and `guideCore` beside `skill` and
`skillCore`, read by `TaskIntents::guides()`.** Both shapes were run against
what the intents own today, and the widening is the one that loses:

- `tests` and `browser-tests` are the two intents whose `rulesQuery` reaches the
  pages the reporting session needed, so widening answers that feedback and
  little else. Eleven of the nineteen intents carry no `rulesQuery` at all, and
  `installation-operations` is one of them —
  `project/installation/booting-a-clone` is unreachable by any widening, and a
  query invented to reach it is the mapping written as a lexical match.
- Where the two shapes disagree they disagree about which is right, not about
  how much. "Write playwright tests for the editor journey" with a package path
  is placed as extension work, so widening per scope reaches
  `extension/testing/phpunit` and not `project/testing/playwright` — while the
  intent's own checklist says the suite belongs to what is deployed rather than
  to the package. The intent knows what the work is; the path knows where the
  file is, and for a browser suite those are two repositories.
- The naming is then deterministic rather than a coverage threshold away from
  silence, which is what a page an intent declares it owns has to be.

**It lands in a `guides` field of its own, not in `rules`**, which is what the
card said. A `rules` entry is a matched section — a body, a heading and a
coverage share — and a page that is named rather than searched has none of the
three; a bodyless record in a declared schema is a hole in the contract to save
a field. `guides` is `skills` for the other corpus, on the line under it, in the
shape `typo3_project_describe` already answers its own `guides` with
(`Schema::guideReference()`, now both tools').

**Four intents name a guide, and one direction is guarded.** `tests`,
`browser-tests`, `changelog` and `installation-operations`; `guideCore` is empty
throughout, because the core pages an intent would name are the three
contribution documents the rule sections in the same answer already name.
`KnowledgeTest` holds every named guide to being a document and to not being the
core's own, and there is no check in the other direction: a document no intent
names is still listed at orientation and served as its resource, where a
published skill nobody routes to is reachable by nobody.

**One of the Evidence bullets above is wrong.** `TaskIntents::RULE_DOCUMENTS` is
not why "prove a rendering change in the browser after fixing a frontend crash"
named no document: measured on the same day, that task matches no intent at all,
so no query is run over any corpus. `any/testing/browser-check` and
`core/testing/proving-a-rendering` are the pages for it and no intent recognized
the work either belongs to, which is recognition rather than placement. That
half was settled the same day:
[`D-GUI-014`](gui-014-looking-at-a-change-is-an-intent-of-its-own.md) is the
intent for looking, it names the browser page on both sides, and it leaves the
probe page to the two skills that grant it.
