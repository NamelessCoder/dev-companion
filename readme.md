# TYPO3 CMS MCP

A local MCP server (plain PHP) that gives MCP-enabled clients a curated TYPO3
**knowledge base**, for the three audiences that work with TYPO3: the core
contributor, the extension author, and the site developer. It holds the
conventions each subsystem is built on and how its mechanisms are used, the
core's own contribution process — rules, the Gerrit workflow, script and
`runTests.sh` feedback, commit message conventions — and a catalog of backend UI
components: context that is otherwise spread across project knowledge, core
conventions, and the official documentation.

**It answers from three sources.** Almost everything comes from the bundled
`knowledge/` files, which are bound to versions: a statement that does not hold
on every covered TYPO3 line carries the ones it does. Broad API, reference and
tutorial questions are searched in the official live TYPO3 documentation
instead, with the requested release and canonical source on every result. And
some questions have no bundled answer that could be right — which labels exist,
which icons are registered, what a configuration value is after every extension
has had its say. Those are properties of an installation, so the server finds
the one you are working in and asks *it*, through its own console or by booting
it in a subprocess of its own. Where that cannot be done, the packages are read
instead and the answer says so and what it leaves out.

**It is queried in English**, whatever language the user is speaking. The
knowledge is written in English and the matching is lexical, so a query in
another language reaches only the technical loanwords the two share. The agent
translates the subject before calling and the answer back afterwards; the server
states this in the instructions it sends at initialize and in
`typo3_server_scope`.

**It writes no patch; it is what one is written from.** Which files changed,
which branch you are on and which tests cover them stay in the checkout and are
read there. What the writing needs is here: which conventions govern a concrete
path, which markup, icon identifier and XLF resource have to be literally right,
which deprecation lands in between, which check the change has to survive, and
what the commit message says.

**The conventions are the core's own**, and several of them have no counterpart
in a project or an extension — the changelog, the Gerrit workflow, the
`runTests.sh` suites. So the answers say which repository they are for, worked
out from structure rather than from wording. What transfers is still answered;
what only the core has is left out with the reason, and in a Composer project
the tools that carry it are not offered at all.

