# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements/](requirements/readme.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Nobody reads it to start. `bin/cli next` prints the one todo that is due and
nothing else, `bin/cli todo list` is the overview, and this is where both of
them read from. Every section opens with a head of labelled lines:

| Line | What it says |
| --- | --- |
| `**Serves:** <ids>` | what this answers for — a requirement, a note, a scenario, a directory. Without it, it is an idea rather than a todo, and ideas go in the note that had them. |
| `**Every:** session` or `**Every:** 7 days` | it recurs and is never deleted. Without it, the section is the queue, and the queue is an order. |
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

---

## Run every open note's query against the server as it is now

**Serves:** feedback/
**Every:** session
**Run:** bin/cli feedback list

Take the notes nothing names yet, one at a time: run the query that produced it
against the server as it is now, then close it, trim it to the half that is
still open, or add the todo below that takes it on — and if it changes the
order, move the queue before starting. A note is evidence about a version of
this server that may no longer exist, which is why it is re-run rather than
read. The ones a todo already names have had that judgement and are listed for
context only.

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

## Decide whether a review-only task runs the project's read-only checks

**Serves:** feedback/2026-07-31-124500-a-review-reads-the-checks-it-never-runs.md

The other half of that note, and a decision rather than a wording change:
`cgl:ci` and `phplint` change nothing and would have turned two findings of the
syntax run from derived into established, while a command that fails still tells
you less than the configuration that would make it fail. Whichever way it goes,
it needs the property `typo3_project_scope` does not report today — which of the
commands it returns write anything — because "run the read-only ones" is not an
instruction an agent can follow against a list that does not say.

## Find an extension checkout with a major in front of it, and run `REVIEW-02` there

**Serves:** EXT-01, feedback/2026-07-30-173821-extension-upgrades-need-a-task-owned-workflow.md

The upgrade workflow has no environment: every extension checkout on this
machine is current, and what it needs is one still declaring `^12.4` or `^13.4`
alone — cloned for the purpose if none turns up. Name it under *Which checkout
plays which environment*, run `REVIEW-02`'s prompt there as a recorded run, and
reduce the note to what that run demonstrates. Read `bin/cli scenarios contract
EXT-01` first for the routing that has to survive.

## Add the `typo3-extension-upgrade` skill

**Serves:** EXT-01, feedback/2026-07-30-173821-extension-upgrades-need-a-task-owned-workflow.md

Once that run exists, add a thin skill that orders installed changelog and
scanner/deprecation evidence, official versioned documentation, shared-versus-
version-specific implementation decisions, and a Composer-resolved test matrix.
It starts from `skills/base.md` like every other skill and states only what it
adds, so project and extension scope are not restated in it. Keep concrete
version facts out of it, publish it through `Installer`, add its forward
acceptance result, and add the requirement and tests that hold only the
behavior the run proves.

## Run the release note's query in `E-EXT`

**Serves:** feedback/2026-07-30-174423-extension-releases-need-a-preparation-and-publication-workflow.md

Verbatim, as a recorded run, before anything is written. This is the largest of
the extension workflows and it composes the conformance, testing and
documentation ones that the todos above still change, which is why it is behind
them.

## Add the `typo3-extension-release` skill

**Serves:** feedback/2026-07-30-174423-extension-releases-need-a-preparation-and-publication-workflow.md

It starts from `skills/base.md` like every other skill, selects the intended
registries and version, and ends preparation with the artifact path, its
checksum, what the archive includes and excludes, the verification results, the
open blockers and the publication steps deliberately not taken. Registry
requirements come from current official documentation rather than from the
skill, and the artifact is built through the repository's own release command
where one exists. Tagging, pushing and registry publication stay a separate
phase that needs an explicit request and a confirmed repository, version and
credentials. What has to fail is an artifact carrying development files or
secrets while the checkout itself is green; if that shape survives the work, it
earns a contract case of its own.

## State the skill authoring contract in one place

**Serves:** feedback/2026-07-30-173821-task-skill-forward-evidence-is-not-repeatable.md

What is left of the note after 2026-08-01 is how a skill is *written* — body
procedural, versioned facts left in their owning tools, references one hop away
and loaded on demand, ownership and failure boundaries stated, a realistic
scenario before a new domain becomes a skill. Several of those are already
assertions in `SkillTest` that each skill restates in its own words; give them
one written form the way `skills/base.md` holds the evidence order, and read the
assertions from that instead of from five copies. It pays off when the next
skill is written, which is what the todos above would do.

## Settle `R-AUD-2` against `META-03`

**Serves:** R-AUD-2

`bin/cli scenarios contract META-03` asks for two paths of different audience in
one session and names the unmet half outright: that the two stay apart is not
guarded. Read `Scope` against that case and settle which of two things is true —
the per-path decision already exists and only the "the audience is uncertain"
answer is missing, or nothing combines the signals at all. The first is a
wording change with a test behind it; the second is a feature, and `D-SCO-6`
already names the flag it would rename (`outsideCore` → what it actually
decides).

## Try `R-AUD-1` against the test written for it

**Serves:** R-AUD-1

It is `not guarded` and may deserve to stay that way — a principle is not a
behaviour, and no test holds one. What it should not stay is untried:
`ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers` was written for
exactly this contradiction, and if it holds the operative half, the entry names
it instead of saying that nothing does. If it does not, the entry says in one
clause why nothing can, and that is the end of it.

## Sort the standing decisions by what could check them

**Serves:** decisions/

Most are standing because they are still true, so reading all of them for their
own sake is not the task and would not finish. Make one pass that splits them
three ways by their **Wrong if** alone: one this repository can answer on its
own (a checkout, a command, a test), one that needs a forward run in an
environment named below, and one that waits on something outside this repository
entirely. Only the first group is work, and it becomes todos. The pass is cheap
— the field is the last one in every file — and it is what turns a count into a
queue. Nothing is checked while sorting.

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
    phplint in CI, no PHPStan, no `Tests/` at all. `REVIEW-02` ran here
    `covered` on 2026-07-31.
  - `/home/benji/projects/bootstrap_package` — TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `bootstrap-package` on PHP 8.5. **Complete**
    infrastructure, which is what it plays. `REVIEW-02` ran here twice on
    2026-07-31, `partial` at 02:55 and `covered` at 08:15 after the corrections
    that run earned.

`E-EXT` is a kind, and one todo above still needs it played by a checkout
neither of these is: one with a TYPO3 major in front of it. Whichever plays it
is named here first.

## Not queued, and deliberately so

**Not an item.**

Things a session may otherwise rediscover and mistake for work:

- **`REVIEW-03`** needs a core checkout with actual uncommitted changes.
  `/home/benji/projects/typo3-cms` is on `main` with a clean tree, so the review
  has nothing to read. It needs a patch in progress before it can run at all.
- **The catalog roadmap** — an API signature lookup, a changelog scaffold, a test
  scaffold, and the structured-output envelope that needs a spike of
  `vendor/mcp/sdk` first. None of it is blocked; none of it serves an open note
  or a forward review either, which is why it is below everything that does.
- **`phpstan/phpstan` 2.2.6 → 2.2.7 and `phpunit` 11.5 → 12.5.** Ordinary
  maintenance, not an item.
