# A report-producing skill says the report is copyable markdown

**Serves:** feedback/2026-08-13-214811-the-review-skill-specifies-the-order-of.md
**Priority:** normal
**Branch:** todo/a-report-producing-skill-says-the-report-is
**Claimed:** 2026-08-13

Say in the report section of `typo3-core-patch-review`,
`typo3-extension-conformance` and `typo3-core-issue-triage` that the report is
markdown the reader can copy, and that the answer is where it goes. A path is
what the caller asks for rather than what the skill prescribes; where one is
asked for, it is outside the checkout being assessed, because the review's own
checklist reports what is untracked beside the patch as a finding. `D-SKL-042`
carries the reading and the maintainer's answer it rests on, and revokes
`D-SKL-040`, which read the file one session wrote as the requirement rather
than as one way of meeting it.

`documentation/contributing/writing-a-skill.rst` is what the wording is held to,
and part of the step is whether a `SkillTest` over a named list can hold this
the way `aWorkflowThatEndsInPublicationStopsAtAVulnerability` holds `R-SKL-020`.
The requirement is written when the change lands, and that commit archives the
feedback.
