# A report-producing skill says the report is a file

**Serves:** feedback/2026-08-13-214811-the-review-skill-specifies-the-order-of.md
**Priority:** normal

Say in the report section of `typo3-core-patch-review`,
`typo3-extension-conformance` and `typo3-core-issue-triage` that the report is
written to a markdown file, and keep the answer to a short summary and that
path. Each says what the file is named — after the subject and whatever
separates two reports of it, the patch set for a review — and that it is written
outside the checkout being assessed, because the review's own checklist reports
what is untracked beside the patch as a finding. D-SKL-040 carries the reading
and what it left open; `documentation/contributing/writing-a-skill.rst` is what
the wording is held to, and part of the step is whether a `SkillTest` over a
named list can hold this the way
`aWorkflowThatEndsInPublicationStopsAtAVulnerability` holds `R-SKL-020`. The
requirement is written when the change lands, and that commit archives the
feedback.
