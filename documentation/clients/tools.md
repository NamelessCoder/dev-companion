# The tool surface

Every tool this server offers, with what it takes and the fields it answers
with. Everything below the first heading is written by `bin/cli tools:index`
from the classes that answer the calls, and `bin/cli tools:check` fails where it
has gone stale. A surface written out a second time by hand stops describing the
answer at the first change nobody carried across.

What a filled answer looks like is not here: that needs an installation to call,
and no test run discovers one. The [readme](../../readme.md) says what each tool
is for in prose; this page says what goes in and what comes back.

A client may be offered fewer than these. `TYPO3_MCP_EXCLUDE_TOOLS` names the
tools a caller does not want offered, the two feedback tools exist only in a
standalone checkout, and `typo3_server_scope` names what was left out.

A field is written as `name` *(type, required)* — description. Required is the
promise: a required output field is present on every path through the tool,
misses included. Fields indented under one are the fields of that object, or of
the entries of that list.

## `typo3_server_scope`

Orientation for this server: what it covers and at which depth, what it
deliberately does not cover, and which tool to call when. Start here when it is
unclear whether this server can answer a question at all, or which of the
lookups is the right one.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes** nothing.

**Answers with**

- `purpose` *(string, required)* — What this server is for.
- `instructions` *(string)* — The boundary statement clients receive at
  initialize time.
- `covers` *(array of object, required)*
  - `topic` *(string, required)*
  - `depth` *(string, required)* — How deeply the topic is covered.
  - `tools` *(array of string, required)*
  - `source` *(string, required)* — Knowledge file or typo3:// resource
    behind the topic.
  - `scope` *(string, required)* — One of `core`, `project`, `extension`,
    `any`. Which kind of work the answers are for. core: the contribution
    process and the scripts of that repository. any: a convention that holds
    wherever TYPO3 is written.
- `doesNotCover` *(array of object, required)*
  - `topic` *(string, required)*
  - `why` *(string, required)*
  - `instead` *(string, required)* — What to do instead of asking this
    server.
- `checkoutDiscovery` *(array of object)*
  - `establish` *(string, required)*
  - `how` *(string, required)*
- `routing` *(array of object, required)*
  - `when` *(string, required)*
  - `call` *(string, required)*
- `versions` *(array of object, required)* — The TYPO3 versions the
  knowledge is bound to. A statement outside a range is left out when a target
  version is known.
  - `major` *(integer, required)*
  - `branch` *(string, required)* — The branch that line is verified
    against.
  - `status` *(string, required)* — lts, stable, or development.
- `excludedTools` *(object, required)*
  - `names` *(array of string, required)* — The tools the caller asked
    not to be offered, and the only reason the list is ever shorter than the
    documented one. Empty unless the variable is set.
  - `variable` *(string, required)* — Environment variable that names
    them.
- `installation` *(object, required)*
  - `found` *(boolean, required)* — Whether there is an installation to
    read at all.
  - `root` *(string or null)* — Absolute path of the installation.
  - `kind` *(string or null)* — core-checkout or composer-project.
  - `via` *(string or null)* — How it was determined: discovery (walked
    up from the start directory) or environment (named by TYPO3_MCP_ROOT).
  - `startedFrom` *(string or null)* — Where the search started, or the
    configured value.
  - `searched` *(array of string, required)* — The directories the search
    walked. A failure here means a layout that cannot be read or a server
    started in the wrong place — this says which.
  - `packageCount` *(integer, required)* — TYPO3 packages found in it.
  - `misconfiguration` *(string or null)* — Set when a configured value
    could not be followed. Nothing falls back to a discovered installation.
  - `console` *(object, required)*
    - `reachable` *(boolean, required)* — False means every
      installation-backed tool answers with unsupported in place of its result.
    - `via` *(string or null)* — ddev, php, or override.
    - `php` *(string or null)* — The PHP version it runs on, where that
      is known.
    - `command` *(string or null)* — The invocation, as it is run.
    - `reason` *(string or null)* — Why it cannot be run. Null when it
      can.
    - `caveat` *(string or null)* — What limits the console that was
      found — a project whose containers are stopped answers what its files
      say and fails on everything that boots TYPO3 against its database. Null
      when nothing limits it.
  - `settings` *(object)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

## `typo3_rule_lookup`

Search the local TYPO3 core contribution rules and script notes by topic.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string, required)* — Topic to look up, in English, for example
  testing, review, deprecation, or code style.

**Answers with**

- `query` *(string, required)*
- `resource` *(string or null)* — The exact XLF resource the result was
  restricted to. Null means the caller did not yet provide the usage context.
- `matchCount` *(integer, required)*
- `matches` *(array of object, required)*
  - `documentId` *(string, required)*
  - `title` *(string, required)* — Title of the knowledge document.
  - `uri` *(string, required)* — typo3://core resource holding the full
    document.
  - `heading` *(string, required)* — Heading of the matched section.
  - `body` *(string, required)* — The section as written, formatting
    included.
  - `coverage` *(number, required)* — Share of the query terms the
    section covers, 0 to 1.
  - `score` *(integer, required)* — Weighted match score; headings weigh
    more than body text.
  - `truncated` *(boolean, required)* — Whether the body was cut; read
    the resource for the rest.
- `documents` *(array of object)* — Documents in the knowledge base with
  the topics they cover. Returned when nothing matched.
  - `id` *(string, required)*
  - `title` *(string, required)*
  - `topics` *(array of string, required)*
- `elsewhere` *(array of string)* — Documents outside the searched ones
  that do match the query.
- `alsoInHints` *(array of object)* — Architecture hints matching the same
  query. They are a second corpus, searched by typo3_architecture_lookup, which
  takes one of these ids.
  - `id` *(string, required)*
  - `title` *(string, required)*
- `scope` *(string, required)* — One of `core`, `uncertain`, `project`,
  `extension`. Which kind of work this answer is for: core, a patch to the
  TYPO3 core itself; project, the site repository around an installation;
  extension, a package in it, whether a sitepackage or a third-party one; or
  uncertain, which means nothing in the call placed the work and what came back
  is the core's own.
- `withheldDocuments` *(array of object)* — Documents that matched and were
  left out because they answer for the core repository alone. Empty inside the
  core. Each is still readable in full as its typo3://core resource, which is
  the way to get one deliberately rather than by accident.
  - `id` *(string, required)*
  - `title` *(string, required)*

## `typo3_script_lookup`

Find notes for TYPO3 core scripts and commands. They are the core checkout's
own: a query that reads as a project or third-party extension is answered with
the boundary instead of with commands that do not exist there.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `task` *(string, required)* — The TYPO3 core task, in English, for
  example unit tests, functional tests, CGL, npm, or dependency install.

**Answers with**

- `query` *(string, required)*
- `resource` *(string or null)* — The exact XLF resource the result was
  restricted to. Null means the caller did not yet provide the usage context.
- `matchCount` *(integer, required)*
- `matches` *(array of object, required)*
  - `documentId` *(string, required)*
  - `title` *(string, required)* — Title of the knowledge document.
  - `uri` *(string, required)* — typo3://core resource holding the full
    document.
  - `heading` *(string, required)* — Heading of the matched section.
  - `body` *(string, required)* — The section as written, formatting
    included.
  - `coverage` *(number, required)* — Share of the query terms the
    section covers, 0 to 1.
  - `score` *(integer, required)* — Weighted match score; headings weigh
    more than body text.
  - `truncated` *(boolean, required)* — Whether the body was cut; read
    the resource for the rest.
- `documents` *(array of object)* — Documents in the knowledge base with
  the topics they cover. Returned when nothing matched.
  - `id` *(string, required)*
  - `title` *(string, required)*
  - `topics` *(array of string, required)*
- `elsewhere` *(array of string)* — Documents outside the searched ones
  that do match the query.
