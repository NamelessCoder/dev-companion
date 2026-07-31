---
id: D-SCO-6
date: 2026-07-29
status: standing
---

# D-SCO-6 — Why "project work is out of scope" kept coming back

**All three prose sources now say what is actually true about who this server
answers for, and the contradiction is held shut by a test rather than by
wording.**

It has been settled several times that the extension author and the site
developer are audiences of this server, and the claim that they are not kept
reappearing. Where it came from, found by reading every place that states a
boundary:

- **Evidence:** the four places that state one.
  - `knowledge/server-scope.json` carried it as a `doesNotCover` entry — "The
    knowledge base is scoped to contributing to the core" — and that entry is
    the canonical text anyone writing about scope copies from.
  - `knowledge/typo3-core-architecture.md` opened its TypoScript section with
    "Configuring an installation with TypoScript is out of this server's scope".
    It is a knowledge document, so the sentence was also handed to callers.
  - `Scope::OUTSIDE_CORE_NOTICE` said the server "only knows the core's own
    conventions". Every tool opens with it, so the framing was reinforced in the
    answers themselves.
  - `requirements/` says the opposite in R-AUD-1 and R-AUD-2 and flags the
    conflict — but R-AUD-2 is **open**, and nothing decided which side wins.
- **Decided:** all three prose sources now say what is actually true, and the
  contradiction is guarded: `ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers`
  fails if the declared scope excludes a subject that has a hint.
- **Assumed:** the remaining pull is the name `outsideCore`. It is a scope word
  for what is really a payload rule — "leave out what only the core repository
  has" — and each session that touches it re-derives a scope sentence from the
  name.
- **Wrong if:** the claim reappears anyway, in a place the guard does not read —
  a tool description, the readme, a hint. Then the flag has to be renamed to
  what it decides (`coreRepositoryOnly`, or the audience of R-AUD-2), rather
  than the sentences being corrected one at a time.
