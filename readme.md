# TYPO3 CMS MCP

A local MCP server (plain PHP) that gives MCP-enabled clients a curated TYPO3
**knowledge base**, for the three audiences that work with TYPO3: the core
contributor, the extension author, and the site developer. It holds the
conventions each subsystem is built on and how its mechanisms are used, the
core's own contribution process — rules, the Gerrit workflow, script and
`runTests.sh` notes, commit message conventions — and a catalog of backend UI
components: context that is otherwise spread across project knowledge, core
conventions, and the official documentation.

Almost everything it answers comes from the bundled `knowledge/` files. Broad
API, reference and tutorial questions can instead be searched in the official
live TYPO3 documentation, with the requested documentation release and
canonical source kept on every result. Component names and summaries are a
curated bundled index, but when an installation is available its backend CSS,
JavaScript, and styleguide templates supply the component contract; the bundled
markup is the fallback. The bundled answers are bound to
versions: the knowledge base covers several TYPO3 lines, and a statement that
does not hold on all of them carries the ones it does hold on. Pass a
`targetVersion`, or let the installation being read decide.

**Those files are trained by being used.** An agent gets a real task in a real
checkout and works under one rule: whatever it would otherwise search for — a
convention, a class name, the markup a component expects — it asks this server
first. Where the server answers, that answer is what the task is done from. Where
it does not, the agent solves the task on its own, the way it would have without
the server at all — and that is the half worth something, because the agent now
holds an answer the knowledge base did not have. So the session ends by handing it
back: every avoidable mistake it ran into, and every recommendation it would give
the next agent on the same task. A gap found that way arrives with its answer
attached, which is what separates it from one guessed at from the outside; see
[Improvement notes](#improvement-notes) for how such a note is written and closed.

**It is queried in English**, whatever language the user is speaking. The
knowledge is written in English and the matching is lexical, so a query in
another language reaches only the words the two happen to share — the technical
loanwords — and otherwise comes back empty. The agent translates the subject
before calling and the answer back afterwards; the server states this in the
instructions it sends at initialize and in `typo3_server_scope`. Supporting a
second query language would mean translating the knowledge base, not the
query — a lexical matcher has nothing else to match on.

**It is a conventions catalog, not a patch assistant.** It cannot see your
branch, your changed files, or which tests cover them — that stays the agent's
job in the checkout. What it does is answer, for a concrete task or path, which
conventions apply and which check to run. `typo3_server_scope` states this
boundary in full, and `typo3_task_guide` names what has to be established in the
checkout.

**The conventions are the core's own**, and several of them — the changelog, the
Gerrit workflow, the `runTests.sh` suites — have no counterpart in a project or
an extension. So the answers say which repository they are for, and they work it
out from structure rather than from wording: an area the installation knows as
somebody's extension, a path in extension layout, and last the installation
itself, because a Composer project is not a core checkout. What transfers is
still answered; what only the core has is left out with the reason.

**And where they cannot be followed at all, they are not offered.** Marking an
answer as core-only tells a site developer what it is worth; it does not keep
the tool that gives it out of the list, and the tool list is the first thing a
client pays for. So the list itself varies: started in a Composer project, the
server leaves out the core contribution surface — the review rules, the Gerrit
workflow, the `runTests.sh` suites — and keeps everything that transfers plus
everything the installation answers. `TYPO3_MCP_PROFILE` decides it outright,
`all` or `project`, and `typo3_server_scope` — which every profile has — names
the active one and what it left out. A caller that wants to remove only selected
tools sets `TYPO3_MCP_EXCLUDE_TOOLS` to their comma-separated names; this is
applied after the profile, and the scope answer names those omissions too.

**One exception, and it is deliberate.** Some questions have no bundled answer
that could be right. Which labels exist, which icons are registered, which
backend modules there are, what a configuration value actually is after every
extension has had its say — all of those are properties of an installation, not
of TYPO3. So the server finds the installation you are working in and asks *it*,
through its own console wherever a command exists: `language:domain:search`,
`debug:backend:modules`, `fluid:namespaces`, `configuration:show`. The icon
registry has no command, so it is read from the packages instead — parsed, never
included. Discovery starts at the working directory
the MCP client launched the server in, and only `bin/typo3-cms-mcp` enables it:
a request-serving endpoint has no such relationship to its callers, and its
document root may itself sit inside an installation. Where the project runs
under DDEV the console is invoked there, because the project declares the PHP
version it needs and the host machine may not have it. A stopped project is
reported, never started — and where an interpreter on this machine reaches its
console anyway, the answer says what that leaves out: everything that boots
TYPO3 against a database that is not running. Where the layout is one discovery
cannot walk to, two
environment variables end the guessing: `TYPO3_MCP_ROOT` names the installation
and `TYPO3_MCP_CONSOLE` the command that reaches its console, for example
`ddev exec .build/bin/typo3`. `typo3_server_scope` names the installation it is
reading, how it got there, and whether the console is reachable.

It is built on the official [`mcp/sdk`](https://packagist.org/packages/mcp/sdk)
and speaks **stdio** (`bin/typo3-cms-mcp`): the MCP client launches it as a
subprocess, so there is no server to host, no network exposure, and no auth to
configure — the process boundary is the trust boundary. Request serving is
read-only apart from the explicit feedback tool. Setup writes only when
requested: `bin/typo3-cms-mcp install` adds this server to `.mcp.json`;
`install --agent=codex` adds the Codex MCP entry and publishes the task skills,
and `update --agent=codex` replaces the complete task-skill directories this
package owns. Existing unrelated settings and skills are preserved, and a
different server entry is never replaced.

## Install

Requirements: **PHP 8.2+** and Composer. The package works both ways — as a
standalone checkout and as a Composer dependency of another project.

### Standalone

Clone the repository and install the dependencies once:

```bash
composer install
```

Then install the entrypoint into the current project's `.mcp.json`:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp install
```

For Codex, install its project configuration and task skills directly:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp install --agent=codex
```

Refresh them after updating this package:

```bash
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp update --agent=codex
```

Install and update add `typo3-cms-mcp.json` and the package-owned skill
directories to the project's `.gitignore`. They do not ignore merged agent or
MCP configuration such as `.codex/config.toml` or `.mcp.json`, which may be
shared by the project.

It writes the following shape with the actual absolute path:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp"]
    }
  }
}
```

This is the setup to use when working on the knowledge base itself, since the
`feedback/` tools only exist in a checkout.

### As a dependency

The package is not published on Packagist, so it is required from a local
checkout (or a Git URL) through a `repositories` entry in the consuming
project's `composer.json`:

```json
{
  "repositories": [
    { "type": "path", "url": "/absolute/path/to/typo3-cms-mcp" }
  ]
}
```

```bash
composer require typo3/cms-mcp
```

Composer then exposes the stdio entrypoint as `vendor/bin/typo3-cms-mcp`.
Install it from the consuming project's root:

```bash
vendor/bin/typo3-cms-mcp install
```

Use `vendor/bin/typo3-cms-mcp install --agent=codex` and
`vendor/bin/typo3-cms-mcp update --agent=codex` for the corresponding Codex
setup.

The same commands support the agent identifiers `amp`, `junie`, `cursor`,
`claude`, `copilot`, `factory`, `kiro`, `opencode`, `antigravity`, `zed`,
`pi`, and `grok`. Each receives the skill at its native project path and,
where the client supports it, its native MCP configuration. Antigravity and Pi
receive skills only.

That writes the same `.mcp.json` shape with an absolute path. In a DDEV project,
run the installer inside DDEV:

```bash
ddev exec vendor/bin/typo3-cms-mcp install --agent=codex
```

The project directory is mounted, so the skills are available to the host at
`.agents/skills`. The generated MCP entry deliberately starts the server with
the project's container PHP, at the `config.bin-dir` the project declares —
`.build/bin/typo3-cms-mcp` in the layout most extension repositories use:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "ddev",
      "args": ["exec", "php", "vendor/bin/typo3-cms-mcp"]
    }
  }
}
```

