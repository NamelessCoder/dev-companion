---
id: R-SKL-8
status: held
restsOn: [D-EVI-2]
---

# R-SKL-8 — A task skill does not run without the server it came from

**Every published task skill establishes that this server answers before it does
any of the work, and stops with that finding when it does not.**

A skill is a copy the installer wrote into somebody else's project. It loads
from disk, so the session reads its order, its rubric and its confidence whether
the tools behind it are connected or not, and nothing on either side reports the
difference. The failure is therefore silent by construction: what comes out is a
review in the skill's voice, built from general TYPO3 knowledge, that no reader
can tell apart from one with evidence under it.

Establishing it costs nothing — the first call of the order is the proof — and
the answer when it fails is the finding itself, not a fallback. Continuing is
allowed only after the session has said the server is missing and been asked to
go on anyway, and then the answer carries that sentence.

## From

Sessions that ran the installed skills against an unconnected server and
returned a full answer regardless, repeatedly and without either side noticing
(2026-07-31).

## Held by

- `SkillTest::theBaseStopsTheTaskWhenTheServerIsNotConnected`, which holds the
  precondition in `skills/base.md` and therefore in every published copy; that
  a session actually stops is not guarded, and will not be — see `D-EVI-2`.
