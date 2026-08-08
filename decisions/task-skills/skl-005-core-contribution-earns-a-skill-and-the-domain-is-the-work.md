---
id: D-SKL-005
date: 2026-08-03
status: open
---

# D-SKL-005 — Core contribution earns two task skills, one for reviewing a patch and one for creating one

**Core contribution earns two task skills — reviewing a patch and creating one —
and what shows it is 35 feedback out of one checkout rather than one run.**

`REVIEW-03` is the run that made it visible: a core patch review that called
this server nothing at all. The corpus behind it had been saying the same thing
since 2026-08-01, unjudged.

## Evidence

- The run. 23 calls in 256 seconds — 22 `Bash`, one `Read`, no server call, no
  skill — session `8622fa17`, `claude-opus-5`, Claude Code 2.1.220, one prompt
  and no steering, judged `partial` in `scenarios/runs/REVIEW-03.json`. Not a
  delivery failure: the transcript's attachments carry the
  `mcp_instructions_delta` in full, first sentence
  `Start every task with typo3_project_scope`, and the `skill_listing` with all
  seven descriptions; only the 22 tools arrived deferred, as names without
  descriptions.
- The nearest skill matches the shape and excludes the checkout.
  `typo3-extension-conformance` is "Review, audit, or improve a TYPO3 project,
  sitepackage, or extension … and report what is wrong with it in priority
  order", against a prompt reading "Review the current changes in this TYPO3
  core checkout … in priority order". It did not fire, and it was right not to.
  The other six are extension or site work by their own descriptions.
- 35 open feedback carry `directory: /home/benji/projects/typo3-cms`, in two
  clusters. Fifteen are patch **review**, from 2026-08-01, across three clients
  and four models — the GD/SVG placeholder patch, `7175fcaf7fe`, and the
  AssetCollector deprecation, each reviewed by a different session. Twenty are
  patch **creation**, from 2026-08-02, one session on Forge #105403: the
  worktree it worked in, the commit message, running unit, functional, cgl and
  phpstan repeatedly, finding whether a patch already existed, pushing to Gerrit
  as a private change, and operating Forge itself. No published skill names one
  of those things.
- One of them asks for it outright. `feedback/2026-08-01-115220`, GPT-5 mini:
  "Proposal: Add a dedicated MCP skill `typo3-patch-review` to support automated
  patch reviews." That is the third signal in
  [judging.md](../../documentation/feedback/judging.md) — a domain reached
  independently by more than one session — and it sat in the queue while
  `REVIEW-03` reported the same gap again.
- The content is here and the order is not.
  `knowledge/documents/typo3-core-scripts.md` holds the core scripts,
  `typo3-commit-messages.md` the commit rules, and `typo3_script_lookup`,
  `typo3_test_run_guide` and `typo3_commit_message_guide` are tools this server
  ships. `bin/cli hints:probe` on the run's own prompt reaches nothing: 40 hints
  are candidates and none matches.
- Where the entry point did fire it was answered thinly.
  `feedback/2026-08-02-144350` is a core session that called
  `typo3_project_scope` and got four `gerrit:setup` commands and no
  `Build/Scripts/runTests.sh`; it ran that script about thirty times and took
  its invocation syntax from elsewhere.

## Decided

- Two skills rather than one. Reviewing a patch and creating one are two task
  shapes with two corpora, and the split is the one the extension side already
  makes: `typo3-extension-conformance` reviews, the development skills build.
  Each is written around what its own cluster shows and nothing else.
- The entry is written before either skill exists, which departs from the rule
  that a decision is written by the commit that implements it. What is settled
  here is that the domain earned them and where the boundary runs; what they say
  is not settled and is not guessable from this repository.
- Not decided here: the names, the order each holds, and what each states. That
  is the reading
  [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) demands
  before a line is written, and it is the todo this entry leaves. A name has to
  say core — `extension-conformance` for a site project is the mistake
  `D-AUD-003` spent four runs on.

## Assumed

- That the two clusters are two skills. The evidence is the task shapes: the
  review cluster never pushes anything, and most of the creation cluster is
  delivery — worktree, checks, Gerrit, Forge — which a review may not touch at
  all (`D-EVI-003`).
- That the corpus says what its titles say. Four of the 35 were read in full for
  this entry; the rest were read as titles, models and directories. Reading them
  is the todo's first step and may move the boundary this entry draws.

## Wrong if

- The reading finds one order that covers both clusters. Then this is one skill
  and the entry split a domain by its verbs, which is what `R-SKL-010` exists to
  prevent.
