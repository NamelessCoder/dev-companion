# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements/](requirements/readme.md)), not the questions real sessions asked
(that is `feedback/`), not the map of what the audiences need (that is
`scenarios/`). Those three outlive the work; this one is consumed by it.

Nobody reads it to start. `bin/cli next` prints the one todo that is due and
nothing else, `bin/cli todo list` is the overview, and this is where both of
them read from. Every section opens with a head of labelled lines:

| Line | What it says |
| --- | --- |
| `**Serves:** <ids>` | what this answers for — a requirement, a feedback, a scenario, a directory. Without it, it is an idea rather than a todo, and ideas go in the feedback that had them. |
| `**Every:** session` or `**Every:** 7 days` | it recurs and is never deleted. Without it, the section is the queue, and the queue is an order. A cadence in days is an appointment and comes before the queue; `session` is a sighting and comes after it, when the queue is empty. |
| `**Checked:** <date>` | when a todo measured in days last ran. The session that runs it writes the date. |
| `**Run:** <command>` | where the step starts. `bin/cli next` runs the ones this repository owns and names the rest. |
| `**Not an item.**` | neither — something a session would otherwise rediscover and mistake for work. |

Then one paragraph, and one only: the **next concrete step**, in enough detail
that someone who has read nothing else can start. "Continue with the bindings"
is not that; "bind the statements in `php.json` against `.checkouts/12.4` and
`13.4`" is. Two steps are two todos — a paragraph is printed whole, and a
session that reads three of them to find where to start is reading instead of
working.

- A finished todo is deleted, not ticked. What it established is already in
  `requirements/`, and the commit is the record that it happened.
- One that turns out to be half done is trimmed to the part that is left.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.
- The queue comes before the sightings. Judging a feedback or a backlog entry is
  what puts something *into* the queue, so a queue that still has entries is a
  queue of things already judged — sighting more of them instead is deciding
  twice and doing nothing. The sightings run when it is empty.

---

## Run the next five open feedback' queries against the server as it is now

**Serves:** feedback/
**Every:** session
**Run:** bin/cli feedback next

Take the five the listing hands over and no more: run the query that produced
each against the server as it is now, then give it one of three answers — close
it, trim it to the half that is still open, or add the todo below that takes it
on, and if that changes the order, move the queue before starting. A feedback
is evidence about a version of this server that may no longer exist, which is
why it is re-run rather than read. Say which of the three each feedback got and
what the re-run showed, so somebody who disagrees with one can say so: five
judgements can be read, and the fifty behind them are what the chunk exists
for. This comes round only when the queue is empty, because a feedback that
became a todo has already been judged and doing it is what is owed next.

---

## Judge what the backlog has been waiting on

**Serves:** requirements/, decisions/
**Every:** session
**Run:** bin/cli backlog list

Give every entry the listing marks as unnamed one of two answers: a todo below
that takes it on, or the sentence in `decisions/` that says why it stays as it
is. Nothing here is checked or built while judging. Three states mean unfinished
and none of them can make a check fail — a requirement marked **open**, one held
by `not guarded`, a decision still `standing` whose **Wrong if** nobody has been
back to — which is why they went unread for as long as they existed, and why a
requirement sat there unbuilt from the day the directory was created. A
requirement no test can hold is a legitimate answer; one nobody has judged is
not.

---

## Check whether `mcp/sdk` has released a newer version

**Serves:** requirements/
**Every:** 7 days
**Checked:** 2026-08-01
**Run:** composer outdated mcp/sdk

