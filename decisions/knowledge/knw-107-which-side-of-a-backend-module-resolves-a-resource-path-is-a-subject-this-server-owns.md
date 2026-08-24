---
id: D-KNW-107
title: Which side of a backend module resolves a resource path is a subject this server owns
date: 2026-08-24
status: confirmed
coveredBy:
  - HintsTest::eitherHalfOfABackendModuleReachesWhichSideResolvesAResource
  - HintsTest::whatTheClientMayCarryIsSaidApartFromWhatItMayDecide
---

# D-KNW-107 — Which side of a backend module resolves a resource path is a subject this server owns

**Which side of a backend module resolves a resource path the controller
validates anyway is inside this server's boundary and missing from it.**

The corpus answers how the TypeScript is built and where its unit tests go, and
says nothing about the PHP the module talks to. A session holding both files is
handed the asset pipeline.

## Evidence

- Re-run on 2026-08-24 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own two paths reaches `backend-typescript`,
  `javascript-unit-tests`, `system-extension-boundaries`,
  `routing-request-handling` and five more — the pipeline, the test layer and
  the hints every core file matches. The same probe on the words the session
  needed, "what backend TypeScript may hold and what belongs in PHP", reaches
  `icon-usage` and `console-commands`.
- The vocabulary is absent. A search of `knowledge/` and `skills/` for
  `typescript` reaches `backend-typescript`, `backend-ui`, three hint files that
  mention it in passing and two extension skill references about an extension's
  own build. None of them names a controller.
- The two hints beside the gap are each about something else.
  `backend-typescript` answers where the source lives, what builds it, what
  eslint decides and where a unit test goes; `backend-ui` answers what a custom
  element renders.
- Verified in `.checkouts/main` on 2026-08-24. The worked example the feedback
  names holds: `FormManagerController::createAction()` takes `$templatePath`,
  `isValidTemplatePath()` matches it against the prototype's `newFormTemplates`,
  and line 150 throws `FormException` 1329233410 where it does not. So the
  server resolves and validates that path on every request regardless of what
  the client sent.
- The feedback's own rule is contradicted by the same subsystem, in the same
  checkout. `form-manager.ts:117` uses each `newFormTemplates` path as a select
  option's `value`, and `create-form-submission-service.ts:31` submits it back
  as `templatePath`. Backend TypeScript carrying an `EXT:` path is what the core
  does today, so "it must not hold one" is not the statement.
- One session, from one patch review. `bin/cli feedback:list` on 2026-08-24
  reports 39 open feedback in four directories, and no other one names a
  question about which layer owns a decision.
- The precedent is `D-KNW-070`: a fact that lives on the seam between backend
  JavaScript and the PHP behind it earned a hint of its own, and the enumeration
  under it was extended once a second session met the same seam.

## Decided

- Taken on, at step 1a of the ladder: the answer is nowhere here, and what fills
  it is a statement rather than a tool. The caller reads no extra source and the
  round trips stay one, which is what `D-FBK-027` asks of anything smaller than
  a tool.
- A hint of its own rather than sentences on `backend-typescript`. One hint is
  one question (`D-KNW-030`): that one answers what builds the asset, this one
  what the asset may decide, and they are reached from different paths — this
  one has to match a `Classes/` path as well, or it never fires for the half of
  the task that is PHP.
- The feedback's suggestion is evidence, not copy. Its rule — backend TypeScript
  must not hold `EXT:` resource paths — is what the reading has to correct: a
  value the server handed the client and validates on return is carried, and a
  value the client *derives* is the one resolved on the side that validates it.
  A suffix match like `endsWith('BlankForm.yaml')` is the second kind, and a
  path delivered in the module's initial data is the first.
- The priority is `normal` rather than `low`. One session reported it, and what
  it counted is three complete reworks of a patch, each turned by the user
  asking the same question again rather than by any lookup.
- What the hint says about TYPO3 waits for the reading, which is the todo's
  first step: whether this holds beyond `ext:form` is what decides between a
  boundary rule and a fact about one wizard.

## Assumed

