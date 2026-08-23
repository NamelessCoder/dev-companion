---
id: D-SKL-004
title: A question no lookup settles is read from the installed source
date: 2026-08-02
status: open
coveredBy:
  - SkillTest::theInstalledSourceIsTheStepAfterTheLookups
---

# D-SKL-004 — A question no lookup settles is read from the installed source

**The order's answer to a question no lookup settles is a finding that says so.
A session that has to produce working markup cannot write one, and the installed
TYPO3 source is named nowhere as the step after it.**

`feedback/2026-08-01-003933` reports a session that guessed at a ViewHelper
contract and changed the markup until the user corrected it. Its sibling
`003356`, filed by the same session three minutes earlier, reports that the same
session read vendor source directly and calls that the reverse of the workflow.
Both are costs, and the boundary between them is where the reading sits in the
order. Nothing here states that boundary.

## Evidence

- The instance the feedback names is answered. `bin/cli hints:probe` with the
  query of the `003448` sibling — *f:if with f:else but no explicit f:then
  swallows the inline then-branch / f:link.typolink output* — reaches
  `fluid-templates` at `appliesTo(16) + text(132)`, and that entry now carries
  the branch rule with the working markup as its example.
  [`D-KNW-016`](../knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-subject-this-server-owns.md)
  wrote the statement and
  [`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
  is what makes a query written in Fluid tags reach it. `003448` is archived. So
  the source reading this feedback holds up as the example would not be needed
  today.
- What is left reaches nothing that answers it. This feedback's own query —
  *reading viewhelper source (IfViewHelper) when unable to determine expected
  behavior* — reaches `fluid-viewhelpers` at `appliesTo(10) + text(68)`, alone.
  That entry says what a ViewHelper class looks like and what its arguments are
  checked against. It does not say where a behaviour question goes once the
  lookups have been asked.
- `skills/base.md` names reading three times, and every one of them is about the
  project's own checkout or is a prohibition. "Do not fall back to general TYPO3
  knowledge or start reading the checkout" is the answer for a server that is
  not there. "**Then** read the checkout. Not before" orders the project's files
  against the lookups. Step 5 adds that "the installed core shows what one
  version implements rather than what it supports", which is a limit on a
  reading rather than an instruction to take one.
- The one sentence for the exhausted case is written for a review. "Where the
  manual has no page for it either, the finding says the question could not be
  settled." The session that filed this was building a content element in
  `site-new`. It had no finding to write and a template that had to render, so
  the sentence is not addressed to it.
- The skill this session names, `typo3contentelementdevelopment`, points the
  same way. Its reading bullet is "Read the nearby content elements, TCA files,
  TypoScript imports, templates, assets, schema and tests — the project's file
  organization is the thing a new element has to fit, and only the checkout has
  it." The installed TYPO3 is not among them.
- [`D-ANS-010`](../answers/ans-010-does-it-still-work-is-a-question-for-the-manual-not-the-changelog.md)
  is the only entry that decides anything about this reading, and it decides
  against it: a miss in the manual "is a finding rather than a licence to
  reconstruct the contract from the installed core". Its first **Wrong if** did
  not fire here. That one asks for a session that follows the routing, calls
  `typo3_documentation_lookup` at the target version and still reads the core by
  hand; this session called neither before the user asked it to.

## Decided

- **Step 4 of the ladder**, wording, on `skills/base.md`. Not 1a: the statement
  that would have prevented the named instance landed, and this feedback adds
  nothing about TYPO3 that is missing. Not 3: the routing that kept the query
  from the entry landed with `D-KNW-024`.
- **Queued, not closed on the spot.** `skills/base.md` is a skill contract, and
  [judging.md](../../documentation/records/judging.rst) puts that on the
  reviewed side. `D-ANS-010` queued its own skill half for the same reason.
- The feedback is **trimmed**. Its example is answered by the archived sibling
  and stays out of the card; what the card carries is the step after the
  lookups, which is the half no entry states.
- **Not the feedback's own wording.** "Read the source before guessing" as it
  proposes would break `D-ANS-010`'s boundary. The installed source says what
  this one installation does, and never what TYPO3 supports. A sentence that
  does not carry that distinction licenses exactly the reconstruction
  `D-ANS-010` refused, and the two entries would then say different things.
- The other lever is named and not taken here: a tool that resolves behaviour
  out of the installed source, which is what `D-ANS-010`'s first **Wrong if**
  reserves. This feedback does not establish it. The behaviour its session
  needed is in the corpus now, so what it shows missing is the named next step
  rather than an answer.

## Assumed

- That `skills/base.md` can carry another sentence at all.
  [`D-SKL-001`](skl-001-the-order-a-task-starts-in-is-one-file.md) watches its
  growth — 496 words when it was written, 960 after the sweep, 1099 now — and
  every sentence added is one the reading can swallow. Where the sentence
  displaces rather than adds is the card's first step, not this run's.
- That a sentence there would have reached this session. It would not have:
  `003356` records that no skill activated in that run at all. The activation
  half is that sibling's, held in `todo/waiting/` behind its own question, and
  the `D-AUD-003` description rewrite of 2026-08-02 is what stands against it.
  This entry is right about the order and says nothing about the reach.

## Wrong if

- A session reaches the sentence with the lookups exhausted and still reports
  that it reconstructed the behaviour by trial and error. Then the named step is
  not the lever and the tool `D-ANS-010` reserves is what was missing.
- A feedback reports the opposite cost once it lands: a session that read the
  installed core early because the base named it, and carried what one version
  implements into an answer as though it were what TYPO3 supports. Then the
  distinction did not survive the wording.
- The same task shape files again with a skill active and the Fluid statement in
  reach, and still names source reading. Then it is the activation rather than
  the order, and the lever is `003356`'s.

## Since then

The step landed on 2026-08-03, as a section of its own rather than in place of
the sentence this entry names. `## When the lookups run out` stands after
"**Then** read the checkout", where the base already orders a reading against
the lookups, and it says what answers the question, what the reading replaces,
and what it may not be carried into: the class that implements the behaviour and
the one it inherits from, changing the code until it works, and what TYPO3
supports.

Step 5 was the other candidate and it is where the reading would not have been
found. Its paragraph asks "does this still work in version N", and the session
this entry judges held a question about what an unaltered ViewHelper does — the
mismatch
[`D-ANS-010`](../answers/ans-010-does-it-still-work-is-a-question-for-the-manual-not-the-changelog.md)
already recorded against the conformance skill's narrower condition, a session
holding a behaviour question does not match itself against a condition written
for another one. What step 5 did give up is the displacement this card asked
for. The review-only sentence is gone from it — a miss in the manual is a result
and not an answer there now — and the limit that stood beside it moved into the
new section, where the reading it limits is the one being ordered.

The **Assumed** above was measured and its arithmetic was two commits stale.
`wc -w skills/base.md` reads 496 at `66813e3`, 1099 at `1960e50` — the number
this entry carries — 1188 at `4767362`, 1367 at `0fac7c6`, and 1452 with this
change. So the tag bounding of 2026-08-02 spent 179 words after this card was
queued, and the budget the card was measured against was not the one it landed
in.

What the wording says about the installed source was read rather than recalled,
in `/home/benji/projects/site-new/vendor` on 2026-08-03. `IfViewHelper` ships in
`typo3fluid/fluid` and not in `typo3/cms-fluid`, which is why the section says
the installed source rather than the installed core. Those packages ship
`Classes/` and no `Tests/`, so a step naming the tests beside the class would
have named something an installation does not have — the class and the one it
inherits from is what is there, and `IfViewHelper` carries the branch contract
in its docblock, with `<f:then>` explicit in every `f:else` example it gives.

## Since then

A second session reached the same shape from the review side, three minutes
before the section landed. `feedback/2026-08-03-144457` reviewed core commit
9f6c6eb9093 and settled three questions by grepping its checkout: whether the
`/typo3/` URL prefix still exists on `main`, whether `#[Autowire(lazy: true)]`
is an established core pattern, and whether `LinkService` builds its handlers
eagerly.

The section reaches the first and the third and not the second. Both of those
ask what a named class does, which is what "the class that implements the
behaviour and the one it inherits from" answers, and the first is step 5's
before it is the section's — the session says so itself: it asked
`typo3_changelog_lookup` where the base routes "does this still hold in version
N" to `typo3_documentation_lookup`, and it counts that skip as its own. Whether
an idiom is established is neither. It is a sweep for call sites, it has no
class to start at, and a reviewer asks it of every alternative it proposes,
which is the count that feedback carries.

That boundary is stated, in `knowledge/server-scope.json` — *PHP source as code:
a method signature, whether a class or member is @internal or public API, an
implementation to copy* — and only `typo3_server_scope` returns it. The session
called no scope tool and `typo3-core-patch-review` orders none, so what is left
of the feedback is step 2 rather than 1a: the sentence exists and does not pass
where the task does. Queued as `2026-08-03-144457` at `normal`, which is what
the two placements and the count set; the feedback stays open behind it.

The review skill already carries that sentence one subject over. Its
changelog-precedent step says the precedent "is still there and the checkout is
what holds it — `Documentation/Changelog`, which this server does not read and
you do", and asks the review to say which of the two answered. The code
precedent is the same sentence with the corpus changed, which is what makes the
placement a choice between two files rather than a wording nobody has.

The dependency-injection slice of that session is not this card's.
`knowledge/hints/di.json` carries `#[Autowire]` and not the lazy form, so a hint
that would have answered the second question is missing — that is 1a, and it is
reported by the sibling `feedback/2026-08-03-144410`, which is in hand on its
own branch.

## Since then

The second question got its placement on 2026-08-03, and it is the review skill
rather than the base. What decides between the two is who asks: a reviewer asks
whether an idiom is established of every alternative it proposes, and a session
that is building something asks how the core wires a subsystem — which is
`typo3_hint_lookup` at step 4 and has a home already. The bar is the reviewer's
too, because a recommendation to a core reviewer needs precedent rather than
taste, and nothing a construction task produces is held to that. So the sentence
is a bullet in `skills/typo3-core-patch-review/SKILL.md` beside the
changelog-precedent step whose wording it shares, and `skills/base.md` does not
grow — which is what
[`D-SKL-001`](skl-001-the-order-a-task-starts-in-is-one-file.md) watches, and
its arithmetic stays at 1452 words.

The bullet says what the base's own step cannot reach and why: that step starts
at the class implementing a behaviour, and this question has no class to start
at. It carries no core identifier, because the attribute it was measured on is
the fact no release of this server corrects.

Both halves were read rather than recalled. In `.checkouts/main` on 2026-08-03
the lazy autowire attribute stands at
`core/Classes/Site/Set/SetRegistry.php:43`,
`form/Classes/EventListener/DataStructureIdentifierListener.php:68` and
`form/Classes/Domain/Configuration/PersistenceConfigurationService.php:41`, five
occurrences across the three, while `knowledge/hints/di.json` carries the plain
attribute alone. So the checkout answered and no lookup here did, which is the
sentence the feedback asked for rather than the tool it offered second — and
that tool is declined in
[`D-ANS-003`](../answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md),
whose reading ends by handing this sentence here.

The feedback's own remaining doubt is settled with it. It asks whether
`typo3_documentation_lookup` would in fact have answered its first question, and
run through `bin/typo3-dev-companion` on 2026-08-03 it does not: at `14.3` the
queries *backend entry point URL path* and *backend routing request context*
return the routing pages of TYPO3 Explained, and none of them states the backend
entry path, which sits in `backend/Classes/Routing/UriBuilder.php:199`. That is
this boundary from the other side rather than a second gap, so the feedback is
archived rather than trimmed. The same two queries at `main` answer
`version-not-covered` with the reason *TYPO3 main is outside the covered
versions: 12.4, 13.4, 14.3, main*, which names `main` on both sides of itself.
That is a defect in the miss and it belongs to nobody's entry yet; it is
recorded here because this run is where it was seen.
