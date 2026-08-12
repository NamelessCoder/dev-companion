---
id: D-KNW-026
date: 2026-08-02
status: open
---

# D-KNW-026 — Where a one-off script may not be written is a gap this server owns

**The corpus places a one-off script and names only `var/` as the wrong place,
so nothing here stood between a session and a PHP file in the webroot.**

The rule this feedback asks for is half written already. What is missing is the
place it does not name, and any way of reaching it from the moment it is needed.

## Evidence

- The feedback's own query reaches nothing.
  `bin/cli hints:probe "writing and executing a PHP script in the live webroot to introspect core classes"`
  matched no hint on 2026-08-02, and returned its 40 candidates as an index.
- The placement rule is here, on `project-repository-layout` in
  `knowledge/architecture-hints/general.json`. `Build/` is for "what runs before
  or around the site rather than in it", and what it lists there includes "a
  one-off script that seeds an installation".
- The same hint says where such a script may not go, and it names one place: "A
  script that exists for one run therefore does not go there because it is
  ignored; it goes into Build/ with the rest of the tooling, or it is not kept
  at all." The place is `var/`, and the reason given is that `var/` is not
  committed.
- The document root occurs three times below `knowledge/` and never as a place a
  file may not be written. They are `public-assets` on `_assets/` publishing,
  `environment-variables`, and `project-extension-tests` on the paths
  `$testExtensionsToLoad` takes.
- The rule is reachable only from a query that already names the answer. Probed
  on 2026-08-02, "where do I put a one-off script" and "one-off debug script
  placement in a TYPO3 project" match nothing at all.
- The one phrasing that reaches it is the one that needs it least. "write a php
  file into the document root and call it in the browser" returns
  `project-repository-layout` at `text(109)` and `public-assets` at `text(67)`.
- That contrast is
  [`D-ANS-024`](../answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md)
  measured on a second rule. A caller who can phrase that query has already
  taken the decision the rule exists to change.
- The corpus endorses a throwaway boot script for a neighbouring purpose and
  says nothing about where it lives. `sitepackage-initial-content` states that
  seeding "is a throwaway script that boots TYPO3 itself", down to
  `Bootstrap::init($classLoader)` and its `$failsafe` argument.
- No tool is what is missing.
  [`D-ANS-003`](../answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md)
  read this same script on 2026-08-02 as evidence about its runtime half, and
  recorded that the answer came from a manual page.
- The other half of the **Suggestion** belongs to a sibling.
  `feedback/2026-08-01-003933` asks for the installed source to be read instead
  of guessed at, which
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  already mapped as "what to do instead". It is in hand on another branch as
  this is written.
- Whether the file was served is not established here. The session wrote
  `/var/www/html/check_record.php`, and what a DDEV TYPO3 project serves from
  that path is a DDEV and Composer question. This run read only this repository.

## Decided

- Step 2 of the ladder. The rule exists, is worded as a rule, and is reached
  only by a caller who already names the document root.
- The clause that would name the document root is missing as well. That is the
  wording half of the same sentence, it lands in the same place, and it is
  therefore one card rather than two.
- Queued rather than closed on the spot. The sentence rests on where a Composer
  installation's document root is, and on what DDEV mounts at `/var/www/html`.
  That is a lookup, and [judging.md](../../documentation/records/judging.rst)
  keeps a lookup out of a run that has read only this repository.
- Not step 1b. A runtime tool for this was refused in `D-ANS-003`, on the
  evidence of this very session.
- The introspection half is not restated. `003933` owns it, and a second card
  for one step is the overlap `bin/cli todo:claim` warns about. The card written
  here names that overlap, so the two sentences are placed together rather than
  twice.
- The feedback stays open. The card in `todo/open/` is what archives it when the
  clause lands.
- The debug leftovers the self-rating reports are the same substitution seen
  again. `fwrite` and `extract` in a regression test and a throwaway
  `LinkDebugTest` are mapped here by `D-FBK-021`, and the clause's "or it is not
  kept at all" is where they land.

## Assumed

- That `project-repository-layout` is the hint that carries it. A session about
  to write a debug file is deciding where a file goes, whether or not it phrases
  the question that way.
- That words in `appliesTo` can make the rule reach. The moment it is needed is
  a decision the caller has already taken, and no path signal carries it.

## Wrong if

- `/var/www/html` turns out not to be served in a DDEV TYPO3 project. The clause
  would then rest on the file outliving the run rather than on it being
  reachable, which is a different sentence.
- A session is offered the extended clause and writes into the document root
  anyway. That is step 4 and a rewrite, not a placement.
- The next report of this comes from a session that never asked where a file
  goes. The rule would then have to arrive from the introspection question,
  which is `003933`'s route rather than this one's.

## Since then

The lookup this entry queued was done on 2026-08-03 and the first **Wrong if**
is what it found. DDEV mounts the project directory at `/var/www/html` and
serves the docroot below it, which its TYPO3 project type sets to `public`, so
`/var/www/html/check_record.php` was at the project root and was never served.
The clause therefore rests on both reasons rather than on the served one alone,
and
[`D-KNW-045`](knw-045-the-document-root-is-named-by-what-configures-it-and-by-what-serves-it.md)
carries the readings and the sentence that landed.

Two of the three measurements above had also gone stale. The clause moved to
`project-build-and-scripts` in `knowledge/hints/project.json` with the refiling
in
[`D-KNW-032`](knw-032-the-corpus-is-filed-by-question-and-two-splits-were-taken-back.md),
and by 2026-08-03 both "where do I put a one-off script" and "one-off debug
script placement in a TYPO3 project" reached it. What still reached nothing was
the feedback's own query, which is the one this entry was written about.
