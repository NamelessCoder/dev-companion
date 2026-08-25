---
id: D-SKL-077
title: The triage intent matches taking an issue off the tracker
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::takingAnIssueOffTheTrackerReachesTheTriageSkill
---

# D-SKL-077 — The triage intent matches taking an issue off the tracker

**`triage` takes "from forge" and "off forge" as strong needles rather than the
bare name, because a brief naming one issue somebody already chose is patch
work.**

`forge` was a weak needle, and a weak match names no skill, so the brief the
feedback asked to be re-run — "fetch another old issue from Forge, create a
branch, work it off" — was answered with `skills: []`.

## Evidence

- The other gate failed first. `Scope::isCoreWork()` matched each of its markers
  with `str_contains`, which made every marker carry its own boundary: the
  tracker was written `'forge '`, and the trailing space that keeps "forget" out
  is where the comma of "from Forge," sits. Read with `Text::containsWord()`,
  which the intent matcher beside it already uses, the brief reads as `core` and
  "I forgot to run the tests before pushing" still does not.
- Measured on 2026-08-25 over twenty-one briefs, with both options applied to
  the same list. Bare `forge` in `match` routed all six briefs that take work
  off the tracker, and five more with it: "report a bug on Forge", "file an
  issue in Forge" and "write a new forge issue" — `reporting`'s job, which no
  published skill owns — were answered with `typo3-core-issue-triage`, and "fix
  Forge 15984 in the FormEngine" and "implement what Forge 98765 asks for" were
  composed as briefs that change nothing, without the deprecation sweep, the
  test coverage or the commit message step.
- "from forge" and "off forge" moved the six and nothing else on that list. The
  three filing briefs kept `reporting` alone, and the two patch briefs kept the
  checklist a patch owes while `forge` still matched them weakly, which is the
  "Possibly also" line and the conditional items.

## Decided

- The needles name the act rather than the tracker. A contributor writes "from
  Forge" and "off Forge" for taking one, and "on Forge" and "in Forge" for
  filing one, so the preposition is what separates the two jobs in the words
  people actually use.
- `forge` stays in `matchWeak`. A brief that names the tracker and nothing else
  may be a triage, and saying so conditionally is what a weak match is for.
- `Scope::isCoreWork()` reads every marker as the word it is, not the tracker's
  alone. A boundary written into one needle is one the next needle will be
  missing.

## Assumed

- The preposition holds outside this list. It was read off twenty-one briefs
  written for the measurement and off the one the feedback carried, not off a
  corpus of real requests, which this repository does not have.
- Routing a filing brief to the triage skill is a cost rather than a bonus. The
  triage workflow is about the backlog, and nobody has reported reaching it from
  a report they were writing.

## Wrong if

- A session asks for a triage in words that name the tracker without taking
  anything off it — "look at Forge 15984 and tell me whether it still stands" —
  and is answered with no skill. That would say the direction is the wrong
  discriminator and the vocabulary needs the number form as well.
- A brief carrying "from forge" turns out to be ordinary patch work often enough
  to be reported: reading an attachment, quoting the reproduction steps.
- `forge.typo3.org` keeps routing filing briefs to the triage skill and that is
  reported. It is the same collision this entry declined to widen, and it is
  untouched here: "open an issue on forge.typo3.org for this crash" answers
  `typo3-core-issue-triage` before this change and after it.
