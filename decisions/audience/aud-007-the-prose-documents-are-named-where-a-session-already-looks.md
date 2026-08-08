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

**The first Wrong if happened**, on 2026-08-05, five hours after that line
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

What follows is not a `typo3_document_list`. The session named the lever itself:
a skill names the resource it expects read whole, at the step that needs it, and
a lookup answer that is one section of a procedure says which procedure. Both
are contracts.

**Since then**, on 2026-08-05, both were built. Every answer rendered by
`Result\Prose` — the rule lookup, the script lookup and the task guide — closes
with the pages its excerpts were cut from, saying they are sections and that a
client may render no resource list. The `Source:` line above each excerpt
already carried the same uri and was read as attribution, which is why saying it
once at the foot is the change rather than another field.

The skill half is two skills and one rule. `typo3-core-patch-development` names
`typo3://guides/core/contribution/gerrit-workflow` at the push step and
`typo3-core-patch-checkout` at the fetch step, which are the two places a step
described a whole procedure and routed to a search for it — the sweep found no
third. `SkillTest::everyResourceASkillNamesIsOneTheServerServes` holds every uri
in a published skill or its references to a document this server serves, because
a dead address in a copy nobody updates is worse than none.

What that does not settle is the first **Wrong if**, which asked whether naming
the documents is enough. It was answered for the two skills that route to a
procedure and for every corpus answer; whether a session now opens the page is
the next run's evidence and not this change's.

**Since then**, on 2026-08-09, that evidence arrived and the answer is no.
`feedback/2026-08-08-224406` is a core patch session that read the guide ids in
`typo3_project_describe`, got the corpus answer's closing line, and opened no
page. The naming reached it on both surfaces this entry asked for, so what is
missing is beside them rather than instead of them: the two skills name a
`typo3://guides` address where the session needs the call, and a cut answer says
it cut a page without saying how much of it it left. That is
[`D-ANS-070`](../answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it-and-by-what-the-answer-left-of-it.md),
and the enumeration this entry decided against is still not what follows.
