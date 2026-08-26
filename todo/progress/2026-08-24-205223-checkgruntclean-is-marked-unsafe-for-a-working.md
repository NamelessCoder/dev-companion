# Name the way to checkGruntClean's answer that leaves the checkout alone

**Serves:** feedback/2026-08-24-205223-checkgruntclean-is-marked-unsafe-for-a-working.md, feedback/2026-08-25-110635-backporting-a-diff-that-touches-build-sources.md, D-ANS-113
**Priority:** normal
**Branch:** todo/checkgruntclean-is-marked-unsafe-for-a-working
**Claimed:** 2026-08-26

Judged on 2026-08-26 as the ladder's step 1a and written up in `D-ANS-113`: the
`runs: git` warning on `checkGruntClean` is the last thing any answer says about
whether the committed JavaScript still matches its TypeScript source, and three
sessions each invented the same replacement. The half about PHP was settled in
the same commit and trimmed off the feedback.

The work is a `knowledge/documents/` page on the core's committed frontend build
output: how a change to it is verified against its source without a build, how
one is rebuilt in a throwaway worktree with `-s build`, and what a backport does
to it — neither conflict side resolves a minified file, because the identifier
mangling is a property of the whole module. Name it from the `checkGruntClean`
and `build` entries in `knowledge/test-suite-hints.json`, so a caller warned off
the suite reaches it, and from `core/contribution/gerrit-workflow`, whose
"Release Branches and Backports" section is where `feedback/2026-08-25-110635`
looked.

First step, and the one thing the judgement could not establish: verify in
`.checkouts/` that `-s build` from a bare worktree reproduces the branch's
committed output. That is `D-ANS-113`'s second **Assumed** and rests on one
reported run on 14.3. Where it does not hold, the procedure is a throwaway clone
and the page says so.
