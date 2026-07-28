# Working on this repository

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

## Knowledge base

- Everything the tools answer from lives below `knowledge/`. The server never
  reads a TYPO3 core checkout at runtime.
- Verify facts against the local core checkout before writing them into
  `knowledge/`, and keep the wording branch-neutral where the fact is
  branch-specific.
