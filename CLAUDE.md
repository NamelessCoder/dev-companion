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
vendor/            # Composer dependencies (mcp/sdk); gitignored
```

`Typo3CmsMcp\Tools` declares every tool and renders its text output;
`Typo3CmsMcp\Knowledge` reads and searches the markdown documents. Tool names,
input schemas, and response formatting live in `src/`; everything they answer
comes from `knowledge/`.

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
