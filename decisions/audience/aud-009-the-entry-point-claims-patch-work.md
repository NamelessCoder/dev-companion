---
id: D-AUD-009
title: 'The entry point claims patch work'
date: 2026-08-08
status: open
coveredBy:
  - KnowledgeTest::theInvocationNotesNameTheInstallAFreshCheckoutOwes
  - ScopeTest::theEntryPointClaimsTheWorkThatEndsBeforeAPatch
---

# D-AUD-009 — The entry point claims patch work

**A session asked whether a bug still reproduces skipped `typo3_task_guide`,
because the instruction beside it says the coding agent writes the patch.** Its
task ended one step before a patch.

## Evidence

- The sentence is verbatim, re-read on 2026-08-08: "The coding agent writes the
  patch; this server supplies the task knowledge and workflows around it." It
  sits immediately after "typo3_task_guide then gives the workflow the task
  belongs to".
- `feedback/2026-08-07-231236` quotes both and says what it concluded: the guide
  read as addressed to work it was deliberately not doing — the user's closing
  words were "before I touch it" — so a read-only triage had no workflow to look
  up. It says it would make the same call again from the same wording, which is
  what makes this the wording rather than the session.
- The task was `SKILL-12`'s prompt: thirty oldest unresolved issues, pick the
  first real bug, say whether it is still a thing. Backlog triage, "does this
  still reproduce" and "what would this cost" all end before a patch exists, and
  all three are named in `scenarios/` as work this server answers for.
- The same shape twice more in one session. `typo3_test_run_guide` was passed
  over at the moment the session started poking at `runTests.sh` with `ls` and
  `command -v`; its description opens "Recommend Build/Scripts/runTests.sh
  commands by topic", which claims running a suite rather than what a checkout
  needs before one can run at all — `feedback/2026-08-07-231249`.
  `typo3_server_scope` was loaded and never called because "the task looked
  legible without orientation" — `feedback/2026-08-07-231203`.
- Nothing routed, because no skill was installed where the client reads them.
  That is a setup fault of this repository's and is why the run settles nothing
  about `SKILL-12`. What it does settle is what the tools carry alone, and the
  tracker half was carried well.

## Decided

- The framing is wrong rather than incomplete. It names the division of labour —
  who writes the patch — where a caller is looking for what this server answers
  for, and a task with no patch at the end of it reads that as somebody else's
  subject.
- So the entry point claims the work that ends before a patch by name: triage,
  reproduction, and pricing a fix. Those are not a concession to one session;
  they are three of the task shapes `scenarios/` already holds cases for.
- `typo3_test_run_guide` claims the earlier question in the same move — what a
  checkout needs before a functional test can run, and which interpreter it runs
  under. That is the question an agent holds at the moment it reaches for `ls`.
- This does not reopen `D-ANS-061`. That entry decided the lever is the tool the
  session does call, and this is the same argument applied to what those tools
  say they are for.

## Assumed

- The wording is what did it. The session says so and says it would repeat the
  reading, which is strong for one reader and is still one reader.
- Claiming the pre-patch tasks does not cost the post-patch ones their clarity.
  Nothing here has measured a description that names both.

## Wrong if

- A session reports reaching `typo3_task_guide` for a triage and finding a
  checklist about writing a patch, which would say the framing was honest and
  the coverage is the gap.
- Naming three more task shapes in the instructions is reported as making them
  longer without making them clearer, which `R-ANS-013` already holds a budget
  for.

## Since then

**2026-08-08.** The sentence is gone and what replaced it names the three: "Not
every task ends in a patch: triaging the backlog, whether a report still
reproduces, and what a fix would cost are answered here." It displaced rather
than added, which `R-ANS-013` requires — the worst case measured 2004 of 2048
characters before it and the replacement cost 34 of the 44 that were left.

`typo3_test_run_guide` claims the earlier question in its first sentence, and
the answer was reordered to match: what a checkout needs before any suite runs
is `preconditions` in the corpus now and the block the answer opens with, where
it was two of seven notes below every suite. Both facts the reporting session
needed were already in that answer and both were at the bottom of it.

`typo3_task_guide`'s description names the three change types that get a brief
of their own, which is the second half of the same claim: `triage` did not exist
when this entry was written and is `D-GUI-011`.

`typo3_server_scope` is untouched. `D-ANS-061` decided the lever is the tool a
session does call, and `todo/open/2026-08-08-090300` is where the inventory
question stands.

### 2026-08-24 — the sentence was rewritten and the next session never loaded it

`feedback/2026-08-24-122308` is a core patch review that ran `runTests.sh` with
the path before `--`, lost a container cycle to it, and reports
`typo3_test_run_guide` as a bare name in a deferred list whose schema it never
fetched. Its ask is that first sentence again, for argument order this time,
which would be the third question one description claims.

That is this entry's first **Assumed** read from the other side. The session of
2026-08-07 said the wording was what did it and this one never saw the wording
at all, which bounds the lever rather than the rewrite: a description reaches
the caller that reads one, and `D-AUD-003` already said that under deferral it
is not a channel. Both sessions passed the tool over at the moment they were
about to run something, which is what the **Decided** above claims that moment
for and is unmoved.

What the later feedback is judged on instead is the corpus:
[`D-KNW-112`](../knowledge/knw-112-the-invocation-notes-say-where-runtests-sh-stops-reading-its-own-options.md)
writes down where the script stops reading its own options, so a session that
already ran the wrong form has something to search on.
