---
id: R-SKL-028
title: A widened request re-establishes what the patch is and what it owes
status: held
restsOn: [D-SKL-079]
heldBy:
  - SkillTest::aWidenedRequestReEstablishesWhatThePatchIsAndWhatItOwes
---

# R-SKL-028 — A widened request re-establishes what the patch is and what it owes

**Where the request widens after the patch is under way, the session
re-establishes what kind of change this is, which branches it reaches and what
it owes.**

Each of the three was settled against the narrower request, and each can flip on
a widening. A change that gains a second subsystem gains that subsystem's build,
its checks and its backport constraint with it, and none of that is re-derived
by carrying on.

Saying which of the three moved is what separates the re-establishing from a
repetition. A widening that moves none of them is an answer too, and it is the
one that lets the work continue.

This is [R-SKL-027](skl-027-a-core-patch-covers-every-point-its-issue-lists.md)
in the other direction. That one holds the patch to the list the issue already
carries; this one is for the list the request grows after the assessment is
over.

## From

`feedback/2026-08-24-225243` (2026-08-24), a session on Forge #93177 whose task
grew eight times, twelve of its twenty user turns arriving mid-tool-call. It
re-derived its own scope four times — one patch or two, which release lines,
whether the client was in it, whether an entry was owed — and threw away two
rounds of client-side work and a changelog entry it had written and matched
against precedent. Both rules that would have carried it were in the skill and
both were written for the assessment.
