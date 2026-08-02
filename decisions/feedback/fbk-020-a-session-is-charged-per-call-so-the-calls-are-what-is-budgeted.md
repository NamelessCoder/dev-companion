---
id: D-FBK-020
date: 2026-08-02
status: open
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
- Not one of the 5414 was issued beside another: every turn carried exactly one.
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
- Every one of the 82 opened `documentation/feedback/working-a-todo.md`; 13
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

- The client is free to batch and did not. A launch that forbids it would make
  the first rule unreachable, and nothing in the transcripts distinguishes the
  two.
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

## Covered by

- `CliTest::theTodoItHandsOverNamesTheFileItIs`
