---
id: R-KNW-068
title: 'A suite that waits for a keypress says it needs a terminal'
status: held
restsOn: [D-KNW-068]
heldBy:
  - KnowledgeTest::aSuiteThatWaitsForAKeypressSaysItNeedsATerminal
---

# R-KNW-068 — A suite that waits for a keypress says it needs a terminal

**A suite whose script waits on a read from `/dev/tty` says so, and says that a
run with no terminal reports SUCCESS having torn down what it installed.**

`runPlaywright()` ends in `read ... </dev/tty`. That is not a container flag, so
`CI=true` does not remove it: without a controlling terminal the redirect fails,
the wait ends at once, the cleanup removes the instance the suite exists to
leave standing, and the exit code is still the one from before the wait. The
banner and the exit code then both say the opposite of what happened, and the
URL printed above them is dead by the time it is read.

The entry says in as many words that `CI=true` does not stand in for a terminal,
and the note declaring `CI=true` says the same from its side. That note is
written for scripted and non-interactive runs, so a session that has set it has
already answered the question the suite is about to fail on, and reads the
failure as something it did wrong.

A false green is the failure a reading session cannot see — the same shape
[`R-KNW-049`](knw-049-a-check-that-can-pass-without-reading-anything-says-so.md)
holds `cglGit` to, and the reason the condition sits in the entry that offers
the command rather than one entry away.

What tears the instance down is named rather than only that it happened. The
cleanup at the end of every run kills the containers on that run's network, so
the outcome holds for a run that reaches that cleanup and for no other. A run
killed earlier — a failing wait, a terminated wrapper — leaves `ac-web-<suffix>`
and `ac-phpfpm-<suffix>` up with the instance still served, and a session
reading the outcome as unconditional reports a working instance as gone.

The invocation notes carry both ways out, because a session that needs a
standing instance has no other. The way through is a terminal allocated with
util-linux `script`, and stdin from something that stays open and never writes.
The way back is the container runtime: what a run left behind is read from
`docker ps` rather than from its exit code, and stopped by name.

## From

A core patch review that ran the prepare suite from a background shell with
`CI=true` set, lost the instance to the failing redirect after several minutes
of composer work, and worked the pty out for itself, one failed attempt and a
2.2 MB log of NUL bytes on the way — `feedback/2026-08-13-214729` (2026-08-13).
The read was verified in `runTests.sh` on `.checkouts/main`, `14.3` and `13.4`.

A headless session taking backend screenshots then hit the other half: its run
ended before the banner, both containers were still serving, and it nearly
reported the instance gone on the strength of this entry's own sentence —
`feedback/2026-08-24-225044` (2026-08-24). `cleanUp()` at the end of the script
is what removes them, on the same three checkouts.
