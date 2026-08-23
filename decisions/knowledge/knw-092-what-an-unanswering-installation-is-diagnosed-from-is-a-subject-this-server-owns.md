---
id: D-KNW-092
title: 'What an unanswering installation is diagnosed from is a subject this server owns'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aFailingInstallationIsSaidWhatItWritesDownAndWhatItOnlyShows
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-KNW-092 — What an unanswering installation is diagnosed from is a subject this server owns

**Nothing below `knowledge/` names the log a TYPO3 exception is written to, and
no hint carries the words a caller at an HTTP 500 arrives with.**

So the feedback is queued at `normal`. The corpus does answer the failure the
reporting session hit — exception 1396795884 is named in two hints — and it
answers it by the number, which is the one thing a session looking at a rendered
error page in a terminal does not cheaply have.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  matches nothing on `"site returns HTTP 500 where is the exception logged"` and
  nothing on `"site answers HTTP 500 exception"`; both come back with the 91
  candidates as the index. `"var/log typo3 log file exception"` reaches
  `project-build-and-scripts`, on the word `log` in a statement about what is
  not committed.
- `var/log` is named in no hint, no skill, no requirement and no page below
  `documentation/`. That sentence is the nearest one: var/ is "TYPO3's writable
  state — caches, logs, sessions".
- What the corpus does answer, it answers by the number. `installation-boot` and
  `project-configuration-files` both name exception 1396795884, and a probe
  carrying that number reaches both. The session had roughly 24,000 characters
  of HTML.
- **The feedback's own remedy does not hold for the failure it reports.**
  `AbstractExceptionHandler::writeLogEntries()` returns before it logs anything
  for a code in `IGNORED_EXCEPTION_CODES`, and 1396795884 is the first entry of
  that list on `.checkouts/12.4`, `13.4`, `14.3` and `main`. The trusted-hosts
  500 that cost the session six round trips is displayed and never written down,
  so an agent sent to the log for it finds nothing there.
- The log is where the rest of them are, on all four. `LOG.writerConfiguration`
  in `DefaultConfiguration.php` binds `FileWriter` with no options at `warning`,
  `FileWriter::getDefaultLogFileName()` builds `Environment::getVarPath()` plus
  `/log/typo3_<ten characters>.log` out of `defaultLogFileTemplate`, and
  `writeLogEntries()` logs an uncaught exception at `critical` with its class,
  code, file, line and message.
- The symptom axis itself is not the gap. `D-ANS-081` and `D-ANS-084` built it
  and measured it over 199 queries, and `feedback/2026-08-17-212010` was trimmed
  on 2026-08-18 to what a hint is curated with — that report counted nine
  debugging cycles and about 45 round trips as its session's largest cost item.
- The fourth **Wrong if** of `D-KNW-054` has fired. The skill is published, its
  boot section names no hint, and its create branch names five without
  `installation-boot` among them; `installation-setup` in
  `knowledge/task-intents.json` does not route to it either. The reporting
  session was on the create branch and ruled the entry out by its title.
- The cost the session counted: four HTTP 500s, about ten round trips, three
  extractions written from scratch, and four HTML bodies through its context.
- A second 500 from another mechanism is reported in the same debrief.
  `feedback/archive/2026-08-17-211306` diagnosed a ViewHelper 500 three calls
  after it happened, so neither of the two was reached from what the session
  could see.

## Decided

- Steps 1a and 2, and queued rather than closed on the spot. The statement is
  about TYPO3 and the placement reaches a published skill, and
  [`judging.rst`](../../documentation/records/judging.rst) puts either on the
  todo's side of the line.
- `normal` rather than the `low` the card arrived at. The cost is counted, a
  standing **Wrong if** fired, and the same debrief reports a second failure
  that was not reached from the symptom either.
- Not `high`. One session wrote both reports, and the repair is one lookup once
  the caller knows to make it.
- A hint of its own rather than a statement inside a task hint. What a failing
  installation is read from is neither the boot sequence nor the install
  sequence, and neither reported 500 happened in either. `D-KNW-054`'s third
  **Wrong if** is the other half: `installation-boot`'s patterns are what keep
  it off an install query, and loading symptoms onto them is what that entry
  warned against.
- The trap is the statement rather than an aside beside it. Sending a caller to
  `var/log` is wrong for every code the handler refuses, this feedback's own
  among them, so what the hint states is which exception is written down and
  which is only shown.
