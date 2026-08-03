# Search the issue tracker by words, and not only by number

**Serves:** feedback/2026-08-02-144511-find-existing-patches-then-write-a-fix-three-of.md, feedback/2026-08-02-145217-task-assess-forge-105403-recording-how-forge.md
**Priority:** normal
**Branch:** todo/search-the-issue-tracker-by-words-and-not-only-by-number
**Claimed:** 2026-08-03

Judged as
[`D-ANS-038`](../../decisions/answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md),
step 1b of the ladder: two sessions searched the tracker by hand for the issues
nobody had linked, and `typo3_forge_lookup` answers one issue by number. Give it
a `query` argument exclusive with `issue`, the shape `typo3_gerrit_lookup`
already has for `issue` and `change`, reading
`/search.json?q=<query>&issues=1&limit=<limit>` through `Http\Fetch` in
`src/Contribution/Forge.php`. One entry per hit carrying the issue number,
subject, tracker, status and URL: `results[].title` arrives as
`Bug #105403 (Under Review): f:image and cache busting issue`, so tracker and
status are readable there rather than in a second call per hit, and the answer
says which query produced it because one wording does not settle the question.
The three-state answer, the `)]}'`-free decode and the plain-agent retry are
`Http\Fetch`'s already. Then `knowledge/server-scope.json` — `covers` and the
routing line for taking an issue on — `bin/cli tools:index` and
`bin/cli tools:record`, and archive both feedback in the commit that ships it.
