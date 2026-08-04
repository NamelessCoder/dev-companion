# Say what the resource surface is for, and who it serves

**Serves:** R-ANS-022
**Priority:** normal
**Waiting on:** which of the three options below closes the document gap — the
    skills offered as a second resource family, the transferable hints rendered
    into documents, or documents written from scratch. The requirement and the
    metadata are done; what is left is prose that does not exist, and what it
    should say is a question about what is wanted rather than one this
    repository can answer.

The question this card waited on was answered on 2026-08-04: the resource
surface is to serve all three audiences, and being the core-contribution corpus
is the gap to close rather than the thing to write down. Two thirds of that is
done.

[`R-ANS-022`](../../requirements/answers/ans-022-a-resource-is-picked-out-of-a-list.md)
is the requirement that was missing, `open` because the documents it demands do
not exist. The six `ResourceDefinition`s now carry `description`,
`annotations.priority` and `size`: a description saying the subject and who its
answers oblige, derived from the covered topic rather than written a second
time, and a priority putting the index above the documents and a document that
holds anywhere above one that stops at the core. `annotations.lastModified` is
not filled, because `Mcp\Schema\Annotations` in mcp/sdk v0.7.0 carries
`audience` and `priority` and nothing else — the field is in the 2025-11-25 spec
and not in the SDK.

## What the reading established

- Five documents, and the coverage says who each serves. `typo3-commit-messages`
  is scope `any`; `typo3-core-rules`, `typo3-gerrit-workflow`,
  `typo3-core-scripts` and `typo3-contribution-sources` are scope `core`.
- `Documents::isCoreOnly` keys off nothing of its own. It finds the covered
  topic in `knowledge/server-scope.json` whose `source` names
  `typo3://core/<id>` and returns whether that topic's `scope` is `core`. A
  document no topic names is core-only by default, and
  `ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope` forbids that case.
- The three audiences of `R-AUD-001` are the core contributor, the extension
  author and the site developer. Of the five documents the last two are offered
  one, and it is the commit conventions.
- Every option that adds a file to `knowledge/documents/` pays a cost that is
  not about resources at all: that directory is what `typo3_rule_lookup`
  searches, a term is weighed by how few sections carry it, and `D-ANS-040`
  records a query moving from 0.508 to 0.462 coverage because four sections were
  added elsewhere. Sections added for a picker move the answers of a lookup
  nobody was touching.

## The options, and what each costs

**A — offer the skills as a second family, `typo3://skill/{id}`.** Eight of the
ten skills are extension and site work — backend modules, content elements, the
development installation, conformance, documentation, release, testing, upgrade
— and two are the core's. All three audiences would have something to pick on
the day it ships, out of prose that is already written and already maintained.
It costs a second family in `Factory` and `ResourceHandler`, a coverage topic
saying what the family is, and a description per skill, which the front matter
already carries. It touches `knowledge/documents/` not at all, so no lookup
score moves. Two things to decide with it: a skill is a workflow written for an
agent mid-task rather than a page a host offers for selection, and a client that
ran `bin/typo3-cms-mcp setup` already has the same files installed — for a
client that never ran it, the resource is the only route there is.

**B — render the transferable hint subjects into documents.** The material
exists, in 34 subject files below `knowledge/hints/`, most of them scope `any`.
It costs a renderer, a coverage topic per subject, and the version bindings:
`since` and `until` are data on a statement and `HintsTest` forbids a version
number in the sentence, so rendering them into prose is the thing the corpus is
built to prevent. Every rendered subject then joins the rule-lookup corpus and
moves the coverage of queries already pinned in `ScopeTest`. The most expensive
option, for information a caller can already get in one `typo3_hint_lookup`
call.

**C — write one document per missing audience.** It costs prose nobody has
written: verified against `.checkouts/`, not restating the hint corpus that
already holds extension and site knowledge, and carrying a coverage topic each.
It is also the option that cannot be started without the answer, because what an
extension author's document should contain is the question rather than the
writing.

**Recommended: A, and not B.** A closes the gap with material that exists and
without touching the search corpus; B duplicates a tool answer at the highest
price on the list. C stays open for a subject a hint cannot carry — a document
is read whole, a hint answers a query — and that subject has to be named before
anybody writes one.

What is already true either way: the surface is now honest about serving one
audience, since every document says whether it transfers and sorts accordingly.
Honest is not served, which is what `R-ANS-022` stays `open` for.