- `alsoInHints` *(array of object)* — Architecture hints matching the same
  query. They are a second corpus, searched by typo3_architecture_lookup, which
  takes one of these ids.
  - `id` *(string, required)*
  - `title` *(string, required)*
- `scope` *(string, required)* — One of `core`, `uncertain`, `project`,
  `extension`. Which kind of work this answer is for: core, a patch to the
  TYPO3 core itself; project, the site repository around an installation;
  extension, a package in it, whether a sitepackage or a third-party one; or
  uncertain, which means nothing in the call placed the work and what came back
  is the core's own.

## `typo3_task_guide`

Build a task checklist enriched with matching architecture hints and relevant
core checks. Built from bundled conventions only: it does not read your
checkout, so it also names what you have to establish there yourself and routes
to the lookups that fit the task. Work that reads as a project or third-party
extension is answered with what transfers only — the core checks, checklist
items and steps that name something only the core repository has are left out
rather than handed over. Pass the paths where the work touches more than one
place: each is placed on its own, so a core path and an extension path in one
call are not answered with one verdict.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `task` *(string, required)* — Short description of the TYPO3 core task,
  in English.
- `area` *(string)* — Affected subsystem or extension, if known.
- `paths` *(array of string)* — The files the task is about, as they are in
  the repository they belong to. Pass them where the work touches more than one
  place: each is placed on its own, so a core path and an extension path in one
  call are not answered with one verdict. The area counts as one of them.
- `targetVersion` *(string)* — The TYPO3 version this task is for, for
  example "13.4" or "14". State one only to narrow to it: conventions that do
  not hold there are then left out, including those the repository needs for
  another major it declares. Left out, the answer holds for every major this
  repository declares typo3/cms-core for, which is what one codebase serving
  two of them needs; where there is no declaration to read, for the version of
  the installation this server was started in.
- `changeType` *(string)* — One of `bugfix`, `feature`, `cleanup`, `test`,
  `documentation`, `unknown`.

**Answers with**

- `task` *(string, required)*
- `area` *(string or null)* — Affected subsystem or path, if one was given.
- `paths` *(array of string)* — The paths this brief was composed for, the
  area among them. Empty where the call named none.
- `scopes` *(array of object)* — Which kind of work each path is. The hints
  are matched per group, so one that came back for a path outside the core
  carries no checks; where every path is outside, the core checks, the
  core-only checklist items and the submission route are left out of the whole
  brief.
  - `path` *(string, required)*
  - `scope` *(string, required)* — One of `core`, `uncertain`, `project`,
    `extension`. Which kind of work this answer is for: core, a patch to the
    TYPO3 core itself; project, the site repository around an installation;
    extension, a package in it, whether a sitepackage or a third-party one; or
    uncertain, which means nothing in the call placed the work and what came
    back is the core's own.
- `changeType` *(string, required)*
- `targetVersion` *(integer or null)* — The TYPO3 major this repository
  runs — stated by the caller, or read from the installation. Null means
  nothing was filtered by version. Where the repository serves several majors,
  targetVersions is what the answer holds for.
- `targetVersions` *(array of integer)* — Every TYPO3 major the answer
  holds for. One entry is the ordinary case. Several mean this repository
  declares typo3/cms-core for more than one of them, so a statement was kept
  when it holds on any — and where two statements about the same subject
  differ, the difference is the constraint the code lives under rather than
  drift. Empty when nothing was filtered by version.
- `domains` *(array of string, required)*
- `scope` *(string)* — One of `core`, `uncertain`, `project`, `extension`.
  Which kind of work the call as a whole reads as. Anything but core means the
  answer holds core conventions that may transfer, not a checklist for the
  task. Where the paths disagree, scopes is the answer and this is what the
  task text and the area alone say.
- `intents` *(array of object)* — The kinds of core work recognized in the
  task text.
  - `id` *(string, required)*
  - `title` *(string, required)*
  - `confidence` *(string, required)* — One of `strong`, `weak`. weak: a
    word named the subject without naming the work, or the intent is a
    core-only one and nothing in the task says this is core work. Either way it
    applies only under its condition.
  - `condition` *(string, required)* — When a weakly matched intent
    applies. Empty for a strong match.
- `architectureHints` *(array of object, required)*
  - `id` *(string, required)*
  - `title` *(string, required)*
  - `category` *(string, required)* — PHP, TypeScript, JavaScript, CSS,
    or General.
  - `scope` *(string or null, required)* — One of `core`, `project`,
    `extension`, `null`. Which kind of work the whole hint obliges. "core"
    means it is a condition of a patch to the TYPO3 core and a convention
    anywhere else — the backend's own design system, the changelog artifact,
    the paths of the mono repository. "project" and "extension" are the mirror:
    what the repository around an installation, or a package distributed on its
    own, has to do, and what is context rather than a condition inside the
    core. Null, the ordinary case, means it holds wherever TYPO3 is written: an
    API that throws throws in a sitepackage too.
  - `hints` *(array of object, required)*
    - `text` *(string, required)* — The statement itself. It reads the
      same on every version it holds for; the range is beside it, never inside
      it.
    - `since` *(integer or null, required)* — First TYPO3 major this
      holds on. Null means as far back as this knowledge base reaches.
    - `until` *(integer or null, required)* — Last TYPO3 major this
      holds on. Null means it still holds.
    - `versions` *(string, required)* — The same range as a sentence,
      empty when the statement is bound to nothing.
    - `scope` *(string or null, required)* — One of `core`, `project`,
      `extension`, `null`. Which kind of work this statement obliges. "core"
      means it is a condition of a patch to the TYPO3 core and a convention
      anywhere else — the backend's own design system, the changelog
      artifact, the paths of the mono repository. "project" and "extension" are
      the mirror: what the repository around an installation, or a package
      distributed on its own, has to do, and what is context rather than a
      condition inside the core. Null, the ordinary case, means it holds
      wherever TYPO3 is written: an API that throws throws in a sitepackage
      too.
  - `checks` *(array of string, required)* — Commands relevant to this
    hint.
- `rules` *(array of object)* — Rule sections that apply to this task.
  - `documentId` *(string, required)*
  - `title` *(string, required)* — Title of the knowledge document.
  - `uri` *(string, required)* — typo3://core resource holding the full
    document.
  - `heading` *(string, required)* — Heading of the matched section.
  - `body` *(string, required)* — The section as written, formatting
    included.
  - `coverage` *(number, required)* — Share of the query terms the
    section covers, 0 to 1.
  - `score` *(integer, required)* — Weighted match score; headings weigh
    more than body text.
  - `truncated` *(boolean, required)* — Whether the body was cut; read
    the resource for the rest.
- `checks` *(array of string, required)* — Commands to run, ready to
  execute from the core root.
- `conditionalChecks` *(array of object)* — Checks that only apply if the
  task really is the kind of work a weakly matched intent suggests.
  - `title` *(string, required)*
  - `condition` *(string, required)*
  - `checks` *(array of string, required)*
- `testSuites` *(array of object)*
  - `suite` *(string, required)*
  - `command` *(string, required)* — Full command, run from the core
    root.
  - `targeted` *(string or null, required)* — Narrowed form for iterating
    on a single file or test.
  - `description` *(string)*
  - `whenToUse` *(string)*
  - `domains` *(array of string)*
  - `versions` *(string or null, required)* — The TYPO3 majors whose
    runTests.sh has this suite, where that is not all of them. Null means every
    covered version.
- `checklist` *(array of string, required)*
- `checkoutDiscovery` *(array of object)* — What this server cannot see and
  the agent has to establish itself.
  - `establish` *(string, required)*
  - `how` *(string, required)*
- `nextTools` *(array of object, required)*
  - `tool` *(string, required)*
  - `when` *(string, required)*

## `typo3_test_run_guide`

