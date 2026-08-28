---
id: D-FBK-020
title: A session is charged per call, so the calls are what is budgeted
date: 2026-08-02
status: confirmed
coveredBy:
  - CliTest::theTodoItHandsOverNamesTheFileItIs
---

# D-FBK-020 — A session is charged per call, so the calls are what is budgeted

**What a session costs is one context per tool call rather than one per token
read, so what it is told is about the number of calls.**

The rules a session is handed were written about what it reads. The 82 worktree
sessions of 2026-08-02 are the first run their cost was measured on, and it sits
somewhere else.

## Evidence

- 82 sessions, 5414 tool calls, 718 million cached input tokens read back and
  5.9 million written out. The context a call re-reads was 124k at its peak and
  82k on average, so a call costs about as much as the session so far.
- ~~Not one of the 5414 was issued beside another: every turn carried exactly
  one.~~ Wrong, and the readings below say why: the shape it was counted in
  cannot hold two calls, so it reads as one per turn whatever the sessions did.
  Grouped on the message, 2020 of them were issued beside another.
- 4046 were `bash`, and 2092 of those were `cat`, `sed`, `grep` and `ls`. The
  file tool was reached for 624 times, the search tool 15 times across all 82
  sessions, and the glob tool never.
- 40 of the 66 calls a session made came before its first change. 546 were `ls`
  against `todo/`, `decisions/` and `requirements/`, 207 of them against `todo/`
  alone, which is the file the command handing the todo over had just read.
- 401 calls were `sed -n` windows into a file that was opened again afterwards.
  One session opened `src/Installation/Extension.php` sixteen times, another
  `src/Tool/ExtensionScope.php` nine.
- Failures were not the cost: 77 errors in 82 sessions, 1.4% of the calls.
- Every one of the 82 opened `documentation/records/working-a-todo.rst`; 13
  opened `AGENTS.md`.

## Decided

- What a session is told is about calls: send what depends on nothing together,
  reach for a file with the client's own file and search tools, and open it once
  rather than in windows. It is in `AGENTS.md` as the rule and in the message a
  parallel session starts with, because that message arrives before the reading
  the rule is about.
- `bin/cli todo:next` names the file the todo is, in the line under the title.
  The command has just read it, and a session that is not given it goes looking
  in a directory that also holds everybody else's claims.
- Rejected: inlining the pages every session opens into the handover. The six of
  them are 103 KB against roughly five calls saved, and unlike a call they are
  paid for again by every call that follows.
- Rejected: telling a session to read less. What was read is what made the
  answers right, and 40 orientation calls is the shape of a step being read
  against the checkout rather than waste.

## Assumed

- ~~The client is free to batch and did not. A launch that forbids it would make
  the first rule unreachable, and nothing in the transcripts distinguishes the
  two.~~ Settled against itself on 2026-08-02: it was free and it did, on 37% of
  its calls, before it was told anything.
- Reading the same in fewer calls does not read it worse. The rule replaces
  three windows into a file with one opening of it, not the file with a memory
  of it.

## Wrong if

- The next run of ten measures the same calls per session, or fewer calls at the
  same cached tokens: then the cost is not where this puts it.
- A session batches and acts on a stale read — an edit composed against a file
  another call in the same message had already changed.
- Sessions stop reaching for `.checkouts/` and the manuals at the same rate. The
  orientation calls were not the waste; the second reading of one file was.

## Confirmed on 2026-08-02

The run of ten is in and none of the three **Wrong if** fired: calls per session
fell from 66.6 to 56.1, and the cached tokens fell with them rather than staying
put.

**One number in the Evidence is wrong, and it is the second bullet.** A
transcript may write one assistant message as several lines, each repeating the
same id and usage, so counting per line can never see two calls in one message.
Grouped on the message, 2020 of the 5465 baseline calls were issued beside
another — the client was free to batch and did, before it was told anything. So
what the rule moved is batching from 37% to 58%.

What the run rules out is the case the **Wrong if** named, fewer calls bought at
the same token cost, and that is the whole of what it settles: the ten todos are
not the 82, and n is 9.

## Confirmed on 2026-08-14

Measured a second time because a feedback reported that eight sessions batched
nothing at all. They batched: the dataset reproduces exactly, and grouped on the
message 218 of the 369 calls were issued beside another. The miscount is the one
corrected above, arrived at again by somebody who had not read that far down —
in this client every line carries exactly one call, so "one call per turn" is an
identity rather than a finding, and a third session filed the same reading an
hour later.

None of the three **Wrong if** fired. What the feedback reports and this
confirms is the second bullet: the rule moved the cheap-`bash` share once and
has not moved it since. Nothing holds any of it and nothing here can, since a
call pattern lives in transcripts outside this checkout.

## Since then

The first reading from the **caller's** side is in, and it profiles a session
using the server rather than working here. The statement holds where it is
stated: the server's 17% of the calls is about 17% of the cost, so what this
server costs its caller tracks how often it is called rather than how much any
one answer said. The profile also separates calls from requests, which is the
distinction three sessions above collapsed into one.

Two things bound it and neither moves it: a payload in a caller's session is
paid back over more and larger requests than one here, and half of everything
this server cost that session was one array reprinted on answers it had asked by
id. The largest single item is not a payload at all — nine debugging cycles cost
more than all the server calls together, every one a question this server did
not answer, which is `D-FBK-027`'s premise measured from the outside.

## Since then

A second caller-side reading measures what the *absence* of an answer costs: a
session spent about thirty round trips, every one of them `bash` and none
against this server, and 43 percent of it went on a single subject nothing here
covered. The number is kept for the reason the one above is, with one addition —
the change it measures has since landed, all three subjects being in the corpus
now. So the same task run again would measure not whether the answers are there
but whether they are called for at all, which is the zero this session also
reported.

**A cost like this one is not visible from inside this repository**, because a
caller's archaeology in somebody else's checkout leaves nothing behind unless a
debrief counts it.

## Since then

The four readings above were judged on 2026-08-22 against `D-DOC-041`, and none
of them is collapsed. Each is a measurement of a different run — the ten
worktree sessions after the rule, the eight of 2026-08-13, and the two caller
sessions that counted themselves — and a number belongs to the day and the
dataset it was taken on. What is not measured per run is nothing.

What the readings overtook is struck above: the evidence bullet counting one
call per turn, which three sessions have now arrived at independently in the
same wrong shape, and the assumption that the client was not batching.
