# SKILL-03 — Make the documentation match the package

**Environment:** `E-SITE`, in the project's site package · **Contract:** `held`
**Held by:**
`SkillTest::extensionSkillsRouteThroughTheirPrimaryEvidenceSourcesInOrder`,
`SkillTest::everySkillStatesWhatItOwns`,
`SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns`

> Review and improve our site package's documentation for integrators and
> editors. Make it match what the package really implements, remove unsafe
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
