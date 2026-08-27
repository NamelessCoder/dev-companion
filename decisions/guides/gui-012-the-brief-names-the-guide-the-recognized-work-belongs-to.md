---
id: D-GUI-012
title: The brief names the guide the recognized work belongs to
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefNamesTheGuideTheWorkIsWrittenUpIn
  - KnowledgeTest::everyGuideAnIntentNamesIsADocument
---

# D-GUI-012 — The brief names the guide the recognized work belongs to

**`typo3_task_guide` names the knowledge document the work it recognized belongs
to, beside the skill it already names.** The brief searches three
core-contribution documents, so the answer that knows what the task is hands a
session outside the core no guide at all.

## Evidence

- `feedback/2026-08-18-074226`. The session learned the guides exist from one
  place, the `guides` key of `typo3_project_describe`, while reading that answer
  for the version and the sites, and made no `typo3_rule_lookup` call in the
  whole session. It then verified a rendering change in a browser without
  `any/testing/browser-check` and added functional tests without
  `extension/testing/phpunit`.
- Measured in this worktree on 2026-08-18. `typo3_task_guide` for "add unit and
  functional tests for a ViewHelper in the blog extension", with an extension
  path, names the skill `typo3-extension-testing` and one document,
  `core/contribution/rules`. For "prove a rendering change in the browser after
  fixing a frontend crash" it names no document at all.
- `TaskIntents::RULE_DOCUMENTS` is why. A brief searches
  `core/contribution/rules`, `core/contribution/commit-messages` and
  `core/contribution/gerrit-workflow`, so every document under `any/`,
  `extension/` and `project/` is unreachable from the tool that recognized the
  task.
- The delivery already stands in the same answer. `Prose::sections()` names each
  page the excerpts were cut from and the `typo3_rule_lookup` call with
  `documentId` that reads it whole, which is `D-ANS-070`, and a named document
  arrives as a call rather than as an address, which is `D-ANS-061`.
- Two more feedback out of the same checkout report the shape from the skill
  side: `feedback/2026-08-18-074245` and `feedback/2026-08-18-081159`. Each is a
  pointer offered once, at orientation, and needed at a moment the session
  reached hours later.

## Decided

- **The ladder's step 2, delivery.** The corpus is here, the client rendered no
  resource list, and the only answer that names a guide is the call a session
  makes before it knows what the work is. What is missing is placement rather
  than a document.
- **The placement is `typo3_task_guide`.** It is the one answer that has already
  recognized what the work is, it names the skill that owns it, and the guide
  belongs on the same line.
- **Queued rather than made on the spot.** It moves
  `TaskIntents::RULE_DOCUMENTS` and the brief's `rules`, which is a declared
  output schema, and both are what `documentation/records/judging.rst` keeps off
  the spot.
- **The guide is named as the call that reads it**, the way the matched sections
  in the same answer already are. Not as a `typo3://guides` address, and not by
  inlining a page into a brief that already carries hints, rules, checks and a
  checklist.
- **What is named is the guide of the recognized work**, not every document
  whose words match the task text. A brief that searches the whole corpus is
  `typo3_rule_lookup` run a second time, and the intent is what tells the two
  apart.
- **Which of two shapes carries it is the todo's first step**: widening the
  searched documents per scope, or a guide named on the intent beside `skill`
  and `skillCore`. Both are read against what each intent owns today rather than
  chosen here.

## Assumed

- A guide named mid-task is read where the same list at orientation was not.
  Nothing here measures that, and the reporting session says it cannot tell
  whether either guide would have changed what it did.
- The work a brief recognizes is the work the session is in. That holds for a
  call made when the task changed shape and not for the one made at the start,
  which is the half `feedback/2026-08-18-081159` reports.

## Wrong if

- A brief names a guide that does not fit the work — testing recognized from a
  word rather than from the task — which would say the naming is a search and
  belongs in `typo3_rule_lookup`.
- A session reports that the guide arrived in the brief and was still not read,
  which would say the placement is not the lever and the page has to be handed
  over whole.
- A named guide repeats what the skill on the same line carries, which would say
  the two are one pointer rather than two.

## Since then

### 2026-08-18 — the shape is the intent, measured against the other one

**The guide is named on the intent, `guide` and `guideCore` beside `skill` and
`skillCore`, read by `TaskIntents::guides()`.** Both shapes were run against
what the intents own today, and the widening is the one that loses:

- `tests` and `browser-tests` are the two intents whose `rulesQuery` reaches the
  pages the reporting session needed, so widening answers that feedback and
  little else. Eleven of the nineteen intents carry no `rulesQuery` at all, and
  `installation-operations` is one of them —
  `project/installation/booting-a-clone` is unreachable by any widening, and a
  query invented to reach it is the mapping written as a lexical match.