**Those files are trained by being used.** An agent gets a real task in a real
checkout and works under one rule: whatever it would otherwise search for, it
asks this server first. Where the server answers, that answer is what the task
is done from. Where it does not, the agent solves the task on its own — and that
is the half worth something, because the agent now holds an answer the knowledge
base did not have. So the session ends by handing it back, and a gap found that
way arrives with its answer attached. See
[Improvement feedback](#improvement-feedback).

It is built on the official [`mcp/sdk`](https://packagist.org/packages/mcp/sdk)
and speaks **stdio** (`bin/typo3-cms-mcp`): the MCP client launches it as a
subprocess, so there is no server to host, no network exposure, and no auth to
configure — the process boundary is the trust boundary. Request serving is
read-only apart from the explicit feedback tool, and setup writes only when
asked. Nothing on your machine is started as a side effect of a lookup: a
stopped DDEV project is reported with the command that would fix it.

## Quickstart

Requirements: **PHP 8.2+** and Composer. The package works both ways — as a
standalone checkout and as a Composer dependency of another project.

```bash
# standalone: clone, install once, then point a project at it
composer install
/absolute/path/to/typo3-cms-mcp/bin/typo3-cms-mcp install

# as a dependency: from the consuming project's root
vendor/bin/typo3-cms-mcp install
```

`install` writes the `typo3-cms-mcp` entry into the project's `.mcp.json`,
leaves every other entry alone, and publishes the task skills to
`.agents/skills` — the two locations a client finds without being configured for
it. `--agent=<id>` writes them where that client actually reads them instead.
Either way the setup is recorded in `typo3-cms-mcp.json`, so a later `update`
refreshes all of them without being told which. The knowledge base ships inside
the package, so nothing else needs to be deployed or configured.

Codex, Claude, Cursor, Copilot, Zed and eight more clients, DDEV projects where
the server has to start inside the container, the generated `.mcp.json` shapes,
and the two environment variables that end a failed discovery:
[documentation/clients/installing.md](documentation/clients/installing.md). Changing this
repository rather than using it:
[documentation/working-on-the-server.md](documentation/working-on-the-server.md).

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
  feedback, by words, type and version. Answers what a release changed rather than
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
  namespaces, the shape of its `Classes/`. Its tables, content elements and
  icons come from the booted installation where there is one, attributed to the
  extension by the `EXT:` reference each entry carries, because a list built in
  a loop is in no file; the rest is read from its files, which is why it answers
  for a third-party extension as well as for the project's own and on a checkout
  that was never set up. The table an override file extends is read from what
  the file does, because those files are numbered rather than named after their
  table.
- `typo3_rule_lookup`: searches local TYPO3 core rules and script feedback, ranked
  by the query terms that separate one section from the rest rather than by
  overlap, and naming the architecture hints that match the same question — the
  caller does not have to know which of the two corpora holds a subject.
- `typo3_script_lookup`: finds matching feedback for TYPO3 core commands. They run
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
- `typo3_catalog_scope`: says where the component contracts came from — the
  active installation or the bundled snapshot — which core revision that
  snapshot was taken from, what it covers, and how to re-check it. Read it to
  judge whether a lookup miss is authoritative: even with installed sources the
  component names stay a curated index rather than every backend class.
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
- `typo3_translation_domain_lookup`: computes the translation domain an XLF file
  resolves to, from its path. Nothing registers a domain — it follows from the
  path by the rules the core itself applies — so this answers for a file in any
  extension and for one a patch is about to add, which is exactly when it cannot
  be looked up. On an installation older than translation domains it answers
  with the full `LLL:EXT:` reference instead, because the domain form renders
  nothing there and fails at runtime rather than at build time.
- `typo3_backend_module_lookup`: lists the backend modules the installation has
  registered, with the extension that declares each one, its place in the module
  tree, its labels and its route. Answered by the installation, so a project
  extension's modules are in it.
- `typo3_fluid_namespace_list`: lists the Fluid ViewHelper namespaces that are
  globally available, so a template knows which prefixes it may use without
  declaring them. Every other namespace is declared per template. Where the
  console cannot be reached, the installed packages' `Namespaces.php` answer and
  `answeredBy` says so.
- `typo3_configuration_lookup`: reads an effective `TYPO3_CONF_VARS` value — the
  value as it is at runtime after every extension has had its say, not the
  shipped default. For configuration whose assembled shape is the question, such
  as `SYS/formEngine/formDataGroup` or `SYS/caching/cacheConfigurations`.
- `typo3_commit_message_guide`: drafts and checks TYPO3 commit messages — from
  parts, or by passing an existing `message` to check and correct one in a
  piece. The emitted draft is ready to commit: the body is wrapped at 72
  characters, and fenced code, indented blocks, lists, and long URLs keep their
  shape. It defaults to the core contribution rules, Forge issue and `Releases:`
  trailer included; `workflow="project"` keeps the subject and body conventions
  and drops the trailers, for the repositories that use the one without having
  the other.
- `typo3_feedback_record`: records what was missing, wrong, or unhelpful about an
  answer as a feedback under `feedback/` (standalone checkout only, see
  [Improvement feedback](#improvement-feedback)).
- `typo3_feedback_list`: lists those feedback, newest first, so they can be worked
  off, filtered by status, category or the tool they are about. `status="closed"`
  reads the ones already worked off out of `feedback/archive/`, where the commit
  that archived one is what says what came of it — the feedback itself is kept,
  not deleted, so the category, the tools and the model are still there to filter
  on (standalone checkout only).

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

## Improvement feedback

What an agent hands back at the end of a task is recorded through
`typo3_feedback_record`, and every feedback becomes its own markdown file under
`feedback/`; `typo3_feedback_list` reads them back, newest first. A feedback is
closed by the commit that implements the improvement, which moves it to
`feedback/archive/`, so `feedback/` only ever holds open items.
`typo3_feedback_list` with `status="closed"` reads the archive with the commit
subject that closed each one, so a gap that was already answered is not reported
a second time and one that needed a code change does not vanish.

Each feedback carries the working directory the session that left it ran in, as
`directory:` in its front matter, so a gap can be checked against the project it
was found in rather than against whatever is at hand. It is the same directory
the stdio entrypoint hands to instance discovery; a feedback left over an endpoint
that has none carries none. That directory is also why the feedback is reported back
as an absolute path: it is written into this server's checkout and not into the
project the session is in, and a relative path sent to a caller standing
somewhere else reads as a write that failed.

A feedback also carries `model:` — the model that left it, as it named itself. Much
of what arrives is about what a session did rather than about what an answer
said, and that is one model's behaviour: unattributed, two models' habits are one
undifferentiated report and neither can be worked off. The write never fails on
it, and a model that does not know its own identifier is asked to send `unknown`
rather than an invented one, so a feedback nobody can attribute says so.

Both tools exist **only in a standalone checkout**. Installed as a Composer
dependency the package lives in `vendor/`, where anything written would be lost
on the next `composer install`; there the server stays strictly read-only and
neither tool appears in `tools/list`.

Working on this repository — layout, conventions, and how feedback is worked off —
is documented in [AGENTS.md](AGENTS.md).
