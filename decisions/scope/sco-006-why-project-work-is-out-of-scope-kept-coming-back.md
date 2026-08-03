---
id: D-SCO-006
date: 2026-07-29
status: open
---

# D-SCO-006 — Why "project work is out of scope" kept coming back

**All three prose sources now say what is actually true about who this server
answers for, and the contradiction is held shut by a test rather than by
wording.**

It has been settled several times that the extension author and the site
developer are audiences of this server, and the claim that they are not kept
reappearing. Where it came from, found by reading every place that states a
boundary:

- **Since then** the three places **Wrong if** names are read too, by
  `ScopeTest::noSurfaceSaysTheCoreIsTheOnlyWorkThisServerAnswersFor`. Its corpus
  is every tool description, `readme.md`, every architecture hint, and the
  scope's own purpose, routing and instructions. It matches the claim in both
  the wordings it was found in. One names who it turns away — "out of this
  server's scope" beside an extension, a project, an installation. The other
  names nobody and confines the server instead, which is what "scoped to
  contributing to the core" and "only knows the core's own conventions" were.
  All three surfaces were already clean on 2026-08-01, so the guard holds a
  boundary rather than reporting a breach.

  What it matches is wording, and that is the weakest thing there is to match.
  So the three sentences recorded above are run through the matcher first, and
  one that has stopped recognising them fails with them rather than passing
  everywhere.

  What stays out of reach is narrower than **Wrong if** was. A wording neither
  form catches is still unguarded. So are the two surfaces left out on purpose —
  the knowledge documents and the skills, where "this holds for the core
  repository" is the true sentence and a guard could only make it harder to
  write. The flag rename is what an occurrence in either asks for, and it is
  unchanged as the next step.

## Evidence

- The four places that state one. - `knowledge/server-scope.json` carried it as
  a `doesNotCover` entry — "The knowledge base is scoped to contributing to the
  core" — and that entry is the canonical text anyone writing about scope copies
  from. - `knowledge/typo3-core-architecture.md` opened its TypoScript section
  with "Configuring an installation with TypoScript is out of this server's
  scope". It is a knowledge document, so the sentence was also handed to
  callers. - `Scope::OUTSIDE_CORE_NOTICE` said the server "only knows the core's
  own conventions". Every tool opens with it, so the framing was reinforced in
  the answers themselves. - `requirements/` says the opposite in R-AUD-001 and
  R-AUD-002 and flags the conflict — but R-AUD-002 is **open**, and nothing
  decided which side wins.

## Decided

- All three prose sources now say what is actually true, and the contradiction
  is guarded: `ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers` fails
  if the declared scope excludes a subject that has a hint.

## Assumed

- The remaining pull is the name `outsideCore`. It is a scope word for what is
  really a payload rule — "leave out what only the core repository has" — and
  each session that touches it re-derives a scope sentence from the name.

## Wrong if

- The claim reappears anyway, in a place the guard does not read — a tool
  description, the readme, a hint. Then the flag has to be renamed to what it
  decides (`coreRepositoryOnly`, or the audience of R-AUD-002), rather than the
  sentences being corrected one at a time.

## Since then

The **Assumed** was right and the pull was the name. On 2026-08-02 `outsideCore`
was removed rather than renamed: one enum, `Knowledge\Scope`, says which kind of
work an answer is for, and there is no longer a field whose only content is what
the work is not
([`D-KNW-005`](../knowledge/knw-005-one-scope-replaced-the-four-vocabularies.md)).
