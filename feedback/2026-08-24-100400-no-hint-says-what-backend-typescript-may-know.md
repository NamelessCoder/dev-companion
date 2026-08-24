---
date: 2026-08-24T10:04:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# No hint says what backend TypeScript may know about the server, and what must stay in PHP

## Observation

Task: review and then rework Gerrit change 95375, "[BUGFIX] Sync new-form template with wizard mode in form manager" — the form manager's new-form wizard picked the wrong start template for its "Blank" mode.

The whole engineering difficulty of this session was one question, asked three times by the user and never answered by this server: which layer owns the decision. The patch under review resolved it in TypeScript by matching a file name suffix, `template.value.endsWith('BlankForm.yaml')`, against integrator-configured `newFormTemplates` paths.

I produced three designs before the right one, and each rework was driven by a user pushback, not by a lookup:
1. Exact core path as a module constant in settings-step.ts. Still an `EXT:` resource path living in TypeScript.
2. PHP hands the path to the JS app in getFormManagerAppInitialData(), JS matches on it. Still the client holding a server resource path, just delivered.
3. The correct one: the client sends no template path at all in blank mode, and FormManagerController::createAction() resolves the blank form. The JS ends up smaller than before the patch — no path, no mode branch, no added state.

The principle behind (3) is general and is exactly the kind of thing typo3_hint_lookup already carries for other subsystems: a TYPO3 backend module's TypeScript may hold UI state and labels; resource paths, configuration resolution and validation belong server-side, because the server already has to validate them anyway and the client cannot be trusted or kept in sync with them.

I called typo3_hint_lookup once in this session, by id, for javascript-unit-tests. The moment a hint would have had to fire: I was holding a diff that put `EXT:form/Resources/Private/Backend/Templates/FormEditor/Yaml/NewForms/BlankForm.yaml` into Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts, with typo3/sysext/form/Classes/Controller/FormManagerController.php open beside it, and the user asked "ist das wirklich richtig das der blank mode als pfad hinterlegt wird?" and later "warum kann das js nicht einfach 'blank' an den php endpunkt übergeben und der weiss was zu machen ist?".

The existing backend-typescript hint covers the source/generated pair, eslint, and where to put unit tests. It says nothing about responsibility split. typo3_task_guide with those two paths returned it plus three asset-pipeline hints, and no hint at all about the PHP the module talks to.

## Query

typo3_hint_lookup(paths=["Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts","typo3/sysext/form/Classes/Controller/FormManagerController.php"], targetVersion="15") — a call I did not make because I did not expect a hint on layer responsibility to exist. Also typo3_task_guide(task="fix which start template a backend wizard submits", changeType="bugfix", paths=[same two], targetVersion="15").

## Suggestion

A hint, id something like backend-client-server-boundary, matching on a path set that spans Build/Sources/TypeScript/<ext>/ and typo3/sysext/<ext>/Classes/. It should state:

- What backend TypeScript legitimately holds: UI state, the editor's in-progress selections, labels via ~labels, endpoint URLs handed to it by the module's own initial data.
- What it must not hold: `EXT:` resource paths, file system paths, decisions the controller re-validates anyway, and any mapping between a UI concept and a server-side resource. If the server has to validate it on arrival, the server is what should resolve it.
- The shape that follows: the client submits the editor's intent (or the absence of a choice), the controller resolves it. An empty or absent argument is a legitimate way to say "the editor made no choice here" — it needs no new request parameter and no signature change.
- The counter-case worth naming: a value the server hands the client in its initial data payload is not the same as the client knowing it. Delivering a path so the client can match on it still puts the decision in the wrong layer; it only moves the constant.

A worked example from this very subsystem would carry it: FormManagerController::getFormManagerAppInitialData() builds the payload, createAction() validates templatePath against newFormTemplates and throws 1329233410 otherwise — so the client resolving which path to send is duplicating a decision the server must make regardless.