Recommend Build/Scripts/runTests.sh commands by topic. Pass the changed paths
and the answer is narrowed to the suites that can actually fail on them — a
Sass-only change gets the CSS suites, not the PHP ones. Which suites the script
offers changes between majors, so a suite that branch does not have is left out
rather than handed over as a command. The script belongs to the core
repository, so paths that read as a project or third-party extension get no
suite at all rather than commands that cannot run there.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — Test or script topic, for example functional,
  phpstan, TypeScript, composer, or CGL.
- `paths` *(array of string)* — The changed file paths, as they are in the
  repository they belong to. Given, only suites touching their domains are
  returned. Each path is placed on its own: one outside the core narrows
  nothing and is named in the answer, because runTests.sh is not in its
  repository.
- `targetVersion` *(string)* — The TYPO3 version the commands have to run
  on, for example "13.4" or "14". Suites that branch's runTests.sh does not
  have are left out. Defaults to the version of the installation this server
  was started in; where there is none, every suite is listed.

**Answers with**

- `query` *(string or null)*
- `paths` *(array of string)* — The paths the answer was narrowed by, given
  ones and ones named in the query.
- `scopes` *(array of object, required)* — Which kind of work each path is.
  Only core paths can run a suite: runTests.sh is not in a project or an
  extension repository, so the others are named in the answer and narrow
  nothing.
  - `path` *(string, required)*
  - `scope` *(string, required)* — One of `core`, `uncertain`, `project`,
    `extension`. Which kind of work this answer is for: core, a patch to the
    TYPO3 core itself; project, the site repository around an installation;
    extension, a package in it, whether a sitepackage or a third-party one; or
    uncertain, which means nothing in the call placed the work and what came
    back is the core's own.
- `domains` *(array of string)* — Domains those paths touch. Empty means
  nothing was narrowed.
- `suites` *(array of object, required)*
  - `suite` *(string, required)*
  - `command` *(string, required)* — Full command, run from the core
    root.
  - `targeted` *(string or null, required)* — Narrowed form for iterating
    on a single file or test.
  - `description` *(string)*
  - `whenToUse` *(string)*
  - `domains` *(array of string)*
  - `versions` *(string or null, required)* — The TYPO3 majors whose
    runTests.sh has this suite, where that is not all of them. Null means every
    covered version.
- `invocation` *(object, required)*
  - `notes` *(array of string, required)*
  - `options` *(array of object, required)*
    - `option` *(string, required)*
    - `description` *(string, required)*
  - `examples` *(array of object, required)*
    - `purpose` *(string, required)*
    - `command` *(string, required)*

## `typo3_architecture_lookup`

Return architecture hints for TYPO3 core paths or task topics, grouped by
section. Where the paths read as a project or third-party extension the hints
still come back — the conventions transfer — but without their core check
commands. The "Backend CSS" and "Backend TypeScript" sections describe the
TYPO3 backend interface and are withheld, with the reason, where the task names
the frontend.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `paths` *(array of string)* — File paths related to the task, as they are
  in the repository they belong to. Each is placed on its own, so a core path
  and an extension path in one call are matched separately — the hints for
  the extension path come back without the core checks.
- `task` *(string)* — Short task description or architecture topic, in
  English. Matching is lexical against English text, so another language
  reaches only the loanwords.
- `id` *(string)* — Ask for one hint by its id, for example language-files,
  instead of matching. Every answer that returns no hint lists the ids there
  are, so a subject that exists can be requested by name rather than guessed
  at.
- `targetVersion` *(string)* — The TYPO3 version the answer has to hold
  for, for example "13.4" or "14". State one only to narrow to it: statements
  that do not hold there are then left out, including those the repository
  needs for another major it declares. Left out, the answer holds for every
  major this repository declares typo3/cms-core for, which is what one codebase
  serving two of them needs; where there is no declaration to read, for the
  version of the installation this server was started in, and where there is no
  installation either, nothing is filtered and every statement carries the
  versions it holds for.
- `limit` *(integer)* — Maximum number of architecture hints.

**Answers with**

- `task` *(string or null)*
- `paths` *(array of string, required)*
- `scopes` *(array of object, required)* — Which kind of work each path is.
  Paths of different scope are matched separately, so a hint that came back for
  one of them is about that path — and a hint for a path outside the core
  carries no checks.
  - `path` *(string, required)*
  - `scope` *(string, required)* — One of `core`, `uncertain`, `project`,
    `extension`. Which kind of work this answer is for: core, a patch to the
    TYPO3 core itself; project, the site repository around an installation;
    extension, a package in it, whether a sitepackage or a third-party one; or
    uncertain, which means nothing in the call placed the work and what came
    back is the core's own.
- `targetVersion` *(integer or null)* — The TYPO3 major this repository
  runs — stated by the caller, or read from the installation. Null means
  nothing was filtered and every statement carries its own range. Where the
  repository serves several majors, targetVersions is what the answer holds
  for.
- `targetVersions` *(array of integer)* — Every TYPO3 major the answer
  holds for. One entry is the ordinary case. Several mean this repository
  declares typo3/cms-core for more than one of them, so a statement was kept
  when it holds on any — and where two statements about the same subject
  differ, the difference is the constraint the code lives under rather than
  drift. Empty when nothing was filtered by version.
- `domains` *(array of string, required)* — Hints outside these domains are
  not returned.
- `withheldCategories` *(array of string, required)* — Categories that
  matched the domains but were left out because the task names the frontend.
  "Backend CSS" and "Backend TypeScript" describe the TYPO3 backend interface
  and are wrong advice for what a website renders; see docs.typo3.org for
  frontend theming.
- `hints` *(array of object, required)*
  - `id` *(string, required)*
  - `title` *(string, required)*
  - `category` *(string, required)* — PHP, TypeScript, JavaScript, CSS,
    or General.
  - `scope` *(string or null, required)* — One of `core`, `project`,
    `extension`, `null`. Which kind of work the whole hint obliges. "core"
    means it is a condition of a patch to the TYPO3 core and a convention
    anywhere else — the backend's own design system, the changelog artifact,
    the paths of the mono repository. "project" and "extension" are the mirror:
    what the repository around an installation, or a package distributed on its
    own, has to do, and what is context rather than a condition inside the
    core. Null, the ordinary case, means it holds wherever TYPO3 is written: an
    API that throws throws in a sitepackage too.
  - `hints` *(array of object, required)*
    - `text` *(string, required)* — The statement itself. It reads the
      same on every version it holds for; the range is beside it, never inside
      it.
    - `since` *(integer or null, required)* — First TYPO3 major this
      holds on. Null means as far back as this knowledge base reaches.
    - `until` *(integer or null, required)* — Last TYPO3 major this
      holds on. Null means it still holds.
    - `versions` *(string, required)* — The same range as a sentence,
      empty when the statement is bound to nothing.
    - `scope` *(string or null, required)* — One of `core`, `project`,
      `extension`, `null`. Which kind of work this statement obliges. "core"
      means it is a condition of a patch to the TYPO3 core and a convention
      anywhere else — the backend's own design system, the changelog
      artifact, the paths of the mono repository. "project" and "extension" are
      the mirror: what the repository around an installation, or a package
      distributed on its own, has to do, and what is context rather than a
      condition inside the core. Null, the ordinary case, means it holds
      wherever TYPO3 is written: an API that throws throws in a sitepackage
      too.
  - `checks` *(array of string, required)* — Commands relevant to this
    hint.
- `availableHints` *(array of object, required)* — The hints that exist in
  the searched domains, returned when none matched. Empty on a hit.
  - `id` *(string, required)* — Ask for this hint outright by passing it
    as id.
  - `title` *(string, required)*
  - `category` *(string, required)*

## `typo3_documentation_lookup`

