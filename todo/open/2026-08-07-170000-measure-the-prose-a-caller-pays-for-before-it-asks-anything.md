# Measure the prose a caller pays for before it asks anything

**Serves:** R-COD-002
**Priority:** low

`bin/cli prose:check` reads `AGENTS.md`, `readme.md` and the markdown below
seven directories, and reaches no file in `src/` — so the half a caller actually
pays for is the half nothing counts. The tool descriptions, the schema field
texts and the `instructions` sent at initialize measured 118202 characters at
connect on 2026-08-03, of which 14502 were the 26 descriptions and 11507 the
input schema fields, and the 1219 characters cut then were found by somebody
reading rather than by a report. Point the check at the payload the registry
declares: same measure — one point per sentence, no sentence restating the one
above it — over the strings `Registry` hands a client, so a description that
grows back is caught by a command instead of by whoever happens to reread it.
Print the total beside the finding, because the number is what says whether the
rule is worth the run. Left at `low` deliberately and not because nobody looked:
what is queued above it are answers that reached callers and were wrong, while
this is a measure that does not exist yet, and nothing is broken while it waits.
