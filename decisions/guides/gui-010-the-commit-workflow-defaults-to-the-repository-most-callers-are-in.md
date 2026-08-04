---
id: D-GUI-010
date: 2026-08-04
status: open
---

# D-GUI-010 — The commit workflow defaults to the repository most callers are in

**`typo3_commit_message_guide` defaults to `project`, and a patch against the
TYPO3 core states `workflow="core"`.**

`D-GUI-002` defaulted to `core` so that dropping rules would be something a
caller asked for. Its own **Wrong if** named what that costs everywhere else,
and the measurement it records is that the cost is the ordinary answer there.

## Evidence

- The cost was measured rather than feared. Called with `changeType` and
  `summary` and no `workflow`, the guide answers with `Resolves: #ISSUE_NUMBER`,
  `Releases: RELEASE_TARGET` and the hard `missing-issue` error — re-measured
  over stdio on 2026-08-04 and recorded on `D-GUI-002`. In a repository with no
  Forge issue behind it, that draft is not one anybody can commit.
- Three audiences read this server and one of them writes core patches. The
  knowledge base is written for core contributors, extension authors and site
  developers alike, and only the first of those has a Forge issue and a release
  target.
- This repository is its own witness. `AGENTS.md` writes by the project rules
  and says so, naming `workflow="project"` as where its two widths come from —
  `D-DOC-013`.
- Every call site that means project already states it: five published skills,
  the extension task intent, and the outside-the-core branch of
  `typo3_task_guide`. What the default decides is only the calls nobody wrote,
  and those are the ones a session makes on its own.

## Decided

- The default is `project`: the keyword, the 52/72 widths and the wrapping, with
  no trailer added or demanded.
- Core is stated rather than assumed. The two core skills, the core task intents
  and the core branch of `typo3_task_guide` name `workflow="core"`, and a core
  checkout is where a caller is most likely to be reading one of them.
- `[SECURITY]` follows the workflow it already followed: refused where the
  caller says `core`, accepted otherwise, because outside the core nobody holds
  the Security Team's reservation. What changes is that the reservation is now
  opt-in.
- The inference `D-GUI-002` refused stays refused. The workflow is still an
  argument and still not read out of the subject text — only which of the two it
  falls back to has moved.

## Assumed

- A core contributor reaches the guide through a route that names the argument.
  The two core skills and the core intents do; a session that calls the tool
  cold does not, and there the failure is quieter than the one it replaces.

## Wrong if

- A core patch is committed with a message the guide drafted and neither
  `Resolves:` nor `Releases:` in it, because nothing in the answer said one was
  missing.
- A contributor gets `[SECURITY]` accepted for a core patch by leaving the
  argument out.
- The routes that are supposed to state `core` stop stating it, so the default
  decides a case it was never meant to.

**Since then**, on 2026-08-04, a session measured what the project workflow is
worth over twelve commits. `feedback/2026-08-04-180133` called
`typo3_commit_message_guide` once, for the first commit, and hand-wrote the
other eleven: the answer was correct — a wrapped draft and a warning that the
summary ran to 68 characters against the preferred 52 — and once the shape of an
accepted message was in the session, the round trip stopped paying.

That is the default working rather than failing. The core workflow carries the
Forge issue and the Releases trailer, which cannot be recalled from the last
commit; the project workflow carries two widths and a wrap, which can. Nothing
is changed for it, and it is written here because a usage curve that drops after
the first call reads as a tool nobody wants when it is a tool that taught.
