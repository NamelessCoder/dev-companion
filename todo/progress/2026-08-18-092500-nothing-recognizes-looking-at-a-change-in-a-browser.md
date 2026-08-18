# Nothing recognizes looking at a change in a browser, so the two pages written for it are named by no brief

**Serves:** D-GUI-012
**Priority:** normal
**Branch:** todo/nothing-recognizes-looking-at-a-change-in-a-browser
**Claimed:** 2026-08-18

Measured on 2026-08-18 while working `D-GUI-012`: "prove a rendering change in
the browser after fixing a frontend crash" matches no intent in
`knowledge/task-intents.json` at all, so a brief for it recognizes nothing,
names no skill and now names no guide either. The two pages written for exactly
that work — `any/testing/browser-check`, which is looking rather than asserting,
and `core/testing/proving-a-rendering`, which is the throwaway functional test
that prints what a snippet renders — are reachable from no brief, and one of
them is half of what `feedback/2026-08-18-074226` reported.

`browser-tests` is the nearest intent and is not it: its needles are
`playwright`, `e2e`, `spec.ts` and `acceptance test`, and its work is writing a
suite. Looking at a change is the step before a spec, which is what
`browser-check` opens by saying.

The step is to establish whether that work is an intent of its own — what its
needles are, whether it changes nothing, which skill owns it on each side, and
which of the two pages it names as its guide per scope — or whether it is
`browser-tests` widened, which would have that intent's checklist and checks
apply to a session that is only looking. Read against what `D-SKL-013` says
about the mapping growing to fill the table: an intent invented for a page is a
route into work nobody asked for.