Outside DDEV — and in a DDEV project that never required the package, where the
container would not see the checkout the server runs from — the generated
configuration uses the absolute entrypoint:

```json
{
  "mcpServers": {
    "typo3-cms-mcp": {
      "type": "stdio",
      "command": "php",
      "args": ["/absolute/path/to/project/vendor/bin/typo3-cms-mcp"]
    }
  }
}
```

The knowledge base ships inside the package, so nothing else needs to be
deployed or configured.

Clients that expose MCP prompts also list `commit_message`. It turns a
summary into the same checked draft as `typo3_commit_message_guide`; the rules
remain in the guide rather than being duplicated in the prompt.

Task skills are authored once below `skills/`. They contain routing and order,
not a second copy of tool answers; client installation publishes them from that
source.

### Smoke test

Two JSON-RPC lines on stdin are enough to see the server come up and list its
tools:

```bash
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"t","version":"1"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' \
  | php bin/typo3-cms-mcp
```

### Core checkouts

The knowledge is bound to TYPO3 versions, so writing it means checking a
statement on both sides of the boundary it claims. `knowledge/versions.json`
declares the lines that are covered, and one command turns them into checkouts
this repository owns:

```bash
bin/core-checkouts            # create what is missing, update what is there
bin/core-checkouts --status   # what exists, at which revision
```

They land below `.checkouts/`, which is gitignored — one treeless clone plus a
worktree per version, so four lines share one object store (under a gigabyte in
total). Nothing at runtime reads them: they are how the knowledge is verified,
not where the answers come from.