- That the boundary generalises. The session read one subsystem, and a rule
  written from one example is a form-manager fact wearing a rule's clothes.
- That a path-matched hint arrives at all. The reporting session called
  `typo3_hint_lookup` once, by id, so `appliesTo` is what decides whether this
  ever reaches a session holding the two files.

## Wrong if

- The reading finds no second subsystem where the client submits an intent and
  the controller resolves it. Then it belongs to `form-framework` as a fact
  about that wizard, and this entry is why it was looked for.
- The core moves the other way — a patch lands that has the client resolve a
  resource the controller then trusts. Then this is a preference this repository
  holds and the core does not, which is not a subject this server owns.
- A session reports the hint arriving and putting the resolution in TypeScript
  anyway. Then the gap was wording rather than knowledge.

## Confirmed on 2026-08-24

The reading the judgement left open was carried out against all four covered
checkouts, and the boundary generalises. The first **Wrong if** was what it
looked for and it does not hold. Two subsystems besides `ext:form` submit an
intent and resolve the resource behind it on the server, and both were read on
12.4, 13.4, 14.3 and `main`. `<typo3-backend-icon identifier="actions-close">`
names an icon and never a file, `IconController::getIcon()` resolving the
identifier through `IconFactory`, and the client's own fallback for one that
does not resolve is another identifier, `default-not-found`. The element browser
is opened with a `mode` and `ElementBrowserRegistry::getElementBrowser()`
resolves the class, throwing exception 1647241086 where nothing is registered
under it. A third is the install tool, whose upgrade wizards submit
`install[identifier]` and are resolved by the service behind it; that one is
named here rather than in the hint, because it was read on `main` alone and the
class holding it is not where 14.3 has it.

The second **Wrong if** is settled the other way round from how it was written.
It asked whether the core would move towards the client resolving a resource;
what the checkouts show is the core moving away from it, and inside this very
subsystem. `form-manager/view-model.ts:200` sets the blank mode's `templatePath`
to a literal `EXT:form/…/NewForms/BlankForm.yaml` on 12.4 and 13.4, and it is
the only literal `EXT:` resource path in the whole backend TypeScript tree on
either. On 14.3 and `main` it is gone: the wizard rewrite dropped the constant
rather than moving it into the payload the module is handed, and the two `EXT:`
occurrences left in the tree are a docblock and an `LLL:` label key. That is the
statement's boundary and both sides of it were read.

So the feedback's own rule is corrected rather than copied, which is what
**Decided** asked the reading to settle. "Backend TypeScript must not hold an
`EXT:` path" is contradicted by `form-manager.ts:117`, whose select carries
every `newFormTemplates` path the server put in the module's initial data, and
`create-form-submission-service.ts:31` submits one back. Carrying a value the
server gave it is what the core does; working one out is what the core removed.
`FormManagerController::createAction()` matching the submitted `templatePath`
against that same configuration is why the client repeating the decision buys
nothing.

One bullet of the feedback's suggestion is left out, and the reading is why. It
asks the hint to state that an absent or empty argument is a legitimate way for
the client to say the editor made no choice, which the controller then resolves
a default from. No covered checkout does that: `createAction()` runs
`isValidTemplatePath()` against whatever arrives and an empty `templatePath`
matches no configured entry, so on `main` the blank mode is rejected rather than
defaulted. It is a good design and it is the session's own, not something the
core demonstrates, and a hint stating it would be this repository's preference
wearing a TYPO3 fact's clothes.

The **Assumed** about arrival was tested and cost the hint one pattern.
`Build/Sources/TypeScript/` reaches the TypeScript half, and claiming the whole
tree put this hint above `javascript-unit-tests` in the ranking `D-ANS-075`
holds — a hint about the seam is not a hint about every file in the tree. The
pattern that carries the arrival is `Classes/Controller/`, which is the half
`backend-typescript` never covered and the half the reporting session had open.
`bin/cli hints:probe` on the words that session needed — "what backend
TypeScript may hold and what belongs in PHP" — now reaches
`backend-client-server-boundary` first at `text only(175)`, against the
`icon-usage` and `console-commands` this entry recorded before it landed.
