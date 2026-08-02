---
id: D-SKL-005
date: 2026-08-03
status: open
---

# D-SKL-005 — The review shape has a skill, and it is bounded away from the core

**A core-checkout review is the one review shape no skill owns, and the run that
showed it called this server nothing at all.**

`REVIEW-03` ran in `E-CORE` against one unpushed `[WIP][BUGFIX]` and produced a
review judged `partial`, four of five. It did it out of `git` and `grep`.

## Evidence

- 23 calls in 256 seconds: 22 `Bash`, one `Read`, no server call, no skill.
  Session `8622fa17`, `claude-opus-5`, Claude Code 2.1.220, the prompt on
  standard input and nothing beside it. The run stands in `scenarios/runs/REVIEW-03.json`.
- Not a launch failure and not a delivery one. The client's own MCP log for the
  session records `Successfully connected (transport: stdio) in 89ms`, both
  input-schema normalisations, and `STDIO connection closed after 256s
  (cleanly)`, so all 22 tools were registered throughout. The transcript's
  attachments carry the `mcp_instructions_delta` in full — first sentence
  `Start every task with typo3_project_scope` — and the `skill_listing` with all
  seven descriptions. Only the tools arrived deferred, as names without
  descriptions, which is `D-AUD-003`'s premise unchanged.
- The nearest skill matches the task shape almost word for word and excludes the
  checkout: `typo3-extension-conformance` is "Review, audit, or improve a TYPO3
  project, sitepackage, or extension … and report what is wrong with it in
  priority order", against a prompt reading "Review the current changes in this
  TYPO3 core checkout … in priority order". It did not fire, and it was right not
  to. The other six are extension or site work by their own descriptions.
- The content was here and the order was not. `knowledge/documents/typo3-core-scripts.md`
  holds the core scripts, `typo3-commit-messages.md` the commit rules, and
  `typo3_script_lookup`, `typo3_test_run_guide` and `typo3_commit_message_guide`
  are tools this server ships. The criterion the run missed is the verification
  one: it reached for the host's `php -l` and named no project command.
  `bin/cli hints:probe` on the run's own prompt matches nothing at all; 40 hints
  are candidates and none is reached.
- The other side of the same shape is already reported. `feedback/2026-08-02-144350`
  is a core session that *did* call `typo3_project_scope` and was answered with
  four `gerrit:setup` commands and no `Build/Scripts/runTests.sh`; it ran that
  script about thirty times and took its syntax from elsewhere. So the entry
  point that fired and the entry point that did not both end at the same missing
  answer.

## Decided

- Nothing is built on this judgement. It names the gap and stops: whether core
  work earns a skill is the research
  [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) demands
  before a line is written, and it is queued rather than answered from here.
- The feedback that produced this entry is trimmed rather than filed whole. Its
  second half — no project-owned command — belongs to `feedback/2026-08-02-144350`,
  which has the better evidence for it, and counting it twice would have put a
  fourth entry beside three that exist.

## Assumed

- That the missing thing is an order rather than a sentence. The instructions
  arrived and named the entry point; a session that reads them and calls nothing
  is evidence about the order, which is the reading `D-AUD-003` already made for
  `REVIEW-01` when it handed that half to `D-SKL-001`.
- That one run is not the whole answer. Two of the three recorded core sessions
  did reach the server, both on fix tasks rather than reviews, and both were
  answered thinly.

## Wrong if

- A second `REVIEW-03` run in the same client and model calls this server with
  no skill added. Then the skill was not the obstacle, and what is left to
  suspect is what the tools answer a core checkout with — starting with the one
  `feedback/2026-08-02-144350` reports.
- Or a core skill is published and the next run still hand-reads the checkout.
  Then the domain did not earn one, and the surface it promised is larger than
  what a core review actually needs from here.