### Tests

```bash
composer ci
```

Lints, runs the static analysis, and runs the test suite: the search and
ranking logic, every tool against its declared schemas and annotations, and the
stdio entrypoint driven as a real subprocess. CI runs the same command on every
supported PHP version.

## Tools

Every tool answers twice: the readable text, and the same answer as
`structuredContent` matching the `outputSchema` the tool declares — matches with
their source, coverage and score, checks as command strings, components, icons
and labels as typed records, commit diagnostics as `level`/`code`/`message`. So
composing several tools does not mean parsing headings and code fences back out
of prose. All tools are annotated `readOnlyHint`; only `typo3_feedback_record`
writes anything, and then only a new file.

Names are `typo3_<subject>_<verb>`, with the verb taken from a fixed set —
`lookup` finds and may find nothing, `guide` composes an answer for a task,
`list` enumerates, `scope` states what a source covers, `record` writes. So the
name already says what shape the answer has.

- `typo3_server_scope`: orientation — what this server covers and at which
  depth, what it deliberately does not, and which tool to call when. Every
  covered topic says what its answers are worth outside the core: `core-only`
  for the contribution process and the scripts of that repository,
  `transferable` for a convention that holds wherever TYPO3 is written,
  `installation` for what is read from the installation itself. It also names
  the active profile and the tools it leaves out, so a shorter list than this
  one has a reason a client can read. The place to start when it is unclear
  whether a question can be answered here at all.
- `typo3_changelog_lookup`: searches the TYPO3 changelog the installed core
  ships — one entry per breaking change, deprecation, feature and important
  note, by words, type and version. Answers what a release changed rather than
  how to write such an entry, and names the versions that installation covers so
  a gap is visible. Nothing is bundled: it grows with a Composer update.
- `typo3_project_scope`: describes the project around the discovered
  installation — its TYPO3 and PHP constraints, the extensions that are its own,
  the sites it configures with the site sets they depend on, and the commands it
  declares in `composer.json` and `package.json`. Files only, so it answers on a
  fresh clone; and the commands it lists are the ones that exist there. The
  dependencies it patches are in it too, because a patched package does not
  behave as its version says.
- `typo3_extension_scope`: what one of those extensions registers — the tables
  its TCA defines and the ones it extends, the content elements it adds and the
  template each renders through, its backend modules and routes, its icons, its
  site sets, its service tags, its middlewares, its Fluid roots and
  namespaces, the shape of its `Classes/`. Declaration files are parsed, never
  executed, so it answers for a third-party extension as well as for the
  project's own; the table an override file extends is read from what the file
  does, because those files are numbered rather than named after their table.
- `typo3_rule_lookup`: searches local TYPO3 core rules and script notes, ranked
  by the query terms that separate one section from the rest rather than by
  overlap, and naming the architecture hints that match the same question — the
  caller does not have to know which of the two corpora holds a subject.
- `typo3_script_lookup`: finds matching notes for TYPO3 core commands. They run
  in a core checkout, and the answer says so — outside one it returns the
  boundary instead of commands that are not there.
- `typo3_task_guide`: builds a task checklist enriched with matching
  architecture hints and relevant core checks. A task that reads as work on a
  project or third-party extension says so first and keeps only what transfers:
  no core checks, no checklist item and no follow-up naming something that only
  the core repository has.
- `typo3_test_run_guide`: recommends `Build/Scripts/runTests.sh` commands
  by topic. Paths that read as a project or third-party extension get no suite:
  the script is part of the core repository. What such an extension needs
  instead — assembling a phpunit suite of its own — is the
  `project-extension-tests` architecture hint.
- `typo3_architecture_lookup`: returns architecture hints for TYPO3 core paths or
  task topics, grouped by section. Outside the core the hints stay and their
  check commands are dropped. Two sections are the backend interface's own —
  `Backend CSS` and `Backend TypeScript` — and are withheld with a reason when
  the task names the frontend, because there they would be inverted advice
  rather than merely irrelevant. An answer that matched nothing lists the hint
  ids there are, and `id` asks for one of them outright.
- `typo3_documentation_lookup`: searches the public tables of contents of TYPO3
  Explained, TypoScript Explained and the TCA Reference on `docs.typo3.org`, for
  an explicitly requested covered release. A compound name is taken apart, so
  `AssetCollector` reaches the page called "Assets". Every result carries its canonical URL, document,
  version and section. Pass that URL back as `page` with the same
  `targetVersion` to read its headings, prose and code examples as text. It
  never falls through to another release, and an unreachable service is
  distinct from a search that answered with no match.
- `typo3_component_lookup`: looks up backend UI components by name or topic and
  returns markup, classes, the custom-property contract, and every source used.
  When `targetVersion` is the active installation, the installed backend CSS
  and JavaScript decide the contract and an installed styleguide example wins
  over bundled markup. The curated catalog remains the searchable index and
  fallback for other versions or missing package evidence; every field says
  which version and source it describes.
