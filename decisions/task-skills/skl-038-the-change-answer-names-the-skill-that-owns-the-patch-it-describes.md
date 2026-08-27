---
id: D-SKL-038
title: The change answer names the skill that owns the patch it describes
date: 2026-08-14
status: open
coveredBy:
  - ForgeTest::aPageOfTheBacklogIsHandedTheWorkflowThatOwnsIt
  - ForgeTest::theRecentEndCarriesNoTriageWorkflow
  - ForgeTest::theWorkflowStandsUnderThePageOfCandidates
  - GerritTest::aNamedChangeIsHandedTheWorkflowsThatOwnIt
---

# D-SKL-038 — The change answer names the skill that owns the patch it describes

**`typo3_gerrit_lookup` names the two core patch skills and the call the order
opens on, where a caller named one change.**

That answer is the one thing a review session which opened no skill did ask for,
and it hands back a ref and a remote and nothing about the workflow it has just
begun.

## Evidence

- **The session.**
  [`feedback/archive/2026-08-12-092545`](../../feedback/archive/2026-08-12-092545-a-german-language-review-request-activated-no.md),
  `/home/benji/projects/typo3-cms` on 2026-08-12, `claude-opus-5[1m]`. The brief
  was a German request naming Gerrit change 95169 by number and by review URL.
  No skill activated at any point, `typo3_project_describe`'s schema was loaded
  and the tool never called, `typo3_task_guide` and `typo3_server_scope` were
  never reached, and the client rendered no resource list. What it did call was
  `typo3_gerrit_lookup`, which handed it the ref and the remote it then fetched
  the patch set with by hand.
- **The second session of the shape, and one hypothesis fewer.**
  [`feedback/archive/2026-08-10-182404`](../../feedback/archive/2026-08-10-182404-a-review-request-quoting-the-skill-s-own.md)
  is the same request shape in the same checkout, judged as
  [`D-SKL-033`](skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md).
  That report named two things that plausibly kept the skill shut: the language,
  and a request naming a local commit rather than a change on the review server.
  This one named the change, by number and by URL, and the skill stayed shut. So
  the second of the two is gone and the first is where it was.
- **What the answer carries today.** Read in this checkout on 2026-08-14:
  `GerritLookup::answer()` ends on two sentences, one holding the commit against
  `git rev-parse HEAD` and one saying the fetch goes to the review server rather
  than to `origin`. Both are about the checkout. No class below `src/Tool/`
  names a skill at all — `typo3_task_guide` is the only route into one, and it
  is data (`D-SKL-013`).
- **The route this server has names the wrong workflow for this brief.**
  `TaskGuide::answer()` run here on 2026-08-14 with the feedback's brief
  verbatim matches the `breaking` intent strongly and `patch-checkout` weakly,
  and names `typo3-extension-upgrade`. The same request in English — "review
  core patch 95169 and say whether it is breaking" — names
  `typo3-core-patch-development`. `typo3-core-patch-review` is carried by the
  `audit` intent alone, whose needles are "review the", "review this", "review
  of" and "reviewing", and neither brief contains one of them.
- **The same shape one level down is already decided.**
  [`D-ANS-061`](../answers/ans-061-an-answer-that-names-a-document-hands-it-over.md):
  a `uri` in an answer is not delivery, and the lever is the tool the session
  does call rather than the one it should have called.
  `TestRunGuide::SCRIPTS_GUIDE` and `BROWSER_CHECK_GUIDE` are what came of it,
  both placed at the moment the caller is certainly reading.
- **The corpus reaches nothing on that brief.** `bin/cli hints:probe` with the
  request verbatim matches no hint and returns the index, detecting the domain
  `php`. Everything below `knowledge/` is English and this server says so three
  times, so that is a property of the matcher rather than a gap this session
  found.

## Decided

- The `change` form of `typo3_gerrit_lookup` names `typo3-core-patch-review` and
  `typo3-core-patch-checkout`, and `typo3_project_describe` as the call the
  order opens on. A caller holding one change is about to review it or to fetch
  it, and those two workflows own it.