- A second `REVIEW-03` run in the same client and model calls this server with
  neither skill published. Then the missing skill was not the obstacle, and what
  is left to suspect is what the tools answer a core checkout with.
- Both are published and the next core run still hand-reads the checkout. Then
  the domain did not earn them, and what a core session needs from here is
  smaller than 35 feedback made it look.

## Since then

Both clusters were read the same day, which settles the second assumption above:
they do not merely report a gap, they contain the two orders. Neither had to be
invented here.

The review order is written down twice by sessions that arrived at it
independently. `feedback/2026-08-01-115716` states the chain that worked —
`typo3_changelog_lookup` for the precedent, `typo3_script_lookup` with
`typo3_test_run_guide` for the exact `runTests.sh` suites, then
`typo3_commit_message_guide` — and asks for it to be named. `2026-08-01-121847`
reaches the same steps from the other end, by finding no entry point at all:
`typo3_server_scope` routes "review, audit or assess" to `typo3_project_scope`,
`typo3_task_guide` and `typo3_extension_scope`, which author changes and read
extensions. What the order has to force is in `2026-08-01-115711` and `115525`:
enumerate what the diff removes or renames, and require an ExtensionScanner
matcher plus a Breaking or Deprecation `.rst` per removal, with method-level
`@internal` waiving the `[!!!]` marker and nothing else. Two findings were
under-stated until a user pushed back, both for want of that step.
`2026-08-01-121852` carries the boundary the order sits on and calls it the most
useful answer of the review: this server never reads the checkout, so the diff
is read here and passed in.

The creation order is one session's whole task, filed in nineteen parts on
2026-08-02 and explicitly offered as a skill body in `2026-08-02-145315`. It
runs: assess the Forge issue before believing the report (`145128`, `144800`,
`145043` — the report was half stale and the maintainers' closure was product
judgement rather than an API fact); reproduce against the target branch as a
functional test, because a ViewHelper needs a rendering context (`144456`);
implement; decide the changelog from its own tree, with the directory named for
the upcoming version and the file `<Type>-<issue>-<CamelCaseSummary>.rst`
(`145315`); run the suites through `Build/Scripts/runTests.sh`, which
`typo3_project_scope` does not name (`144350`) and which needs its own
dependencies inside a worktree (`144950`) and passes falsely there for `cglGit`
(`144326`); then the delivery half — the asymmetric `origin`, the `Change-Id`
hook, and the Gerrit REST query that says whether a patch already exists
(`144848`, `145230`), reached over Anubis bot protection that answers a
browser-like `curl` with HTTP 200 and a challenge page (`145217`).

What the reading also shows is that the two orders share their middle and not
their ends: both establish the change and run the checks through the same three
tools, the review stops before anything is written, and the creation half is
mostly what happens after the code is right. That is the boundary this entry
assumed and it holds.

## Since then

The review order above was read off `feedback/2026-08-01-115716`, and one of the
four links it names does not hold. Re-run on 2026-08-03 through
`bin/typo3-dev-companion` from `/home/benji/projects/typo3-cms`, the checkout it
was written in: `typo3_changelog_lookup` reaches `13.0/Breaking-101955` from
`image generation` and from `removed public methods`, which are the words the
entry is titled in, and from nothing the diff carries. `GifBuilder` reaches four
entries and not that one, `getTemporaryImageWithText` reaches none, and the
session's own query `GifBuilder placeholder preview thumbnail` at version 15
reaches none — the answer now names the version filter as what emptied it.

