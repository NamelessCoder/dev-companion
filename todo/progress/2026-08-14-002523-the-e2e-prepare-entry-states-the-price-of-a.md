# The e2e-prepare entry states the price of a Playwright-only diff

**Serves:** feedback/2026-08-13-214708-the-e2e-prepare-description-talks-a-session.md
**Priority:** normal
**Branch:** todo/the-e2e-prepare-entry-states-the-price-of-a
**Claimed:** 2026-08-13

Judged step 4, wording, against `D-KNW-068`, whose **Since then** carries the
reading. Rewrite the `e2e-prepare` `whenToUse` in
`knowledge/test-suite-hints.json` so what the suite is for is looking at a
change in a browser, and so the sentence beside the command says the price
instead of a way round it: neither `-s e2e` nor `-s e2e-prepare` takes a test
path or a filter, so a Playwright-only change costs the whole suite and that run
is the reportable one — every e2e case builds `COMMAND` from
`PLAYWRIGHT_PROJECT` alone and reaches no `"$@"` on `.checkouts/main`, `14.3`
and `13.4`. Where the printed local commands stay in the entry, say what they
are worth and what they need, because a run outside the project's runner is what
`skills/typo3-core-patch-review/SKILL.md` calls evidence about your machine:
they are `npm --prefix=Build run playwright:run -- --project e2e`,
`Build/package.json` carries `playwright:install` separately, and the container
path runs `IMAGE_PLAYWRIGHT` — so a local run needs browsers the host may not
have, which is what cost the reporting session its quarter hour. That last
statement was read on `main` only, so read it on `13.4` and `14.3` before
binding it. Then write the requirement it holds to, and archive the feedback in
the same commit. `feedback/2026-08-13-214729` is unjudged and lands on the same
`whenToUse`, so whoever takes that on rewrites this sentence again — do both in
one pass rather than twice.
