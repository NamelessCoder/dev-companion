---
id: D-KNW-107
title: Which side of a backend module resolves a resource path is a subject this server owns
date: 2026-08-24
status: open
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
