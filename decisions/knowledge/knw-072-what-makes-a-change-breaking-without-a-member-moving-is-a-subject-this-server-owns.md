---
id: D-KNW-072
title: 'What makes a change breaking without a member moving is a subject this server owns'
date: 2026-08-14
status: confirmed
---

# D-KNW-072 — What makes a change breaking without a member moving is a subject this server owns

**That a change moving no PHP member can still be breaking is inside this
server's boundary and missing from it.**

Every statement here about what makes a change breaking is keyed on a member
being removed, narrowed or widened. A patch that changes what the frontend
renders for content nobody edited passes all of them, and the reviewer asking
which type it owes — which is what decides whether it may be backported at all —
is answered by nothing.

## Evidence

- Re-run on 2026-08-13 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query matches nothing, and 88 hints come back as the
  index. Two narrower probes match nothing either: "changed rendered frontend
  markup for existing content is breaking", and "changed default TypoScript
  output breaking change".
- The statements that do exist all name a member. `public-api-surface` in
  `knowledge/hints/public-api.json` is titled "Changing a Public Method
  Signature"; `breaking-not-assessed` in `src/Knowledge/CommitMessage.php` says
  "a removed, narrowed or widened public or protected member makes the change
  breaking"; `## Breaking Changes` and `## Changed Signatures` of
  `knowledge/documents/core/contribution/commit-messages.md` are about the PHP
  API and the extension scanner; and the `breaking` intent of
  `knowledge/task-intents.json` settles the question on whether anything outside
  the core calls an `@internal` member.
- The four-type definition points the same way, and it is one word narrower than
  its source. `## Changelog Files` and the `changelog` intent both write
  Breaking as "where it moves or removes core functionality third-party code may
  use", while `Documentation/Changelog/Howto.rst` in `.checkouts/main` writes
  "moved or removed a specific part of Core functionality that **may break or
  affect third-party code**". *Affect* is what carries a change in what is
  rendered, and it is the word this server dropped.
- The core does file such a change as Breaking. `.checkouts/main` carries
  `Changelog/12.0/Breaking-98308-LegacyHTMLAttributesBorderAndLongdescRemovedFromFrontendRendering.rst`
  and
  `Changelog/12.0/Breaking-96831-EnforceHTMLSanitizerDuringFrontendRendering.rst`,
  both changes to rendered frontend markup rather than to a signature. Whether
  that is the rule, and whether a maintained line ever carried one, is the
  reading the todo owes.
- The route the session was on carries the surface and not the type.
  `skills/typo3-core-patch-review/references/checklist.md` names **Behaviour**,
  down to "a shape of output other code asserts on", and **Documentation and
  changelog** separately. Nothing joins the two, so a reviewer who has
  established that the output shape changed is not told which type that owes.
- The backport half is here already, and the feedback asks for it anyway.
  `public-api-surface` states that a maintained release line carries no breaking
  change, no deprecation and no feature; `## Changelog Files` states that
  `Important` is the only one of the four an LTS release may carry; and
  `typo3_commit_message_guide` reports `breaking-release-target` where a
  `Releases:` line names anything but `main`. All three predate the session.
- One report, and the second in this family. `bin/cli feedback:list` on
  2026-08-13 holds 18 open feedback, all from `/home/benji/projects/typo3-cms`.
  [`D-KNW-065`](knw-065-what-a-public-method-commits-its-author-to-is-a-subject-this-server-owns.md)
  took the same question on from the other side — writing a patch rather than
  reviewing one — and covered the third move of a signature.

## Decided

- Step 1a, queued rather than closed on the spot. What the core files as
  breaking with no member moved is a claim about TYPO3, and it is read across
  `.checkouts/` before anything is written.
- `normal` rather than the `low` the card arrived at, on the grounds `D-KNW-065`
  set: one session, so not more than that, and what the silence lets through is
  a breaking change wearing a `[BUGFIX]` on a patch whose point is reaching two
  release lines.
- Not `high`. Nothing is blocked on it, and the session's own verdict came out
  right — by its own account from recall rather than from method.
- The feedback is trimmed rather than taken whole. Which types a maintained
  release line admits is answered; what stays open is the classification of a
  change that moves no member.
- `typo3_changelog_lookup`'s description stays as it is. It retrieves entries
  and its verb is `lookup`, which is what tells a caller the shape of its
  answer; classification is owned by `typo3_rule_lookup`, `typo3_hint_lookup`
  and the `breaking` intent, and a description promising "which type is this"
  would promise an answer the tool does not hold.
- Where the statement goes is the todo's, as it was in `D-KNW-065`. The
  candidates are the `public-api-surface` hint, whose title is about a signature
  and would have to widen or split; the `breaking` intent's first checklist
  step, which already settles the question before it says how to write the
  entry; and `## Changelog Files`, where the four-type definition sits.
- Not the feedback's own wording. Its author was guessing about this repository
  as much as this run guesses about TYPO3, and its proposed text asserts what
  has to be read first.

## Assumed

- That the two 12.0 entries are the rule rather than two picks. No sweep of
  frontend-output changes across the covered majors was run, and that sweep is
  the todo's first step.
- That placement decides this rather than the sentence. The backport half was
  written before the session ran and did not reach it, which is the same failure
  in the same domain one rung down.
- That one session wrote this feedback and the three beside it. They share a
  directory, a model and seventy seconds, and nothing in a feedback records a
  session.

## Wrong if

- The sweep finds the core filing a changed frontend markup for unchanged
  content as `Important`, or as a casual bug fix owing no entry at all. The gap
  is then one sentence saying so, and the feedback's premise was wrong.
- A maintained release line turns out to carry such a Breaking entry. The
  backport half is then wrong rather than absent, and `public-api-surface`'s
  statement about what a release line carries is too flat.
- The statement lands and a review still answers the classification from memory.
  The lever is then the checklist joining **Behaviour** to the changelog
  surface, rather than a sentence in the corpus.
- The same surface is reported again from writing a patch rather than from
  reviewing one. Placement in the review checklist is then the wrong half, and
  one card carries both.

## Confirmed on 2026-08-14

The sweep found the rule rather than two picks, and found the boundary in a
place nobody here had looked. The core files a change with no member moved as
breaking routinely — five entries on the development line, beside the TypoScript
removals — and the first **Wrong if** does not hold: the definition is one word
short as this entry read it, the source writing "may break or **affect**
third-party code" and giving changed frontend output as its own example.

The second **Wrong if** holds and is where the boundary runs: no maintained
line's directory carries a Breaking entry, and the last that did is four majors
back. What those lines take instead is the same change under the other type, so
the target branch decides between the two where the effect is the same — the
half the reporting session needed and the half nothing here stated. The
placement came out as a split rather than a widening.
