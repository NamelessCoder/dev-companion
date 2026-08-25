# I never asked where PageRenderer state is filled and cleared in the FE pipeline, and it was the w...

**Serves:** feedback/2026-08-24-183609-i-never-asked-where-pagerenderer-state-is.md, D-KNW-124
**Priority:** normal

Judged step 1a in `D-KNW-124`, which holds what was already read so it is not
read again: the vocabulary is absent, the path-scoped probe reaches the hint
that applies to every core file, and `bodyContent` is cleared in
`renderPageWithUncachedObjects()` on 14.3 and `main` and in `reset()` alone on
13.4. Write the hint `frontend-render-pipeline-state` into
`knowledge/hints/page-rendering.json`, matched by
`Classes/Page/PageRenderer.php` and `frontend/Classes/Http/RequestHandler.php`
beside the words the entry names, enumerating the phases and the `reset()`
asymmetries against all four checkouts and binding what does not hold on all of
them; then add the fixture trap to `core-tests` in
`knowledge/hints/testing.json`.
