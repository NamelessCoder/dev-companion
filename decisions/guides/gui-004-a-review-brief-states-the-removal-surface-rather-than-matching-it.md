---
id: D-GUI-004
title: A review brief states the removal surface rather than matching it
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aReviewBriefNamesWhatTheChangeRemoves
---

# D-GUI-004 — A review brief states the removal surface rather than matching it

**A brief for a review states what the diff removes as a surface of its own,
because a review's task text never says what the diff takes away.**

`R-GUI-006` says a review may not be answered with a checklist for changing
something. This is the constraint the shape that fills it is under, read off a
second instance of the same property.

## Evidence

- The feedback's own call reproduces. `TaskGuide::answer` was re-run on
  2026-08-03 with its arguments — task "review the core patch replacing GD-based
  error thumbnails with a static SVG placeholder", `changeType` cleanup. It
  comes back recognized as Patch submission alone. Its checklist is the five
  generic items, "Keep the cleanup mechanical", two Gerrit items and the
  commit-message item. Nothing in it names a removal or a public API.
- Nothing about that call was phrased wrong. The `breaking` intent of
  `knowledge/task-intents.json` matches on "breaking", "remove public", "removed
  public", "drop support", "@internal", "is this breaking" and "public api". A
  review's task text names the subject of the patch, and what the diff removes
  is what the review is about to find out. So the one intent that carries the
  matcher, the marker and the two `.rst` checks cannot be reached from a review
  at all.
- `D-ANS-035` records the same property from the writing side, as its second
  **Assumed**: that a caller reaching `typo3_task_guide` phrases the removal so
  the `breaking` intent fires. This feedback is an instance where nobody could.
- What the feedback asks for is here, on the routes a core review now takes.
  `## Breaking Changes` of `knowledge/documents/typo3-commit-messages.md` states
  the matcher entry per removal, which matcher file takes which kind, and what
  the scanned tag claims. The `breaking` intent carries the entry and the tag as
  two checklist items, with `checkRst` and `checkExtensionScannerRst` as its
  checks. `skills/typo3-core-patch-review/SKILL.md` forces "Enumerate what the
  diff removes or renames before asking", and Public API is the first surface of
  its `references/checklist.md`.
- The route a core review takes no longer passes `typo3_task_guide`. The
  `routing` block of `knowledge/server-scope.json` sends "Reviewing a TYPO3 core
  patch … rather than a project or an extension" to `typo3_rule_lookup` per
  obligation, `typo3_changelog_lookup` for the precedent, `typo3_test_run_guide`
  with the changed paths, then `typo3_commit_message_guide`. The skill was
  drafted in `b97da4b` on 2026-08-03 and `src/Server/Installer.php` publishes
  it.
- The one rule the feedback asks to be stated was declined on a reading before
  this. It asks that method-level `@internal` waive the `[!!!]` marker and
  nothing else, with `Breaking-101955` as the precedent. `D-ANS-035` read
  `.checkouts/main` and found the core did the opposite with the very patch that
  review was about: `b08282345cd6175b02d69b710f19cd9cd40a04f8` removes the
  `@internal` `GifBuilder::getTemporaryImageWithText()` as a plain `[TASK]`,
  with no marker, no changelog entry and no matcher. `D-FBK-038` states what
  decides it instead — whether anything outside the core calls it.
- `R-GUI-006` has a second instance. Its own is a conformance audit of a site
  package in `site-new` (`feedback/2026-07-31-194826`, `nemotron`-era corpus);
  this one is a core patch review in `typo3-cms`, by a different model, in a
  different checkout, on a different task shape.

## Decided

- The feedback is trimmed rather than archived. Everything it asks for that a
  core review now reaches is answered — the enumeration by the skill, the
  matcher and the tag by `## Breaking Changes` and the `breaking` intent, the
  two checks by that intent — and the half that reproduces is the direct
  `typo3_task_guide` call, which is `R-GUI-006`.
- Whatever fills `R-GUI-006` states the removal surface unconditionally. An
  intent keyed on the task text is the shape that cannot work here, for the
  reason the re-run shows: the caller does not yet know what the diff removes.
- `skills/typo3-core-patch-review/references/checklist.md` is the written
  account of that surface, and it did not exist when `R-GUI-006`'s card was
  written. It is a second source for that card's first step, beside the
  conformance checklist the card already names.
- Not closed on the spot and not taken on as something new. The change is an
  audit shape on `typo3_task_guide`, which touches the declared `changeType`
  enum, and `R-GUI-006` already carries it with a card of its own.
- `R-GUI-006` is no longer a single report. Two sessions from two task shapes is
  what takes a card off `low`, and the card that carries the work is another
  session's claim, so the raise is stated here and on the card that serves this
  feedback rather than on that one (`D-FBK-010`).
- The feedback's own rule is not written into `knowledge/`. It is a statement
  about TYPO3 that a reading of the checkout contradicts, and adopting it would
  put a guess where a verified entry's authority is.

## Assumed

- That a session told to review a core patch reaches the skill or the routing
  entry. Neither is reachable from inside a `typo3_task_guide` answer, and that
  is the whole of what stays open here.
- That the review skill's Public API surface is close to what an audit brief
  would state. It is written for a core patch, and `R-GUI-006`'s own instance is
  a site package, where what a diff removes is not the same question.

## Wrong if

- A session reviews a core patch through the skill and still under-states a
  removal. Forcing the enumeration was then not the lever, and what to look at
  is the wording of the surface rather than its presence.
- `R-GUI-006` is filled with an intent that matches on the task text. The next
  review brief is then silent about removals for exactly the reason this one
  was.
- A third instance of `R-GUI-006` arrives from a task shape neither of these two
  covers. The property is then wider than a review, and the shape belongs to a
  task that changes nothing rather than to an audit.

## Since then

`D-GUI-006` filled `R-GUI-006` with the `audit` change type, and the second
**Wrong if** had happened: the review shape arrives and names no removal,
because the shape says how a finding is reported and not what to look for. So
the surface is stated on the intent now, with the core's own rule from
`D-ANS-035` rather than the feedback's — `@internal` waives nothing.

The feedback's own call did not reach it, because a stated change type overruled
the words: the caller was reviewing and had classified the patch under review
rather than their own work. `D-GUI-009` answered that on 2026-08-04 — the
skeleton stays and the words append the surface, since which of the two callers
made the call cannot be read off it.
