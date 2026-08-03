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
`bin/typo3-cms-mcp` from `/home/benji/projects/typo3-cms`, the checkout it was
written in: `typo3_changelog_lookup` reaches `13.0/Breaking-101955` from
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
