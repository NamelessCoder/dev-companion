# Installed task skills

These scenarios exercise the task skills published with the server. Run them in
the Printworks TYPO3 site project without mentioning a skill, tool, checklist,
or intended answer. See [readme.md](readme.md) for the general procedure.

---

## SKILL-01 — Find the missing tests before adding them

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Review the Printworks sitepackage's test coverage. Tell me which important
> behaviors are not protected, choose the right test layer for each gap, and
> give me the exact commands we should run. Do not change files yet.

**What has to come out of it**

- The answer inspects production code, nearby tests, configuration, and declared
  project commands before recommending work.
- Each proposed test names a concrete unprotected behavior and explains why
  unit, functional, browser, or architecture coverage is the lowest useful
  layer.
- Commands exist in the checkout or are declared by the project; no TYPO3 core
  `runTests.sh` suite is offered.
- Missing infrastructure and commands not executed are clearly marked.

**How it fails**

- A generic testing pyramid without file-level evidence.
- Invented PHPUnit paths, Composer scripts, or core-only commands.
- Treating a file-presence assertion as proof of runtime behavior.

---

## SKILL-02 — Audit the sitepackage for TYPO3 conformance

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Audit the Printworks sitepackage for TYPO3 conformance and upgrade readiness.
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

---

## SKILL-03 — Make the documentation match the package

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Review and improve the Printworks sitepackage documentation for integrators
> and editors. Make it match what the package really implements, remove unsafe
> example data, and stop once the requested workflows are verifiable.

**What has to come out of it**

- Integrator and editor material are separated and placed in the repository's
  existing README/manual structure.
- Keys, defaults, registrations, paths, and examples come from the checkout;
  external TYPO3 claims use versioned official evidence.
- Credentials, personal data, internal hosts, and machine-specific paths are
  removed or replaced with named placeholders.
- A dynamic-registration parser miss is described as a tooling limitation, not
  as proof that the feature does not exist.
- The work stops at the completion gate and reports validations and remaining
  unknowns rather than continuing an unbounded survey.

**How it fails**

- Copying local passwords or personal addresses into polished examples.
- Replacing checkout truth with incomplete parsed installation output.
- Creating a competing documentation structure or promising internal details as
  stable API.
