---
id: D-SCO-011
date: 2026-08-04
status: open
---

# D-SCO-011 — A tool that describes one thing carries `describe`, and `scope` stays with the sources

**`typo3_project_scope` and `typo3_extension_scope` are renamed
`typo3_project_describe` and `typo3_extension_describe`, `describe` joins the
tool verbs as the sixth, and `Knowledge\Scope` is untouched.**

`scope` named two things that both go out on the wire: the enum whose cases are
`core`, `project`, `extension`, `any` and `uncertain`, and the tool verb for
what a source covers. The two tools whose names read as cases of that enum were
also the two carrying the verb wrongly, so one rename settles both.

## Evidence

- The four descriptions say which verb each tool actually has.
  `typo3_server_scope` opens "Orientation for this server: what it covers and at
  which depth, what it deliberately does not cover", and `typo3_catalog_scope`
  "Report whether component contracts come from the active installation or the
  bundled fallback … what they cover". Both state the coverage of a source.
- `typo3_project_scope` opens "Describe the project around the TYPO3
  installation this server was started in", and `typo3_extension_scope`
  "Describe what one installed extension registers". Both describe one thing the
  caller named, and neither states a boundary.
- The two tools whose verb was wrong are exactly the two whose subject is a case
  of `Knowledge\Scope`: `project` and `extension`. A client is handed
  `typo3_project_scope` and, inside the answers, a `scope` field that can read
  `project` — and nothing said the two were unrelated.
- `Knowledge\Scope` is the one vocabulary `D-KNW-005` collapsed four spellings
  into: `binding`, `provenance`, `audience` and an `outsideCore` boolean. It is
  declared on every statement in `knowledge/`, computed for every path, and read
  back in `scope` and `scopes` fields of the answers.

## Decided

- Two tools move, not four. `typo3_server_scope` and `typo3_catalog_scope` keep
  the verb, because what they answer is what the verb promises. Renaming them as
  well would have cost four contract breaks to fix a collision that only two of
  them are in.
- The enum does not move. It is the wider vocabulary of the two — one field on
  every statement and every path against two tool names — and `AGENTS.md`'s rule
  for a tie is that the name a client outside this checkout can see wins. Here
  both are visible, so the count decided it.
- `describe` is the sixth verb and is added to `ToolNamingTest`, which is the
  only place the vocabulary is defined. A `describe` answers for one thing the
  caller named and states what that thing is; a `scope` answers for a source and
  states the boundary of what it can be asked.
- The classes follow the names: `Tool\ProjectScope` is `Tool\ProjectDescribe`
  and `Tool\ExtensionScope` is `Tool\ExtensionDescribe`. Every tool class here
  is the CamelCase of its tool name and `Registry` is the only thing that lists
  them, so leaving the classes behind would have been the
  two-names-for-one-thing the rename removes.
- What recorded a call under the former spelling keeps it and gains a line
  saying so — the three forward runs and the recorded halves of four tool pages
  — because a recording says what it is of (`D-DOC-006`). The archived feedback
  and the decisions that name the old spelling stay as they are: an entry is
  dated evidence.

## Assumed

- No client stores a tool name anywhere this server could not reach. The names
  go out at `tools/list` on every connection and the skills are republished by
  `bin/typo3-cms-mcp update`, so a stale name survives only in a skill file
  somebody copied by hand.
- That a caller reads the verb at all. Nothing measures it; what the verb buys
  is that two tools with one answer shape cannot be named apart, which
  `ToolNamingTest::toolsSharingAnOutputSchemaShareTheirVerb` does hold.

## Wrong if

- A later tool wants `scope` for a subject that is neither a source's coverage
  nor a description — an answer about one call's placement, say, which is what
  `Knowledge\Scope` computes and what `typo3_task_guide` already returns as
  `scopes`. Then the verb `scope` is the collision rather than these two names,
  and what has to go is the verb.
- `describe` attracts a tool that should have been `lookup`. The tell is a
  `describe` whose subject is not named by the caller, or one where finding
  nothing is a legitimate answer: both of those are the `lookup` shape, and a
  `describe` that can miss is a `lookup` wearing the wrong verb.
- A session calls `typo3_project_scope` and gets `Unknown tool`, from a skill
  copied by hand or a client that cached the list. Two of the nine skills open
  on this call, so the failure lands in the first step of the order rather than
  somewhere a session can work around.

## Covered by

- `ToolNamingTest::everyToolIsNamedSubjectThenVerb`
- `ToolNamingTest::everyToolNameWrittenInTheKnowledgeBaseIsRegistered`
- `ToolNamingTest::everyToolNameAnAnswerNamesIsRegistered`
- `ToolNamingTest::everyToolIsDescribedInTheReadme`
