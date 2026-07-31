# Working on this repository

## Layout

```
bin/typo3-cms-mcp  # stdio entrypoint (the client launches it as a subprocess)
bin/cli            # everything this repository is kept in order by; run it with nothing for the list
src/Cli.php        # what `bin/cli` supports, and src/Cli/ one class per subject
src/               # PHP classes (knowledge loading, tools, SDK wiring)
src/ServerFactory.php  # builds the mcp/sdk server from the tool definitions
src/Mcp/           # SDK handlers: tool dispatch and typo3://core resources
src/Catalog/       # the component catalog and the translation domain derivation
src/Documentation.php  # reads the public index and pages of versioned TYPO3 manuals
src/Instance.php   # finds the TYPO3 installation the agent is working in
src/Installer.php  # writes guarded generic or agent-specific client setup
src/Project.php    # the repository around it; src/Extension.php one extension in it
src/Profile.php    # which half of the server a client is offered (TYPO3_MCP_PROFILE)
src/Typo3Cli.php   # runs that installation's console, via DDEV where there is one
src/bootstrap.php  # locates the Composer autoloader
knowledge/         # the knowledge base (markdown + JSON), the data source
feedback/          # improvement notes left by agents (standalone checkout only)
scenarios/         # user prompts and what has to come out of them, one case per file
scenarios/forward/ # open forward reviews: a repository review and nothing more; the only kind that is run and recorded
scenarios/contracts/ # targeted cases per audience, task skill and cross-cutting situation: one named task shape each
scenarios/runs/    # one recorded forward run per review: where it ran, against which server, which skills and tools it reached for, and the judgment per criterion
skills/            # canonical task skills installed into supported agent clients
skills/base.md     # the order every task starts in, copied into each published skill as references/base.md
requirements/      # what must hold, and what holds it there: one requirement per file, grouped by what it is about; open ones are the backlog
decisions/         # what a change assumed, and what would show it to be wrong: one decision per file, grouped by what it is about
todo.md            # the order of the work and where the last session stopped; `bin/cli next` prints one of them
src/Todo.php       # todo.md as data: what recurs and how often, what is queued, what each todo serves
documentation/     # how a procedure is carried out, one page each; the rules stay here, the steps live there
tests/             # unit, tool contract, and stdio smoke tests
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

`Typo3CmsMcp\Tools` declares every tool and builds its answer;
`Typo3CmsMcp\Knowledge` reads and searches the markdown documents. Tool names,
schemas, and response formatting live in `src/`; bundled answers come from
`knowledge/`, live manual results from `docs.typo3.org`, and installation facts
from the installation being read.

## Where a session starts, and what it owes the next one

    bin/cli next

That is the whole of it, and it prints **one todo** — the first that is due,
whole, with its own command already run. Not the queue, not the backlog, not the
five paragraphs of why that one is in front. `bin/cli todo list` is the
overview, for whoever wants it.

Keeping [todo.md](todo.md) current is part of the work, not a step after it. The
commit that finishes a todo **deletes** it; one that turns out to be half done is
trimmed to the part that is left; a change of order is written down before the
work starts; and new work found along the way is added as a todo that names what
it serves. A session that ends with the file matching what is actually true has
handed over correctly, whatever else it did.

What `next` can never do is run a note's own query against the server as it is
now. A note is evidence about a version of this server that may no longer exist,
and that reading is the session's.

How `next` decides what is due, and how the work moves between `feedback/`,
`requirements/`, `decisions/` and `todo.md`:
[documentation/the-record.md](documentation/the-record.md).

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

This holds for prose as well. `knowledge/`, `readme.md`, and the tool
descriptions are read by someone paying per token for every sentence that says
nothing.

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

Every tool returns a `ToolResult`: the text plus the same answer as data. The
data half is a contract — clients may validate it against the output schema the
tool declares in `Typo3CmsMcp\ToolSchemas`, so a field a schema requires has to
be present on every path through the tool, misses included. Add fields rather
than renaming them.

## Checks

```bash
composer ci     # lint, static analysis, tests — what CI runs
composer test   # phpunit only
composer stan   # phpstan only
```

- `tests/Unit/` covers the searching, ranking, and rendering logic;
  `tests/Contract/` holds every tool to its declared schemas and annotations, on
  a hit and on a miss, and to the naming schema; `tests/Smoke/` drives
  `bin/typo3-cms-mcp` as a subprocess over JSON-RPC.
- A behaviour worth a rule in `knowledge/` is worth a test: ranking that must
  prefer one match over another, an answer that must say "no match" instead of
  guessing, a catalog field that must stay usable.
- `FeedbackTest` writes real notes below `feedback/` and removes them again.
  A leftover file carries `phpunit-feedback-fixture` in its text.

`bin/cli` is what everything else in this repository is kept in order by, and
`bin/cli checkouts update` is what creates the core checkouts a knowledge change
is verified against:
[documentation/working-on-the-server.md](documentation/working-on-the-server.md).

## Feedback workflow

Agents using this server record improvement notes through `typo3_feedback_record`.
Each note is one markdown file below `feedback/`.

Where a note comes from is usually a real session. `scenarios/` is where those
sessions are written down so they can be run again: one prompt per file, in the
words a user would use, with the environment it has to be run in and what has to
come out of it. It holds two kinds, and mixing them is what the split of
2026-07-31 undid. An **open forward review** in `scenarios/forward/` asks for a
review of the repository and names no subsystem, skill, tool or expected
finding — what the agent chooses to inspect is the evidence, and only these are
run and recorded. A **targeted contract case** in `scenarios/contracts/` names
one task shape so its routing stays held; it proves that a known task still gets
its workflow, never that an agent discovered the subject.

A recorded run is a source of notes in its own right, and the good ones are a
source too. Whatever a run taught that is not specific to the repository it ran
against becomes a note here rather than a paragraph in the run's own evidence:
that field is read once, by whoever judged that run, while `feedback/` is what
every later session walks. Running a review, judging it, and reading one that
stopped without an error: [documentation/forward-runs.md](documentation/forward-runs.md).

A prompt names a kind of project, never one installation on somebody's machine —
that lives in `todo.md`, where it can go stale without taking a case with it.
Cases the server cannot answer yet belong here too: the suite is the map of what
the three audiences need, not a regression net around what already works. A
review marked `gap` names the requirement that is still open; a run of it
produces the note that says what the task needed beyond it.

A note is worked off in a commit that both implements the improvement **and
deletes the note file**. The commit is the record that the gap was closed, so
the `feedback/` directory only ever holds open items. Never mark a note as done
by editing its `status:` front matter, and never delete one that was only
partially addressed — trim it to the part that is still open instead.

What outlives the note is split three ways, and keeping them apart is what keeps
any of them readable: `requirements/` for what must be true from now on and what
holds it there, `decisions/` for what a change rested on and what would show it
wrong, `todo.md` for the order of the work. Add the requirement in the commit
that works the note off; name its test in the commit that writes that test.

Three states mean unfinished — a requirement marked **open**, one held by
`not guarded`, a decision still `standing` whose **Wrong if** nobody has been
back to. All three are legitimate, so no check may fail on them, which is why
nothing read them for as long as they existed. `bin/cli backlog list` is that
reading, and what a session owes anything on it is a judgement: an item in
`todo.md`, or the sentence in `decisions/` that says why not.

The full account — the note lifecycle, what each of the three files holds, and
what `bin/cli backlog list` reports — is in
[documentation/the-record.md](documentation/the-record.md).

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

Before committing, reread the paragraphs your change touches rather than
searching for a keyword. The sentence that goes wrong is usually the general one
written before the exception existed, and it will not contain the word you would
grep for.

## Commits

- Work directly on `main`; no feature branches.
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
  TYPO3 exposes a command; package-owned registries and component contracts read
  the files those packages ship without executing them. Add to `knowledge/` by
  default; reach for the installation only when the answer genuinely depends on
  which packages and TYPO3 version are active.
- Checkout discovery is enabled per entrypoint and never derived from `getcwd()`
  on its own. Only `bin/typo3-cms-mcp` calls `Instance::discoverFrom()`: a
  request-serving endpoint has no such relationship to its callers, and its
  document root may itself sit inside an installation.
- Never load an installation into this process. `Typo3Cli` shells out, so its
  autoloader, its dependencies, and its PHP version stay on the other side of a
  process boundary and a failure is an exit code rather than a dead session.
- Never start something on the caller's machine as a side effect of a lookup. A
  stopped DDEV project is reported with the command that would fix it.
- Add new rules or scripts to `knowledge/` first; promote recurring workflow
  logic to a tool only when it has earned it.
- Verify facts against the core checkouts below `.checkouts/` before writing
  them into `knowledge/`, and bind what does not hold on all of them. The
  checkouts are this repository's own — one worktree per covered version,
  created and updated by `bin/cli checkouts update`, gitignored and re-fetchable
  at any time. Verifying against whatever checkout happens to be on the machine
  makes the evidence unreproducible for the next person.

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
[documentation/knowledge-versions.md](documentation/knowledge-versions.md).
