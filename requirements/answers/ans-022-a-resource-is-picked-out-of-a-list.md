---
id: R-ANS-022
title: 'A resource is picked out of a list'
status: held
restsOn: [D-AUD-004]
heldBy:
  - ResourceSurfaceTest
  - ResourceSurfaceTest::everyLinkASkillWritesResolvesToAResourceThisServerServes
  - ResourceSurfaceTest::onlyAPublishedSkillIsOffered
  - ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope
  - ScopeTest::everyPublishedSkillIsAnnouncedByTheScope
  - StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo
  - StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy
---

# R-ANS-022 — A resource is picked out of a list

**A `typo3://` resource is picked out of a list, so it says what it is and what
it obliges, and every audience has one to pick.**

Resources are application-driven: the host offers them for selection, or the
user searches them, where a tool is called by the model in the middle of a task.
The list is therefore the whole of what the choice is made on, and the spec says
what a client reads it by — `description` to understand what it is being
offered, `annotations.priority` to sort what is worth the context, `size` to
know what reading one costs. A resource offered as a name and a mime type is one
nobody picks.

Who does the picking is the second half. The tool list serves the three
audiences of [`R-AUD-001`](../audience/aud-001-core-extension-and-site-work-are-each-served.md), and
the document corpus behind `typo3://guides/{id}` serves one, because most of it
is the core repository's own and says so. The published task skills are the
other way round: most of them are extension, sitepackage and project work, and
two are the core's, which `knowledge/server-scope.json` is where to read off.
Both families are offered, so each audience picks something that holds where it
is working — out of prose that is written and maintained either way.

What is picked has to be the whole of what was picked. A skill is a directory:
its body is short routing and every one of them opens by sending the reader to
`references/base.md`, which is a file in no skill here — `Installer` writes it
when it publishes one. So the body is served at `typo3://skill/{id}/SKILL.md`,
where the relative links it already carries resolve to the reference URIs this
server answers, and nothing has to be rewritten for them to point somewhere. The
references are a resource template rather than list entries: they are followed
from the workflow that names them, and a checklist offered beside its own
workflow is an entry nobody can choose between.

## From

The reading of 2026-08-04 that found no file in `requirements/` naming
`typo3://` at all, and the answer that settled what the surface is for: it
serves all three audiences rather than being the core-contribution corpus it
was. The three ways of closing that were priced on the card, and the skills were
chosen because they cost `knowledge/documents/` nothing — that directory is also
what `typo3_rule_lookup` searches, and `D-ANS-040` measured a query falling from
0.508 to 0.462 coverage because four sections were added elsewhere.

## Held by

- `ResourceSurfaceTest`, for what a client reads before it picks: every resource
  describes itself, says who its answers oblige, and declares the size and the
  priority a picker sorts by
- `ResourceSurfaceTest::everyLinkASkillWritesResolvesToAResourceThisServerServes`,
  for the half a description cannot carry — every link a workflow writes,
  resolved against the URI it is served at, is a URI this server answers
- `ResourceSurfaceTest::onlyAPublishedSkillIsOffered`, so a draft is not
  published by the back door
- `ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope` and
  `ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`, for the coverage that
  says which audience each one is for — nothing else says it, and what no topic
  names is offered as core-only
- `StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy` and
  `StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo`, on the
  list and both families as they go over the wire
