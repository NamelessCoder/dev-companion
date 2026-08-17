---
id: D-FBK-020
date: 2026-08-02
status: confirmed
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
  **This one is wrong**, and the confirmations below say why: the shape it was
  counted in cannot hold two calls, so it reads as one per turn whatever the
  sessions did.
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

## Confirmed on 2026-08-02

The run of ten is in. None of the three **Wrong if** fired, and the first one —
the same calls per session, or fewer calls at the same cached tokens — is the
one this entry was waiting on. Calls per session fell from 66.6 to 56.1, and the
cached tokens fell with them rather than staying put.

The ten are the directories under
`~/.claude/projects/-home-benji-projects-typo3-dev-companion--worktrees-*` whose
transcript was last written after 16:00; the other 82 are the baseline, and no
directory is in both sets. One of the ten is the aborted first attempt at this
measurement, `6ee04fe8-8d02-4101-b875-65683cefdac3`, which read transcripts and
blocked rather than working a todo, so both the ten and the nine without it are
below. The measuring session itself is excluded from both.

| Measure                        | Baseline 82  | The ten    | The nine   |
| ------------------------------ | ------------ | ---------- | ---------- |
| Tool calls per session         | 66.6         | 53.4       | 56.1       |
| Calls issued beside another    | 2020 of 5465 | 314 of 534 | 291 of 505 |
| Cheap `bash` of all `bash`     | 2023 of 4111 | 81 of 261  | 74 of 245  |
| Cached tokens read per session | 8.99M        | 7.14M      | 7.59M      |

**One number in the Evidence above is wrong, and it is the second bullet.** A
transcript may write one assistant message as several JSONL lines, one per
content block, each repeating the same `message.id`, the same `requestId` and
the same `usage`. Over the 82 that split factor is 1.94, so counting `tool_use`
blocks per line can never see two calls in one message: it is not that no turn
carried two, it is that the shape the count was taken in cannot hold two.
Grouped on `message.id` — `requestId` gives the identical totals — 2020 of the
5465 baseline calls were issued beside another, in 77 of the 82 sessions. So the
**Assumed** bullet is settled too, and against itself: the client is free to
batch and *did*, on 37% of its calls, before it was told anything.

The same split inflates the fourth number. Summed per line the baseline is
736.9M cached tokens, which is the recorded 8.99M per session and reproduces the
figure the first attempt reported; counted once per message it is 404.2M, or
4.93M per session. The table uses the per-line method on both sides, because
that is the one the recorded baseline was taken in and the **Wrong if** is a
comparison. Counted per message the fall is steeper, not shallower: 4.93M to
3.85M.

What the rule moved, then, is not batching from nothing but batching from 37% to
57.6%, in 9 of 9 sessions rather than 77 of 82. Beside it the `bash` habit is
where the calls actually went: 50.1 `bash` calls per session became 27.2, the
cheap quarter of them fell from 49.2% to 30.2% of all `bash` (62.3% to 40.0%
counting a cheap command anywhere in a pipeline rather than at its head), the
file tool went from 7.6 calls per session to 17.8 and the search tool from 0.18
to 3.4. The glob tool is still never reached for, on either side.

The other two **Wrong if** did not fire either. No session batched onto a stale
read: of the 132 batched messages in the run, none carried two calls against one
path, where the baseline has 12 that do — every one of them a pair of `Edit`
against a single file. And the orientation reads held rather than being traded
away for the saving: `.checkouts/` went from 4.74 calls per session to 5.78, in
the same share of sessions, and `documentation/` with `AGENTS.md` from 6.91 to
7.00, in all nine. What fell is the reading this entry named as the waste —
`todo/` from 6.63 per session to 5.00.

Two things this does not establish. The ten todos are not the 82 todos, so a 16%
fall in calls per session is a fall across different work and n is 9; and
because the cached tokens fell by 15.6% while the calls fell by 15.8%, this run
is consistent with the cost being per call and equally consistent with it being
per token. What the run rules out is the case the **Wrong if** named — fewer
calls bought at the same token cost — and that is the whole of what it settles.

## Confirmed on 2026-08-14

Measured a second time, on the eight worktree sessions of 2026-08-13, because
`feedback/2026-08-13-224311` reports that those eight batched nothing at all.