- The `issue` form takes none of it. "Has somebody already fixed this" precedes
  triage, patch development and review alike, and `D-SKL-013` already declines
  to route the `submission` intent for spanning two skills. The same holds for
  `typo3_forge_lookup`, which is that question one host over.
- Not `typo3_server_scope`. Two sessions finished a task without calling it for
  the same stated reason, and naming a tool nobody invokes is what `D-ANS-061`
  ruled out. What the tail names is a workflow and a call, and both are acts.
- Against a sweep of the other tools' answers. One tool and one moment:
  `D-SKL-013`'s third **Wrong if** is what a route invented for a row nobody
  asked for costs.
- The descriptions stay as they are. `D-SKL-033` weighed the wording and the
  German trigger words, and this feedback adds a session to its evidence rather
  than reopening it.
- **The route is repaired too, and separately.** A review request naming a
  change number reaching an upgrade or a development workflow is a defect in
  `knowledge/task-intents.json` rather than in this answer, and which repair it
  takes — widening `audit`, or `breaking` not routing where the brief is a
  review — is a reading of the matcher that a card of its own carries.

## Assumed

- That a session given a skill's name in an answer loads it. Nothing here
  measures that; what is measured is sessions not acting on a bare uri
  (`D-ANS-061`), and `D-SKL-013` carries the same assumption for the guide.
- That the moment is what a description cannot have. The tail arrives after the
  session has committed to the task and asked a question of its own, rather than
  competing for the brief, and nothing here can see whether that is read
  differently.
- That naming a skill a project has not installed costs nothing, which is
  `D-SKL-013`'s second **Assumed** unchanged.

## Wrong if

- A session reports reading the tail and reviewing by hand anyway. Then the name
  is one step short of the handover here too, and what is left is the order
  itself in the answer — the shape `TestRunGuide::SCRIPTS_GUIDE` took.
- A session that already had the skill open reports the tail as noise on an
  answer it asked a narrow question of. Then it belongs behind a condition the
  way the fetch sentence is, rather than on every change answer.
- A review session reports no skill and no entry point with this tail in place.
  Then the answering side is not the channel either, and what is left untried is
  the project's own agent instruction file, which `D-SKL-033` recorded.

## Since then

Built on 2026-08-14. `GerritLookup::workflow()` is the tail, on the `change`
form and where something came back, and
`GerritTest::aNamedChangeIsHandedTheWorkflowsThatOwnIt` holds what it names and
what it leaves out. A skill named in prose in a class is what no release of this
server corrects in somebody's project, so
`SkillTest::everySkillNamedByAToolIsPublished` reads every name below
`src/Tool/` against what the installer publishes — the guard
`everySkillNamedInKnowledgeIsPublished` gives the ones routed to from
`knowledge/`. It scans three names rather than two: the evidence above is one
out, because `typo3_feedback_record` already named `typo3-extension-conformance`
in its schema as the example a session reports a skill by. No session has read
the tail yet, so all three **Wrong if** stand as they were.

A second moment is named on 2026-08-18, and the bullet above it is what decides
that it is one rather than a sweep. `feedback/2026-08-17-213027` read
`manual: null`, `readme: null` and `tests: []` out of `typo3_extension_describe`
twice, wrote the manuals by hand and shipped no test, and each of those three
absences has a published skill that owns it. That is one tool and one moment
again — the caller named the extension, asked, and is holding the object — and
`D-SKL-053` carries the reading and the boundary.

The first **Wrong if** fired on 2026-08-24. `feedback/2026-08-24-122413` is a
review of Gerrit change 95179 in `/home/benji/projects/typo3-cms` on
`claude-opus-5[1m]`, and
`typo3_gerrit_lookup(change: "95179", messages: "people")` was among its first
calls. No skill opened at any point in that session, and `typo3_task_guide` was
never called either. The tail was in the answer it read: `workflow()` fires on
every answered `change` form, it landed here on 2026-08-14, and the session
quotes the `instructions` in the wording `D-AUD-012` gave them on 2026-08-19 —
so the server it talked to is later than both. Re-run in this checkout on
2026-08-24, the same call still ends on the two workflow names.

