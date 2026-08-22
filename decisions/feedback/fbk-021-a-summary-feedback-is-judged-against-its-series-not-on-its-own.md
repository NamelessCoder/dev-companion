---
id: D-FBK-021
date: 2026-08-02
status: open
---

# D-FBK-021 — A summary feedback is judged against its series, not on its own

**A feedback that summarises a session which filed one report per subject is
judged by mapping its halves onto those siblings, not walked down the ladder
again.**

Such a summary carries no lever of its own. Every subject in it is a subject
somewhere else too, so judging it as a report produces what the ladder's
preamble names: a gap with a fourth entry written next to three that exist.

## Evidence

- `feedback/2026-08-01-002951` is the first feedback of the series
  [`D-FBK-006`](fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md)
  was written from — the one that kept the shared opening. Its **Suggestion**
  names four things to cover, and each is the whole subject of a named sibling
  filed within six minutes of it:

  | Half of the summary                        | The sibling that owns it                            |
  | ------------------------------------------ | --------------------------------------------------- |
  | `f:if` needs an explicit `f:then`          | `003448`, with the working markup; `003000`         |
  | preview data is the record, not TypoScript | `002926`, `002928`, `002930`, `002745`              |
  | Record API field access                    | `002928`, whose query is three Record API phrasings |
  | test request type and dataset priming      | `003003`; `003929` for the database half            |

- Two of those four are already judged, and by another card than this one. The
  record variable is
  [`D-KNW-014`](../knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md),
  step 1a, with `todo/progress/2026-08-02-133246` serving `002745`. The Fluid
  half and the functional-test half are unjudged, with their own cards in
  `todo/open/`.
- A series produces summaries in the plural. `003103` is a second one of the
  same session — its query is "whole-session roundtrip audit: backend preview,
  Record API, Functional test, Fluid" — open, with a card of its own.
- The summary's query reaches less than its halves do. `bin/cli hints:probe` on
  the four joined by semicolons reaches `project-extension-tests` and
  `core-tests`, and nothing else. Asked one at a time the same four reach
  `fluid-templates`, `content-elements`, `frontend-records` with
  `tca-schema-api`, and `project-extension-tests` — so the joined query misses
  three of the four hints its own parts find. That is
  [`D-ANS-021`](../answers/ans-021-a-manual-query-is-told-what-short-buys.md)
  measured on a hint probe rather than on the manual.
- Nothing about TYPO3 was established here, deliberately. Whether an `f:else`
  really forces an explicit `f:then`, and what `project-extension-tests` already
  says about priming, is the reading `003448` and `003003` owe.

## Decided

- The judgement of a summary is the mapping above, and it is written here so it
  is not derived a second time. What getting it wrong costs is one knowledge
  entry written twice, from two judgements of one session, with no reader left
  who could tell which reading either rests on.
- No todo is derived from this feedback. Every step it would name is already a
  step on a sibling's card, and a second card for the same step is the overlap
  `bin/cli todo:claim` was taught to warn about.
- Nothing on another branch was touched. `002930`, `003000` and the
  record-variable todo are in hand in other worktrees as this is written, so
  adding this feedback to their `Serves:` lines would edit one file in two
  worktrees at once.
- Whether the summary may be archived now is **not** decided here. It is the
  question on the card, and the card stays in `waiting/` serving the feedback,
  which keeps
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant either way the question is answered.

## Assumed

- That an agent reads its summary being closed differently from its specific
  reports being closed. Nothing has measured it. `D-FBK-017` assumes the
  opposite reading for the ordinary case, which is why the archiving is a
  question here rather than an answer.
- That the mapping is complete, and no half of the summary is orphaned. It was
  read off the four clauses of the **Suggestion** against the `Query` line of
  every feedback that session filed.

## Wrong if

- ~~A half turns out to have no sibling. The mapping is then wrong, the summary
  is the only report of that subject, and the ladder is owed after all.~~ Fired
  from the fourth summary on, and the mapping was right each time. An orphaned
  half is what the summary adds, and the ladder was walked over that row rather
  than over the file.
