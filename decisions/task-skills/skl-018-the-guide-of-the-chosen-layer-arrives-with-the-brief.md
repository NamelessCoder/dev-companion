---
id: D-SKL-018
date: 2026-08-04
status: open
---

# D-SKL-018 — The guide of the chosen layer arrives with the brief

**A task whose words and paths name a test layer is answered with that layer's
implementation guide by name, rather than with one sentence offering three.**

A session wrote a backend Playwright project — login setup, storage state,
project dependencies, four specs — and never opened `references/playwright.md`,
which is the file that work is for.

## Evidence

- `feedback/2026-08-04-180052`. The skill says "After selecting a layer, read
  only its implementation guide" and lists the three in one sentence.
  `references/static-quality.md` was read because the check-layer half of the
  task named it first; the browser half arrived several layers into a nested
  activation — cleanup, then conformance, then testing — with the checklist
  already in hand.
- What it cost is on the record: the storage-state constant placed in the login
  setup spec, rejected with "Playwright Test did not expect test() to be called
  here" and refactored after one run. The second cost, locators finding nothing
  inside the backend module, is `D-KNW-060` and is not the guide's to answer.
- The brief already carries this shape. `D-GUI-007` puts a selection of hints
  into it and says whose they are, and the skill that owns the task is named in
  it since `b614b12`. A reference is one hop further along the same path.
- `knowledge/task-intents.json` has `tests` and no entry for a browser layer, so
  the brief has nothing to name today: the intent that matches "playwright",
  "e2e" or "browser test" does not exist.
- A reference path written into the corpus is a path this repository writes
  between its own files, which `bin/cli links:check` and `LinksTest` already
  hold. So naming a skill's file from `knowledge/` does not create a link
  nothing watches.

## Decided

- The judgement is **step 3**, routing. The guide exists, it is named in prose,
  and the pointer competed with two siblings at the moment of choosing.
- The lever is the **tool answer**, not the skill. A published skill's copy in
  somebody else's project is not corrected by the next release of this server,
  and the brief is composed here on every call.
- The smaller half — the three guides as a table keyed by the layer chosen in
  the step before, so selection and pointer are one act — is not rejected. It
  rides along the next time that skill is edited rather than earning a change of
  its own.
- Nothing here decides what the new intent matches or what its checklist says.
  That is read off the guide it points at.

## Assumed

- That a named file arriving in a tool answer is harder to walk past than one of
  three in a sentence. That is the reporting session's own reading of why it
  skipped the guide, and it is one session.

## Wrong if

- The brief names the guide and the next browser-test session still works from
  the checklist. Then what competes is the reading order of a nested activation,
  and the pointer was never the lever.
- The intent matches tasks that are not browser work, and briefs start carrying
  a guide nobody needs. Then the layer is not readable off a task's words.

## Since then

On 2026-08-04, the intent was written and the brief was run.
`knowledge/task-intents.json` carries `browser-tests` — matching "playwright",
"e2e", "spec.ts" and their neighbours — whose checklist opens with
`skills/typo3-extension-testing/references/playwright.md` by name and whose
`tools` name the two hints that exist. Called over stdio with "add a Playwright
spec that proves the backend preview of a content element" and a `spec.ts` path,
the answer recognizes `browser-tests` beside `content-element` and returns the
guide as its first checklist line.

What that took was data and no code: the brief already renders an intent's
checklist, so naming a file there needs no field of its own, and
`bin/cli links:check` holds the path like every other one this repository
writes. The narrower reading of this entry — a `guide` field beside `skill` —
was not built, because nothing needs to tell the two apart yet.

The match list deliberately avoids the bare word "test", which `tests` already
carries. A browser task matches both, which is correct: what the layer adds is a
guide and a served site, not a different way of testing.

### 2026-08-19 — the hint block was weighed against this reasoning and stands on another

Whether a brief that names an owning skill should withhold the hints that
skill's own steps fetch was asked as a possible amendment here. It is not one:
this entry names a file where three competed and removes no content, while the
hint block is a quoted selection carried under `D-GUI-007`.
[`D-GUI-016`](../guides/gui-016-the-brief-carries-its-hints-whether-or-not-it-names-a-skill.md)
is where that was settled, and it changes nothing about the brief.
