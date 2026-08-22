---
id: D-FBK-047
title: 'The debrief asks what an answer left out'
date: 2026-08-18
status: open
---

# D-FBK-047 — The debrief asks what an answer left out

**The debrief prompt asks what an answer stopped short of, what the session
wished for and what the run cost, beside the calls it already asked about.**

A session reports what it did, and what it did is call tools. A list weighted
towards the surface comes back describing the surface, and the corpus the task
actually turned on goes unmentioned.

## Evidence

- Of the ten bullets the prompt carried, six asked about the surface — skills
  twice, calls, resources, names, errors — and one asked what the session had to
  establish elsewhere. That one ended "that this server should have answered",
  which hands the scope judgement to a session that has read `doesNotCover` and
  will drop exactly the findings that say the boundary is wrong.
- The 29 open feedback on 2026-08-18: 17 `idea`, 6 `missing-knowledge`, 5
  `tool-gap`, 1 `bug`. The 404 in the archive: 140 `missing-knowledge`, 122
  `idea`, 64 `tool-gap`, 43 `bug`, 35 `wrong-answer`.
- No bullet asked whether a statement held on the TYPO3 version the session was
  working against, though `since` and `until` are what the corpus binds with.
- What a session costs is measured in `D-FBK-020` from this repository's own
  worktree sessions. For a session using the server as a caller — the ones this
  prompt is handed to — there is no figure at all.

## Decided

- Two bullets added: the answer that was right and stopped one step short, which
  is what the corpus fails at rather than absence; and what the session would
  have wanted, asked without the scope test it would otherwise apply.
- Three widened. The round-trip bullet asks which single call would have settled
  a question several went into. The name bullet extends from what a tool is
  called to what it answered, because knowledge under a term nobody tried reads
  as knowledge that is not there. The error bullet names a statement that did
  not hold on the version in front of the session.
- The scope clause is gone from "what you had to establish elsewhere": all of
  it, sorted here rather than there.
- The cost is asked in the closing, after the findings, and reaches no file. It
  is information for whoever pasted the prompt.
- Rejected: a cost field on `typo3_feedback_record`. One figure establishes
  nothing, the series is what would, and keeping it is the maintainer's job
  rather than a schema's.
- Rejected: merging the two skill bullets to hold the list at ten. The
  counterfactual half is what the page defends, and no reader asked for a
  shorter list.
- The prompt moved out of `documentation/records/readme.rst` into a page of its
  own, so what a person pastes has somewhere to be pointed at.

## Assumed

- A client reports what a session cost, or the session says that it does not.
  Nothing here can check either.
- Twelve bullets are still answered to the end. The one that catches a list read
  half way is the last, which asks what the list did not.

## Wrong if

- The next twenty feedback are as thin on the corpus as the last twenty:
  `missing-knowledge` and `wrong-answer` together stay under a quarter of what
  arrives.
- The wish bullet comes back with wishes the server already answers, which would
  mean the session dropped its knowledge of the tools along with the scope test.
- The answers thin out towards the end of the list, or stop before it.

## Since then

`feedback/2026-08-18-071603` is the first measurement of this list from inside a
session, and it names which question produced which finding across the ten
reports it summarises. Five of the twelve bullets are named as having produced
one, the round-trip and the resource bullets among them, so the list works where
it was already read.

Two things it names are not in the list, and both are folded into bullets rather
than added as bullets thirteen and fourteen, because what this entry rejected
was the list growing and not the questions.

- The round-trip bullet asked which calls the session would not make again, and
  that is answered from the calls it remembers as notable: re-walking the actual
  list found five checkout calls where two had been reported, two of them
  returning nothing at all. It now asks for the walk and says what memory drops.
- Nothing asked the session to read the answers it received a second time.
  `typo3_task_guide` had returned two installation intents at strong confidence
  and one skill, which is the gap `071526` reports, and the session read past it
  while the answer was on the screen. The bullet about an answer that stopped
  one step short now carries it, because both are findings about an answer that
  was right.

What the feedback also shows is the boundary of a written list. Three findings
were left on the floor until the user asked three plain questions of their own,
and one of them — "is documentation missing" — the session could only answer
with nothing, having made no call it could report on. A prompt gets what it asks
for; `D-FBK-048` is what makes the asking cheap enough to happen at all.