- ~~Every sibling lands and the summary is still open, because each closing
  commit archives only the feedback it worked off. The summary would then need
  an owner rather than a mapping.~~ Fired on 2026-08-03 on the first three
  summaries, and what they needed was a reading of where their siblings had
  landed. The ninth was closed by the mapping itself.
- A session judges a summary by walking the ladder anyway, and writes the entry
  that a sibling's todo writes again a week later. That is the failure this is
  written against, and nothing checks for it.

## Since then

`003103` — the second summary this entry named — was judged on 2026-08-02 by the
mapping rather than by the ladder, and the first **Wrong if** did not fire. Its
**Suggestion** has five clauses where `002951` had four, and every one of them
lands on something:

| Half of the summary                                            | Where it is                                                                                                                                                                                                                                   |
| -------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| what a v14 preview template receives, and how a field resolves | [`R-KNW-041`](../../requirements/knowledge/knw-041-a-preview-template-answer-says-what-the-template-is-handed.md), from [`D-KNW-014`](../knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md) |
| `typo3_documentation_lookup` reaching *Record objects*         | `002928`, whose premise [`D-ANS-021`](../answers/ans-021-a-manual-query-is-told-what-short-buys.md) rejected                                                                                                                                  |
| the Fluid conditional, and object access on the record         | `003448` and `003000`; the record half is `R-KNW-041`                                                                                                                                                                                         |
| a debug script the container could not see                     | `003938`, with `003933` for what to do instead                                                                                                                                                                                                |
| one cache command instead of `rm` on the cache directory       | `003937`                                                                                                                                                                                                                                      |

Two of the five are answered rather than carried, which is what makes this
summary a different case from `002951`. The preview statements are on the
`content-elements` hint: one variable on 14, a path resolved through the
record's `has()` and `get()`, null where the path is neither a schema field nor
a system property, and five TCA types that come back as records. Re-run on
2026-08-02 against the live 14.3 manuals, `Record API` returns *Record objects*
third and `record objects` returns it first, so the page was never missing from
the index; `Record API fluid access` still answers with the wrong pages, and
each of them now names what it matched on — `acces (title)`, `api (path)` —
which is what `D-ANS-021` built.

The feedback's own phrasing reaches none of it. `bin/cli hints:probe` on "what
does a v14 backend preview template receive and how do its fields resolve"
matches no hint, while `tt_content preview template` reaches `content-elements`
at `appliesTo(10) + text(123)`. That is the dilution ceiling
[`D-ANS-006`](../answers/ans-006-an-identifier-is-found-however-it-is-spelled.md)
meets rather than a second gap in the corpus, and the hint is past 544 words.

One sliver maps to no sibling, and the ladder is still not owed for it. That the
host's `/tmp` is invisible inside the container, and that `php -r` with a `$var`
fails through `ddev exec`, is the container around the installation and the
caller's own shell: `doesNotCover` names *running an installation: server and
container setup* and sends it to the TYPO3 documentation. The question the
session was actually asking — how to read a class it has installed — is inside
the boundary, and `003938` and `003933` own it.

Archiving is the same open question as before, and it is not answered twice.
`003103` waits on the answer `002951` waits on, with one thing added in its
favour: two of its five halves have landed, so `closed` would not stand over an
untouched subject here.

## Since then

`2026-08-01-003634` is a third summary of the same session, judged on 2026-08-02
by the mapping, and the first **Wrong if** did not fire again. Its shape is new
here. It is not a roundup of subjects but a self-rating: five scored sections,
whose headings are the workflow of `skills/typo3-extension-testing/SKILL.md`.
The mapping still holds, because the sections are the same subjects under the
skill's words.

