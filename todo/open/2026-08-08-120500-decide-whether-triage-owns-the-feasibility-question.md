# Decide whether triage owns the feasibility question it promises

**Serves:** feedback/2026-08-07-233431-typo3-core-issue-triage-promises-deciding.md
**Priority:** normal

`typo3-core-issue-triage`'s description claims "deciding whether a report is
worth taking on, and for saying what a maintainer would need before it can
move". Its body stops at the verdict and hands the rest to
`typo3-core-patch-development`, so there is no step between "still happens" and
"write the patch" — and the user's actual question was "what I would be signing
up for before I touch it".

The reporting session worked out a procedure and would work it out again: read
the revert reason out of the related issue, grep for production callers of the
method the reverted patch touched, and establish whether the path named in the
revert still routes through it. For Forge 15984 that was one caller outside its
own class, and the path that blew up in 2012 no longer touches the method at
all. It states the general form, which is the part that transfers: a reverted
core fix becomes re-attemptable when the shared consumer that made it expensive
has been rebuilt, or when the caller set has shrunk to the one site the fix
needs.

Two answers and neither is decided. Add the step, firing where an issue's
relations or notes show a merged-then-reverted fix — which
`todo/open/2026-08-08-090000` is about to make visible in the answer, so read
that first. Or narrow the description so it stops promising an assessment it has
no procedure for. A skill lands in somebody else's project, so this is the
expensive kind either way.
