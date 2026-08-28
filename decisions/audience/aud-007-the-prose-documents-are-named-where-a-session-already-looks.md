---
id: D-AUD-007
title: The prose documents are named where a session already looks
date: 2026-08-04
status: open
coveredBy:
  - ScopeTest::theIndexNamesTheCallThatReadsAWholeProcedure
---

# D-AUD-007 — The prose documents are named where a session already looks

**What this server ships as prose documents is named in what every session
receives, because the list they are enumerated from is rendered by the client or
not at all.**

A session spent a repo-wide cleanup with the resource tools visible in its own
tool list and finished without learning whether there was anything to read.

## Evidence

- `feedback/2026-08-04-180133`: the client offered `ListMcpResourcesTool` and
  `ReadMcpResourceTool`, so the capability was there; nothing pointed at what
  this server offers through it, and the session never spent the call. Its own
  words: if the answer is "several", the discovery path is currently a guess.
- The sentence exists and sits behind a call that session did not make.
  `src/Tool/ServerScope.php:337` states that every prose document is readable as
  a `typo3://guides` resource whatever the tool list holds; the same feedback
  reports `typo3_server_scope` was never called, because
  `typo3_project_describe` had already answered what it went in for.
- The `instructions` in `knowledge/server-scope.json` — the one thing every
  client receives before any call — open with `typo3_project_describe` and
  `typo3_task_guide` and name no document.
- [the resource surface](../../documentation/server/resources/readme.rst): a
  resource is chosen by the host application or by the user rather than by the
  model mid-task. So a client that renders no list leaves the session nothing to
  find, and that is the half this side is blindest to.
- [`D-AUD-003`](aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  is the same shape one surface over: a session whose every criterion the
  conformance skill's body would have met never loaded it, and all thirty-five
  of its calls went through Bash.

## Decided

- The judgement is **step 1b**, the shape is missing. The index exists as
  `typo3://guides` and nothing a session *calls* enumerates it.
- **Taken on**: the instructions say that the corpus carries prose documents,
  what they are for, and how one is addressed. That is where a session that
  never calls a scope tool still reads it.
- The instructions name the index and not the documents one by one. A list that
  grows is a line that goes stale, and this one grows with the corpus.
- Whether a typo3_document_list tool follows is left open. The verb is the one
  `typo3_reference_list` already uses, and what decides it is whether a session
  holding the instructions still ends without reading the page it needed.

## Assumed

- That a client which renders no resource list still delivers the `instructions`
  it received at initialize. Every report this server has of its instructions
  landing comes from sessions in clients that also showed resources.

## Wrong if

- ~~A session that received the instructions still finishes a task the corpus
  has a document for without opening it. Then naming them is not enough and the
  enumeration has to be callable.~~ Fired on 2026-08-05 and again on 2026-08-09,
  and a callable enumeration is not what followed.
- The line has to be edited whenever a document is added. Then it named the
  documents rather than the index, which is what this entry decided against.

## Since then

The first **Wrong if** happened five hours after the line shipped: a core
session ran two skills end to end without learning what `typo3://guides` holds.
Its client rendered no resource list, and the clause naming the index sat in a
sentence about `typo3_server_scope`, which the session skipped deliberately
because the skills gave it an order of calls. That is the case this entry could
not see: a session arriving through a skill reads the skill, and the
instructions reach it as background. The lever is not a document list — it is a
skill naming the resource at the step that needs it, and a lookup answer saying
which procedure its section came from.

## Since then

On 2026-08-09 that evidence arrived and the answer is no.
`feedback/2026-08-08-224406` is a core patch session that read the guide ids in
`typo3_project_describe`, got the corpus answer's closing line, and opened no
page. The naming reached it on both surfaces this entry asked for, so what is
missing is beside them rather than instead of them: the two skills name a
`typo3://guides` address where the session needs the call, and a cut answer says
it cut a page without saying how much of it it left. That is
[`D-ANS-070`](../answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md),
and the enumeration this entry decided against is still not what follows.

## Since then

On 2026-08-14 a session held the naming and opened a page whole, in a client
that rendered no resource list: it reports the guide ids in
`typo3_project_describe` as the only place the corpus was named to it, and read
the page its skill named at the fetch step — a paragraph its own words would not
have matched in a search. The assumption it flags as unchecked holds, since that
listing enumerates the whole corpus. So the first **Wrong if** has an instance
the other way, and what produced the read is the skill's step rather than the
listing.

## Since then

On 2026-08-19 the clause was displaced by the call it should have named. A sixth
feedback quoted the sentence back, called this server nothing, and finished
without learning any guide exists. What was wrong is where it pointed: every
other surface had moved to `typo3_rule_lookup` with a `documentId`, and the one
statement every client receives was the last place routing through a resource
scheme. So the clause is gone and the index gained the call instead — the index
rather than a sentence, because an entry naming an excluded tool is dropped
where a sentence would still point at it. The feedback's own suggestion, naming
the documents there, is refused on the budget: it cost 1153 characters where 27
were free.

## Since then

Two readings carried this out and established nothing beyond it. The line is one
clause rather than a paragraph, because the `instructions` carry a budget a
first draft went over by 75 characters. And both contracts the first reading
named were built: every answer rendered by `Result\Prose` closes with the pages
its excerpts came from, and the two core patch skills name the workflow document
at their push and fetch steps —
`SkillTest::everyResourceASkillNamesIsOneTheServerServes` holds every uri in a
published skill to a document this server serves.
