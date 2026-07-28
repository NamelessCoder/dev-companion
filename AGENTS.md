# Working on this repository

## Layout

```
bin/typo3-cms-mcp  # stdio entrypoint (the client launches it as a subprocess)
src/               # PHP classes (knowledge loading, tools, SDK wiring)
src/ServerFactory.php  # builds the mcp/sdk server from the tool definitions
src/Mcp/           # SDK handlers: tool dispatch and typo3://core resources
src/Catalog/       # the component, icon, and label lookups
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
  a hit and on a miss; `tests/Smoke/` drives `bin/typo3-cms-mcp` as a subprocess
  over JSON-RPC.
- A behaviour worth a rule in `knowledge/` is worth a test: ranking that must
  prefer one match over another, an answer that must say "no match" instead of
  guessing, a catalog field that must stay usable.
- `FeedbackTest` writes real notes below `feedback/` and removes them again.
  A leftover file carries `phpunit-feedback-fixture` in its text.

## Feedback workflow

Agents using this server record improvement notes through `typo3_make_me_better`.
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

- Everything the tools answer from lives below `knowledge/`. The server never
  reads a TYPO3 core checkout at runtime.
- Add new rules or scripts to `knowledge/` first; promote recurring workflow
  logic to a tool only when it has earned it.
- Verify facts against the local core checkout before writing them into
  `knowledge/`, and keep the wording branch-neutral where the fact is
  branch-specific.