| Half of the self-rating                                                                                                            | Where it is                                                                                                                                          |
| ---------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| §1 `references/base.md` unread, neither scope call made, remembered `composer test:*` in place of the declared commands            | `003356`, judged in [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)                                                  |
| §2, §3, §5 rendered frontend and backend preview established by curl, the project's own `Tests/E2E` harness untouched, no spec run | `003533`, judged in [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md), with a card in `todo/open/` |
| §4 tests written against core ViewHelpers to find out what they do                                                                 | `003933`, which reports the same substitution of experiment for reading                                                                              |
| §4 `fwrite` and `extract` debug left in the regression test, and a throwaway `LinkDebugTest`                                       | `003938`, whose **Suggestion** asks for the safe way to introspect an installed class                                                                |
| the premise that the skill was never activated                                                                                     | withdrawn by `003736`; `D-KNW-017` records the withdrawal and reads the trigger out                                                                  |

Its own query reaches less than its halves do, which is the pattern
[`D-ANS-021`](../answers/ans-021-a-manual-query-is-told-what-short-buys.md)
names and `002951` already showed on a probe. Re-run in this checkout on
2026-08-02, `bin/cli hints:probe` matches nothing for the feedback's own `Query`
line, nothing for either clause of its **Suggestion** in the words it wrote them
— "surface the testing skill whenever test layers are touched", the base to
checklist order — and nothing for "which commands does this project declare for
running its tests". The one phrasing that reaches is "route rendered-output
verification to a browser test", at `browser-tests appliesTo(12) + text(98)`,
and it reaches because it names the layer. That is `D-KNW-017` measured a second
time from a different sentence, not a second gap.

