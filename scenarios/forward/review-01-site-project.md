# REVIEW-01 — Review a TYPO3 site project

**Environment:** `E-SITE` · **Status today:** `partial`

> Review this TYPO3 project and its site package. Identify the most important
> concrete problems, risks, or missing safeguards, prioritize them, and tell me
> what should happen next. Do not change files.

**What has to come out of it**

- The review establishes the installation, project, site package, TYPO3 version,
  and available project-owned checks before drawing conclusions.
- Findings come from concrete checkout or installation evidence and name the
  affected location, consequence, confidence, and next action.
- Priorities follow user impact and failure risk rather than file count,
  stylistic preference, or the order in which files were opened.
- A subsystem is judged against the conventions that govern it, asked for before
  the judgment rather than after, and a subsystem present in the checkout but
  never asked about is reported as unassessed rather than left out.
- Confirmed defects, recommendations, missing evidence, and subjects outside
  this server's scope remain visibly distinct.
- The answer stops at a short prioritized review and routes follow-up work to
  the applicable workflow without starting unrelated implementation.

**How it fails**

- It returns an exhaustive generic TYPO3 checklist instead of reviewing this
  project.
- It invents missing features or requirements that neither the checkout nor the
  user asked for.
- It treats an incomplete parser or unavailable runtime source as proof that a
  feature is absent.
- It recommends core-only paths or commands for the project.
- It changes files during a review-only request.