- Where the two shapes disagree they disagree about which is right, not about
  how much. "Write playwright tests for the editor journey" with a package path
  is placed as extension work, so widening per scope reaches
  `extension/testing/phpunit` and not `project/testing/playwright` — while the
  intent's own checklist says the suite belongs to what is deployed rather than
  to the package. The intent knows what the work is; the path knows where the
  file is, and for a browser suite those are two repositories.
- The naming is then deterministic rather than a coverage threshold away from
  silence, which is what a page an intent declares it owns has to be.

**It lands in a `guides` field of its own, not in `rules`**, which is what the
card said. A `rules` entry is a matched section — a body, a heading and a
coverage share — and a page that is named rather than searched has none of the
three; a bodyless record in a declared schema is a hole in the contract to save
a field. `guides` is `skills` for the other corpus, on the line under it, in the
shape `typo3_project_describe` already answers its own `guides` with
(`Schema::guideReference()`, now both tools').

**Four intents name a guide, and one direction is guarded.** `tests`,
`browser-tests`, `changelog` and `installation-operations`; `guideCore` is empty
throughout, because the core pages an intent would name are the three
contribution documents the rule sections in the same answer already name.
`KnowledgeTest` holds every named guide to being a document and to not being the
core's own, and there is no check in the other direction: a document no intent
names is still listed at orientation and served as its resource, where a
published skill nobody routes to is reachable by nobody.

**One of the Evidence bullets above is wrong.** `TaskIntents::RULE_DOCUMENTS` is
not why "prove a rendering change in the browser after fixing a frontend crash"
named no document: measured on the same day, that task matches no intent at all,
so no query is run over any corpus. `any/testing/browser-check` and
`core/testing/proving-a-rendering` are the pages for it and no intent recognized
the work either belongs to, which is recognition rather than placement. That
half was settled the same day:
[`D-GUI-014`](gui-014-looking-at-a-change-is-an-intent-of-its-own.md) is the
intent for looking, it names the browser page on both sides, and it leaves the
probe page to the two skills that grant it.

### 2026-08-21 — the placement carries five documents, and an audit reaches none

**`feedback/2026-08-19-094457` is the first report from after this was built,
and it is the same step of the ladder one rung in: the placement works and the
page the session needed is outside it.** The session audited an extension before
a v14 release, improvised the procedure
`extension/compatibility/running-on-a-declared-major-that-is-not-installed`
writes up, and saw that page named only in the `guides` tail of
`typo3_project_describe` at orientation. Re-run in this worktree on 2026-08-21:

- `typo3_task_guide`, task "full audit of the blog extension before its v14
  release", `changeType="audit"`, `targetVersion="14"`, one extension path:
  `skills` is `typo3-extension-health` and `guides` is empty, as the session
  reported. The `audit` intent carries no `guide`, and `TaskIntents::owned()`
  lets only an intent carrying `changesNothing` name one in that brief.
- Five intents name a guide today — `documentation`, `tests`, `browser-tests`,
  `browser-check`, `installation-operations` — against fifteen documents.
  Neither `extension/compatibility/` page is among them, and no intent
  recognizes compatibility work at all. That is the other direction this entry
  already noted nothing checks, measured as a cost.
- Delivery rather than a gap: `typo3_rule_lookup` with the session's own subject
  as a query returns six sections, four of them from the page it needed and the
  page first. The corpus answers the question; nothing named the call at the
  moment the work needed it.
- Two files name that corpus to a session, and an audit reaches neither.
  `skills/base.md` step 5 names the sibling page
  `extension/compatibility/a-declared-major-that-is-not-installed`, inside the
  deprecation sweep and under a condition of it — and the sweep exempts a task
  that produces no change, which is what an audit is. The page the session
  needed is named in `skills/typo3-extension-upgrade`, and the session was in
  `typo3-extension-health`, which names neither.

**The suggestion cannot be taken where the feedback puts it.** It asks
`typo3_task_guide` to name the page for any repository whose declared major is
not installed. That brief reads no checkout, and the pair the condition is
computed from — `coreConstraint` against the installed version — is
`typo3_project_describe`'s. A condition over the caller's own state is written
where the state is already reported, which is the audit order of a skill; an
intent recognizes the work from the task text instead, and needs no state. Which
of the two carries it is the todo's first step, read against what each owns, the
way the shape above was.

**Queued at `normal` rather than made on the spot**, because both candidates are
contracts: `knowledge/task-intents.json` is what every brief is recognized
through, and a skill is a file in somebody else's project.

**The second half of that feedback is already answered and is trimmed off it.**
It asks that a `documentId` naming no document say so explicitly. Re-run with
the mis-transcribed id the session used, the answer opens *No knowledge document
is called "…"* and lists all fifteen ids with their titles, and `documents` is
declared in `Schema::knowledgeLookup()` rather than arriving undeclared. That
wording is in `RuleLookup::wholeDocument()` since 2026-08-07 and the session ran
on 2026-08-19: it asked for what it had been given and read past it, which is
the same finding as the half above and not a second lever.

### 2026-08-21 — the audit carries them, because the condition is state

**The two `extension/compatibility/` pages are named in
`skills/typo3-extension-health`, not by an intent.** Both candidates were run
against what each owns today, and the intent loses on the case that was
reported: the condition is the checkout's state, and an intent matches task
text. Measured in this worktree the same day, with an extension path and
`targetVersion="14"`:

- The reporting session's own task, "full audit of the blog extension before its
  v14 release" with `changeType="audit"`, matches `audit` and nothing else. So
  does "audit the extension and check whether it still runs on every TYPO3 major
  it declares", which names the work outright. Neither text carries a needle a
  compatibility intent could take without taking "release" or "major" with it,
  and that is the mapping written as a lexical match this entry already rejected
  for `installation-operations`.
- An intent names its guide on every brief it matches, and the feedback asks for
  the page where the declared major is not installed. A package declaring the
  installed major alone would get it anyway, which is this entry's first **Wrong
  if** — a brief naming a guide that does not fit the work.
- The skill can state that condition because it already holds the answer.
  `coreConstraint` and `typo3Version` are one `typo3_project_describe` away and
  the base's first step makes that call, so the step costs no round trip.

**The step sits in the audit half, after the surface lookups.** It names both
pages as the `typo3_rule_lookup` calls that read them, splits them by what each
settles — the reading for whether a symbol is there, the run for a claim that
has to be run — and says what an audit that runs neither owes instead: the gap
named on the finding rather than the range claimed. `typo3_rule_lookup` joins
that skill's `ROUTING_SKILLS` entry third, which is what holds the step in place
and in order.

**No intent recognizes compatibility work at all, which is the other half and is
queued.** "Make the extension compatible with v14", "prove the package runs on
TYPO3 13 and 14" and "does this still work on v13?" match no intent, so they
reach no skill and no guide — `typo3-extension-upgrade` owns that work and names
the running page already, and nothing routes a caller to it from the task text.
That is recognition rather than placement, the same split as `D-GUI-014`, and it
is queued as
`todo/open/2026-08-21-143000-no-intent-recognizes-compatibility-work.md`.

### 2026-08-21 — the queued half is written, and it is a sixth guide

**The recognition half above is
[`D-GUI-018`](gui-018-keeping-a-package-on-the-majors-it-declares-is-an-intent.md),
the same split as `D-GUI-014` and settled the same way.** `compatibility` is the
sixth intent to name a guide, it names
`extension/compatibility/running-on-a-declared-major-that-is-not-installed` and
routes `typo3-extension-upgrade`, and the three task texts the entry above
measured as reaching nothing now reach it and nothing else. So does `EXT-01`,
which reached no intent at all before.

**The third Wrong if is answered rather than triggered.** The guide names the
page the skill's own step 3 also names, which is a repetition on the face of it;
it is the right page anyway, because that page's second paragraph hands the
reader on to the sibling `a-declared-major-that-is-not-installed` for the
question it does not answer. Naming the running page reaches both, and naming
the reading page would reach one.

### 2026-08-24 — the second Wrong if is satisfied, and a guide is withheld by the change type

**Three sessions report a page named to them and unread, once from each
placement this entry and its neighbours built.** That is the second **Wrong if**
above, and one report was the case it was written for.

- `feedback/2026-08-24-140239` had `project/installation/booting-a-clone` named
  twice — in the `guides` field of the brief and in the `documents` field of the
  installation-setup hint — and opened neither. The recognition half of that
  feedback is settled in
  [`D-GUI-014`](gui-014-looking-at-a-change-is-an-intent-of-its-own.md); this is
  the half that is left.
- `feedback/2026-08-24-173211` read the `guides` array at orientation,
  registered that two pages existed, and assembled both procedures by hand.
- `feedback/2026-08-24-183345` had the page named inside the review skill's own
  body, in a sentence it quotes back, and skipped it.

So the pointer has now failed from the orientation answer, from the brief and
from a skill body. What that says is what this entry's **Assumed** already put
at risk: a guide named mid-task is read where the same list at orientation was
not, and nothing measures it. **The question this raises is not one a judging
run may answer** — whether a page has to be handed over whole rather than named
is a change to what every brief weighs, and this rung is
`documentation/records/judging.rst`'s step 5.

**A second cost, measured the same day: a brief that changes nothing withholds a
guide the caller asked for.** `TaskIntents::owned()` lets only an intent
carrying `changesNothing` name a page, which is `D-SKL-039`'s reasoning applied
to guides. Run in this worktree, "boot the environment and add functional tests"
with `changeType="operations"` confirms `tests` and names
`extension/testing/phpunit` nowhere. That reasoning was written for a review,
where the words of the change under review are not the caller's own work; an
operations brief has no change under review, and there the words are exactly
what the caller is doing. Whether the two cases may share one filter is the
other half of the question above.

### 2026-08-25 — the fourth surface is a tool description, and it was in context

**The question this entry raised about a page is reported for a tool, from the
same debrief as one of the three above.** `feedback/2026-08-24-140421` skipped
`typo3_extension_describe` in a session about one extension, and it had that
tool named twice over: `skills/base.md` makes it step 2 of a fixed order, and
the session had loaded its schema, which under a deferring client is what
carries the `description` into context at all — the mechanism
`feedback/2026-08-19-090401` describes for the same client.

The wording was not what was missing, which is what makes this the same rung
rather than step 4. Read on 2026-08-25, `base.md` step 2 already says *What it
does not ship is answered too, and that is the half no file listing can give
you*, which is aimed exactly at the session's own reason for skipping — it had
read every file by hand. The tool's `description` names the deprecated-files
verdict in full: the four files, the predicate each turns on, and why no
changelog search over the extension's code reaches them,
[`D-ANS-009`](../answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-the-file.md).
The session found that defect in the extension anyway, by another route, and
names it as what one call would have handed it at the start.

So the surface a tool is chosen on was in the session's context, complete, and
the call was not made. What the feedback asks for instead is the same move this
entry's question is about, transposed: fold the verdict into
`typo3_project_describe`, the answer the session does make, rather than name the
tool that carries it. That changes what an orientation answer volunteers to
every caller, and the reason it is not decided here is this entry's — it is
`documentation/records/judging.rst`'s step 5, and the question is up on that
feedback's own card.

### 2026-08-25 — a core page outlives the reason `guideCore` is empty

**A core intent names its guide in `tools` rather than in `guides`, and that is
where the changelog page arrives.** The reason recorded here for leaving
`guideCore` empty throughout — that the core pages an intent would name are the
three contribution documents the rule sections in the same answer already name —
stopped describing the corpus when
[`D-KNW-111`](../knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md)
split `core/contribution/changelog` out as a fourth.

Measured in this worktree while judging
[`feedback/2026-08-24-173211`](../../feedback/2026-08-24-173211-the-guides-list-was-returned-and-never-pulled.md),
whose first suggestion is that the brief name the guide covering the task it was
asked about. For a core changelog task the brief's `guides` is empty and its
rule sections come from `core/contribution/rules` and
`core/contribution/commit-messages`, so nothing in either field is the page.
`TaskIntents::RULE_DOCUMENTS` is why, and adding the page to it moves neither —
the intent's two section slots go elsewhere on its own query.

**The conclusion holds on the other reason the entry gives.** The `changelog`
intent's `tools` names `typo3_rule_lookup` with the `documentId`, first in the
list and with what the page carries, which is this entry's own rule that a guide
is named as the call that reads it. A `guideCore` beside it would be a second
pointer to one page in one answer, which is the third **Wrong if** here. So what
is corrected is the reason and not the shape.

### 2026-08-27 — the fourth report is a rule that was read, and it bounds what the pull can buy

**A rule delivered whole, in the answer, and quoted approvingly is still not
what decided the moment it was about.** Three reports have the shape the card
serving `feedback/2026-08-24-140239` is blocked on — a page named to a session
and never opened, from the brief and a hint, from the orientation answer, and
from a skill body. This is the fourth and it is on the other side of that line.

`feedback/2026-08-24-225153` asked `typo3_rule_lookup` for `changelog entry` and
received the obligation as prose, not as a pointer: *A casual bug fix carries
none, because the commit message is what informs the reader*, re-run on
2026-08-27 and still the first match at 100%. The session names it as one of the
five answers it would keep. Two hours later it wrote an `Important` entry for a
hardening patch anyway, matched it against neighbouring entries, ran `checkRst`,
and deleted it once the user objected — `feedback/2026-08-24-225243`, its own
second cost, filed 50 seconds after the strength.

So the content was in context, in the caller's own words, and had already been
read. Handing the guide over whole is what the three reports above ask for, and
on this one it would have changed nothing: nothing about the delivery was
missing. What the moment of need lacked was not the page but the return to it,
and `checkRst` cannot supply that — the corpus already says a wrong type passes
every suite and is caught in review or not at all.

That is evidence for the question and not an answer to it. Both cards stay in
`todo/waiting/`, and the count they are to be written against is four reports
from three surfaces plus this one, where the surface worked.
