---
id: D-EVI-007
title: A case no test holds says so with its exit code
date: 2026-08-18
status: open
coveredBy:
  - CliTest::anAppointmentComesUpOnlyWhileItsCommandFindsWork
  - ScenariosTest::aContractCaseNoTestHoldsSaysSoWithItsExitCode
---

# D-EVI-007 — A case no test holds says so with its exit code

**`bin/cli scenarios:contract` exits nonzero where the case it prints says
`not guarded`, so the todo reading those cases is due on their state rather than
on a list.**

The todo had the list, and both halves of it went wrong at once while nothing
failed.

## Evidence

- `read-the-contract-cases-no-test-can-hold` was last run on 2026-08-02 and read
  again on 2026-08-18, 16 days past a 14-day cadence. `bin/cli todo:list` marked
  it due for the last two of those days and `bin/cli todo:next` handed it over
  on none, because `TodoNext::perform()` reads a `bin/cli` command's exit code
  as whether there is work and `scenarios:contract` exited 0 whatever it
  printed. It is the only one of the five recurring todos whose `**Checked:**`
  had not moved since 2026-08-03; the other four run on a command that exits
  nonzero or on one this console does not own.
- The five cases it named were not the cases that needed reading. `SITE-01` had
  been guarded since `8a23def3` added
  `ScopeTest::decidingOneSitesConfigurationIsDeclinedInTheOrientation`, which is
  the boundary the todo described as unheld. Six cases had become unguarded and
  were named nowhere in it — `CORE-04`, `CORE-06`, `SKILL-11`, `SKILL-12`,
  `SKILL-13`, `SKILL-14` — of which three carry cards of their own in
  `todo/waiting/` and three carry nothing.

## Decided

- The exit code answers the question the cadence cannot: `not guarded` in the
  `Held by` statement is 1, everything else is 0, and a case that later gets a
  test stops asking to be read without anybody editing the todo. `D-FBK-012` is
  the mechanism this uses.
- `scenarios:show` keeps a 0 either way. A forward review claims its state on a
  recorded run, so the same question of it would be answered by
  `scenarios:check` rather than by whether a test exists.
- Named no case at all the command answers for every one of them, and that is
  what the todo runs. Rejected: naming one case in the `**Run:**` line and
  letting it stand for the rest. `SKILL-09` says in as many words that it
  measures the others, which made it the obvious sentinel and would have put the
  same failure one case further out — the todo goes quiet the day that one is
  guarded and the others are not.
- The todo names the criterion and the command that prints the cases instead of
  the cases. Rejected: rewriting the list against today's cases, which is what
  was there and what went stale, and would go stale again on the next case that
  changes state.
- `ScenarioReport::report()` returns nothing. It always returned 0, and a return
  value nobody varies read as though the exit code were the printer's to decide
  when it is each command's.

## Assumed

- That a case's `Held by` statement is true. It is written by hand and read as
  data here, so a case is owed a reading exactly as long as somebody says it is.

## Wrong if

- A case is given a test that does not hold what the case measures, and its line
  drops `not guarded` anyway. The exit turns 0 with the reading still owed, and
  nothing here can tell that from the case being held: the only thing read is
  the sentence. What would show it is a case that stopped being printed and a
  later session finding the behaviour gone.
- The reading the todo now asks for is more than a session does, so the date is
  written without the cases being read. That is the failure a list of five could
  not have — it was small enough to look done.
