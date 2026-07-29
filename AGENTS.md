# Working on this repository

## Layout

```
bin/typo3-cms-mcp  # stdio entrypoint (the client launches it as a subprocess)
src/               # PHP classes (knowledge loading, tools, SDK wiring)
src/ServerFactory.php  # builds the mcp/sdk server from the tool definitions
src/Mcp/           # SDK handlers: tool dispatch and typo3://core resources
src/Catalog/       # the component catalog and the translation domain derivation
src/Instance.php   # finds the TYPO3 installation the agent is working in
src/Typo3Cli.php   # runs that installation's console, via DDEV where there is one
src/bootstrap.php  # locates the Composer autoloader
knowledge/         # the knowledge base (markdown + JSON), the data source
feedback/          # improvement notes left by agents (standalone checkout only)
tests/             # unit, tool contract, and stdio smoke tests
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

`Typo3CmsMcp\Tools` declares every tool and builds its answer;
`Typo3CmsMcp\Knowledge` reads and searches the markdown documents. Tool names,
schemas, and response formatting live in `src/`; everything they answer comes
from `knowledge/`.

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

## Feedback workflow

Agents using this server record improvement notes through `typo3_feedback_record`.
Each note is one markdown file below `feedback/`.

A note is worked off in a commit that both implements the improvement **and
deletes the note file**. The commit is the record that the gap was closed, so
the `feedback/` directory only ever holds open items — a note that is still there
has not been addressed yet.

- One note per commit where possible. When one change closes several notes,
  delete all of them in that commit and mention them in the commit body.
- Never mark a note as done by editing its `status:` front matter; delete it.
- Do not delete a note that was only partially addressed. Instead, trim the note
  down to the part that is still open and explain the remaining gap.

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
written in `knowledge/` or in a rendered answer to the registry. Those catch a
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

- Everything the tools answer from lives below `knowledge/`, with one exception:
  what is registered in an installation is asked of that installation through
  `Typo3Cli`, because no bundled answer could be right for it. Add to
  `knowledge/` by default; reach for the console only when the answer genuinely
  depends on which packages are active.
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
- Verify facts against the local core checkout before writing them into
  `knowledge/`, and keep the wording branch-neutral where the fact is
  branch-specific.

### How an architecture hint is written

A hint is served without a branch. `typo3_server_scope` promises that the
server does not know which TYPO3 version the caller works on, so a hint that
only holds on one version is a hint that is wrong for everyone else. Five rules
follow from that; `HintsTest` enforces the mechanical ones.

- **State the shape that is current, not the history it replaced.** A bullet
  whose payload is "X is deprecated" becomes a bullet whose payload is "this is
  what new code looks like". The predecessor is then implied and stays a clause,
  not a bullet of its own.
- **No version numbers, no concrete changelog file names, no counts.** All three
  are a snapshot of one checkout and go stale silently. Counts measured while
  writing a hint are evidence for the author and belong in the commit message,
  not in the answer.
- **Where the answer is branch-specific, give the procedure, not the result.**
  Name what to read in the checkout — an `@deprecated` annotation, a
  `trigger_error(..., E_USER_DEPRECATED)` call, `Documentation/Changelog/`, the
  extension scanner matchers. A procedure works on every branch, a list only on
  the one it was taken from.
- **When a fact holds on some branches only, write the rule that holds on all of
  them.** `css-browser-target` is the model: not "this feature is allowed from
  version X", but "the evergreen baseline of the target release year decides".
- **"Check whether X" is not a hint, it is a check.** `hints` carries
  statements, `checks` carries commands that run. A check-shaped sentence with
  no command behind it tells the caller nothing it did not know already.