What the session reports is the answer's completeness rather than its tail: the
lookup "answered that so completely in one call that the work then looked like a
sequence of concrete edits rather than a workflow needing a procedure". It names
neither skill, so whether it read that far is not established. What is
established is that both names stood on the answer it then worked from, and that
neither was opened.

So the name is one step short of the handover, and what the **Wrong if** already
named as what is left is queued rather than decided here:
`todo/open/2026-08-24-122413`, at `normal`, weighs the order itself in the
answer in the shape `TestRunGuide::SCRIPTS_GUIDE` took. Two readings in the same
corpus bear on it. `feedback/2026-08-24-183447` is a session that read
`core/contribution/gerrit-workflow` whole and reports it as the model of a
document read — the procedure end to end, and no search after it — which is
content delivered at the moment of asking working where a name did not. That
session is also the second **Wrong if**'s first case: it held both skills open,
called this tool for change 91127, and reported the answer as what worked rather
than the tail as noise.

The order was built into the tail on 2026-08-24. `workflow()` keeps the two
names and the call, and carries four steps under them: establish the patch
before judging it, the three ways in and that a branch of your own naming is
none of them, that a patch which no longer applies is the finding, and that an
instruction to change the patch ends the review. Each is a step the reporting
session took by hand and took differently — it judged the diff without
establishing the target branch, it carried the patch onto `bugfix-81619`, and it
rebased onto `origin/main` as a matter of course. The last one names
`typo3-core-patch-development`, which is the third skill that session's request
matched and neither named workflow owns;
`SkillTest::everySkillNamedByAToolIsPublished` reads that name too.

What it costs is measured rather than estimated. `bin/cli tools:measure` on the
recording of the same day: `typo3_gerrit_lookup` went from 8,510 bytes of text
over three calls to 9,952, which is 721 bytes on each of the two `change`
answers and 6.1% of the tool, and its place in the listing did not move. The
whole of both workflows stays in the skills, and what the answer carries is the
steps that decide the result.

The second **Wrong if** is what that spends, and it stands unchanged as the
thing to watch: a longer tail is a larger interruption for a session that
already holds the skill, and its first case reported the answer as what worked
rather than the tail as noise. It was read against the tail of two lines, so the
next case is read against this one.

### 2026-08-25 — the backlog answer is a second moment, and the bullet that declined it was written for the issue form

`feedback/2026-08-24-173116` is `/home/benji/projects/typo3-cms` on
`claude-opus-5[1m]`, sent to fetch another old Forge issue and work it off. It
enumerated the backlog with `typo3_forge_lookup(open: "oldest")`, chose ten
candidates itself, and found four of them already fixed — by reading code, by
`git log -S`, and by writing throwaway functional tests. What it reports is that
no call answers whether a report still reproduces, so a triage of the old
backlog picks blind and verifies by hand.

The order that removes most of that cost was published in its checkout and
stayed shut.
[`typo3-core-issue-triage`](../../skills/typo3-core-issue-triage/SKILL.md) opens
on finding the candidates, states that age is a candidate and never a finding,
carries the five readings of
[`D-SKL-031`](skl-031-a-triage-picks-a-candidate-on-where-the-symptom-shows.md),
and hands the list over rather than choosing from it. The session did the
opposite of each. `feedback/2026-08-24-173236` is the same session's other
report and names it from the transcript: the skill was in its listing,
`typo3_task_guide`'s schema was loaded and the tool never called, and
`typo3_changelog_lookup` was never reached at all.

**Decided** declines this tail for `typo3_forge_lookup`, and what this section
corrects is the reach of that bullet rather than its reasoning. It rules on "has
somebody already fixed this", which precedes triage, patch development and
review alike, and adds the tracker as that question one host over. The `issue`
form is that question. The `open` form is not: it enumerates the core's backlog,
one workflow owns a caller holding it, and the `triage` intent in
`knowledge/task-intents.json` already routes that call to
`typo3-core-issue-triage`. So this is the ladder's step 3 with step 2's repair —
the route is in the data, and the placement that reaches the session is the
answer it did call, which is `D-ANS-061` again.

