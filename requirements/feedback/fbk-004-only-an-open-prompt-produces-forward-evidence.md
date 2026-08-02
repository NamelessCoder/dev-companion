---
id: R-FBK-004
status: held
---

# R-FBK-004 — Only an open prompt produces forward evidence

**Only a prompt that names no subsystem, skill, tool, expected defect or
implementation shape produces forward evidence.**

Those are the open forward reviews in `scenarios/forward/`, one per working
context, and they are the only cases a run can be recorded for. A case that
names its own task shape lives in `scenarios/contracts/`, carries a contract
state instead of a mark, and is printed for inspection rather than run. Either
kind is one file with one prompt, so a judgment cannot be about a prompt nobody
can identify, and the environment a prompt names is a kind of working directory
rather than one installation on somebody's machine — which checkout plays it
belongs in `todo/reference/`. A contract state is settled by no run, so every case
names the tests that hold it, or that something is not guarded, and a test it
names has to exist.

## From

A suite whose prompts prescribed the feature, subsystem and often the
implementation shape they were meant to discover, and whose site prompts named
one person's project (2026-07-31).

## Held by

- `ScenariosTest::everyCaseHasAFileOfItsOwn`
- `ScenariosTest::aTargetedContractCaseIsNotSomethingARunCanAnswer`
- `ScenariosTest::everyContractCaseNamesWhatHoldsIt`; that a prompt stays free
- Of a named installation is not guarded.
