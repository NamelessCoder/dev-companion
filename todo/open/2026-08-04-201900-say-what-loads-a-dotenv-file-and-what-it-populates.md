# Say what loads a `.env` file and what it populates

**Serves:** feedback/2026-08-04-175953-task-fix-an-audit-finding-that-readme-md.md, knowledge/hints/
**Priority:** normal

Step 1a on the observation, not on the category: `environment-variables` states
that TYPO3 ships no `.env` reader and that whatever loads one is the project's
choice, and stops there — so a session that had just fixed a readme documenting
variables nothing read walked past a `.gitignore` advertising a `.env` nothing
loads, the same failure one layer down. Verify the mechanics against an
installed `symfony/dotenv` rather than against the report: what `loadEnv()`
populates, what `overrideExistingVars` decides, and that `usePutenv()` is what
makes `getenv()` — the form this hint's own example uses — see anything at all.
Check on which majors the package's behaviour differs, then extend the hint with
what a project owes a `.env` it has committed to.
`knowledge/documents/project/testing/playwright.md` already carries the sibling
reading for the suite's own loader; keep the two saying the same thing about
precedence.