Take a patch or minor release on its own merit — v0.7.1 sits unreleased on
`main` since 27.07. and carries stdio hardening, which is worth having for a
server whose only transport is stdio. A release that speaks `2026-07-28` is the
one to watch for (PR #403, protocol version negotiation): when it lands, the
bump is the constraint in `composer.json` and `PROTOCOL_VERSION` in
`tests/Smoke/StdioServerTest.php`. It serves no single requirement because it
serves the precondition of all of them: every answer this server gives travels
over the protocol version the SDK speaks, and on the day a client stops offering
that version, every requirement fails at once and not one of them says so.

---

## Widen the `D-SCO-6` guard to the surfaces it does not read

**Serves:** decisions/

The claim that project work is out of scope kept coming back, and the guard
catches it in one place while the entry names three it does not read: a tool
description, the readme, a hint. Extend that guard across all three — the same
assertion, more surfaces — and the "Wrong if" is answered by a test instead of
by whoever notices the sentence next. If the claim then reappears in a surface
no test can reach, the flag rename the entry proposes (`outsideCore` →
`coreRepositoryOnly`) is the next step, and it is a todo of its own.

## Descend into the worked example `D-CAT-2` only checks the path of

**Serves:** decisions/

Existence is all that is checked, and the entry names the exact failure:
`Build/tests/playwright/e2e` surviving a rewrite that moves the fixtures
elsewhere. Open it in `/home/benji/projects/typo3-cms` and name the two or three
files that actually carry the shape the entry promises. What would hold it is
`bin/cli catalog check` asserting those files rather than the directory — the
form the entry itself calls the honest one.

## Give `D-CAT-1` a digest to notice markup by

**Serves:** decisions/

The binding is derived from the names in an entry, so markup that changed while
every name stayed reads as unchanged, and a range with a hole in it reports as
no binding at all. Both are readable from `.checkouts/`: derive the binding for
every catalog entry on every covered version and compare it against what the
entry claims. What would hold it is a digest of the matched markup recorded per
entry per checkout in `bin/cli catalog check`, so a silent rewrite fails the
check instead of aging quietly into a wrong answer.

## Try `D-DIS-2` against an absolute `bin-dir`

**Serves:** decisions/

The entry names two shapes it accepts and ignores: an absolute `bin-dir`, which
Composer allows, and a console invoked from somewhere other than the root — a
DDEV project whose container working directory is the docroot. The first is
answerable here with a fixture `composer.json` and no installation at all. What
would hold it is an `InstanceTest` case per shape; the DDEV half needs one of
the environments below and is that case's second half rather than a todo of its
own.

## Try `D-DIS-1` against a root that is also in vendor

**Serves:** decisions/

Two shapes are named and neither has been seen here: a monorepo whose root
declares a TYPO3 package type without being the thing worked on, and a setup
that installs the root into the vendor directory as well, where both entries
resolve to one realpath under one key. Build both as fixtures and check that the
second collapses as intended and the first does not silently win. What would
hold it is those two fixtures as `InstanceTest` cases — the decision is about
resolution, and resolution is testable without an installation.

## Give `D-DIS-3` something to read besides the exit code

**Serves:** decisions/

A label query asks the console with a regex and reads the exit code alone, so a
command that exits 0 and prints nothing usable for any other reason becomes a
confident "none" where nothing was established. Take one console command that
can do that and see what the answer looks like. What would hold it is a
`Typo3CliTest` case with a fixture command that exits 0 on unusable output, and
an answer that says "nothing established" rather than "none" — the two are
different results and the tool currently has one shape for both.

## Hold the `D-DIS-4` constant to `knowledge/versions.json`

**Serves:** decisions/

The version comes from the core package, and the entry names one number in one
place — the constant in `Tools` — that a backport of the domain API into a 13.x
patch would make wrong. Check the constant against what the checkouts and
`knowledge/versions.json` actually say today. What would hold it is a
`VersionsTest` assertion tying the constant to the declared versions, so the two
cannot drift apart silently; the second "Wrong if" — a caller working on a
version other than the installation found — is a feature (a stated version) and
belongs in its own todo if the reading shows it happening.

## Watch `D-DIS-5` for a boot that changes the checkout

**Serves:** decisions/

Reading a registry by booting the installation buys the only complete answer and
risks a side effect a lookup must not have: an `ext_localconf.php` that writes
outside the cache, or a hard failure on a database that is not running. The
symptom is concrete — a lookup that modifies the checkout, or one that takes the
full 90 seconds. Run the registry lookups against one environment below with a
clean tree and check `git status` afterwards. What would hold it is that check
as a recorded step of the run rather than a test here: this repository has no
installation to boot, and a test that mocks one would be measuring the mock.

## Try `D-VER-4` against range spellings from the wild

**Serves:** decisions/

A supported range is read off the package's own declaration, and the entry names
the failure as a spelling that answers false for a major it does serve — which
surfaces as a statement missing from an answer, not as an error. Collect the
spellings the three extension checkouts below actually use (`^12.4.37 ||
^13.4.15` among them) and run each through the resolution. What would hold it is
that table as a data provider, one row per spelling with the majors it must
answer true for; the second half — a repository declaring far wider than it
tests — is a watch and no test can hold it.

## Check whether `D-KNW-3` has grown a fourth value

**Serves:** decisions/

`provenance` and `binding` stay two axes as long as no value reads naturally on
both; a fourth that does would mean they were one axis after all. Read the
current value sets of the two and settle whether that has happened. What would
hold it is a `KnowledgeTest` assertion that the two sets stay disjoint — cheap,
exact, and it fails on the day the merge becomes the right entry instead of
waiting for somebody to notice the overlap.

## Try `D-KNW-1` with a backend-only task that mentions a content element

**Serves:** decisions/

Sitepackage work is answered from the General category, and the named failure is
a backend-only task coming back with the sitepackage layout because it mentioned
a content element. That is one call: ask `typo3_task_guide` with exactly that
task text and read what comes back. What would hold it is a contract case in
`scenarios/contracts/` for that task shape, plus the assertion in the guide
tests; the other half — General growing until every answer is made of it — is a
size the category can be measured for in the same pass.

## Try `D-CAT-3` against a styleguide template that opens with scaffolding

**Serves:** decisions/

Two failures are named: component state that exists only at runtime and appears
in neither compiled CSS nor installed JavaScript, and a styleguide template
whose first matching example is page scaffolding rather than component markup.
The second is readable from an installation below and is the one with a fix the
entry already names — an explicit selector in the curated index. What would hold
it is a `CatalogTest` fixture whose first match is scaffolding, asserting the
extractor takes the selected example rather than the first one.

## Try `D-SCO-3` against a checklist item that names a core path on purpose

**Serves:** decisions/

Core-only is decided per line by what the line names, and the entry names the
line that would break it: advice about reading the core as a reference rather
than changing it, which names a core path while belonging to everyone. Read the
current checklists for exactly that shape. If one exists, the per-line
derivation needs the explicit flag after all and that is a change with a test
behind it; if none does, the entry says so in a **Tested on** line and the
derivation keeps its case.

## Try `D-SCO-4` against a core contributor working on `fluid_styled_content`

**Serves:** decisions/

The frontend is recognised by name and only the two UI sections go, with
`styleguide` and `backend` named in the notice as the escape. The named loss is
concrete: a core contributor working on frontend rendering who wanted the CSS
hints. Ask the scope with that task text and read whether the notice makes the
escape usable. What would hold it is a `ScopeTest` case per side — the sections
that go, and the escape that brings them back — so the notice is held to being
an escape rather than an apology.

## Settle what `D-GUI-1` does when the placeholder is pushed

**Serves:** decisions/

A missing release target becomes a placeholder rather than `main`, and the entry
is wrong the moment a placeholder shows up in a pushed commit — at which point
the guide would have to refuse the draft outright. This repository cannot see
other people's pushes, so the reading is what the draft itself does: check that
the placeholder is unmistakable in the rendered message rather than a plausible
branch name. What would hold that half is a `CommitMessageTest` assertion on the
marker; the pushed-commit half is evidence from a forward run and stays
unguarded until one produces it.

## Try `D-FBK-2` against the check that would have to add the line

**Serves:** decisions/

The order of the work is declared rather than inferred, and the entry names two
ways that turns into bureaucracy: sections arriving without the head line, and
the paragraph thinning out while the `Serves:` line grows. `bin/cli todo check`
already reports the first. Read this file for the second — this queue has just
grown by thirty-five sections and is the sharpest test the decision has had.
What would hold it is a `TodoTest` assertion that every queued section has a
body at all, which is the mechanical half; that one paragraph is one step stays
unguardable by design, and the entry says so.

## Hold every `check` command in `D-EVI-3` to what it may not do

**Serves:** decisions/

A review runs the checks that cannot change the code, and the entry is wrong if
a run reports a checkout modified by a command classified `check`. The
classification lives in the skills, so it is readable here: list every command
any skill declares as `check` and confirm none of them writes. What would hold
it is a `SkillTest` assertion over that declared set against a list of
known-writing commands, so a skill that classifies a fixer as a check fails
before a run finds out the expensive way.

## Read the corpus for the statement `D-VER-1` cannot express

**Serves:** decisions/

A range is data on the statement, which means it cannot say "true on 12.4 and 14
but not on 13" — such a statement has to become two. Read the bound statements
for one that plainly has a hole in it, which is a corpus read and needs no
installation. What would hold it is weaker than a test and worth saying out
loud: nothing can detect a hole a statement does not admit to, so the entry
either gains a **Tested on** line saying the corpus has none today, or the
statement that has one is split in the same commit.

## Try `D-AUD-3` against a second `REVIEW-01` run

**Serves:** decisions/

The entry names its own experiment: the instructions carry the entry point
because the tool descriptions never arrive, and it is wrong if the second
`REVIEW-01` run still reaches for Bash alone. Run it in an environment below and
record it. What follows is already written down — the wording was never the
obstacle, and what is left to suspect is the skill's name, then the possibility
that a repository review needs almost nothing this server has. Nothing here can
hold this; the run is the evidence, and this todo is the one that produces it.

## Read two consecutive review runs for the `D-EVI-1` diffusion

**Serves:** decisions/

Forward evidence comes from a review rather than a prompt that knows the answer,
and it is wrong if two consecutive runs produce findings too diffuse to tie back
to a requirement or a feedback — then the prompt measures the model's taste. The
runs in `scenarios/runs/` are the material and the reading is a pass over them,
not a new run. The second half is sharper and testable here: the contract cases
rot if nothing schedules them, and whether anything does is a question about
this repository's own recurring todos.

## Watch a forward run cross the boundary `D-EVI-2` reads for

**Serves:** decisions/

A skill crossing is read rather than run, and the proxy is worth nothing if a
run that happens to cross shows the session editing documentation with the
backend-module skill still the only active one. No run has to be commissioned
for it: the reading is a standing instruction on runs that already happen in the
extension environments below, and what it produces is either a **Tested on**
line here or `R-SKL-3` back to needing the evidence this entry says it will not
get.

## Ask a client what it does with the `D-ANS-1` unavailable shape

**Serves:** decisions/

The unavailable case keeps the result shape and carries its reason in an
`unavailable` object, and it is wrong if clients ignore that object and read a
miss as a registry answer. Only a session can show that, so this is a reading
of recorded runs and open feedback for a miss that was reported as a fact. What
would hold it afterwards is not a test but the next lever the entry already
names — `isError: true`, bluntest, and it turns the answer into an error, which
is why it was not taken first.

## Read recorded sessions for the `D-ANS-3` miss

**Serves:** decisions/

Retrieval stays lexical and runtime inspection stays narrow, and two things
would show that wrong: real queries repeatedly missing a present section after
short English alternatives, and a diagnosis that cannot be completed from
project files, effective configuration and the caller's own checkout. Both are
properties of sessions, so read the runs and the open feedback for either. The
entry says what the evidence is worth: record that session, because it supplies
both the tool boundary and the safe result shape.

## Measure the `D-AUD-1` signals against the runs that used them

**Serves:** decisions/

Three audiences are combined from several signals, and the combining is
unnecessary complexity if one signal identifies the audience reliably on its own
— `typo3/sysext/` in the touched paths comes closest. That is measurable against
the recorded runs and the checkouts below: for each, ask whether the single
signal would have answered the same as the combination. What would hold it is
the same table as a test if the answer is that the combination earns its keep,
and a deletion if it does not — the entry is one of the few here whose wrong
half is smaller than its right one.

## Contribute to the core from `E-SITE` and see what `D-AUD-2` costs

**Serves:** decisions/

Two profiles, and the named failure is somebody contributing to the core from a
session started in a site installation who then has to set
`TYPO3_MCP_PROFILE=all` to get the rules back. `E-SITE` below is exactly that
session. Run one core-shaped task there and read whether the rules arrive. The
second half — a deployment with no installation to read at all — is where a
third profile would earn its name, and it is a separate answer this run does not
produce.

## Ask `D-SCO-2` a core task that names neither a sysext path nor Gerrit

**Serves:** decisions/

A core-only intent asks for evidence rather than silence, and the cost is
stated: a contributor whose task text names neither gets the submission rules as
conditional rather than as fact. Ask the guide with exactly such a text and read
whether the condition line keeps that cheap. What would hold it is a contract
case for that task shape — the routing stays held, and the entry keeps its cost
sentence or gains the correction that the condition line reads as a hedge.

## Try `D-SCO-5` from a checkout that is not the one being worked on

**Serves:** decisions/

The installation is evidence about the task and the weakest kind, and two shapes
break it: a client run from a site installation while the core sits elsewhere,
and `TYPO3_MCP_ROOT` pointing at a site installation for labels and icons while
the questions are about the core. The second moved the boundary and was not
introduced to do that, which makes it the half worth reading first — and it is
readable here, with the variable set against one of the checkouts below. What
would hold it is a `ScopeTest` case pinning what the variable may and may not
move.

## Commit in a project repository and see whether `D-GUI-2` holds

**Serves:** decisions/

The commit workflow is asked for rather than inferred, and the entry is wrong if
agents commit in a project repository, never pass the argument, and the hard
`missing-issue` error becomes the normal answer there. A run in one of the
extension checkouts below produces that evidence in one call. The next step is
already named: `typo3_task_guide` computes `outsideCore` and could hand the
workflow to the commit guide in the follow-up call it suggests, which is a
change with a contract test behind it.

## Read one prose section on an LTS for the `D-VER-2` failure

**Serves:** decisions/

The prose is not bound and says which half it is, which fails when a section
misleads on an LTS badly enough that the sentence does not save it — and that
statement then belongs in the hints rather than in the document. Pick the
documents most likely to have moved since 12.4 and read one section against the
oldest covered checkout. What would hold it is nothing here: the failure is a
reader being misled, and the only guard is that the statement moves into the
corpus where `since`/`until` can carry it.

## Read `D-FBK-3` against the session it was written for

**Serves:** decisions/

A session is handed one todo, not the file, and three things would show that
cut in the wrong place: sessions asking for context `next` withheld, sessions
opening with `todo list` anyway, and a recurring todo blocking the queue for
more than a session or two. The third is measurable right now — the two
recurring readings at the top of this file answer "there is work" on every run
by design, and whether that is a block or the point is the question. The first
two need sessions, and the entry names the fourth honestly: nothing can tell one
step from three.

## Count how often `D-FBK-1` has reported the same id unnamed

**Serves:** decisions/

The backlog is read out rather than enforced, and the entry names its own
threshold: the same id still reported with `no todo.md item names it` after
three sessions that ran the check, or a standing count that only ever grows
while nobody sorts. The second half has just been answered — the count is being
sorted by these thirty-five todos — so what is left is the first, and it is a
reading of `bin/cli backlog list` across sessions rather than a test. If it
trips, the shape to reach for is the one the entry rejected: `bin/cli
requirements check` failing while an `open` entry is unqueued.

## Wait for the producer `D-KNW-4` needs

**Serves:** decisions/

Package knowledge needs a producer before it needs discovery, and the entry is
wrong the day one real extension is ready to ship agent material. Nothing in
this repository can bring that day closer, which is what makes this the only one
of the thirty-five that waits on something outside it entirely. The order when
it arrives is written: add its scenario first, record package and version in
every answer, then implement the narrowest discovery path that package can
actually publish — and a second producer is what justifies extracting a shared
format.

## Let `typo3_task_guide` be asked about more than one area

**Serves:** R-AUD-2

The audience is decided per path now (`D-SCO-8`), and this tool is the one that
cannot use it: `area` is a single string, so the `META-03` prompt reaches it as
one question and gets one answer. Give it the paths of the work — a `paths`
array beside `area`, decided by the same `Scope::audiences()` the two path tools
call — and split what the brief states per audience: the checklist, the checks
and the checkout discovery are already filtered entry by entry, so what is new
is which paths each filtered list is for. `outsideCore` and `audience` stay as
they are; `audiences` is the addition. It sits at the end of the queue because
the todo it comes out of named it as out of its own scope, which makes it new
work rather than the half that was left.

## Which checkout plays which environment

**Not an item.**

The standing answer to a question every run asks first. A scenario names a kind
of directory; which one on this machine plays it belongs here, where it can go
stale without taking a case with it. A forward run is a fresh MCP client session
with the installed skills, and a session in this repository may neither activate
those skills nor grade its own implementation as behavioral evidence.

- **`E-SITE`** — `/home/benji/projects/site-new`, site package below
  `extensions/printworks_sitepackage`, TYPO3 14.3.5 under DDEV. The server is a
  dependency there: refresh the skills with `ddev exec php
  vendor/bin/typo3-cms-mcp update --agent=claude`. It carries a `.gitignore`
  modification belonging to another session; leave it.
- **`E-EXT`** — two checkouts play it, and which one a run needs is a property
  of the run. In both the server is **not** a Composer dependency, so it is
  reached from this checkout: `php
  /home/benji/projects/typo3-cms-mcp/bin/typo3-cms-mcp install --agent=claude`
  from the project root publishes the skills and writes the host-php `.mcp.json`.
  Repeat it after any skill change — the published skills are a copy and nothing
  reports it when they are older than the server. The generated ignore block in
  each `.gitignore` and the untracked `.mcp.json` are from that install and stay.
  - `/home/benji/projects/syntax` — `bk2k/syntax` 5.0.0, TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `syntax` on PHP 8.2, declared `^13.4 ||
    ^14.3`. **Static quality infrastructure is incomplete**: php-cs-fixer and
    phplint in CI, no PHPStan, no `Tests/` at all. `REVIEW-02` ran here twice
    on 2026-07-31, `covered` at 12:21 and `covered` again at 13:32 — the second
    against the server that runs the checks, and the first run of any kind to
    reach the console half from an extension checkout.
  - `/home/benji/projects/bootstrap_package` — TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `bootstrap-package` on PHP 8.5. **Complete**
    infrastructure, which is what it plays. `REVIEW-02` ran here twice on
    2026-07-31, `partial` at 02:55 and `covered` at 08:15 after the corrections
    that run earned.
  - `/home/benji/projects/news` — `georgringer/news` 13.0.2 at `3fe278a2`,
    TYPO3 **13.4.33** below `.Build/vendor` (capital B), host PHP 8.3, no DDEV.
    **A major behind the world**, which is what it plays: it declares
    `^12.4.37 || ^13.4.15` on PHP `>= 8.1 < 8.5` while 14 is out, so the
    declared range does real work in a run instead of being quoted. It owns
    `Build/Scripts/runTests.sh`, two per-major workflows, 30 test classes and a
    `Documentation/` tree, and at 132 classes it is the only checkout here large
    enough that a review has to choose what to open. Cloned `--single-branch`
    **on purpose**: `origin/main` carries the finished v14 migration, and the
    checkout that plays this environment for `EXT-01` must not hand that answer
    over with one `git log` — fetching another branch into it ends its
    usefulness for that scenario. It is also the one checkout here that carries
    a **correct escaping opt-out**: six `<f:format.htmlentitiesDecode>` around
    `{newsItem.title}` and `{newsItem.alternativeTitle}` in `Detail.html` and
    its two `Styles/` copies, all inside `<n:titleTag>`, whose
    `TitleTagViewHelper` returns nothing and hands the rendered children to
    `NewsTitleProvider`; the installed 13.4.33 core puts the resolved title
    through `htmlspecialchars()` into `<title>|</title>` in `PageRenderer`.
    That shape is what `SKILL-09` needs, so it is the checkout that case is
    read in. `REVIEW-02` ran here on 2026-07-31, `partial`
    at 14:23; a first attempt at 14:02 on the `12-13` branch was discarded
    rather than judged, because that branch is 185 commits behind `13.x` and 0
    ahead, and the run spent its top finding saying so.

## Not queued, and deliberately so

**Not an item.**

Things a session may otherwise rediscover and mistake for work:

- **`REVIEW-03`** needs a core checkout with actual uncommitted changes.
  `/home/benji/projects/typo3-cms` is on `main` with a clean tree, so the review
  has nothing to read. It needs a patch in progress before it can run at all.
- **The catalog roadmap** — an API signature lookup, a changelog scaffold, a test
  scaffold, and the structured-output envelope that needs a spike of
  `vendor/mcp/sdk` first. None of it is blocked; none of it serves an open feedback
  or a forward review either, which is why it is below everything that does.
- **`phpstan/phpstan` 2.2.6 → 2.2.7 and `phpunit` 11.5 → 12.5.** Ordinary
  maintenance, not an item.
