# Working on this repository

## Layout

```
bin/typo3-cms-mcp  # stdio entrypoint (the client launches it as a subprocess)
bin/cli            # everything this repository is kept in order by; run it with nothing for the list
src/               # grouped by what a class is: only Paths and the bootstrap sit loose
src/Server/        # starting this server and setting a project up for it
src/Server/Entrypoint.php  # what `bin/typo3-cms-mcp` runs: the commands, the usage, the transport
src/Server/Factory.php     # builds the mcp/sdk server from the tool definitions
src/Server/Installer.php   # writes guarded generic or agent-specific client setup
src/Server/Profile.php     # which half of the server a client is offered (TYPO3_MCP_PROFILE)
src/Tool/          # one class per tool: its description, its schemas, its answer
src/Tool/Tool.php  # the interface each one implements; ReadOnlyTool carries the annotations
src/Tool/Registry.php  # every tool this server has, and the only place one is switched on
src/Result/        # what a tool hands back, and what several of them build one from
src/Knowledge/     # what this package knows about TYPO3: read from knowledge/, or computed where a rule has to be applied rather than looked up
src/Knowledge/Scope.php  # the one vocabulary for which kind of work an answer is for, and how a path is placed in it
src/Knowledge/Coverage.php  # knowledge/server-scope.json: what the server covers, what it does not, and the initialize instructions
src/Knowledge/Catalog/  # the component catalog and the translation domain derivation
src/Installation/  # what only the installation being read can answer: its icons, labels, namespaces, changelog, project and extensions
src/Installation/Instance.php  # finds the TYPO3 installation the agent is working in
src/Installation/Typo3Cli.php  # runs that installation's console, via DDEV where there is one
src/Installation/Typo3Runtime.php  # boots it in a subprocess and asks its container
src/Installation/probe.php  # what runs over there; never included here
src/Manual/        # the third source: the public index and pages of the versioned TYPO3 manuals
src/Contribution/  # the fourth source: the services the core's own process runs through, read over their APIs
src/Http/Fetch.php # the one way this server reads a host outside itself: the timeouts, the redirect limit and the agent, once
src/Search/        # the lexical matching every prose and label lookup goes through
src/Feedback/      # the feedback channel: what `typo3_feedback_record` writes and `bin/cli feedback` reads
src/Sdk/           # the adapters onto mcp/sdk: tool dispatch and typo3://core resources
src/Paths.php      # where this checkout keeps things; the one class both halves share
src/bootstrap.php  # locates the Composer autoloader
src/Upkeep/        # what `bin/cli` runs on this repository, and nothing the server answers with
src/Upkeep/Cli.php # the console application, and the only place a command is switched on
src/Upkeep/Command/  # one class per command, named `<subject>:<verb>` by its own #[AsCommand]
src/Upkeep/TestingFramework.php  # which typo3/testing-framework release each covered major is read against
knowledge/         # the knowledge base (markdown + JSON), the data source
knowledge/documents/  # the prose corpus: searched by typo3_rule_lookup, served as typo3://core resources, and published by being in here
feedback/          # feedback left by agents about this server (standalone checkout only)
feedback/archive/  # the ones that were worked off; kept, because a session's report is evidence
scenarios/         # user prompts and what has to come out of them, one case per file
scenarios/forward/ # open forward reviews: a repository review and nothing more; the only kind that is run and recorded
scenarios/contracts/ # targeted cases per audience, task skill and cross-cutting situation: one named task shape each
scenarios/runs/    # one recorded forward run per review: where it ran, against which server, which skills and tools it reached for, and the judgment per criterion
skills/            # canonical task skills installed into supported agent clients
skills/base.md     # the order every task starts in, copied into each published skill as references/base.md
requirements/      # what must hold, and what holds it there: one requirement per file, grouped by what it is about; open ones are the backlog
decisions/         # what a change assumed, and what would show it to be wrong: one decision per file, grouped by what it is about
todo/              # the order of the work and where the last session stopped: one todo per file, and where a file sits is the stage it is in; `bin/cli todo:next` prints one of them
todo/open/         # the queue, read by the priority in each head and then by the date in each name
todo/progress/     # what a session has in hand: out of the queue, offered to nobody else, naming the branch the work is on
todo/waiting/      # what nothing here can start, carrying the question it is blocked on; closing is a deletion and no fourth directory
src/Upkeep/Links.php # every path this repository writes between its own files, and whether it still resolves
src/Upkeep/Todo.php  # todo/ as data: what recurs and how often, what is queued, what each todo serves
documentation/     # how a procedure is carried out, grouped by subject; the rules stay here, the steps live there
.githooks/         # the hooks this checkout commits through; `composer install` points git at them
tests/             # unit, tool contract, and stdio smoke tests
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

Every class below `src/` sits in the group it belongs to, and a new one that
fits none of them is a group nobody has named yet rather than a file at the
root. The four an answer can come from are `Knowledge/`, what this package ships;
`Installation/`, the TYPO3 the caller is standing in; `Manual/`, which is
`docs.typo3.org`; and `Contribution/`, the services the core's own process runs
through. The last two reach outside this process, which is what their tools'
`openWorldHint` says and what a caller reads before it calls. `Tool/` is what a
client can call, `Result/` is what it hands back, `Server/` is how it is started
at all, and `Upkeep/` is not part of the server. Only `Paths` sits loose,
because it is the one thing the server and the upkeep both stand on.

Both binaries are the same shape: locate the autoloader, hand the arguments to
the class that owns them — `Server\Entrypoint`, and `Upkeep\Cli` by way of the
console application it builds. What a command does, what it is called and what
it takes are declared beside the code that does it, never in `bin/`.

## Where a session starts, and what it owes the next one

    bin/cli todo:next

That is the whole of it, and it prints **one todo** — the first that is due,
whole, with its own command already run. `bin/cli todo:list` is the overview,
for whoever wants it.

The todo it prints is a claim, not an instruction: one session's belief about
where to start, written before the work it describes and by somebody who has
left. So it is read against what the repository does today, and where that
reading says the step is done, impossible or two steps, the file is corrected
before the work rather than after it.

A question the step turns on is **settled**, never recalled. What cannot be
established here is a result and is recorded as one — the todo trimmed to what
is still open, the requirement `not guarded`, the decision saying what evidence
would settle it. What none of those can absorb is a guess written with the
confidence of a reading, because nothing afterwards can tell the two apart.

What has no source here is **asked**, before the change rather than in the
commit that presents it. Ask with the reading done — the options, what each
costs, a recommendation — and put the todo back among them, because a todo that
waits is not a worse outcome than one done on a guess. What comes back goes into
`todo/` or `decisions/`, because an answer that lives only in the conversation
ends with it.

Nor in whatever the agent can remember on its own. A client that offers a
private memory across sessions is not a place this repository keeps anything:
what was established goes into `todo/`, `requirements/`, `decisions/` or
`documentation/`, where the next session reads it and a wrong line can be
corrected. A note only one agent can see is read by nobody else, drifts as the
checkout moves under it, and is believed anyway because it arrives sounding
settled — the failure this server exists to answer, turned on the repository
itself.

Keeping [todo/](todo/readme.md) current is part of the work, not a step after
it. A session that ends with the queue matching what is actually true has handed
over correctly, whatever else it did.

What is read before the first change, how a question is settled, what is asked
and what the file says afterwards:
[documentation/feedback/working-a-todo.md](documentation/feedback/working-a-todo.md).
How `next` decides what is due, and how the work moves between `feedback/`,
`requirements/`, `decisions/` and `todo/`:
[documentation/feedback/readme.md](documentation/feedback/readme.md).

Several sessions at once is `bin/cli todo:claim <n>`, one worktree and one
branch each. `main` carries who has what in hand, the branch carries the
half-finished work, and `todo:next` in a worktree hands over that worktree's
claim rather than the front of the queue. What that costs and what still
collides:
[documentation/feedback/working-todos-in-parallel.md](documentation/feedback/working-todos-in-parallel.md).

## How a session reads

A session is charged one context per call, not one per token. The 82 worktree
sessions of 2026-08-02 read 718 million cached input tokens back over 5414
calls, each of which paid for everything before it again, and wrote 5.9 million
out. The number of calls is the budget; what one of them returns is nearly free
— `D-FBK-020`.

- **Send the calls that do not depend on each other together.** Not one of those
  5414 was issued beside another, and most of what a session reads at the start
  turns on nothing that came before it.
- **Reach for a file with the client's own file and search tools.** Three
  quarters of the calls were `bash`, and half of those were `cat`, `sed`, `grep`
  and `ls` spelling out what one call answers.
- **Open a file once, whole.** 401 calls were `sed -n` windows into a file the
  session went on to open again; one opened `src/Installation/Extension.php`
  sixteen times.

What that does not license is reading less. The step is still read against what
the repository does today, and a question it turns on is still settled from a
source — in fewer calls, not from memory.

## What things are called

One thing, one word. Where two compete, the one that wins is the one somebody
outside this checkout can see — a tool name, a directory name, a CLI subject —
because those are known by clients installed months ago and by paths people
wrote down, while prose can be rewritten this afternoon. A synonym introduced in
a paragraph is copied into a schema a week later.

A directory below `src/` is named in the **singular**, for what one of the
things in it is: `Tool/` holds one tool per class, `Command/` one command,
`Manual/` one manual. The plural names the directory after its contents, which
is what the file system already says, and it splits every name in two — a class
lands in `Commands/` and is referred to as a command all the way down. The
directories outside `src/` keep the names their callers know them by.

What arrives through `typo3_feedback_record` is **a feedback**, countable, and
[documentation/glossary.md](documentation/glossary.md) is where that and the
words around it are defined. Two of them go wrong reliably: **record** is the
verb it arrives by and never a noun, because in TYPO3 a record is a row in the
database and this server explains that meaning to its callers; and **verdict**
belongs to `scenarios/`, where it is how a run came out, while what becomes of a
feedback is its **answer**.

## Less is more

Every task is also an occasion to leave the code smaller than it was. A change
is finished when what it added is there **and** what it made unnecessary is
gone; carrying both shapes at once is the expensive half of every feature.

- Before writing an abstraction, look for the one the change makes redundant.
  Two answers of the same shape share a formatter, and a branch nobody takes is
  deleted rather than kept for symmetry.
- Prefer the change that removes a concept to the change that adds one. A
  parameter that is always passed the same value is not a parameter, and a
  helper with one caller is that caller.
- Code written to be general is speculation until the second caller exists. The
  second caller is also what shows what the two actually have in common — guess
  before it arrives and the abstraction is built around the wrong thing.
- Deleting needs no feature to justify it. A simplification that stands on its
  own is its own commit, and a review of code nobody has touched in a while is
  a legitimate task.
- Shorter is not the same as denser. Fewer concepts, fewer branches, fewer
  moving parts — not fewer lines wrung out of the same logic.

What this does not license is a smaller API. Where a framework offers several
ways in, take the one its own documentation and tests treat as the main path,
even where that means more files. A cheaper-looking variant that needs an extra
nudge to behave is not the smaller change: the nudge is the added concept, and
it is presented as a trade-off exactly where none was on offer. Say which way in
is the main one and which is being proposed; do not decide it for brevity.

## Prose

Short and precise, everywhere: `knowledge/`, the tool descriptions, this file,
`documentation/`, a commit message. Every reader pays per token, and half of
them are machines.

- One point per sentence. A sentence that restates the previous one in other
  words is deleted, not shortened.
- The rule first, the reason after it, and only where the reason is not
  obvious. A justification nobody would dispute is filler.
- One example, where an example is needed at all. The second one rarely adds a
  case and always adds a paragraph.
- Say what is, not what it is not. A list of what something is not belongs
  where the confusion actually happened.
- Length is a symptom. A paragraph that will not come out short is usually two
  points, or one that is not yet understood.

`bin/cli prose:check` counts what that costs: the sentences over 30 words, worst
file first. It fails on one of them — the bold sentence a requirement or a
decision opens with, because a reader who stops after it is supposed to know
what was settled. The rest is a report, since a long sentence can be the right
one and a rewrite driven by a counter produces two short sentences saying what
one said.

## Tool names

Every tool is named `typo3_<subject>_<verb>`. The prefix never varies, the
subject is what the tool is about, and the verb comes from a closed list of
five, because the verb is what tells a caller which shape the answer has:

- `lookup` — a query goes in, matching entries come out, and finding nothing is
  a legitimate answer: `typo3_component_lookup`, `typo3_rule_lookup`.
- `guide` — an answer composed for the task at hand, which always exists:
  `typo3_task_guide`, `typo3_commit_message_guide`.
- `list` — an enumeration of what is there, no query needed:
  `typo3_feedback_list`.
- `scope` — what a source covers and where its boundary runs:
  `typo3_server_scope`, `typo3_catalog_scope`.
- `record` — the tool writes something: `typo3_feedback_record`.

A new tool takes the verb whose answer shape it already has, and two tools
sharing an output schema share their verb. When none of the five fits, the tool
is probably doing two things at once — split it before inventing a sixth verb.
If a sixth is genuinely needed, add it to `ToolNamingTest` in the same commit,
so that list stays the only place the vocabulary is defined.

Leave `core` out of a name: this server is about the TYPO3 core throughout, so
the segment separates nothing.

A tool is one class in `src/Tool/`, implementing `Typo3CmsMcp\Tool\Tool`: what
it is called, what it takes, what shape it answers in, and the answer itself
stand in one file, so a description cannot go stale against the answer it
describes without the two being edited apart. `Typo3CmsMcp\Tools` is the list of
them, and the only place a tool is switched on. Nothing else belongs below
`src/Tool/` — what more than one tool builds its answer from is
`Typo3CmsMcp\Result\`, and the adapters onto `mcp/sdk` are `Typo3CmsMcp\Sdk\`.

The word is the protocol's: an MCP tool is what the SDK declares as
`Mcp\Schema\Tool`, beside `Prompt` and `Resource`, so the qualifier saying which
kind of tool is meant is already the root namespace. Nothing here is a "server
tool" — a tool is defined by the protocol rather than by the side offering it,
and both sides speak of the same one.

Every tool returns a `ToolResult`: the text plus the same answer as data. The
data half is a contract — clients may validate it against the `outputSchema()`
the tool declares, so a field a schema requires has to be present on every path
through the tool, misses included. Add fields rather than renaming them. A
record shape more than one tool answers with belongs in
`Typo3CmsMcp\Result\Schema`, so a client reads one model rather than two.

## Checks

```bash
composer ci     # lint, coding guidelines, static analysis, tests — what CI runs
composer test   # phpunit only
composer stan   # phpstan only
composer cgl    # rewrite to the guidelines; cgl:ci reports and rewrites nothing
```

```bash
bin/cli knowledge:format          # the JSON below knowledge/, in the one form
bin/cli knowledge:format <path>   # only that part of it
```

- The JSON below `knowledge/` is written by that command and by nothing else:
  PHP's pretty print at the indentation `.editorconfig` states, slashes and
  unicode as they were typed, key order untouched. `JsonTest` fails on a file
  that is not in the form, so a hand-reindented one is caught where a reviewer
  reading the statement would not look. `.editorconfig` is where an indentation
  is said at all — `StructureTest` holds the PHP one to php-cs-fixer's.
- The guidelines are php-cs-fixer's own, and `.php-cs-fixer.dist.php` is where
  they are declared: PER-CS 3.0 and the few rules on top of it this repository
  writes by. A rule is added there when the code already follows it and the
  fixer is what keeps it followed — not to introduce a style nobody has written
  in yet, which is a reformatting of the whole tree wearing a rule's clothes.
- One file, one class. A second class in a file is not autoloadable under
  PSR-4, so it works until somebody uses it from anywhere else and then fails
  as a missing class — held by `StructureTest::everyFileDeclaresOneClass`.
- A directory is read with `symfony/finder`, whatever the depth. `glob()`,
  `scandir()` and `RecursiveDirectoryIterator` were two idioms for one
  question, and the deep one cost a dozen lines each time — held by
  `StructureTest::everyDirectoryIsReadThroughTheFinder` and stated in `D-COD-003`.
  A directory that may be absent is guarded with `is_dir()`, because Finder
  throws where `glob()` returned nothing.
- Every entrypoint is driven by a test that goes through it. `tests/Unit/`
  reaches a class at a time, which is where a command can be held to its rules
  and still be unreachable: what it reads is resolved from where its own file
  sits, and moving that file is not something any of those tests goes past.
  Both binaries have such a test, and a third would need one — held by
  `StdioServerTest`, `EntrypointTest` and `UpkeepTest`.
- `tests/Unit/` covers the searching, ranking, and rendering logic;
  `tests/Contract/` holds every tool to its declared schemas and annotations, on
  a hit and on a miss, and to the naming schema; `tests/Smoke/` drives both
  entrypoints as subprocesses — `bin/typo3-cms-mcp` over JSON-RPC, `bin/cli`
  by its reading commands.
- `src/Upkeep/Command/` and the console are held to each other by
  `UpkeepCommandTest`: every class in the directory is registered, the
  application carries no command that is not one of them, each is named
  `<subject>:<verb>` and describes itself, and what a command declares on the
  parameters of its `__invoke` is what the console binds. That last one is the
  quiet failure — the console reads those parameters at one moment only, and a
  command it stops asking keeps every argument in its signature while refusing
  the caller who passes one.
- Every path this repository writes between its own files resolves —
  `bin/cli links:check`, and `LinksTest` so a rename that misses a reference
  fails the suite rather than the next reader. The anchor is not held, because a
  heading moves and the link still lands on the page.
- A behaviour worth a rule in `knowledge/` is worth a test: ranking that must
  prefer one match over another, an answer that must say "no match" instead of
  guessing, a catalog field that must stay usable.
- `FeedbackTest` writes real feedback below `feedback/` and removes them again.
  A leftover file carries `phpunit-feedback-fixture` in its text.

`bin/cli` is what everything else in this repository is kept in order by, and
`bin/cli checkouts:update` is what creates the core checkouts a knowledge change
is verified against:
[documentation/working-on-the-server.md](documentation/working-on-the-server.md).
`bin/cli environment:create E-SITE` makes the other kind — a DDEV project with
TYPO3 installed in it, below `.environments/` and gitignored the same way, for
the half of this server that needs an installation to answer from. It is the
environment and never the subject of a recorded review (`D-EVI-004`).

## Feedback workflow

Agents using this server record improvement feedback through
`typo3_feedback_record`, one markdown file per feedback below `feedback/`.
`scenarios/` holds the sessions those came out of, so they can be run again:
open forward reviews in `scenarios/forward/`, targeted contract cases in
`scenarios/contracts/`. A prompt names a kind of project, never one
installation on somebody's machine — that lives in `todo/reference/`, where it can go
stale without taking a case with it.

- A feedback arriving brings its card with it: `.githooks/pre-commit` runs
  `bin/cli todo:sync` where the commit touches `feedback/`, and stages what it
  wrote. It repairs and never refuses, so a commit made without it — another
  checkout, `--no-verify` — is caught by `bin/cli todo:check` and by CI instead.
- A feedback is worked off in a commit that both implements the improvement
  **and** archives it with `bin/cli feedback:archive <feedback>`, so `feedback/`
  only ever holds open items and the commit that moved it is the record of what
  came of it.
- Never mark one done by editing its `status:` front matter, and never archive
  one that was only partially addressed — trim it to the part that is left.
- Nothing is deleted from the archive. A feedback is a session's report about
  this server, which is evidence nothing else here holds.
- What outlives it is split three ways: `requirements/` for what must be true
  from now on and what holds it there, `decisions/` for what a change rested on
  and what would show it wrong, `todo/` for the order of the work.
- Three states mean unfinished — a requirement marked **open**, one held by
  `not guarded`, a decision still `open` whose **Wrong if** nobody has been
  back to. All three are legitimate, so no check may fail on them, and
  `bin/cli backlog:list` reads them out instead.

How each of those is carried out — the debrief that gets a feedback out of a
session this repository cannot read, judging one, what each of the three files
holds, and what `bin/cli backlog:list` reports:
[documentation/feedback/readme.md](documentation/feedback/readme.md). The
sections a requirement is written in and what its three states mean:
[documentation/feedback/writing-a-requirement.md](documentation/feedback/writing-a-requirement.md).
The sections a decision is written in, what a later session adds to the foot of one,
and what `open`, `confirmed` and `revoked` promise a reader:
[documentation/feedback/writing-a-decision.md](documentation/feedback/writing-a-decision.md).
Running a forward review, judging it, and reading one that stopped without an
error:
[documentation/evidence/forward-runs.md](documentation/evidence/forward-runs.md).
What each kind of scenario is for: [scenarios/readme.md](scenarios/readme.md).

## What describes this server to someone else

Four things describe this server outward, and a change that leaves any of them
wrong is not finished — it is a change plus a false statement. They ship with
the code, so a stale one is not a documentation debt, it is a lie the server
tells its callers.

- `readme.md` — what the server is and what it will not do. Its opening
  paragraphs are a promise; when a capability changes what the server may touch,
  that promise is the first thing that becomes false.
- `knowledge/server-scope.json` — `covers`, `doesNotCover`, `routing`, and the
  `instructions` clients receive at initialize time. A new tool belongs in
  `covers` and in `routing`; a boundary that moved belongs in `doesNotCover`.
- `AGENTS.md` — the layout list and the rules here, including this one.
- Every tool `description` and `outputSchema` in `src/`, which is the only
  documentation a client actually reads.

Some of it is already guarded: `ScopeTest` holds the scope and the tool list to
each other in both directions, and `ToolNamingTest` holds every tool name
written in `knowledge/`, in a skill, or in a rendered answer to the registry —
a skill matters twice over, because it is installed into somebody else's
project, where a stale name is not corrected by the next release of this server. Those catch a
name going stale, not a sentence going false. Prose is on you.

That property is also why a skill is written under rules of its own — what it is
named and routed by, what it may state, what it leaves to the tool that owns it,
and what has to be shown before a domain becomes one at all:
[documentation/clients/writing-a-skill.md](documentation/clients/writing-a-skill.md), where every
rule names the test that holds it.

Before committing, reread the paragraphs your change touches rather than
searching for a keyword. The sentence that goes wrong is usually the general one
written before the exception existed, and it will not contain the word you would
grep for.

## Commits

- Split changes into small, single-purpose commits and commit as soon as each
  part is verified.
- Only commit the files you changed yourself in this session. The working tree
  may already contain unrelated modifications or staged changes from someone
  else — leave them alone.
- Always stage explicitly with `git add <path>`. Never use `git add -A`,
  `git add .`, `git commit -a`, or any other blanket staging.
- Before committing, check `git status` and `git diff --staged`. If something is
  staged that you did not change, unstage it (`git restore --staged <path>`)
  instead of committing it along.

## Knowledge base

Three audiences read what is written here: core contributors, extension authors,
and site developers — and the same person is often two of them in one checkout,
because extensions are developed inside site installations. All three are served
deliberately, so knowledge that holds only for core contribution is written as
core-only rather than as the rule, and knowledge that holds only from one TYPO3
version says so; see the audience requirements in `requirements/audience/`.

In the code and in every payload that is one word, `scope`: the `Knowledge\Scope`
enum, whose cases are `core`, `project`, `extension`, `any` and `uncertain`. A
statement in `knowledge/` declares one, a path is placed in one, and nothing
else says the same thing under another name — `binding`, `provenance`,
`audience` and an `outsideCore` boolean were four spellings of it until
`D-KNW-005`. Audience stays the word for the idea, in `requirements/audience/`
and in prose; `scope` is the word anything machine-readable uses.

- **Everything below `knowledge/` is written in English**, and so is every query
  that reaches it. That is a property of the matcher rather than a preference:
  matching is lexical, so a query in another language reaches only the loanwords
  the two share. The server tells the calling agent to translate — in the
  `instructions` it sends at initialize, in `typo3_server_scope`, and on the
  free-text parameters of the tools that match against prose. Adding a second
  query language would mean translating the corpus, not the query, so a German
  sentence in a hint is not a nice extra, it is a statement nothing can find.
- Everything the tools answer from lives below `knowledge/`, with one exception:
  facts owned by an installation are read from that installation, because no
  bundled answer could be right for it. Runtime registries use `Typo3Cli` where
  TYPO3 exposes a command and `Typo3Runtime` where it does not — that boots the
  installation in a subprocess and asks its container, which is the only source
  that knows what a package registers dynamically. Component contracts and the
  fallbacks read the files those packages ship without executing them. An answer
  that came from the files where the container was meant to answer says so and
  says what it leaves out: a **failsafe** container is core-only and looks
  complete, so it is never handed on. Add to `knowledge/` by default; reach for
  the installation only when the answer genuinely depends on which packages and
  TYPO3 version are active. The order those three are asked in, how the probe is
  delivered, and what a fallback owes the caller:
  [documentation/knowledge/asking-the-installation.md](documentation/knowledge/asking-the-installation.md).
- The installation is never derived from `getcwd()` on its own. `Instance` walks
  up from a directory it was handed, keeps it private and null until then, and
  `Server\Entrypoint` is the only thing that hands one in — a request-serving
  endpoint has no such relationship to its callers, and its document root may
  itself sit inside an installation. Naming the root with `TYPO3_MCP_ROOT` is a
  decision rather than a derivation and holds everywhere.
- Never load an installation into this process. `Typo3Cli` shells out, so its
  autoloader, its dependencies, and its PHP version stay on the other side of a
  process boundary and a failure is an exit code rather than a dead session.
- Never start something on the caller's machine as a side effect of a lookup. A
  stopped DDEV project is reported with the command that would fix it.
- Add new rules or scripts to `knowledge/` first; promote recurring workflow
  logic to a tool only when it has earned it. **What earns it is the round trips
  it takes off the caller.** A session is charged one context per call
  (`D-FBK-020`), so a question that costs it four calls and a trap — the Forge
  issue that answers 403, then 200 with a challenge page, then JSON whose
  decision sits in a field nobody would guess — is worth a tool that answers it
  in one. The cost moves here permanently and that is the trade rather than the
  objection: a surface somebody else owns becomes ours to keep true, for a
  saving every session gets. What does not earn it is a fact the caller reads
  once from its own checkout, and anything whose lookup would report
  `unavailable` often enough that the call buys nothing (`D-FBK-027`).
- Verify facts against the core checkouts below `.checkouts/` before writing
  them into `knowledge/`, and bind what does not hold on all of them. The
  checkouts are this repository's own — one worktree per covered version,
  created and updated by `bin/cli checkouts:update`, gitignored and re-fetchable
  at any time. Verifying against whatever checkout happens to be on the machine
  makes the evidence unreproducible for the next person. A statement whose
  subject is `typo3/testing-framework` is verified there too, in
  `.checkouts/testing-framework/<line>` — the same command keeps one worktree per
  release line the covered branches pin, because that package releases on a cycle
  of its own and the core repository does not contain it.

### Which versions an answer holds for

The knowledge base covers more than one TYPO3. A statement that does not hold on
all of them says so **as data, not as prose** — `since` and `until` on the
statement, never a version number in the sentence, which `HintsTest` enforces.
A bound statement is verified on both sides of its boundary and the commit
message names both branches; that is evidence nobody can reconstruct later.
Which versions are covered is declared in `knowledge/versions.json` and nowhere
else.

The mechanism exists because the alternative is worse: a caller on an LTS given
a `main` answer changes code that then fails at runtime, and the failure is
silent. What follows from it — where the binding sits, what belongs in `hints`
rather than `checks`, `binding: "core"`, and why the catalogs withhold an entry
instead of qualifying it — is in
[documentation/knowledge/versions.md](documentation/knowledge/versions.md).