They batched. The feedback's dataset reproduces exactly — 369 calls between the
claim at 22:20:42Z and the report at 22:43:11Z, 37 to 59 per session, 223 of
them `bash`, 96 `Read`, 18 `Grep`, no `Glob`, and 79 opening with `ls`, `grep`,
`sed`, `cat`, `head` or `wc` — so it is the same eight transcripts read again.
Grouped on `message.id` rather than on the line, 96 of the 247 assistant
messages carried more than one call, and 218 of the 369 calls were issued beside
another. That is 59.1% against the 57.6% above.

The miscount is the one corrected here on 2026-08-02, arrived at again by
somebody who had not read that far down. Every JSONL line in this client's
transcript carries exactly one `tool_use` block: over the whole board that night
it is 1780 lines for 1780 calls, in 38 sessions. Counted per line, "one call per
turn" is therefore an identity rather than a finding, and no run of sessions can
move it. Grouped on the message, those 38 issued 43.7% of their calls beside
another and 35 of them batched at least once. A session filed the same reading
51 minutes later over 1234 calls, which makes three.

None of the three **Wrong if** fired. Calls per session are 46.1 against the
56.1 of the run of nine, and the cached tokens fell with them rather than
staying put — 6.71M per session counted per line and 3.21M counted per message,
against 7.59M and 3.85M. No batched message carried two calls against one path.
`.checkouts/` was reached for 2.88 times per session in 5 of the 8 against 5.78
in all nine, and that is the work rather than the rule: all eight were judging a
feedback, which `documentation/records/judging.rst` describes as a run that
reads this repository and establishes nothing about TYPO3.

What the feedback reports and this reading confirms is the second bullet. 27.9
`bash` calls per session against 27.2, 35.4% of them opening with a cheap
command against 30.2%, `Glob` still never reached for. The rule moved that
number once and has not moved it since.

Nothing holds any of it and nothing here can: a call pattern lives in the
client's transcripts, one directory per worktree outside this checkout, in the
shape that produced this miscount three times. So it is measured per run and
written into this entry, which is the answer to the feedback's question of what
would hold the bullets. What was wrong was the evidence under the first bullet,
and `AGENTS.md` carried the disproved sentence to every session until this
commit.

## Since then

The first reading from the **caller's** side is in. `feedback/2026-08-17-211826`
profiles one session in `/home/benji/projects/site-demo` that built a TYPO3 v14
sitepackage plus a distribution extension carrying the content, on 14.3.6, and
measured itself off its own client transcript deduplicated on `requestId`.
Everything above is a session working in this repository; this one is a session
using the server.

| Measure                     | The build session                                        |
| --------------------------- | -------------------------------------------------------- |
| Tool calls / requests       | 215 / 148, so 1.45 calls per request                       |
| Cached input read           | 38.06M, or 257k per request                                |
| Cache written / output      | 620k / 174k                                                |
| Cost at Opus 5 list rates   | $29.59, and $39.04 including the debrief                    |
| Calls against this server   | 36 of 215, 17%                                             |

**The statement holds where it is stated.** The server's 17% of the calls is
about 17% of the cost — roughly $4.98 amortised over the turns each answer
stayed in context — so what this server costs its caller tracks how often it is
called rather than how much any one answer said. The reading also puts the
entry's own count beyond argument: the profile separates calls from requests as
two numbers, which is the distinction three sessions above collapsed into one.

Two things bound it, and neither moves it. The first is the multiplier: 257k of
context re-read per request against the 82k average of the 82, so a payload in a
caller's session is paid back over more and larger requests than one here. The
second is what that does to a payload that is both large and repeated — the
session reports ~$2.57 of its $4.98, half of everything this server cost it, as
the `availableHints` array reprinted on 21 answers it had asked by id.
`feedback/2026-08-17-212300` is that half, with a card of its own, and the lever
is the array rather than the rule.

The largest single item is not a payload at all. Nine debugging cycles cost the
session roughly 45 `bash` round trips, more than all 36 server calls put
together, and every one of them was a question this server had no answer to or
did not deliver. That is
[`D-FBK-027`](fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)'s
premise measured from the outside, and the nine are on the board as
`feedback/2026-08-17-212010` and its siblings.

What this does not do is test the three **Wrong if**. They compare runs of
worktree sessions in this checkout, n is 1 here, and nothing in this repository
can reproduce a figure read out of a client transcript in somebody else's
project. It is kept because a later change to answer size or routing has
something to be measured against, which is what the reporting session asked for
and the only form a keep can take for a number.
