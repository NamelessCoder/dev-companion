# META-04 — The client shows only the data

**Environment:** `E-SITE`, with a client that renders `structuredContent` and
drops the text block · **Contract:** `held` — `R-ANS-002`
**Held by:** `ScopeTest::theInstallationDiagnosticIsDataRatherThanProse`,
`ScopeTest::anUnanswerableLookupCarriesItsReasonInTheData`,
`LabelSearchTest::whatEachWordReachesOnItsOwnIsInTheAnswerRatherThanOnlyInTheText`

> Same task as `SITE-03`, run in that client.

**What has to come out of it**

- Everything the agent needs in order to act is in the structured data: whether
  a lookup was answered by the installation or by nothing, the reason where it
  was not, the installation and console diagnostic, the coverage and source of a
  match.
- The session reaches the same conclusion it reaches with the text visible.

**How it fails**

- A conclusion that only holds because a sentence in the text block was read —
  and therefore silently inverts in this client (`R-ANS-002`).
