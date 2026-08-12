---
id: D-ANS-048
date: 2026-08-04
status: open
---

# D-ANS-048 — A tool declares what can answer it, and both readers render that

**Every tool declares its sources as `answersFrom()`, and the description a
client reads, the `answeredBy` cases and the orientation answer are rendered
from that one declaration.**

A caller could read what a tool is about and not whether it would answer at all
with the containers down. Three tools declared
`answeredBy: installation | packages` while only ever emitting `installation`,
so the schema promised a fallback that was never going to arrive.

## Evidence

- Of the 26 offered tools, 8 can be answered by the installation, 8 by the files
  the packages ship, 12 by the bundled knowledge, 3 by a network service and 2
  by this checkout — several by two of them. Nothing outside the source of each
  tool said so.
- `typo3_configuration_lookup`, `typo3_schema_lookup` and
  `typo3_backend_module_lookup` emit `'installation'` on every path and declared
  both cases. `typo3_project_scope` and `typo3_changelog_lookup` emit
  `'packages'` on every path and declared both.
- Four descriptions restated their own path in prose, in four different
  wordings, and one of them named `answeredBy` from inside the sentence that was
  about to be contradicted by it.

## Decided

- The declaration is a `Source` enum on the tool, not a string list: a case that
  does not exist fails to compile rather than fails a test.
- `Registry::definitions()` appends the clause to the description, so a tool
  that gains or loses a source cannot keep saying the old thing where a client
  reads it. Every description ends the same way, which is what makes it
  skimmable at the cost of one short sentence per tool per session.
- `Schema::answeredBy()` takes the tool's own declaration and keeps the two
  sources that label a single answer. A knowledge file and a network service are
  what a whole tool reads, never one call, so they are not cases of it.
- `typo3_server_scope` groups the offered tools by source rather than listing a
  source per tool: the question it is asked is about the state of the machine,
  and a tool with two sources belongs under both.
- `documentation/server/tools/answer-sources.md` is written by `tools:index`
  from the enum, and every tool page links its sources into it. The names alone
  do not carry the difference that matters, and a reader meeting `packages` for
  the first time has one place to go.

## Assumed

- A source list is stable enough to be worth appending to every description. A
  tool that changed sources per call would make the clause a lie the schema
  cannot correct.

## Wrong if

- A tool is added whose answer comes from something none of the five names, and
  the nearest case is picked to avoid touching the enum — the clause then says
  something true of no source.
- The clause is measured against a client's tool-list budget and loses: 26 tools
  carry it in every session, and nothing here has weighed that against what a
  client will hold.

## Covered by

- `SourceTest::theAnsweredByCasesAreTheDeclaredSources`
- `SourceTest::theDescriptionACallerReadsCarriesThem`
- `SourceTest::theOrientationAnswerGroupsEveryOfferedTool`