The session says so itself. `feedback/2026-08-01-115112`, filed four seconds
earlier by the same model in the same directory, reports that it found
`Breaking-101955` by grepping `Documentation/Changelog` because the lookup could
not reach it. So the strength credits a tool for the finding its sibling records
as that tool's miss, which is the third corpus in which the credit is misplaced
and the second time on this server's own matcher
([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).

That changes the order's first step rather than the decision above it. A
reviewer holds what the diff removes — a class and a method name — and that is
the one thing the matcher does not carry
([`D-ANS-030`](../answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md),
whose own example is this method). Until the two cards serving `115112` land,
the review order reaches a precedent by the entry's own subject words or from
the checkout's `Documentation/Changelog`, and it says which. An order that opens
with a step that misses in the case the review needed it would ship that miss
into somebody else's project.

The other three links reproduce. `typo3_script_lookup` on functional tests
returns the `runTests.sh` section with `CI=true` and the `--` passthrough;
`typo3_test_run_guide` with the patch's four changed paths and
`targetVersion: 15` narrows to the php domain and carries
`checkExtensionScannerRst` with the sentence that names a removal as what it is
for; `typo3_commit_message_guide` returns the patch's message corrected and
warns that the 68-character summary is over 52, which `2026-08-01-115115`
reports as well. One credit there is looser than the answer: the narrowing is to
a domain and returns 13 suites, and the four the report calls "the exact
`runTests.sh` suites" are the ones the session picked out of them.

The feedback stays open behind the card this entry left for the review cluster,
which nothing had written until now.

## Since then

The step that reading corrected is in the published skill.
`typo3_changelog_lookup` keeps its place in the review order — the precedent is
still the strongest argument a review makes — and what changed is what it is
asked with: the words the entry is titled in rather than the identifier the diff
removes, no version filter narrowing it to the branch the patch targets, and the
checkout's `Documentation/Changelog` where neither reaches it, with the review
saying which of the two answered. Written so it stays right when the two cards
serving `feedback/2026-08-01-115112` land: what it forbids is reading an empty
answer as "no precedent exists", which is the mistake either way.
`SkillTest::aPrecedentIsListedByTypeAndVersionBeforeItIsAskedForInWords` holds
it, and the three calls above were reproduced from
`/home/benji/projects/typo3-cms` once more before the line was written.

That closes the card this entry left for the review cluster, and
`feedback/2026-08-01-115716` with it. Both skills are published; what is left of
this entry is its **Wrong if**, and only a run reaches that.

## Since then

The delivery half was re-run on 2026-08-03 against the server as it stands, to
judge `feedback/2026-08-02-144848`. Most of what that feedback reports has
landed since it was written.

`typo3_rule_lookup` on `gerrit push private change` returns six sections, from
`typo3-gerrit-workflow` and `typo3-commit-messages`. They carry the push URL
that points at review.typo3.org while the fetch stays on GitHub,
`composer gerrit:setup` and the `commit-msg` hook, the `HEAD:refs/for/main`
magic ref, `%wip`, and the one commit that is amended rather than appended to.
They also carry the `Change-Id` that binds a new patch set to its review.
`typo3_commit_message_guide` was given that patch's message with a `Change-Id`
trailer and returned it verbatim. Its `message` argument now states in its own
description that unknown trailers such as `Change-Id` are kept, so an amended
patch set stays valid. That is the second half of the feedback's suggestion,
answered where it asked for it.

Four things are not here, and the word `private` in that query reaches none of
them.

- **The unlisted push.** `%private` occurs nowhere in `knowledge/` or `skills/`.
  The skill that owns the step says it does: *"`typo3_rule_lookup` for the
  Gerrit workflow has both forms."* It has one. The same skill makes the
  question mandatory — *"Ask whether the change goes up visible to everyone or
  unlisted, every time"* — and calls the push the step that is not reversible.
  So the one obligation the skill will not let a session skip is routed to an
  answer carrying only the variant that publishes.
- **Where the push goes.** The corpus has `git remote set-url --push`, which is
  what a human runs once per clone. It has no way to read what a checkout is
  already configured to push to, and the skill says to establish that without
  naming a key. `remote.origin.pushurl` and `.gitreview` are what answer it.
  `.checkouts/main/.gitreview` carries `host`, `port`, `project` and
  `defaultbranch=main`, exactly as the feedback reports.
- **Whether the refspec holds from a git worktree.** The user asked outright.
  Two sessions answer yes from practice, and nothing here answers at all.
- **A Forge issue that is closed.** `knowledge/task-intents.json` routes a
  session to read that an issue is closed. Nothing says a change may not hang
  off one, or that reopening it comes before the push.

The 72-character limit is answered, in `typo3-commit-messages.md`, but it is
attributed to the message rules rather than to the hook.
`.checkouts/main/Build/git-hooks/commit-msg` enforces it in `checkForLineLength`
and rejects a missing `Resolves:` in `checkForResolves`. The corpus credits that
hook with the second check only.

**Judged 1a, and queued at `high`.** The knowledge is missing rather than
mislaid: the tool exists, the document exists, and the fact is not in it. So
neither placement nor wording would deliver it. The priority is not the `low` a
card arrives at, for two reasons. `144848` and `145230` report the same four
items from two task shapes. And the skill's promise makes the gap reachable at
exactly the moment the irreversible step is taken.

`feedback/2026-08-02-144848` is trimmed to those four and stays open behind the
card. `145230` keeps its own card, because it carries the Gerrit read direction
as well, which this judgement did not look at.

## Since then

`feedback/2026-08-02-144800` is one of the three this entry cites for the
creation order's first step, and judging it on 2026-08-03 says the shape it
asked for arrived while half of what it asked for did not. The feedback wanted
the assessment guidance on `typo3_task_guide` at `changeType=bugfix`. Re-run
with its own arguments — `area=fluid`, `targetVersion=15.0` — the guide returns
"Confirm the target TYPO3 core branch and issue context" and "Reproduce the bug
first", and nothing about how a report that was closed is read.
`bin/cli hints:probe` and `typo3_rule_lookup` reach nothing on the question
either. So the answer went to `typo3-core-patch-development` rather than to the
placement the feedback named, which is this entry working as decided.

What the skill absorbed is one of that feedback's four readings: the
maintainers' comments can be product judgement rather than an API fact. Two are
in neither the skill nor anywhere else, and both are what actually turned the
session's conclusion — that a closure reason records what the conversation did
rather than what the report is worth, and that a named alternative closes an
issue only if it does what the reported code did. The run behind them is
recorded: the issue was closed "for lack of feedback" after sixteen months, and
the alternative the maintainer offered drops width, height and cropping, which
`.checkouts/main` confirms — `f:uri.resource` registers `resource`, `path`,
`extensionName`, `absolute` and `useCacheBusting`, and `f:uri.image` registers
`width`, `height` and `crop` beside them.

The gap is wording rather than shape: the section exists and states its one
reading as a disposition. `D-SKL-009` is what the repair is held to, since a
disposition three runs read and none followed is what that entry was decided on,
and the same skill has no recorded run to spend words against. The feedback is
trimmed to the two readings and its card carries them at `normal`. Its
neighbours in the cluster are judged on their own cards: `145128` kept the nine
procedural steps and says so, and `144814` kept the maintainer statement that
the checkout contradicts.

## Since then

`feedback/2026-08-02-144814` is the third of the assessment cluster, and what
was left of it is a step rather than a fact. A rule quoted from a tracker
comment or from prose documentation is a claim to verify against the checkout,
the way a path and an identifier already are.

It is written into "Establish the issue before you believe it" as an act with an
object (`D-SKL-009`). What it names is the class, its docblock and the core's
own tests for the form under dispute, and the report says which of the three
carries the rule. The strength that source puts on it is carried rather than
rounded up, which is the distinction `D-KNW-043` corrected the corpus on. It
sits before the reproduction step: the reading that ended that session's
assessment ended it before anything was reproduced.

`R-SKL-015` and `SkillTest::aRuleQuotedAtTheIssueIsVerifiedInTheCheckout` hold
it, and the feedback is archived. The section is now five steps and three
paragraphs, and the two cards still out — `145128` for the two lookups and
`144800` for the closure reading — land in the same one. Whoever writes the
second of those weighs the length against `D-SKL-010`'s third **Wrong if**,
which is where a section that grows a sentence per feedback stops being an
order.

## Since then

`feedback/2026-08-02-145230` is the read direction of the task the section
before last judged from the write side, and judging it on 2026-08-03 answers
most of it. Everything the session established by hand about the review server
is one call now. `typo3_gerrit_lookup` with `issue: 105403`, re-run from
`/home/benji/projects/typo3-cms`, prints the query `message:105403` and an empty
answer, under the caveat that a private change is invisible to an anonymous
read. Asked `issue: 110348` it returns change 95040 as `MERGED` on `main`. The
endpoint, the `)]}'` prefix the response opens with, `message:<issue>` as the
question, and that no user-agent games are needed are all inside the tool.
`D-ANS-033` is where that was decided, and this feedback is one of the four
sessions it counts. Its second suggestion is answered too:
`typo3_commit_message_guide` was given that patch's message with a `Change-Id`
trailer and returned it verbatim.

What the re-run found still open is the routing, and it is a rung this cluster
has not landed on before. The question the whole task opened with — has somebody
already fixed this — was asked by no order that writes a patch. Read on
2026-08-03, `typo3-core-patch-development` opened with "Establish the issue
before you believe it" and told the reader to read the issue itself. It named
neither `typo3_forge_lookup` nor `typo3_gerrit_lookup`, and `typo3_task_guide`,
called with this task's own shape, named neither either — it returns "Confirm
the target TYPO3 core branch and issue context". `typo3-core-patch-review` names
both, and asks the second with the `Change-Id` off a commit that exists already,
which is the other end of the tool. So the pair was reachable from the order
that judges a patch and not from the one that writes it.

The server itself is not silent. The `routing` block of
`knowledge/server-scope.json` carries "Taking a Forge issue on, before believing
what it describes" with both calls in order. `typo3_server_scope` is the entry
point this feedback's session used. That is what makes this step 3 rather than
step 1: the tool exists, the route exists, and the skill that owns the task
shape did not fire it.

The card that step was queued on was never needed. `feedback/2026-08-02-145128`
was judged the same day, from the write side of the same task, and the step it
wrote is this one: "Establish the issue before you believe it" now names
`typo3_forge_lookup` with the issue number as step 3 and `typo3_gerrit_lookup`
with the same number as step 4, the second **before any code is written** and
under the sentence `D-ANS-033` holds it to — nothing public names the issue
rather than nobody has fixed it. `R-SKL-016` and
`SkillTest::theAssessmentBeforeAPatchReadsTheIssueAndTheReviewServer` hold it.
Read from either end of the same task, the two judgements asked for one step,
which is the evidence this decision was made on rather than a collision.

The placement the feedback asked for is not taken, for the reason the section
above gives. `typo3_project_scope` in a core checkout answers with the four
`composer gerrit:setup` scripts and nothing about the review server, and the
order belongs in the skill.

One item is closed on the spot. The feedback reports that the `commit-msg` hook
rejected its first message for the 72-character limit, and
`knowledge/documents/typo3-gerrit-workflow.md` credited that hook with the
`Change-Id` and the `Resolves:` check only. `checkForLineLength` was read again
in `/home/benji/projects/typo3-cms/Build/git-hooks/commit-msg`, where it runs
before `checkForResolves` and rejects any non-comment line over 72 characters,
and the document now says so. Nothing was left to establish: the fact is the one
the section above already recorded from `.checkouts/main`.

The write half of this feedback is `144848`'s four items, carried at `high`
already, so `145230` was trimmed to the routing gap, and the gap is closed by
the step above. It is archived.

## Since then

The four things the delivery half was missing are in
`knowledge/documents/typo3-gerrit-workflow.md`, and `typo3_rule_lookup` on
`gerrit push private change` returns two sections rather than six, re-run on
2026-08-03 from the bundled core checkout:
`Push a Private or Work in Progress Change` first and
`Pushing From a Git Worktree` behind it, both carrying every term of the query
and neither truncated. The four sections that used to answer that query carry
none of its subject and no longer clear the coverage floor.

Three of the four are not checkout facts, so where each came from:

- `%private` and `%wip` are Gerrit's own documentation, served by
  review.typo3.org. `user-upload.html` has the push options and the way back out
  — `%remove-private`, `%ready`, and that omitting `private` on a later push
  does not publish a change — and `intro-user.html` has what each one is:
  private decides who can see the change, work in progress decides who is asked
  to act. The contribution manual's cheat sheet carries `%wip` alone, which is
  why the corpus did.
- Where a checkout pushes is `remote.origin.pushurl`, `remote.origin.push` and
  `.gitreview`. The setup guide at docs.typo3.org writes the first two, and the
  bundled checkout is the case that shows why reading them is a question of its
  own: it has no `pushurl` at all, fetches and pushes GitHub, and its
  `.gitreview` names review.typo3.org.
- The worktree answer was established rather than taken from the two sessions
  that reported it from practice. In a scratch clone with a worktree, `HEAD`
  resolves to the worktree's own commit, `git rev-parse --git-path hooks` points
  at the clone's hook directory, a `commit-msg` hook there runs for a commit
  made in the worktree, `remote.origin.pushurl` reads the same in both, and
  `HEAD:refs/for/main` carried exactly the worktree's commit.
- The closed issue is the one that stays partly open.
  `Build/git-hooks/commit-msg` checks `^Resolves: #[0-9]+$` and nothing about
  the issue behind the number, and Gerrit does not ask Forge, so nothing refuses
  the push. The manual's issue workflow says a report moves to "Under Review"
  when its first patch set arrives and that anyone may patch any issue, and it
  demands no reopening anywhere. Whether the automation also moves a closed
  report is unestablished, and the document says so: Forge answers a fetch of
  the issue with HTTP 403, which is the bot protection
  `feedback/2026-08-02-145217` reports.

[`R-KNW-057`](../../requirements/knowledge/knw-057-the-push-a-session-cannot-take-back-is-answered-in-full.md)
is what holds the four, and `feedback/2026-08-02-144848` is archived with it.
