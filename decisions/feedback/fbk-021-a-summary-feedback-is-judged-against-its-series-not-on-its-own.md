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

  | Half of the summary                       | The sibling that owns it            |
  | ----------------------------------------- | ----------------------------------- |
  | `f:if` needs an explicit `f:then`         | `003448`, with the working markup; `003000` |
  | preview data is the record, not TypoScript | `002926`, `002928`, `002930`, `002745` |
  | Record API field access                   | `002928`, whose query is three Record API phrasings |
  | test request type and dataset priming     | `003003`; `003929` for the database half |

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

- A half turns out to have no sibling. The mapping is then wrong, the summary is
  the only report of that subject, and the ladder is owed after all.
- Every sibling lands and the summary is still open, because each closing commit
  archives only the feedback it worked off. The summary would then need an owner
  rather than a mapping.
- A session judges a summary by walking the ladder anyway, and writes the entry
  that a sibling's todo writes again a week later. That is the failure this is
  written against, and nothing checks for it.

## Since then

`003103` — the second summary this entry named — was judged on 2026-08-02 by the
mapping rather than by the ladder, and the first **Wrong if** did not fire. Its
**Suggestion** has five clauses where `002951` had four, and every one of them
lands on something:

| Half of the summary                                    | Where it is                                              |
| ------------------------------------------------------ | -------------------------------------------------------- |
| what a v14 preview template receives, and how a field resolves | [`R-KNW-041`](../../requirements/knowledge/knw-041-a-preview-template-answer-says-what-the-template-is-handed.md), from [`D-KNW-014`](../knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md) |
| `typo3_documentation_lookup` reaching *Record objects*  | `002928`, whose premise [`D-ANS-021`](../answers/ans-021-a-manual-query-is-told-what-short-buys.md) rejected |
| the Fluid conditional, and object access on the record  | `003448` and `003000`; the record half is `R-KNW-041`      |
| a debug script the container could not see              | `003938`, with `003933` for what to do instead             |
| one cache command instead of `rm` on the cache directory | `003937`                                                  |

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

| Half of the self-rating                                                                                                | Where it is                                                                          |
| ---------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| §1 `references/base.md` unread, neither scope call made, remembered `composer test:*` in place of the declared commands  | `003356`, judged in [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md) |
| §2, §3, §5 rendered frontend and backend preview established by curl, the project's own `Tests/E2E` harness untouched, no spec run | `003533`, judged in [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md), with a card in `todo/open/` |
| §4 tests written against core ViewHelpers to find out what they do                                                       | `003933`, which reports the same substitution of experiment for reading                |
| §4 `fwrite` and `extract` debug left in the regression test, and a throwaway `LinkDebugTest`                             | `003938`, whose **Suggestion** asks for the safe way to introspect an installed class  |
| the premise that the skill was never activated                                                                           | withdrawn by `003736`; `D-KNW-017` records the withdrawal and reads the trigger out    |

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
map: the `typo3_project_scope` strength and the `processingTaskTypes` suggestion
onto `114526` and `115220` from the same session, the seven classes read by hand
onto `114526`, and an editor's `grep_search` timeout onto the boundary
`doesNotCover` already draws. The sixth maps onto nothing.

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
