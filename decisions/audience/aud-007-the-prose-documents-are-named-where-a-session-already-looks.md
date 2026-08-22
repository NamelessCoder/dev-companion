---
id: D-AUD-007
title: The prose documents are named where a session already looks
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
- [the resource surface](../../documentation/server/resources/readme.rst): a
  resource is chosen by the host application or by the user rather than by the
  model mid-task. So a client that renders no list leaves the session nothing to
  find, and that is the half this side is blindest to.
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

On 2026-08-05 the first **Wrong if** happened, five hours after that line
shipped. A core session ran two skills end to end and finished without learning
what `typo3://guides` holds (`feedback/2026-08-05-034022`). Its client offered
`ListMcpResourcesTool` and `ReadMcpResourceTool` and rendered no list, and the
clause naming the index was in a sentence about `typo3_server_scope`, which the
session skipped deliberately: the skills gave it an order of calls, so it never
needed orientation.

That is the case this entry could not see from where it stood. A session
arriving through a skill reads the skill, and the instructions reach it as
background it has no reason to act on. The page it wanted was the Gerrit push
procedure, which exists whole at
`typo3://guides/core/contribution/gerrit-workflow`, and the skill sends it to
`typo3_rule_lookup` for the same subject — so what it would have got is the
section a search matched, with nothing saying the rest of the page is there.

What follows is not a typo3_document_list. The session named the lever itself: a
skill names the resource it expects read whole, at the step that needs it, and a
lookup answer that is one section of a procedure says which procedure. Both are
contracts.

## Since then

On 2026-08-09 that evidence arrived and the answer is no.
`feedback/2026-08-08-224406` is a core patch session that read the guide ids in
`typo3_project_describe`, got the corpus answer's closing line, and opened no
page. The naming reached it on both surfaces this entry asked for, so what is
missing is beside them rather than instead of them: the two skills name a
`typo3://guides` address where the session needs the call, and a cut answer says
it cut a page without saying how much of it it left. That is
[`D-ANS-070`](../answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it-and-by-what-the-answer-left-of-it.md),
and the enumeration this entry decided against is still not what follows.

## Since then

On 2026-08-14 a session held the naming and opened a page whole.
`feedback/2026-08-13-214927` reviewed a Gerrit change in a client that rendered
no resource list, and reports the guide ids in `typo3_project_describe` as the
only place the corpus was named to it — enough that it never called
`typo3_server_scope`, which is the route this entry could not rely on.
`feedback/2026-08-13-214838` is the same session and carries the read:
`typo3_rule_lookup` with `documentId` `core/contribution/gerrit-workflow`, which
`typo3-core-patch-checkout` names at its fetch step. The paragraph it needed —
that the change refs are on the review server and `remote.origin.pushurl` is
what to fetch from — is one its own words would not have matched in a search.

The assumption the session flags as unchecked holds. `ProjectDescribe::guides()`
enumerates `Documents::documents()`, so the list it read is the whole corpus
rather than a selection of it, and reading it as complete was correct.

So the first **Wrong if** has an instance the other way for the first time. Only
the enumeration is read here; which surface produced the read is the session's
own account, and it names the skill's step rather than the listing — the listing
said what exists, and the skill said to open one of them.

## Since then

On 2026-08-19 the clause was displaced by the call it should have named.
`feedback/2026-08-18-113425` is a sitepackage session under a client that lists
the tools by name and defers their schemas. It quotes the clause back, called
this server nothing at all, and finished without learning that any guide exists.
It is the sixth feedback to quote that sentence, beside five in the archive from
2026-08-05 and 2026-08-07.

What was wrong is where the sentence pointed rather than that it named the
index. Every other surface had moved to the call: `Result\Prose` closes with
`typo3_rule_lookup` and a `documentId`, `typo3_test_run_guide` names its two
pages that way, and `typo3_project_describe` has enumerated the corpus at the
foot of its answer since 2026-08-08 — measured on 2026-08-19 against a checkout
of the reporting session's own shape, an extension repository with nothing
installed, which answers with every id and title. The one statement every client
receives was the last place routing through a resource scheme, and it warned
about the client instead of naming what works.

So the clause is gone and the index gained what it paid for: *the whole
procedure, not one fact out of it: typo3_rule_lookup with a documentId
typo3_project_describe lists*. The index rather than the sentence, because
`Coverage::offered()` drops an entry naming a tool the caller excluded
([`D-AUD-011`](aud-011-the-instructions-index-the-question-each-tool-answers-because-a-name-is-all-a-deferring-client-shows.md)),
while a sentence in `then` naming `typo3_rule_lookup` would point at a tool that
can be taken away. The longest assembly moves from 2021 characters to 2026 of
the 2048
[`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
holds, and `ScopeTest::theIndexNamesTheCallThatReadsAWholeProcedure` holds the
entry and the exclusion.

The feedback's own suggestion is refused on the same measurement. Naming the
documents in the `instructions` cost 1153 characters on 2026-08-19, where 27
were free, so this entry's second **Wrong if** is priced rather than argued.

Neither **Wrong if** is satisfied by the session. The corpus has no document for
what it wanted — reviewing an incoming pull request against an extension, and a
project package's release and backport policy, each filed as its own feedback —
so it is not a session that finished without opening a page that exists.

## Since then

Two readings carried out what this entry decided and established nothing beyond
it, so each is a line here rather than a section of its own. Judged on
2026-08-22.

- 2026-08-04: the line is one clause rather than the paragraph this entry
  imagined, because the `instructions` carry a budget —
  `ScopeTest::theInstructionsFitWhatAClientKeeps` holds them to 2048 characters,
  and a first draft naming four documents put the excluded-tools case over it by
  75. What survived kept this entry's decision, the index rather than the
  documents.
- 2026-08-05: both contracts the first reading above named were built. Every
  answer rendered by `Result\Prose` closes with the pages its excerpts were cut
  from, and `typo3-core-patch-development` and `typo3-core-patch-checkout` name
  `typo3://guides/core/contribution/gerrit-workflow` at the push and the fetch
  step — the two places a step described a whole procedure and routed to a
  search for it. `SkillTest::everyResourceASkillNamesIsOneTheServerServes` holds
  every uri in a published skill to a document this server serves.
