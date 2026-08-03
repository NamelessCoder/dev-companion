# SKILL-02 — Audit the site package for TYPO3 conformance

**Environment:** `E-SITE`, in the project's site package · **Contract:** `held`
**Held by:**
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`,
`SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`,
`ScopeTest::aHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks`

> Audit our site package for TYPO3 conformance and upgrade readiness.
> Prioritize real findings with evidence and severity. This is a review only;
> do not fix anything.

**What has to come out of it**

- The audit covers only subsystems evidenced by the checkout and active
  installation, including output-context escaping and security boundaries.
- Every finding names a concrete location, evidence, consequence, severity,
  remediation, and relevant project check.
- Parser gaps for dynamic PHP registration are not reported as absent features;
  checkout evidence wins for what the package implements.
- The answer stops after findings and distinguishes recommendations and missing
  evidence from verified violations.

**How it fails**

- Severity based on style preference or file count rather than consequence.
- Optional subsystems reported as missing defects.
- The review silently starts changing tests, documentation, or backend code.
