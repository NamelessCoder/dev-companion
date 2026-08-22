---
id: D-ANS-039
title: 'The Extbase fork is delivered by the content-element intent'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aContentElementTaskIsOfferedTheExtbaseForkWithoutNamingIt
---

# D-ANS-039 — The Extbase fork is delivered by the content-element intent

**The `content-element` intent carries the Extbase-or-not fork, worded as
whether a controller has to answer the request rather than as a choice between
two categories.**

It is worded that way because on the covered versions a plugin is a content
element: one selector holds both, and what differs is what renders inside the
element. That is the delivery step 2 of
[`D-ANS-027`](ans-027-the-extbase-fork-is-placed-where-a-caller-who-has-not-chosen-passes.md)
queued, and what
[`R-ANS-016`](../../requirements/answers/ans-016-a-content-element-task-is-offered-the-extbase-fork.md)
demands.

## Evidence

- What the fork actually asks was read in `.checkouts/`, and it is not two
  categories of thing. On 14.3 and on `main`,
  `ExtensionUtility::registerPlugin()` hands
  `ExtensionManagementUtility::addPlugin()` a `SelectItem` that is appended to
  `$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items']` — the
  same column `addRecordType()` writes for any other element. So one selector
  holds both and the editor picks from one list.
- What differs is what renders inside it. `configurePlugin()` generates
  `tt_content.<signature> =< lib.contentElement` with `templateName = Generic`
  and `20 = EXTBASEPLUGIN` in 14.3, 13.4, 12.4 and `main` alike. An element
  fills that slot with its own rendering and its data processors; a plugin fills
  it with a controller dispatch. That is the fork, stated as what a caller
  chooses between rather than as a name for two kinds of registration.
- The category wording would have been wrong on the newest covered majors and
  ambiguous on the oldest. 12.4 defaults `configurePlugin()` to `list_type` and
  writes `tt_content.list.20.<signature>`; 13.4 defaults to `CType` and warns on
  `list_type`; 14.3 and `main` throw on anything but `CType`. Only the pre-13
  form is a category of its own, and it is the one going away.
- The probe contrast still holds.
  `bin/cli hints:probe "new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering"`
  reaches `content-elements`, `content-element-shape` and `tca-formengine` on
  2026-08-03, and neither `extbase` nor `frontend-records`.
- `skills/base.md` orders `typo3_task_guide` at step 3 and `typo3_hint_lookup`
  at step 4, so the intent's checklist reaches a task before the hints do and
  before the checkout is read at all. It is matched on the words of the task —
  `content element`, `CType`, `tt_content` — which is what the reporting session
  described its work as.
- The intent already carried the wrong categories in the two places this
  feedback is about: its condition read "rather than the page or the plugin
  around it", and its `typo3_extension_describe` line described that answer as
  the templates elements render through.
- The route to the two hints is in the checklist item rather than in the
  intent's `tools` list, because `TaskGuide::nextTools()` keeps one entry per
  tool name and `content-element` is not the first matching intent to name
  `typo3_hint_lookup`. On this task text the surviving entry is the extension
  testing one.

## Decided

- The `content-element` checklist gains two items, placed after the editor
  workflow and before the registration: the fork itself, and what the extension
  already does as evidence about it. A checklist is where a decision that is
  still free belongs, and the intent is the one artifact reached by a task
  describing its work rather than naming a subject.
- Not the `content-elements` hint. That hint answers how a record type is
  registered, which is a question asked once the shape is chosen, and a fork
  filed there is the second **Wrong if** of `D-ANS-027` waiting to happen — read
  as a rule about plugins rather than about the element being built.
- Not the skill's architecture section.
  `skills/typo3-content-element-development` opens by keeping itself to routing
  and design method and sending versioned TYPO3 facts to the tools, and "a
  plugin is a `CType` like any other since v14" is exactly such a fact. It is
  also installed into other projects, where no release of this server corrects
  it.
- The condition is reworded with it. An intent conditioned "rather than the
  plugin around it" excludes the caller whose answer is a plugin, which is the
  one this fork exists for.
- The `typo3_extension_describe` line now says what that answer is evidence
  about — the architecture the extension already has — with where the templates
  are kept as the second half rather than the whole. That is what the feedback
  needed and did not name.

## Assumed

- That a task guide reaching a session is a task guide read. Nothing here can
  see whether a checklist item was acted on, and the failure this answers is a
  session that never considered an option rather than one that considered and
  dismissed it.
- That naming the two hint ids in the checklist text is worth as much as a
  `tools` entry. Both are prose in the same answer; only the entry is deduped
  away.

## Wrong if

- A report arrives from a content-element session that read the fork and
  answered it by category anyway — "this is an element, not a plugin". The
  wording would then still be carrying the two categories the checkouts say are
  one, and the sentence rather than the placement is what is wrong.
- A session builds a content element against an extension whose scope answer
  said `kind: plugin` and never weighs it. That is step 4 of the ladder and a
  rewrite: delivered and not taken.
- A task guide brief comes back without these items because the caller called
  `typo3_hint_lookup` alone. The intent would then be the wrong owner for
  anything a session has to see, and the hint or the skill is where it belongs.
- The checklist grows long enough that a fork placed sixth in it is read as
  procedure. Seven items is what a content-element task now carries.