What this summary adds beyond the mapping is where the boundary runs, which is
what
[`D-FBK-018`](fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
says a strength carries. Its two strengths are that the functional PHPUnit layer
was the right one for the rendering defect and that the regression test it left
behind passes; its costs are all on the other side of that same line, where
rendered output had to be seen. So the crossing `D-KNW-017` queues has to reach
the caller who wants to know whether a page came out right, without taking the
below-the-UI defect with it.

No todo is derived. Every step this feedback would name is a step on a sibling's
card, and archiving is the question `002951` and `003103` already carry.

## Since then

The first **Wrong if** fired on the fourth summary judged this way, and the
mapping is what found it rather than what missed it.
`feedback/2026-08-01-114807` is a numbered transcript of a GPT-5 mini session
reviewing one core patch in `/home/benji/projects/typo3-cms`. Five of its halves
map: the `typo3_project_describe` strength and the `processingTaskTypes`
suggestion onto `114526` and `115220` from the same session, the seven classes
read by hand onto `114526`, and an editor's `grep_search` timeout onto the
boundary `doesNotCover` already draws. The sixth maps onto nothing.

That sixth is a failed test run and what the session concluded from it, and it
is the one thing in the file no sibling states. `114526` mentions the same
failure in a clause and files it under "not MCP issues"; only the transcript
says what it would do instead, which is a runner the core checkout does not
contain. So the ladder was owed to that half alone, and it is
[`D-ANS-031`](../answers/ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md).

Two things about the mechanism hold up. A summary is where a session states a
conclusion, and its siblings are where it states subjects — which is why a
detailed transcript orphans a half more readily than a roundup of suggestions
does. And the mapping is cheap enough to run first even when it fails: five rows
of it stood, and the ladder was walked over one row rather than over the file.

Archiving is not the question here that it is above. This summary has a half
nobody else owns, so it stays open behind that half's todo, and
[`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
invariant holds without the sixth answer the three cards in `todo/waiting/` are
waiting on.

## Since then

The second **Wrong if** fired, and the three summaries were archived without the
sixth answer having been given.

Every sibling has landed. `002745`, `002926`, `002928`, `002930`, `003003` and
`003448` were archived on 2026-08-02, `003000` and `003929` on 2026-08-03, and
the halves the other two summaries name — `003356`, `003533`, `003736`,
`003933`, `003937`, `003938` — with them. So the branch the cards named as the
conservative one is satisfied on its own terms: `closed` stands over subjects
that were each worked off, which is what
[`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
asks of the archive, and the question the cards were blocked on did not have to
be answered to get there.

What held them open is what the **Wrong if** describes. Each closing commit
archives the feedback it worked off and nothing else, so no commit reached the
summaries, and nothing reported the state: `bin/cli todo:check` said
`0 problems` while three cards waited on a question the work had already
overtaken. The cards are deleted by the commit that archives the feedback, which
is the invariant's other half.

Archiving on supersession is still undecided, and the case for it has changed
shape rather than gone away. What the cards weighed was whether `closed` may
stand over an open subject; what this shows is that the alternative is held by a
watch nobody keeps.

## Since then

The first **Wrong if** fired on a fifth summary, and the orphaned half is not a
subject the ladder can be walked over. `feedback/2026-08-17-211826` is a
measured profile of one build session in `/home/benji/projects/site-demo` — a
TYPO3 v14 sitepackage plus a distribution extension carrying the content, 215
tool calls across 148 requests, 36 of them against this server, $29.59 at Opus 5
list rates. The mapping is the widest in this entry, because the session filed
one report per subject and twenty of them are still open:

| Half of the summary                                                      | Where it is                                                        |
| ------------------------------------------------------------------------ | ------------------------------------------------------------------ |
| `availableHints` is 78% of what `typo3_hint_lookup` transferred          | `212300`, which the session split out of this file for that reason |
| nine debugging cycles, ~45 round trips, the largest cost item            | `212010`, which states the same count                              |
| the index abandoned the moment a symptom appears                         | `205945`                                                           |
| `page-content-areas` fetched after the exception it would have prevented | `211306`                                                           |
| `project-build-and-scripts` fetched only during the review               | `211118`                                                           |
| the trusted-hosts cycle                                                  | `205850` and `212702`                                              |
| the stale TCA cache cycle                                                | `212117`                                                           |
| the Fluid argument-type cycle                                            | `212100`                                                           |
| three hints carrying an incorrect statement, and the partial collision   | archived: `205817`, `204536`, `211418`, `205759`, `205830`         |
| the whole-session profile                                                | nowhere else                                                       |

One row of the ten is answered rather than carried, and five archived siblings
are what answered it. `c595d790`, `fde155a9`, `30ba620d` and `f3219aba`
corrected the four hints between the report and this judgement, each leaving an
assertion in `HintsTest` behind it. So the sentence in the profile that counts
three hints as carrying an incorrect statement was already out of date when it
was read.

**The orphaned half of a measured summary is the measurement.** The ladder's
five steps each name something missing, misplaced or misworded, and a number is
none of them — which is the position
[`D-FBK-018`](fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
puts a strength in. So the **Wrong if** fires in form and not in substance: the
summary is the only report of its subject, and what is owed is that the number
is written where a later change can be measured against it, not that the ladder
is walked. It is in
[`D-FBK-020`](fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md),
which is where this repository already keeps a per-run cost reading.

The summary's own query reaches less than its halves do, for the fourth time.
`bin/cli hints:probe` on it matches `content-elements` and `sitepackage-layout`
and nothing else, against the 21 hints the session fetched by id — the dilution
[`D-ANS-021`](../answers/ans-021-a-manual-query-is-told-what-short-buys.md)
names, measured on a probe as it was on `002951`.

This feedback is archived by the commit that writes the measurement, and that
does not answer the question the three summaries above are waiting on. They were
held open over subjects nobody had worked; this one has a half that nothing else
reports and that half lands here. What `closed` stands over is therefore the
thing only this file said, and the nine subjects beside it are each named on a
card of their own.

## Since then

The first **Wrong if** fired on a sixth summary, and the orphaned half is a
conclusion rather than a subject. `feedback/2026-08-17-212218` is from the same
build as the fifth and names three prescriptions it executed in part — a step
naming two hint ids, the closing sentence naming a hint's neighbours, and the
five bullets of *Verify at the right layers* — with the repetition stated as the
finding:

| Half of the summary                                                                            | Where it is                                                                                                                                            |
| ---------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| step 5 of `typo3-development-installation` named two ids under one clause                      | archived `211118`, judged in [`D-SKL-044`](../task-skills/skl-044-a-step-that-names-two-hint-ids-says-what-each-one-alone-answers.md)                  |
| the closing neighbour sentence read at the moment the answer lands                             | archived `211306`, judged in [`D-KNW-087`](../knowledge/knw-087-a-listed-neighbour-says-what-it-prevents.md)                                           |
| the browser bullet of the verification section named no lookup                                 | `205945` case four, judged in [`D-SKL-045`](../task-skills/skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md), held by `R-SKL-024` |
| a multi-item prescription in prose has nothing at the end asking whether the list was finished | nowhere else                                                                                                                                           |

Three of the four rows are answered rather than carried, and each was corrected
on 2026-08-18 — `7dab8ef8`, `9111d6a8` with `1f189b5f`, and `4ec22687`. So the
mapping is what dates the summary rather than what closes it: every example in
it describes a file that has since changed at the step it names.

The fourth row is the ladder's step 5 and it is
[`D-SKL-049`](../task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-the-corrections-it-would-sit-on.md).
What that adds to the mechanism is where a summary's own conclusion goes. A
roundup orphans a subject and a measurement orphans a number; this one orphans
the reading the session drew across its three reports, and that reading is the
one thing the siblings cannot carry, because each of them is one instance of it.
The summary stays open behind it, which is `D-FBK-017`'s invariant with the card
in `waiting/` rather than in `open/`.

## Since then

The first **Wrong if** fired on a seventh summary, and its orphaned half is a
conclusion about the debrief rather than about the server.
`feedback/2026-08-18-071603` is from a boot of the `t3g/blog` repository in
`/home/benji/projects/blog`, and its subject is how its own siblings came to
exist: which question of the debrief produced which finding. That makes the
mapping unusually exact, because the feedback names the pairs itself:

| Half of the summary                                                                | Where it is                                                                                                                                                                                                                                     |
| ---------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| "which calls would you not make again" → the two `typo3_project_describe` answers  | archived `070333`, judged in [`D-ANS-085`](../answers/ans-085-the-project-answer-is-owed-by-the-repository-not-by-the-installation-in-it.md)                                                                                                    |
| "which documents did you read whole" → the guides                                  | archived `070538`, which names the page it wanted; its list-was-never-rendered half was trimmed on 2026-08-18 into [`D-ANS-085`](../answers/ans-085-the-project-answer-is-owed-by-the-repository-not-by-the-installation-in-it.md) and `074226` |
| "what did you never put to it" → `typo3_extension_describe`                        | archived `070611`, corrected and taken further by `071500`                                                                                                                                                                                      |
| "where did a name not mean what it said" → the `--create-site` tagging             | archived `070632`                                                                                                                                                                                                                               |
| "what did it save you from" → the ddev restart for `additional.php`                | archived `070515`                                                                                                                                                                                                                               |
| the undercount: five checkout calls, two of them empty, and the hint never fetched | `071500` and `071435`                                                                                                                                                                                                                           |
| "are skills missing" → two installation intents, one skill                         | `071526`                                                                                                                                                                                                                                        |
| "is documentation missing" → answered with nothing                                 | nowhere, and deliberately: the session made no call it could report on                                                                                                                                                                          |
| feedback quality is a function of the debriefing, not of the session               | nowhere else                                                                                                                                                                                                                                    |

Eight of the nine rows land, five of them on siblings that were already
archived. The ninth is the conclusion, and the ladder was walked over that row
alone: it reaches step 1b, and it is
[`D-FBK-048`](fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md).

What this adds to the mechanism is that a summary can be evidence about the
prompt that produced it. The other six report on the server; this one reports on
the instrument, and the pairs it names are the only measurement anybody has of
which question yields what. Two of them are already
[`D-FBK-047`](fbk-047-the-debrief-asks-what-an-answer-left-out-and-what-the-session-wanted.md)'s,
which is why that entry carries the clauses and this one carries the mapping.

## Since then

An eighth summary was judged by the mapping on 2026-08-18, and its orphaned half
leaves the series rather than staying in it. `feedback/2026-08-18-080710` is a
whole session in `/home/benji/projects/blog` — the `[blog.isPost()]` TypoScript
conditions of `ext:blog` repaired on TYPO3 v14 while they had to keep working on
v13 — and the number it reports is zero: not one call to this server in the
work, with `typo3_server_scope` and `typo3_feedback_list` called afterwards for
the debrief. Seven of its eight halves land on reports from the same directory,
five of them from the same twelve minutes:

| Half of the summary                                                                           | Where it is                                                                                                                                                        |
| --------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| the checkout won on the merits, and `doesNotCover` "PHP source as code" says so               | `080743`, the same session's strength report                                                                                                                       |
| every tool arrived deferred, so a first call costs two round trips                            | `074627`, judged in [`D-SKL-060`](../task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it-and-a-deferring-client-is-answered-in-the-instructions.md) |
| nothing in the task named a subject the server advertises loudly                              | `080630`, on the upgrade skill's description, and `081159` on when a skill is matched                                                                              |
| middleware order and the moment a superglobal is populated, assumed uncovered and never asked | `081228`, which asks for that document by stage and by major                                                                                                       |
| running the other declared major, and proving the verdict against a frontend                  | `081129` and `081100`                                                                                                                                              |
| the guides list was never rendered to the session                                             | archived `074226`                                                                                                                                                  |
| the report's own observation was cut at 4000 characters                                       | `080803`                                                                                                                                                           |
| `typo3_changelog_lookup` is the call it would make next time, on discoverability              | `113308`, in `/home/benji/projects/bootstrap_package`                                                                                                              |

**A session that made no call is evidence about the entry surface, and about no
answer below it.** Every rung of the ladder under that surface needs a call to
have been made, so a zero-call report leaves them untested rather than passed —
which is what keeps this one from being read as a lookup that answered badly.
The two halves it does carry are both above the line: the boundary the session
read and agreed with, which is
[`D-FBK-018`](fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)'s
shape and `080743`'s subject, and the price of the first call.

The orphan is the ladder's step 2. The line the feedback names as the one that
would have caught it — `routing`, starting work on a major you have not built on
recently, before asking what a version changed — has stood in
`knowledge/server-scope.json` since `2acd8e4d` on 2026-07-29, and it is
reachable only through `typo3_server_scope`, which is a call. The channel that
survives schema deferral is the `instructions`, and `D-SKL-060` left the
question of what they should name to `113308` on purpose. So the half travels
there rather than to a card of its own, and the corpus behind that card is now
two directories and two task shapes.

The feedback's first suggestion is answered and was trimmed. It asks for the git
invocation that settles a cross-major question to be named in an answer instead
of described, which is what
[`a-declared-major-that-is-not-installed`](../../knowledge/documents/extension/compatibility/a-declared-major-that-is-not-installed.md)
does — written at 13:16 the same day, six hours after the session, and routed to
from `knowledge/server-scope.json`. Which branches a caller has checked out
stays outside, because `doesNotCover` says this server never runs git.

Nothing is missing from the corpus either, and the probe is later than the
session again. `bin/cli hints:probe` on the task's own words returns
`typoscript-conditions` at `appliesTo(20) + text(180)`, a hint written on
2026-08-18 in `899dbdbb` and `3b7a0939`.

No todo is derived. The card in `todo/progress/` is deleted by this commit and
`todo/open/2026-08-18-113308` names both feedback in its `Serves:` line, which
is
[`D-FBK-040`](fbk-040-the-card-a-judgement-folds-into-another-is-deleted-by-the-same-commit.md)
rather than the `waiting/` the first three summaries went to. The feedback stays
open behind it: what it reports is still there, and archiving is what tells its
author otherwise —
[`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md).

## Since then

A ninth summary was judged by the mapping on 2026-08-18, and it is the first one
the mapping **closes**. `feedback/2026-08-18-081228` is the round-trip
accounting of the session above — roughly 30 calls, all Bash, counted from the
transcript and grouped by what they established — and the row the eighth summary
handed it is the one that had not landed yet. It has since.

| Half of the summary                                                                                              | Where it is                                                                                                                                                         |
| ---------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 13 round trips: what a condition can see, which global is populated when, and the event ahead of matching        | archived `080532`, judged in [`D-KNW-101`](../knowledge/knw-101-what-a-typoscript-condition-can-reach-at-evaluation-time-is-a-gap-this-server-owns.md)              |
| 1 of those 13 ruling out a request-scoped service, and 2 on constructor injection through `makeInstance`         | archived `080604`, judged in [`D-KNW-100`](../knowledge/knw-100-how-an-extension-extends-a-typoscript-condition-is-a-gap-this-server-owns.md)                       |
| 7 round trips proving the verdict against the running frontend, filed separately by the session itself           | archived `081100`, judged in [`D-KNW-102`](../knowledge/knw-102-proving-a-typoscript-condition-verdict-against-a-running-installation-is-a-gap-this-server-owns.md) |
| 2 locating changelog entries by listing a directory, where `typo3_changelog_lookup` would have searched by words | `113308`, which the eighth summary already sent its discoverability half to                                                                                         |
| 2 on the caller's own conventions — `Services.yaml`, a `Listener` directory, the existing test style             | its own repository, which `doesNotCover` puts outside                                                                                                               |
| the two core directories asked for as `typo3_reference_list` entries                                             | [`D-CAT-005`](../catalog/cat-005-a-reference-entry-names-a-form-to-imitate-and-a-fact-is-answered-by-a-hint.md)                                                     |
| the measurement: 13 of roughly 30 on one uncovered subject                                                       | [`D-FBK-020`](fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)                                                                           |

Four of the seven rows are answered rather than carried, and the three hints
behind them were written between 16:22 and 17:15 the same day — six hours after
the session filed at 10:12 local. So the mapping dates this summary the way it
dated the sixth, except that here the dating is the whole judgement: the
feedback asks for the frontend lifecycle "by stage and by major" and names the
questions it must answer directly, and every one of them now reaches a hint.
Probed in this checkout, "what does a TypoScript condition see at evaluation
time" returns `typoscript-conditions` at `appliesTo(20) + text(193)`, "is
TYPO3_REQUEST populated before TypoScript conditions are matched on v14" at
`appliesTo(33) + text(240)`, and "which PSR-14 event fires before TypoScript
condition matching" at `appliesTo(20) + text(133)`.

**What is left of the suggestion is a shape rather than an answer.** A table
keyed by every stage of the frontend stack would say at each one what is
assembled, and only one of its rows has ever been paid for. Nobody has reported
a cost at the others — the corpus carries no second report of a middleware or
lifecycle question — so building the rest is the speculation
`documentation/records/judging.rst` rules out when it says one session's
suggestion is a suggestion. The row that was paid for is written, and the next
report is what would add another.

This summary is therefore *already answered* and archived by this commit, which
is the outcome the second **Wrong if** describes from the other side. What held
the first three open was that each closing commit archives only the feedback it
worked off; here the judgement of the summary is the commit that reads the
siblings' landings against it. The card in `todo/progress/` goes with it, and no
todo is derived.

## Since then

The nine readings above were judged on 2026-08-22 against `D-DOC-041`, and none
of them is collapsed. Each carries the mapping of one summary, which is what the
first **Decided** bullet says this file is for — a mapping written down once, so
that a second judgement of the same session does not derive it again. What a
reader pays for the history here is what that bullet bought.

Two of the three **Wrong if** are struck above, and what the readings after the
third added instead is where an orphaned half goes. A transcript orphans a
subject, a measurement orphans a number, a prescription orphans the reading
drawn across three reports, a debrief report orphans a conclusion about the
instrument, and a session that made no call orphans the entry surface.

One observation recurs and is owned elsewhere. That a summary's own query
reaches fewer hints than its halves do was measured four times, the last of them
on the fifth summary, and every one of them landed on `D-ANS-021`. A tenth
summary need not measure it again.