Search or read the official live TYPO3 documentation for a covered TYPO3 line.
Search with several short English queries; every result carries a canonical
URL. Pass one of those URLs back as page with the same targetVersion to receive
that page as text, including headings and code examples. This reaches
docs.typo3.org, unlike the bundled convention lookups.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: true`

**Takes**

- `queries` *(array of string)* — Short search queries in English. Pass
  alternatives separately, for example ["page title event", "page title
  provider"].
- `page` *(string)* — Canonical page URL returned by an earlier search.
  Pass it with the same targetVersion and without queries to read the page as
  text.
- `targetVersion` *(string, required)* — Covered TYPO3 version whose
  official manual must answer, for example "13.4" or "14". There is no fallback
  to another release.
- `limit` *(integer)*

**Answers with**

- `mode` *(string, required)* — One of `search`, `page`.
- `status` *(string, required)* — One of `answered`, `empty`,
  `unavailable`.
- `targetVersion` *(string, required)* — The exact documentation release
  searched.
- `source` *(string, required)* — The external documentation host.
- `queries` *(array of string, required)*
- `results` *(array of object, required)*
  - `title` *(string, required)*
  - `url` *(string, required)* — Canonical URL of the matching
    documentation page.
  - `document` *(string, required)* — Official document identifier.
  - `documentTitle` *(string, required)*
  - `documentVersion` *(string, required)*
  - `section` *(string, required)*
  - `excerpt` *(string, required)* — Short route into the source, empty
    only when the result page could not be read after its index matched.
  - `content` *(string, required)* — The selected page as text in page
    mode; empty in search mode.
- `unavailable` *(object or null, required)* — Why nothing was answered,
  where status says unavailable. Null otherwise.
  - `cause` *(string, required)* — One of `version-not-covered`,
    `source-not-answering`. version-not-covered: the release asked about is
    outside the ones this server knows the manuals for, and asking again
    changes nothing. source-not-answering: docs.typo3.org did not answer this
    time, and the same call may answer the next.
  - `reason` *(string, required)*

## `typo3_component_lookup`

Look up TYPO3 backend UI components by name or topic. Where the target is the
active installation, its backend CSS, JavaScript, and installed styleguide
templates supply the component contract; the curated catalog supplies the
searchable names and fallback markup. Without usable installed sources, the
bundled version-bound snapshot answers. Returns markup, classes, custom
properties, and every source used.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — Component name, class, or topic, for example badge,
  card, search box, or input-group. Omit to list the catalog.
- `targetVersion` *(string)* — The TYPO3 version the markup has to hold
  for, for example "13.4" or "14". Components not verified there are withheld.
  Defaults to the version of the installation this server was started in; where
  there is none, the whole catalog is returned and every entry carries the
  versions it was verified on.

**Answers with**

- `query` *(string or null)*
- `targetVersion` *(integer or null)* — The TYPO3 major the answer was
  composed for — stated by the caller, or read from the installation. Null
  means nothing was withheld and every entry carries the versions it was
  verified on.
- `matchCount` *(integer, required)* — How many components hold on the
  target version. Ones withheld for it are in withheld, not here.
- `components` *(array of object, required)*
  - `name` *(string, required)*
  - `title` *(string, required)*
  - `summary` *(string)*
  - `rootClass` *(string, required)*
  - `variants` *(array of string)*
  - `modifiers` *(array of string)*
  - `subComponents` *(array of string)*
  - `customProperties` *(array of string)*
  - `markup` *(string)* — Canonical markup of the component.
  - `examples` *(array of string)*
  - `sassPath` *(string or null, required)* — Primary Sass source in the
    core checkout; null for a web component that carries its own styles.
  - `sassPaths` *(array of string)* — Every Sass source the component
    spans. A component can be split across several files.
  - `demoPath` *(string or null, required)* — Styleguide demo in the core
    checkout, if there is one.
  - `matchedIn` *(array of string)* — Where the query matched: name,
    keywords, sub-component classes, description.
  - `classes` *(array of string, required)* — Every class of this
    component found in the installed backend CSS. Empty for a bundled fallback
    or a custom element without external CSS.
  - `sourceFiles` *(array of string, required)* — Installed package files
    consulted for the component contract. Empty for the bundled fallback.
  - `markupSource` *(string, required)* — One of `installation`,
    `catalog`. Whether markup came from an installed styleguide example or the
    bundled curated fallback.
  - `contractVersion` *(string, required)* — TYPO3 version whose classes
    and custom properties this entry describes.
  - `describesVersion` *(string, required)* — TYPO3 version whose markup
    this entry describes. It can differ from contractVersion when the installed
    styleguide has no matching example and bundled markup is the fallback.
  - `since` *(integer or null)* — The TYPO3 major this entry starts
    holding at, or null when it holds on every covered version.
  - `until` *(integer or null)* — The TYPO3 major it stops holding after,
    or null when nothing has replaced it.
  - `verifiedOn` *(string, required)* — The same range as a sentence,
    empty when the entry holds on every covered version.
- `withheld` *(array of object, required)* — Components this catalog has
  but was never verified on the target version. Left out of components rather
  than handed over — an empty answer here means "not verified where you are",
  not "does not exist".
  - `name` *(string, required)*
  - `title` *(string, required)*
  - `sassPaths` *(array of string)* — What to verify the entry against on
    the target version.
  - `demoPath` *(string or null)*
  - `since` *(integer or null)* — The TYPO3 major this entry starts
    holding at, or null when it holds on every covered version.
  - `until` *(integer or null)* — The TYPO3 major it stops holding after,
    or null when nothing has replaced it.
  - `verifiedOn` *(string, required)* — The same range as a sentence,
    empty when the entry holds on every covered version.
- `checklist` *(object)*
  - `title` *(string, required)*
  - `intro` *(string)*
  - `items` *(array of string, required)*
- `componentSource` *(string, required)* — One of `installation`,
  `catalog`. installation when the class and custom-property contract was read
  from the active TYPO3 packages; catalog when the bundled snapshot answered.
- `catalog` *(object, required)* — The core revision behind catalog
  answers, and how it relates to the installation being read. A miss means "not
  in this snapshot".
  - `repository` *(string)*
  - `branch` *(string, required)*
  - `version` *(string, required)* — TYPO3 version of the snapshot.
  - `commit` *(string, required)* — Core revision the catalogs were taken
    from.
  - `verifiedAt` *(string, required)*
  - `installedVersion` *(string or null)* — TYPO3 version of the
    installation this server was started in, where there is one. Null means
    there was nothing to compare the snapshot with.
  - `skew` *(string or null)* — Set when that installation and the
    snapshot are different TYPO3 majors, and what to do about it. Null when
    they agree or nothing is known.

## `typo3_system_extension_lookup`

Answer whether an extension is part of the TYPO3 core, and on which versions:
the system extensions of every covered TYPO3 line, by extension key and
Composer package name, each with what it is for and the range it is shipped on.
Independent of any installation, which is the point — the question comes up
for a package that is not installed, and "is this core" is otherwise answered
from memory. A miss means the name is not a system extension on the covered
versions, never that it does not exist.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — An extension key ("theme_camino"), a Composer
  package name ("typo3/cms-impexp"), or a word from what it does ("redirects").
  Omit to list everything the core ships.
- `targetVersion` *(string)* — The TYPO3 version to answer for, for example
  "13.4" or "14". Restricts the answer to what that line ships. Defaults to the
  version of the installation this server was started in; where there is none,
  every entry comes back with the range it is shipped on.

**Answers with**

- `query` *(string, required)*
- `targetVersion` *(integer or null)* — The TYPO3 major the answer was
  composed for — stated by the caller, or read from the installation. Null
  means every covered version is in the answer and each entry carries its own
  range.
- `matchCount` *(integer, required)* — How many system extensions matched.
  Zero means the name is not one of them on the versions asked about, not that
  no such package exists.
- `extensions` *(array of object, required)*
  - `key` *(string, required)* — The extension key, as the directory
    below typo3/sysext is named.
  - `package` *(string, required)* — The Composer package name to require
    it by, where an installation does not have it already.
  - `description` *(string, required)* — What it is for.
  - `since` *(integer or null, required)* — First covered major that
    ships it. Null means every covered major does.
  - `until` *(integer or null, required)* — Last covered major that ships
    it. Null means it is still shipped on the newest one.
  - `shippedOn` *(string, required)* — The range in words, empty when it
    is shipped everywhere this knowledge base reaches.
- `coveredVersions` *(array of integer, required)* — The TYPO3 majors this
  answer was derived from.

## `typo3_reference_list`

List the worked examples the TYPO3 core ships of its own conventions, and what
each one is a reference for: the theme extension, the styleguide, the Extbase
fixture extension, the content element rendering, the browser test suite, the
static analysis setup. Read one of these before inventing a layout or a test
harness — they are the version-correct, currently-passing form of what a
convention describes, and every hint here is a summary of one. Paths are
relative to a core checkout; where the answer names a Composer package, an
installation that has it holds the same files below vendor/.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `targetVersion` *(string)* — The TYPO3 version to list for, for example
  "13.4" or "14". An example that branch does not have is left out rather than
  qualified. Defaults to the version of the installation this server was
  started in; where there is none, every entry comes back with the range it
  exists on.

**Answers with**

- `targetVersion` *(integer or null, required)* — The TYPO3 major the list
  was composed for — stated by the caller, or read from the installation.
  Null means every covered version is in it and each entry carries its own
  range.
- `matchCount` *(integer, required)* — How many worked examples exist on
  the version asked about.
- `references` *(array of object, required)*
  - `id` *(string, required)* — Stable identifier of the example.
  - `path` *(string, required)* — Where it is, relative to the root of a
    core checkout.
  - `package` *(string or null, required)* — The Composer package that
    ships it, so an installation can read it below vendor/. Null means it
    exists only in the core repository, as everything below Build/ does.
  - `reference` *(string, required)* — What it is a worked example of.
  - `caveat` *(string or null, required)* — What not to conclude from it
    — that it is read rather than depended on, or which part of it is the
    core's own. Null where there is nothing to warn about.
  - `hint` *(string or null, required)* — The architecture hint whose
    conventions it demonstrates, for typo3_architecture_lookup. Null where no
    hint covers the subject yet, which is exactly when reading the example is
    worth most.
  - `since` *(integer or null, required)* — First covered major that has
    it. Null means every covered major does.
  - `until` *(integer or null, required)* — Last covered major that has
    it. Null means the newest one still does.
  - `existsOn` *(string, required)* — The range in words, empty when
    every covered version has it.
- `coveredVersions` *(array of integer, required)* — The TYPO3 majors this
  answer was derived from.

## `typo3_translation_domain_lookup`

Compute the translation domain an XLF file resolves to, from its path. The
domain is the canonical way to reference a label (backend.alt_doc:key) in TCA,
LanguageService::sL() and f:translate, and it is registered nowhere — it
follows from the path by the rules the core itself applies, which live in
TranslationDomainMapper on one branch and TranslationDomainResolver on the
next. Because it is computed, it also answers for a file outside the core and
for one a patch is about to add. Where the version it is composed for is older
than translation domains, it answers with the full LLL:EXT: reference instead:
the domain form renders nothing there and fails at runtime rather than at build
time. That version is the one stated as targetVersion, and the installation
this server was started in where none is — state it when the work is on a
branch other than what is installed.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `path` *(string, required)* — The XLF file path, either as an EXT:
  reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or
  relative to a core checkout
  ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").
- `targetVersion` *(string)* — The TYPO3 version the label is being written
  for, for example "13.4" or "14". It decides one thing here and it decides it
  entirely: below the version that resolves domains the domain form renders
  nothing, so the answer is the LLL:EXT: reference instead. Defaults to the
  installation this server was started in, which is the wrong answer for a
  backport branch or a second checkout — state it there.

**Answers with**

- `path` *(string, required)* — The XLF path the domain was computed from.
- `targetVersion` *(integer or null)* — The TYPO3 major the answer was
  composed for — stated by the caller, or read from the installation. Null
  means neither said, and the domain comes back unqualified: it is the form
  from 14 onwards, and nothing placed this call on a version.
- `domain` *(string or null, required)* — The translation domain it
  resolves to. Null when the path names no extension, and also when the version
  this was composed for is too old to resolve domains at all — there the full
  LLL:EXT: reference is the answer.
- `domainOnNewerVersions` *(string or null)* — Set only in that second
  case: what the domain would be on a version that has them. It is not usable
  on this installation.

## `typo3_label_lookup`

Search the labels registered in the TYPO3 installation you are working in.
Reuse is local to the translation resource already used at the consuming code:
pass resource whenever it is known, and do not reference a match from another
module or package merely because its text is identical. Answered by the
installation itself through its console, with the resource overrides it
applies. Where the console cannot be reached — an installed TYPO3 whose
database has no schema yet is the common case — the same packages' XLF files
are read instead, and answeredBy says which of the two answered.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string, required)* — Words from the label text or its
  trans-unit id, for example "save document" or "labels.title". Several words
  are matched independently, ignoring case and order: a label has to carry
  every one of them, in its text or in its id. When none carries all of them,
  the answer says how far each word reaches on its own.
- `extension` *(string)* — Restrict the search to the extension that owns
  the consuming code.
- `resource` *(string)* — Restrict the search to the exact XLF resource
  already used at the consuming code, for example
  "EXT:my_sitepackage/Resources/Private/Language/Backend/Import.xlf". A match
  from another resource is not a reuse candidate.
- `limit` *(integer)* — Maximum number of labels to return.

**Answers with**

- `query` *(string, required)*
- `matchCount` *(integer)*
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `terms` *(array of object)*
  - `term` *(string, required)* — One word of the query; a label has to
    carry every one of them.
  - `matchCount` *(integer, required)* — How many labels this word alone
    reaches — where to narrow when the query as a whole reaches none.
- `labels` *(array of object)*
  - `ref` *(string, required)* — Translation domain reference
    (package.resource:key) — the canonical form.
  - `domain` *(string, required)*
  - `key` *(string, required)* — The trans-unit id.
  - `source` *(string, required)* — The label text in the searched
    locale.
  - `resource` *(string)* — The XLF file it lives in.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `query`, `resource`,
`matchCount`, `answeredBy`, `terms`, `labels` — or `query`, `unsupported`.

## `typo3_fluid_namespace_list`

List the Fluid ViewHelper namespaces that are globally available in the TYPO3
installation you are working in, so a template knows which prefixes it may use
without declaring them. Every other namespace has to be declared per template
with an xmlns attribute. Answered by the installation itself; where its console
cannot be reached, by the Configuration/Fluid/Namespaces.php the installed
packages declare, which answeredBy reports as "packages".

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes** nothing.

**Answers with**

- `matchCount` *(integer)*
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `namespaces` *(array of object)*
  - `prefix` *(string, required)* — The prefix usable in a template
    without declaring it, for example "core".
  - `phpNamespaces` *(array of string, required)* — The PHP namespaces it
    resolves ViewHelpers from.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `matchCount`,
`answeredBy`, `namespaces` — or `unsupported`.

## `typo3_configuration_lookup`

Read an effective TYPO3_CONF_VARS value from the installation you are working
in — the value as it is at runtime after every extension has had its say, not
the shipped default. Use it for configuration whose assembled shape matters,
such as SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or
SYS/fluid.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `path` *(string, required)* — Slash-separated path into TYPO3_CONF_VARS,
  for example "SYS/fluid" or "SYS/formEngine/formDataGroup".

**Answers with**

- `path` *(string, required)* — The TYPO3_CONF_VARS path that was read.
- `found` *(boolean)* — Whether the installation has a value at that path.
  Present only where one was asked: false is a statement about an installation,
  and where there was none to ask, unsupported stands in place of this answer.
- `value` *(object)* — The effective runtime value, of whatever shape the
  configuration has.
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `path`, `found`,
`answeredBy` — or `path`, `unsupported`.

## `typo3_backend_module_lookup`

List the backend modules registered in the TYPO3 installation you are working
in, with the extension that declares each one, its place in the module tree,
its labels and its route. Answered by the installation, so a project
extension's modules are in it.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — Module identifier, label, route, or extension name
  to filter by. Omit to list every module.

**Answers with**

- `query` *(string, required)*
- `matchCount` *(integer)*
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `modules` *(array of object)*
  - `identifier` *(string, required)*
  - `parents` *(array of string, required)* — The modules it sits under,
    outermost first.
  - `extension` *(string, required)* — The package that declares it.
  - `labels` *(string)* — Its label, with the translation domain
    reference behind it.
  - `path` *(string, required)* — The backend route it answers on.
  - `position` *(string)* — Its declared before/after position, if any.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `query`, `matchCount`,
`answeredBy`, `modules` — or `query`, `unsupported`.

## `typo3_icon_lookup`

Validate or find an icon identifier in the TYPO3 backend icon registry of the
installation you are working in. The registry is read from the running
installation, so what a package registers in a loop or from ext_localconf.php
is in the answer as well as what its Configuration/Icons.php declares; where
the installation cannot be booted — no console, or a checkout with no
configuration yet — the T3Icons set, the package registration files and the
flag images are read instead, answeredBy says 'packages', and the answer states
what that leaves out. Identifiers spell shapes rather than intents, so concept
words are mapped: "warning" finds actions-exclamation-triangle. Backend only:
the identifiers are resolved by IconFactory and rendered by <core:icon>, and a
frontend template can use neither.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — Identifier, identifier fragment, or concept, for
  example "actions-open", "delete", or "warning". Omit to list the categories
  and concept words.
- `limit` *(integer)* — Maximum number of identifiers to return.

**Answers with**

- `query` *(string, required)*
- `matchCount` *(integer)* — For a complete identifier, 1 only when that
  exact identifier is registered and otherwise 0. For a concept search, the
  number of matching icons.
- `suggestionCount` *(integer)* — Related identifiers returned beside an
  identifier validation. The actions-/content- usage prefix alone never makes
  an icon a suggestion.
- `exactMatch` *(boolean)* — Whether the query was a registered identifier.
  False for a query shaped like one that is not registered — the listed icons
  are then suggestions, not the answer.
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `icons` *(array of object)*
  - `identifier` *(string, required)*
  - `category` *(string, required)*
  - `aliasOf` *(string or null, required)* — The identifier this one is
    an alias of.
  - `source` *(string, required)* — Where it is registered: t3icons,
    flags, or the EXT:<key>/Configuration/Icons.php that declares it.
  - `matched` *(integer)* — Query terms it matched.
  - `score` *(integer)*
  - `why` *(array of string)*
- `categories` *(array of string)* — Returned when no query was given.
- `concepts` *(array of string)* — Concept words that map to a shape.
  Returned when no query was given.
- `scope` *(string)* — Where these identifiers may be used: the backend
  registry, not frontend rendering. Carried by every answered lookup.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `query`, `matchCount`,
`suggestionCount`, `exactMatch`, `answeredBy`, `icons` — or `query`,
`unsupported`.

## `typo3_changelog_lookup`

Search the TYPO3 changelog of the installation you are working in: one entry
per breaking change, deprecation, feature and important note, in the version it
was released in. This is the first stop when building on a major you have not
built on recently, not only a lookup after the fact — what separates a
current answer from a two-major-old one is written down here and almost nowhere
else. Answers "what did this version deprecate", "what changed about X", "which
release introduced Y". Read from the core package on disk, so it covers exactly
the versions that installation ships and grows with a Composer update. Every
word of the query has to be carried by an entry; narrow further with type and
version.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `query` *(string)* — Words the entry has to carry, matched against its
  title. Omit to list a version or a type as a whole.
- `type` *(string)* — One of `breaking`, `deprecation`, `feature`,
  `important`. Restrict to one kind of change. Breaking and deprecation are
  what affects existing code.
- `version` *(string)* — Restrict to a version, by prefix: "14" covers 14.0
  through 14.3.x, "13.4" covers 13.4 and 13.4.x.
- `limit` *(integer)* — Maximum number of entries.

**Answers with**

- `query` *(string, required)*
- `matchCount` *(integer)* — Entries carrying every word of the query,
  before the limit.
- `entries` *(array of object)*
  - `type` *(string, required)* — One of `Breaking`, `Deprecation`,
    `Feature`, `Important`.
  - `version` *(string, required)* — The version directory it was
    released in.
  - `issue` *(string, required)* — Forge issue number.
  - `title` *(string, required)*
  - `tags` *(array of string, required)* — Index tags. FullyScanned or
    PartiallyScanned means the extension scanner has a matcher for it.
  - `file` *(string, required)* — EXT: reference of the entry, to read
    the description and the migration.
- `versions` *(array of string)* — The versions this installation ships
  changelog entries for, newest first. Anything outside them is not in this
  answer.
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `query`, `matchCount`,
`entries`, `versions`, `answeredBy` — or `query`, `unsupported`.

## `typo3_project_scope`

Describe the project around the TYPO3 installation this server was started in:
its TYPO3 and PHP constraints, the extensions that are its own rather than
TYPO3's, the sites it configures with the site sets each depends on, and the
commands it declares in composer.json and package.json, each with what running
it does to the sources: a check that hands the code back as it was, a change
that rewrites something, or unknown where the declared body does not say. Read
from files only — no console, no database — so it answers on a fresh clone.
Call it before recommending or running a check: the commands listed here are
the ones that exist in this repository, and the ones marked check are the ones
a task told not to change files can run.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes** nothing.

**Answers with**

- `root` *(string or null)* — Absolute path of the project. Null when there
  is no installation to describe.
- `kind` *(string)* — core-checkout or composer-project.
- `typo3Version` *(string or null)* — The TYPO3 version installed here,
  read from the core package.
- `phpConstraint` *(string or null)* — What composer.json requires of PHP.
- `coreConstraint` *(string or null)* — What it requires of typo3/cms-core.
- `extensions` *(array of object)* — Extensions that are not TYPO3 system
  extensions.
  - `key` *(string, required)*
  - `path` *(string, required)* — Relative to the project root.
  - `origin` *(string, required)* — One of `project`, `third-party`,
    `fixture`. project: inside the repository, so what it is working on.
    third-party: installed as a dependency. fixture: shipped by the
    repository's test setup, below a Tests/ directory, so it exists to be
    loaded by a suite rather than developed.
- `sites` *(array of object)*
  - `identifier` *(string, required)*
  - `base` *(string, required)*
  - `rootPageId` *(integer or null, required)*
  - `sets` *(array of string, required)* — The site sets this site
    depends on, by their composer-style name.
  - `languages` *(array of string, required)*
- `commands` *(array of object)* — What this repository declares. A check
  that is not here does not exist here.
  - `command` *(string, required)* — Ready to run in this repository.
  - `source` *(string, required)* — composer.json or package.json.
  - `declares` *(string, required)* — The body the manifest declares for
    it, lines joined with &&.
  - `runs` *(string, required)* — One of `check`, `change`, `unknown`.
    What running it does to the sources, read off the body rather than by
    running it. check: it reports and hands the code back as it was, so a task
    told not to change files can run it — it may still write a cache of its
    own. change: it rewrites something. unknown: the body does not say, which
    is what a test suite is, because it runs the project's own code.
- `patches` *(array of object)* — Patches from extra.patches. A patched
  package does not behave as its version says.
  - `package` *(string, required)* — The dependency being patched.
  - `description` *(string, required)* — What the patch is for, where
    composer.json says.
  - `file` *(string, required)* — The patch file, relative to the project
    root.
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `root`, `extensions`,
`sites`, `commands`, `patches`, `answeredBy` — or `unsupported`.

## `typo3_extension_scope`

Describe what one installed extension registers: the tables its TCA defines and
the ones it extends, the content elements it adds to tt_content and the Fluid
template each renders through, its backend modules and routes, its icons, its
site sets, the service tags it hangs into the container, its middlewares, its
Fluid roots and namespaces, and the shape of its Classes/ directory — and
what it ships beside all of that: its manual, its README, the test layers it
has, and its XLF files with the source language each one declares. Those four
are answered even when they are not there, because the absence of a manual or a
translation is what a file listing cannot show. The tables, content elements
and icons are read from the booted installation where there is one and
attributed to this extension by the EXT: reference each entry carries, so a
list built in a loop or a table added by a PHP call is in the answer;
everything else is read from that extension's own files, parsed and never
executed, so it answers on a fresh clone and for a third-party extension as
well as for the project's own. answeredBy says which of the two answered, and
where it says packages the answer names what that leaves out.
typo3_project_scope names the extensions this can be called for.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `extension` *(string, required)* — The extension key, as
  typo3_project_scope reports it, for example "my_sitepackage" or "news".

**Answers with**

- `key` *(string, required)* — The extension key that was asked for.
- `path` *(string or null)* — Absolute path of the extension. Null when the
  installation does not have it.
- `origin` *(string or null)* — One of `system`, `project`, `third-party`,
  `fixture`, `null`. system: TYPO3's own. project: inside the repository.
  third-party: installed as a dependency. fixture: below a Tests/ directory, so
  it belongs to the test setup.
- `composerName` *(string or null)* — The Composer package name it
  declares.
- `description` *(string or null)* — What its composer.json says it is.
- `requires` *(array of object)* — What it requires, which is where a
  version conflict during an upgrade comes from.
  - `package` *(string, required)*
  - `constraint` *(string, required)*
- `tcaTables` *(array of string)* — Tables its Configuration/TCA/ defines,
  by file name.
- `tcaOverrides` *(array of string)* — Tables it extends below
  Configuration/TCA/Overrides/.
- `contentElements` *(array of object)* — The content elements it adds to
  tt_content, and where each renders.
  - `identifier` *(string, required)* — The CType value, read from an
    addTcaSelectItem() call in one of those override files. An identifier
    assembled at runtime or taken from a constant is not among them.
  - `templateName` *(string or null, required)* — The Fluid template it
    renders through, from tt_content.<identifier>.templateName in this
    extension's TypoScript. Null where its TypoScript does not set one —
    another extension or the site configuration may.
  - `source` *(string or null, required)* — The TypoScript file that set
    it, relative to the extension.
- `backendModules` *(array of string)* — Module identifiers from
  Configuration/Backend/Modules.php.
- `backendRoutes` *(array of string)* — Route names from
  Configuration/Backend/Routes.php and AjaxRoutes.php.
- `icons` *(array of string)* — Identifiers from Configuration/Icons.php.
  typo3_icon_lookup searches every package at once.
- `siteSets` *(array of object)*
  - `name` *(string, required)* — The composer-style set name a site
    depends on.
  - `path` *(string, required)* — Relative to the extension.
- `middlewares` *(array of string)* — Middleware identifiers from
  Configuration/RequestMiddlewares.php, across the request scopes.
- `serviceTags` *(array of string)* — Tags its Services.yaml carries, such
  as data.processor, event.listener or console.command.
- `fluidRoots` *(array of string)* — Which of Resources/Private/Templates,
  Partials and Layouts exist.
- `fluidNamespaces` *(array of string)* — Prefixes it registers globally in
  Configuration/Fluid/Namespaces.php.
- `typoScript` *(array of string)* — Files below Configuration/TypoScript/.
- `classes` *(array of object)*
  - `kind` *(string, required)* — The Classes/ subdirectory, for example
    EventListener or DataProcessing.
  - `files` *(integer, required)* — PHP files below it.
- `files` *(array of string)* — Registration files it ships, from
  ext_localconf.php to Initialisation/data.t3d.
- `notReadStatically` *(array of string)* — Registration files that are
  there but whose entries do not stand in their own text: each assembles its
  list while it runs, so what it registers is missing from the lists above
  rather than absent. The booted installation is what answers for them; an
  empty list here means every file that exists was read.
- `artifacts` *(object)* — What it ships beside its registrations. Every
  key is present even when the artifact is not, because the absence of a
  manual, a test or a translation is the answer a file listing cannot give.
  - `manual` *(string or null, required)* — Its manual entry point,
    "Documentation/" where the directory exists without one, null where the
    extension ships no manual at all.
  - `readme` *(string or null, required)* — The README it ships, null
    where there is none.
  - `tests` *(array of string, required)* — The layers below Tests/, for
    example Unit and Functional. Empty where the extension ships no tests.
  - `languageFiles` *(array of object, required)*
    - `path` *(string, required)* — Relative to the extension.
    - `sourceLanguage` *(string or null, required)* — The
      source-language its own <file> element declares, null where it declares
      none. This is what the file says, not what it should say.
    - `translations` *(array of string, required)* — Locales of the
      prefixed files beside it, such as de for de.messages.xlf.
- `installed` *(array of string)* — On a miss: the extension keys this
  installation does have.
- `answeredBy` *(string)* — One of `installation`, `packages`.
  installation: its assembled runtime state answered. packages: read from the
  files the installed packages ship, because the console could not be asked —
  overrides applied at runtime are not reflected.
- `unsupported` *(object)*
  - `cause` *(string, required)* — One of `no-installation`,
    `misconfigured`, `installation-not-answering`. no-installation: nothing to
    ask from here, and searched says where it looked. misconfigured: an
    installation was named and could not be used, so nothing was searched for.
    installation-not-answering: one was found and its console did not answer
    — a stopped container or a database with no schema, which is a state that
    ends without reinstalling anything.
  - `reason` *(string, required)* — What stopped it, in the words the
    attempt produced.
  - `diagnosis` *(string)* — What the reason means where the message
    alone does not say it — a console that starts and then fails on a missing
    table has a database without a schema, not a broken installation. Empty
    where nothing beyond the reason is known.
  - `searched` *(array of string, required)* — Every directory the
    discovery walked, in order. "Nothing was found" and "the server was started
    somewhere else" wear one sentence, and only this tells them apart. Empty
    where discovery never ran.
  - `misconfiguration` *(string or null)* — What was set and could not be
    used. Null where nothing was set.
  - `settings` *(object, required)*
    - `root` *(string, required)* — Environment variable that names the
      installation root.
    - `console` *(string, required)* — Environment variable that names
      the console command.

The answer carries exactly one of these sets of fields: `key`, `path`,
`origin`, `tcaTables`, `tcaOverrides`, `contentElements`, `backendModules`,
`icons`, `siteSets`, `serviceTags`, `files`, `notReadStatically`, `artifacts`,
`answeredBy` — or `key`, `unsupported`.

## `typo3_catalog_scope`

Report whether component contracts come from the active installation or the
bundled fallback, which TYPO3 core revision the fallback catalogs were taken
from, what they cover, and how to re-check them. Call this to judge whether a
lookup miss is authoritative: even with installed sources, component names
remain a curated index rather than every backend class.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `targetVersion` *(string)* — The TYPO3 version to report the catalog's
  coverage for, for example "13.4" or "14". Defaults to the version of the
  installation this server was started in.

**Answers with**

- `catalog` *(object, required)* — The core revision behind catalog
  answers, and how it relates to the installation being read. A miss means "not
  in this snapshot".
  - `repository` *(string)*
  - `branch` *(string, required)*
  - `version` *(string, required)* — TYPO3 version of the snapshot.
  - `commit` *(string, required)* — Core revision the catalogs were taken
    from.
  - `verifiedAt` *(string, required)*
  - `installedVersion` *(string or null)* — TYPO3 version of the
    installation this server was started in, where there is one. Null means
    there was nothing to compare the snapshot with.
  - `skew` *(string or null)* — Set when that installation and the
    snapshot are different TYPO3 majors, and what to do about it. Null when
    they agree or nothing is known.
- `verifyCommand` *(string, required)*
- `scope` *(object, required)* — One entry per catalog describing what it
  contains.
- `counts` *(object, required)* — One entry per catalog with its number of
  entries.
- `targetVersion` *(integer or null)* — The TYPO3 major the coverage was
  reported for — stated by the caller, or read from the installation. Null
  means the whole catalog answers.
- `verifiedCount` *(integer, required)* — How many components were verified
  on that version.
- `componentSource` *(string, required)* — One of `installation`,
  `catalog`.
- `withheld` *(array of object, required)* — Components this catalog has
  but was never verified on the target version. Left out of components rather
  than handed over — an empty answer here means "not verified where you are",
  not "does not exist".
  - `name` *(string, required)*
  - `title` *(string, required)*
  - `sassPaths` *(array of string)* — What to verify the entry against on
    the target version.
  - `demoPath` *(string or null)*
  - `since` *(integer or null)* — The TYPO3 major this entry starts
    holding at, or null when it holds on every covered version.
  - `until` *(integer or null)* — The TYPO3 major it stops holding after,
    or null when nothing has replaced it.
  - `verifiedOn` *(string, required)* — The same range as a sentence,
    empty when the entry holds on every covered version.

## `typo3_commit_message_guide`

Draft and check a TYPO3 commit message. Either assemble one from parts
(changeType plus summary) or pass an existing message to check and correct it.
The returned draft is ready to commit: the body is wrapped at 72 characters,
with fenced code, indented blocks, list structure, and long URLs left intact.
Defaults to the core contribution rules; pass workflow="project" in a project
or extension repository of your own, where the subject and body conventions
apply but the Forge issue, the Releases: trailer and the changelog do not.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `message` *(string)* — A complete existing commit message to check,
  subject and trailers included. Unknown trailers such as Change-Id are kept,
  so an amended patch set stays valid.
- `workflow` *(string)* — One of `core`, `project`. Which rules to apply.
  "core": a patch against the TYPO3 core, with the Forge issue and the
  Releases: trailer required. "project": any other repository — the keyword,
  the 52/72 character limits and the wrapping are checked, no trailer is added
  or demanded, and [SECURITY] is allowed.
- `changeType` *(string)* — One of `BUGFIX`, `FEATURE`, `TASK`, `DOCS`,
  `SECURITY`. TYPO3 commit message keyword. [SECURITY] is reserved for the
  TYPO3 Security Team and is only accepted with workflow="project".
- `summary` *(string)* — Summary text without the TYPO3 keyword prefix.
- `issue` *(string)* — Forge issue number, with or without leading #.
- `relatedIssues` *(array of string)* — Optional related Forge issue
  numbers.
- `releases` *(array of string)* — Target releases, for example main or
  13.4. Left out, the draft carries a RELEASE_TARGET placeholder and the checks
  ask for it — the branches a change is released on are not guessed.
- `body` *(string)* — Optional commit body. It is wrapped at 72 characters
  in the draft.
- `isBreaking` *(boolean)* — Whether this is a breaking change requiring
  [!!!].
- `isDeprecation` *(boolean)* — Whether this is a deprecation.

**Answers with**

- `message` *(string, required)* — The commit message, ready to use.
- `checks` *(array of object, required)*
  - `level` *(string, required)* — One of `error`, `warning`, `info`.
  - `code` *(string, required)* — Stable identifier of the check, for
    example summary-too-long.
  - `message` *(string, required)*
- `workflow` *(string, required)* — One of `core`, `project`. Which rules
  the draft was written and checked against. "core" adds the Forge issue and
  the Releases: trailer and demands them; "project" applies the subject and
  body rules alone.

## `typo3_feedback_record`

Leave feedback about a gap, wrong answer, or missing capability of this
knowledge server — and about what it did well, because what worked is what
must not be broken later. The feedback is stored as markdown in this server's
own checkout — not in the project you are working in, so do not look for the
file there — and is read back with typo3_feedback_list. Use it whenever an
answer was incomplete or a lookup found nothing that should have been there.
One feedback per subject: a feedback carrying three complaints is worked off
three times over or not at all.

`readOnlyHint: false` · `destructiveHint: false` · `idempotentHint: false` · `openWorldHint: false`

**Takes**

- `observation` *(string, required)* — What was missing, wrong, or
  unhelpful. Be specific enough to act on later, and open with one line naming
  the task you were given, so the feedback can be traced back to what exposed
  it. Written in English, like everything else here.
- `model` *(string, required)* — The model recording this feedback, as it
  identifies itself, for example claude-opus-5 or gpt-5.3-codex. Read it where
  it is written down — what your client reports for the current session, or
  the person running you — rather than from what you remember about yourself.
  A feedback about what a session did or did not do is evidence about one
  model's behaviour, and one filed as "unknown" cannot be told apart from
  another model's. That fallback is for a session that looked and could not
  find out; an invented identifier is still worse than none.
- `category` *(string)* — One of `missing-knowledge`, `wrong-answer`,
  `tool-gap`, `bug`, `idea`. missing-knowledge: the knowledge base lacks the
  answer. wrong-answer: the answer was incorrect. tool-gap: no tool covers the
  need. bug: the server misbehaved. idea: anything else.
- `tool` *(string or array)* — The tool the observation is about, for
  example typo3_component_lookup, or the skill it activated, for example
  typo3-extension-conformance. Several may be named, as a list or separated by
  commas.
- `query` *(string)* — The arguments that produced the unsatisfying result,
  or the task text where a whole session is what produced it. This is what lets
  somebody re-run the feedback against a later version of the server instead of
  reading it.
- `suggestion` *(string)* — What the server should have answered or should
  be able to do instead.

**Answers with**

- `file` *(string, required)* — Path of the recorded feedback, relative to
  this server's own checkout.
- `path` *(string, required)* — The same feedback as an absolute path. It
  is in the server's checkout, not in the project the feedback was recorded
  from.

## `typo3_feedback_list`

List improvement feedback recorded via typo3_feedback_record, newest first, so
they can be worked off. Filter by status, by category, or by the tool a
feedback is about. A feedback that was worked off is kept, not deleted, so
status="closed" answers "what became of what I reported" — the feedback as it
was recorded, plus the commit that closed it.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

**Takes**

- `status` *(string)* — One of `open`, `closed`, `all`. open: the feedback
  still in the backlog. closed: the ones already worked off, each with the
  commit subject saying what came of it. all: both. The category and tool
  filters apply to either.
- `category` *(string)* — One of `missing-knowledge`, `wrong-answer`,
  `tool-gap`, `bug`, `idea`. Restrict the list to one category.
- `tool` *(string)* — Restrict the list to the feedback about one tool, for
  example typo3_label_lookup. A feedback naming several tools is matched by
  each of them.
- `limit` *(integer)* — Maximum number of feedback to return.

**Answers with**

- `count` *(integer, required)*
- `notes` *(array of object, required)*
  - `file` *(string, required)*
  - `date` *(string, required)*
  - `category` *(string, required)*
  - `status` *(string, required)* — open while the feedback is in the
    backlog, closed once it was worked off and moved to the archive.
  - `model` *(string, required)* — The model that left the feedback.
    "unknown" where it named none or predates the field.
  - `tool` *(string, required)* — The tools the feedback is about,
    comma-separated. Empty when it names none.
  - `tools` *(array of string, required)* — The same names as a list, to
    filter or group by without parsing.
  - `title` *(string, required)*
  - `closedBy` *(object or null, required)* — The commit that worked the
    feedback off. Null while the feedback is open.
    - `commit` *(string)*
    - `date` *(string)*
    - `subject` *(string)* — The commit subject: what came of the
      feedback.
