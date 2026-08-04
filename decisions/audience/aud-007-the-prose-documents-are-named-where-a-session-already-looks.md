---
id: D-AUD-007
date: 2026-08-04
status: open
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
- [the resource surface](../../documentation/resources/readme.md): a resource is
  chosen by the host application or by the user rather than by the model
  mid-task. So a client that renders no list leaves the session nothing to find,
  and that is the half this side is blindest to.
- [`D-AUD-003`](aud-003-the-instructions-carry-the-entry-point.md) is the same
  shape one surface over: a session whose every criterion the conformance
  skill's body would have met never loaded it, and all thirty-five of its calls
  went through Bash.

## Decided

- The judgement is **step 1b**, the shape is missing. The index exists as
  `typo3://guides` and nothing a session *calls* enumerates it.
- **Taken on**: the instructions say that the corpus carries prose documents,
  what they are for, and how one is addressed. That is where a session that
  never calls a scope tool still reads it.
- The instructions name the index and not the documents one by one. A list that
  grows is a line that goes stale, and this one grows with the corpus.
- Whether a `typo3_document_list` tool follows is left open. The verb is the one
  `typo3_reference_list` already uses, and what decides it is whether a session
  holding the instructions still ends without reading the page it needed.

## Assumed

- That a client which renders no resource list still delivers the `instructions`
  it received at initialize. Every report this server has of its instructions
  landing comes from sessions in clients that also showed resources.

## Wrong if

- A session that received the instructions still finishes a task the corpus has
  a document for without opening it. Then naming them is not enough and the
  enumeration has to be callable.
- The line has to be edited whenever a document is added. Then it named the
  documents rather than the index, which is what this entry decided against.

**Since then**, on 2026-08-04, the line was written and it is one clause rather
than the paragraph this entry imagined. The `instructions` carry a budget —
`ScopeTest::theInstructionsFitWhatAClientKeeps` holds them to 2048 characters,
including the prefix that grows when a caller excludes tools — and a first draft
naming four documents and what reading one saves put the excluded-tools case
over it by 75.

What survived is the clause on the sentence that was already there: what
`typo3_server_scope` answers now reads as including the whole procedures served
as `typo3://guides` resources, which the client may not list at all. That keeps
the entry's own decision — the index rather than the documents — and the budget
is why it could not have gone the other way.