- The two statements that already name 1396795884 stay where they are. Each is
  right for the procedure it describes, and whether either owes a neighbour line
  is the todo's to decide against `D-KNW-087`.
- The skill takes the routing at the step where the site is proved. Its proving
  step asks for the site to answer on both sides and says nothing about what to
  do when it does not, which is exactly where the reporting session stood; the
  boot section takes the reference `D-KNW-054` asked for in the same work.
- Curated with phrases of several words. `D-ANS-084` crosses the domain gate on
  those alone, and an agent at a 500 is regularly in another domain — the
  ViewHelper case is the worked example.
- Neither archived nor trimmed. Neither half of the feedback is answered
  anywhere today.

## Assumed

- That an agent at a failing site asks this server at all. A corpus can only be
  curated for the query somebody makes, and this session made none — it went to
  curl.
- That the displayed message and the logged one are the same text where both
  exist. DDEV's generated `additional.php` sets `displayErrors`, which is why
  the page carried it, and nothing was read about the configurations where the
  two differ.
- That the ignored list is stable enough to state. 1396795884 is its first entry
  on all four checkouts and the entries below it differ between 12.4 and the
  rest, so the statement may need a binding the todo establishes.
- That one session wrote this feedback and the sixteen beside it. They share a
  directory, a model and three quarters of an hour, and nothing in a feedback
  records a session.

## Wrong if

- The statement lands and a session at a 500 still scrapes the page. What is
  missing would not be in the corpus at all: a caller that stops asking when
  something breaks is a question for the `instructions` sent at initialize, and
  this entry would have answered the wrong rung.
- The new hint comes back on a query about an installation that works, or
  displaces `installation-boot` on a boot query. Its phrases would be the
  general words rather than the symptom, which is `D-ANS-084`'s second **Wrong
  if** arriving from the corpus side.
- The reading finds the log holds the trusted-hosts exception after all — a
  writer configured below the ignored check, or a handler that is not the
  core's. The hint's central caveat would be wrong, and this entry would have
  rested on a list read in one class.
- A release drops the ignored list, or a project's own `writerConfiguration`
  turns out to be the ordinary case rather than the exception. The statement
  would be version-bound, or wrong more often than right.
- The skill names the log at its proving step and a session reports scraping the
  page anyway. Then the lever is the wording rather than the placement, and this
  is step 4 of the ladder.

## Since then

`installation-exception-output` is the hint, in `knowledge/hints/project.json`
beside `installation-boot`, and the skill names it where the site is proved and
`installation-boot` where a declared clone is booted.

The third **Assumed** is settled and no statement carries a binding.
`IGNORED_EXCEPTION_CODES` is the same ten codes in the same order on
`.checkouts/12.4`, `13.4`, `14.3` and `main`, and so is the default
`LOG.writerConfiguration` — one `FileWriter` at `warning` with no options — and
`FileWriter`'s `/log/typo3_%s.log` template. `IGNORED_HMAC_EXCEPTION_CODES`
gained two entries in 13.4 for the core `HashService`, which is why the
statement names that list rather than counting it.

The display half turned out to be the sharper statement, and the entry did not
have it. `SYS/displayErrors` is `-1` by default and the request's remote address
against `SYS/devIPmask` then picks the handler, so a caller off the development
address gets `ProductionExceptionHandler` and an empty message under "Oops, an
error occurred!". That handler's `discloseExceptionInformation()` makes exactly
three exceptions, and the first of them is 1396795884 — the code the logger
refuses. So the trusted-hosts failure is shown whatever the configuration and
written down nowhere, and every other uncaught exception is the other way round
on a production page. The two halves invert, which is what the hint states.

A PHP fatal is the boundary the reading added. `SYS/errorHandlerErrors` and
`SYS/exceptionalErrors` both mask out `E_ERROR`, `E_PARSE`, `E_COMPILE_ERROR`
and `E_CORE_ERROR`, so an exhausted memory limit reaches no exception handler
and is in the web server's log rather than the installation's. A caller told to
read `var/log` and finding nothing needs that case named, or the empty file
reads as a broken instruction.

`installation-boot` gets no pointer back, against what the last **Decided** left
open. The reference that was missing runs from the symptom into the procedure,
which is the direction the reporting session needed; a reader who has already
reached `installation-boot` has its trusted-hosts statement in front of them.
Its closing statement is one of the six `D-KNW-087` owns, and a fourth id
appended to that formula is the change that entry is queued to make.