**The `open` form takes the tail and the `issue` form keeps none of it**, in the
shape `GerritLookup::workflow()` took above: the skill name, and under it the
readings that decide a candidate. The card serving `feedback/2026-08-24-173116`
carries it at `normal`. What it costs is measured rather than estimated —
`bin/cli tools:measure` in this checkout on 2026-08-25 reads
`typo3_forge_lookup` at 33,879 bytes of text over 14 calls, against the 721
bytes the Gerrit tail put on each change answer.

The feedback's own two suggestions are not built, and the boundary is why. A
call naming which of an issue's classes still exist in the checkout reads PHP
source as code, and one surfacing merged commits as candidate fixes runs git;
`knowledge/server-scope.json` declares both as what this server does not do.
What stays inside that boundary is `typo3_changelog_lookup` for whether the area
was reworked since the report, and `typo3_gerrit_lookup` for whether a patch
exists — steps 5 and 4 of the skill, and the two calls the session names as the
ones it never made. Its third fallback, a statement in `typo3_server_scope` that
reproduction belongs to the checkout, is declined for the reason **Decided**
already gives: the enumeration answer has said so since 2026-08-05, that
sentence stood in the answer this session worked from, and naming a tool nobody
invokes is what `D-ANS-061` ruled out.

Built on 2026-08-25. `ForgeLookup::workflow()` is the tail and stands under the
rows of an enumeration: it names `typo3-core-issue-triage`, that handing the
page over comes before choosing from it, and `D-SKL-031`'s five readings
cheapest first. The two calls the reporting session says it never made are named
with the readings they make cheap — `typo3_gerrit_lookup` with the first, and
`typo3_changelog_lookup` after the five, since a rework is what turns a valid
report into one about code that is gone. A breakdown gets none of it: it answers
the shape of a set rather than the candidates in it, and the early return that
prints it is above this.
`ForgeTest::aPageOfTheBacklogIsHandedTheWorkflowThatOwnsIt` holds what it names
and what it leaves out, and `theWorkflowStandsUnderThePageOfCandidates` holds
the placement.

What it costs is measured rather than estimated. The tail is 1,371 bytes, and
`bin/cli tools:measure` in this checkout on 2026-08-25 reads
`typo3_forge_lookup` at 33,879 bytes of text over 14 calls. Four of those
recorded calls are enumerations that answered with rows — 3,342, 3,131, 2,750
and 1,923 bytes — so the tail is 5,484 bytes on the tool, 16.2% of its text,
against the 6.1% the Gerrit tail put on its own. The recorded pages are not
re-recorded here, which is why the arithmetic is stated rather than a second
`tools:measure` reading. Each of those four asked for three rows: the reporting
session asked for 25, so what a triage actually holds carries the tail against a
page several times that.

The second **Wrong if** is what that spends, and this tail is twice the size of
the one it was last read against. A session that already holds the triage skill
open and asks a narrow question of the backlog is the case to watch, and the
answer to it is the same one named there: a condition, the way the fetch
sentence on the Gerrit answer is.

### 2026-08-27 — the tail is the neglected end's, and the enumeration gained another

The section above reads "the `open` form takes the tail" because the form had
two orderings and both were a triage of the backlog.
[`D-ANS-116`](../answers/ans-116-a-duplicate-check-reads-the-recent-end-of-the-backlog.md)
added a third, `newest`, for the other question about the same set: whether a
defect somebody has just found is already filed.

That one takes no tail, and the reason is the tail's own first instruction.
"Hand the page over rather than choosing from it" is what a triage owes a
backlog, and it is the opposite of what a caller checking for a duplicate is
doing — they are reading the subjects to decide, which is the one step that
question cannot be handed to a workflow. So the condition this section named as
what the second **Wrong if** would eventually want is taken here, on the
ordering rather than on the session:
`ForgeTest::theRecentEndCarriesNoTriageWorkflow` holds which orderings carry it.
