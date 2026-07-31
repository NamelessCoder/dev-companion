# SKILL-07 — Build a backend module, then document the extension

**Environment:** `E-SITE`, in the project's site package ·
**Contract:** `open` — `R-SKL-3`
**Held by:**
`SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition`,
`SkillTest::theBaseFixesTheOrderEveryTaskStartsIn`,
`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`;
that a session actually hands over at that point is not guarded, and this case
read by hand is what stands in for it (`D-EVI-2`)

> Add a backend module for reviewing imported records, including the module
> shell, status list and refresh action. Once it works, document the public
> workflow for the extension's maintainers and editors in the right place.

**What has to come out of it**

- The backend-module workflow establishes project and extension scope and owns
  the implementation through its verification.
- Once implementation is verified, that workflow stops and the documentation
  workflow activates before documentation files are edited.
- The verified extension key, target TYPO3 version and public behavior cross the
  boundary; implementation detail that is not public does not.
- Documentation is updated in the site package's existing README/manual
  structure, not in the project-level README around it.

**How it fails**

- The backend-module skill remains the only active skill and edits
  documentation itself.
- Both workflows run as one undifferentiated checklist with no observable
  ownership boundary.
- Extension functionality is documented at project level or internal controller
  details are presented as stable public behavior.
