---
id: D-EVI-007
date: 2026-08-18
status: open
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
- The todo names the criterion and the command that enumerates the cases instead
  of the cases. Rejected: rewriting the list against today's cases, which is
  what was there and what went stale, and would go stale again on the next case
  that changes state.
- `ScenarioReport::report()` returns nothing. It always returned 0, and a return
  value nobody varies read as though the exit code were the printer's to decide
  when it is each command's.

## Assumed

- That `SKILL-09` stays unguarded for as long as any of them is. The todo's
  `**Run:**` names one case, so due-ness is that case's answer standing in for
  all of them, and the case says in as many words that it measures the rest. It
  is the one thing here that a list would not have got wrong.

## Wrong if

- `SKILL-09` is guarded while another case still says `not guarded`, and the
  todo goes quiet again with work left in it. What would show it is a
  `grep -rl 'not guarded' scenarios/contracts/` that finds cases on a day
  `bin/cli scenarios:contract SKILL-09` exits 0 — the same failure as the one
  above, one sentinel later.
- A case is guarded by a test that does not hold what the case measures, so the
  exit turns 0 while the reading is still owed. The exit code reads the
  `Held by` statement, and that statement is written by hand.

## Covered by

- `ScenariosTest::aContractCaseNoTestHoldsSaysSoWithItsExitCode`
- `CliTest::anAppointmentComesUpOnlyWhileItsCommandFindsWork`