- `typo3_system_extension_lookup`: says whether an extension is part of the
  core and on which versions, by extension key or Composer package name, with
  what it is for. It answers for an extension that is not installed, which is
  when the question comes up; a miss means "not a system extension on these
  versions", never "does not exist".
- `typo3_reference_list`: names the worked examples the core ships of its own
  conventions — the theme extension, the styleguide, the Extbase fixture
  extension, the content element rendering, the browser suite, the static
  analysis setup — with one line on what each is a reference for and which
  versions have it. Every hint here is a summary of something that exists in
  full and passing, and where a hint is thin, reading the example is the better
  answer.
- `typo3_icon_lookup`: validates and discovers icon identifiers (the registered
  T3Icons names), grouped by category, so unknown identifiers are caught before
  runtime. Every answer says which half of TYPO3 they belong to: the registry is
  the backend's, and frontend rendering reaches none of it.
- `typo3_label_lookup`: searches the labels the installation has registered (XLF
  trans-units) and returns the domain reference (`package.resource:key`) and the
  source text. Pass the XLF `resource` already used at the consuming code:
  reuse is local to that resource, and a matching label in another module or
  package is not a shared vocabulary.
  Several words are matched independently, ignoring case and order: a label has
  to carry every one of them, and where none does, the answer says how far each
  word reaches on its own. Where the console cannot be reached — an installed
  TYPO3 whose database has no schema yet — the same packages' XLF files are read
  instead, and `answeredBy` says which of the two answered.
- `typo3_commit_message_guide`: drafts and checks TYPO3 commit messages — from
  parts, or by passing an existing `message` to check and correct one in a
  piece. The emitted draft is ready to commit: the body is wrapped at 72
  characters, and fenced code, indented blocks, lists, and long URLs keep their
  shape. It defaults to the core contribution rules, Forge issue and `Releases:`
  trailer included; `workflow="project"` keeps the subject and body conventions
  and drops the trailers, for the repositories that use the one without having
  the other.
- `typo3_feedback_record`: records what was missing, wrong, or unhelpful about an
  answer as a note under `feedback/` (standalone checkout only, see
  [Improvement notes](#improvement-notes)).
- `typo3_feedback_list`: lists those notes, newest first, so they can be worked
  off, filtered by status, category or the tool they are about. `status="closed"`
  reads the ones already worked off out of the commits that deleted them, each
  with the subject that says what came of it (standalone checkout only).

## Resources

- `typo3://core`: knowledge index — the server's scope and routing, plus the
  available knowledge documents.
- `typo3://core/{documentId}`: Markdown resource for a single knowledge
  document, for example `typo3://core/typo3-core-rules`.

## Knowledge base

Everything the tools and resources answer from lives in `knowledge/`. The
markdown documents are what `typo3://core/{documentId}` serves; the JSON files
drive the individual tools:

- `typo3-core-rules.md`
- `typo3-core-scripts.md`
- `typo3-commit-messages.md`
- `typo3-gerrit-workflow.md`
- `typo3-contribution-sources.md`
- `architecture-hints/` (one JSON file per section: `php.json`, `fluid.json`,
  `typoscript.json`, `css.json`, `typescript.json`, `general.json`)
- `catalog/` (the component catalog: `components.json`,
  `component-checklist.json`, `meta.json`, and the system extensions:
  `system-extensions.json`)
- `test-suite-hints.json`, `task-intents.json`, `icon-concepts.json`,
  `server-scope.json`, `versions.json`

All knowledge files are read fresh on every request, so editing them takes
effect immediately — no restart or rebuild.

Useful upstream sources:

- TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
- TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

## Improvement notes

What an agent hands back at the end of a task is recorded through
`typo3_feedback_record`, and every note becomes its own markdown file under
`feedback/`; `typo3_feedback_list` reads them back, newest first. A note is
closed by deleting it in the commit that implements the improvement, so
`feedback/` only ever holds open items — and that commit is where
the closed half is read back from: `typo3_feedback_list` with `status="closed"`
answers what became of a note, so a gap that was already closed is not reported a
second time and one that needed a code change does not vanish.

Each note carries the working directory the session that left it ran in, as
`directory:` in its front matter, so a gap can be checked against the project it
was found in rather than against whatever is at hand. It is the same directory
the stdio entrypoint hands to instance discovery; a note left over an endpoint
that has none carries none.

Both tools exist **only in a standalone checkout**. Installed as a Composer
dependency the package lives in `vendor/`, where anything written would be lost
on the next `composer install`; there the server stays strictly read-only and
neither tool appears in `tools/list`.

Working on this repository — layout, conventions, and how notes are worked off —
is documented in [AGENTS.md](AGENTS.md).
