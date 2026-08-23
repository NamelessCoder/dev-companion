---
id: D-KNW-101
title: 'What a TypoScript condition can reach is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::aConditionIsAnsweredWithWhatItIsHanded
  - HintsTest::whichGlobalsAConditionCanReadIsBoundToItsMajor
---

# D-KNW-101 — What a TypoScript condition can reach is a subject this server owns

**What a TypoScript condition is handed, and which globals are populated when it
runs, is inside this server's boundary and absent from it, so the feedback is
queued.**

A session repairing an extension's own condition on v14 read the ordering out of
three core branches by hand, because the migration the changelog prescribes —
read the page off the request — has no request to read it from where a condition
runs.

## Evidence

- The feedback's own query still reaches nothing. Run in this checkout on
  2026-08-18,
  `bin/cli hints:probe "typoscript condition variables page request v14"` and
  `bin/cli hints:probe "TSFE removed TypoScript condition ExpressionLanguage provider"`
  both classify `typoscript`, take 21 hints as candidates and match none of
  them.
- The subject is absent from the corpus rather than badly worded. `knowledge/`
  and `skills/` carry no occurrence of `ExpressionLanguage`, `TYPO3_REQUEST`,
  `$GLOBALS['TSFE']` or `AfterPageAndLanguageIsResolvedEvent`.
- The report's decisive claim holds. On `.checkouts/13.4`,
  `frontend/Classes/Middleware/TypoScriptFrontendInitialization.php:123` assigns
  `$GLOBALS['TYPO3_REQUEST']`, and that middleware runs before the one that
  compiles TypoScript. On `.checkouts/14.3` and `main` the frontend's only
  assignment is `frontend/Classes/Http/RequestHandler.php:98` and `:103`, which
  the middleware stack wraps — so the global is unset while conditions are
  evaluated and a fix resting on it passes on 13 and fails silently on 14.
- Two corrections to how the report gets there, neither of which changes that
  verdict. `PrepareTypoScriptFrontendRendering` is not gone on 14.3; what is
  gone is `TypoScriptFrontendInitialization`, whose work moved into it. And 13.4
  assigns the global a second time, in
  `PrepareTypoScriptFrontendRendering.php:184`, after the `frontend.typoscript`
  attribute is already set — so it is not what a condition on 13.4 sees either.
- The variable set is assembled in two places. `.checkouts/14.3`'s
  `PrepareTypoScriptFrontendRendering::prepareConditionMatcherVariables()`
  returns `request`, `pageId`, `page`, `fullRootLine`, `localRootLine`, `site`
  and `siteLanguage`; 13.4 returns those plus `tsfe`. Then
  `IncludeTreeConditionMatcherVisitor::initializeExpressionMatcherWithVariables()`
  wraps `request` in a `RequestWrapper`, adds `context` and `tree`, adds
  `frontend`, `backend` and `workspace` where the aspect is there, and unsets
  `pageId`, `localRootLine` and `fullRootLine` before constructing the
  `Resolver`.
- The version-neutral way in is real. `AfterPageAndLanguageIsResolvedEvent` is
  byte-identical on `.checkouts/13.4`, `14.3` and `main`, and is dispatched from
  `PageInformationFactory::create()` — `13.4:116`, `14.3:127` — which runs in
  `TypoScriptFrontendInitialization` on 13.4 and inside
  `PrepareTypoScriptFrontendRendering` on 14.3, before the condition matching in
  both.
- The third covered LTS is a different reading and was not made here.
  `.checkouts/12.4` carries the event class but no `PageInformationFactory`, so
  where it is dispatched relative to condition matching is open.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3 across a version boundary, and writing it is the
  todo's work.
- `normal`, not the `low` the card arrived at. The failure the gap produces is a
  wrong condition verdict with no error, no log and no failing build, and it is
  version-bound, so a session that verifies on one major ships the bug on the
  other. Not `high`: one session in one directory reported it.
- The changelog half of the suggestion is not built. `typo3_changelog_lookup`
  answers from the changelog an installation ships and from docs.typo3.org;
  noting per entry where no request is in scope would mean a commentary corpus
  keyed by changelog number, kept here against text this server does not own,
  and saying once what a hint says once. The hint is also what a TypoScript task
  reaches without knowing an issue number.
- Inside the boundary. `doesNotCover` excludes PHP source as code, and what is
  asked for is when a global is populated relative to the middleware order —
  version-bound fact rather than a signature.
- The feedback's own account is not copied down: the two corrections above are
  what the checkouts say, and its `RequestWrapper` and unset-again claims were
  confirmed rather than taken.
- No other card is taken over. The two further TypoScript feedback from the same
  directory ask different questions — how an extension registers a condition
  provider, and how a verdict is proven against a running frontend — so this one
  carries only what it reports.

## Assumed

- That the session's account of its own installation holds. Nothing here started
  a frontend or evaluated a condition; the ordering was read out of the
  checkouts.
- That this is one statement rather than two. What a condition is handed, and
  what an extension does to get a page record into one, may turn out to be a
  hint each.

## Wrong if

- A hint reaches either probe query once the corpus grows around it. Then what
  was missing was the placement rather than the statement, and this was step 2.
- Something in the default 14 frontend stack populates
  `$GLOBALS['TYPO3_REQUEST']` before TypoScript is compiled — a middleware not
  read here, or a path other than the default stack. The statement is then a
  default rather than a rule.
- `AfterPageAndLanguageIsResolvedEvent` turns out not to be dispatched before
  condition matching on 12.4. The hint binds `since: 13` rather than holding on
  every covered major, and the recommendation is wrong for the oldest one.

## Confirmed on 2026-08-18

The statement was written as a hint of its own, `typoscript-conditions` in
`knowledge/hints/typoscript-conditions.json`, and both probe queries the
evidence recorded as reaching nothing now reach it.

The 12.4 reading the entry left open came out the other way from the **Wrong
if** above. `AfterPageAndLanguageIsResolvedEvent` is dispatched from
`TypoScriptFrontendController::determineId()` — `.checkouts/12.4:718` — which
the `tsfe` middleware calls, and `prepare-tsfe-rendering` declares itself
`after` that one and is where the conditions are matched. So the event is ahead
of condition matching on every covered major and the recommendation is not
bound; what binds is the accessor a listener takes the record off, because the
event carries the controller on 12.4 and the `PageInformation` from 13.4 on. The
variable set is the same reading on 12.4 as the entry recorded for 13.4, minus
the middleware: it is assembled in `getFromCache()` and completed by the same
visitor, `tsfe` included.

Both globals are populated before condition matching on 12.4 as well —
`TypoScriptFrontendInitialization.php:55` and `:114` — so the statement about
them binds at 14 rather than at 13.
