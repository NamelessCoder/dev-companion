# Name where the core's TypeScript style is decided

**Serves:** feedback/2026-08-13-215716-nothing-says-what-the-core-s-eslint-config.md
**Priority:** normal
**Branch:** todo/name-where-the-cores-typescript-style-is-decided
**Claimed:** 2026-08-14

Step 1a on the ladder, decided in `D-KNW-077`: the probe reaches
`backend-typescript` and nothing there says where the style is decided. Write
into `knowledge/hints/backend-ui.json`, on the `backend-typescript` entry, that
`Build/eslint.config.mjs` decides the style and
`CI=true ./Build/Scripts/runTests.sh -s lintTypescript` settles a question about
it, and that `member-ordering` is enforced while `no-explicit-any` and
`no-inferrable-types` are off — so a rule that is off is not imitated back into
a finding. The file and the three rules read the same on `12.4`, `13.4`, `14.3`
and `main` (`D-KNW-077`, Evidence), so no `since` is owed and the reading is
done. Copy no further rules across, run
`bin/cli knowledge:format knowledge/hints/backend-ui.json`, and archive the
feedback in the same commit.
