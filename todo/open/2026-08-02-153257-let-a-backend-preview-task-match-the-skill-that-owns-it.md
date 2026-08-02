# Let a backend-preview task match the skill that owns it

**Serves:** feedback/2026-08-01-002926-debrief-of-a-typo3-14-backend-content-element.md
**Priority:** normal

Ladder step 3, the trigger, on the evidence in the **Since then** of
[`D-AUD-003`](../../decisions/audience/aud-003-the-instructions-carry-the-entry-point.md):
`typo3-content-element-development` describes itself as "frontend content
elements" and reaches `previews` ninth of eleven, so a backend preview matched
neither it nor `typo3-backend-module-development`, whose "backend UI work" means
a module. Rewrite the `description` so a custom backend preview for a CType
matches it, without it reading as the backend-module skill — the words a user
brings, per `documentation/clients/writing-a-skill.md`, and the same file's rule
that the description is the only part read before the skill is chosen. Read the
other six descriptions for the same shape in that pass, one that names a single
side of a domain it owns both sides of; nothing has been checked against that
yet. Leave the skill body alone: its one preview line can only gain a route once
`feedback/2026-08-01-002745-task-show-assigned-related-groups-in-a-typo3-14.md`
and `feedback/2026-08-01-002930-debrief-of-a-typo3-14-backend-preview-task-the.md`
have established what a preview template receives on 14 and what the default
renderer already draws, and both of those are being worked. The commit that
lands this is where the requirement belongs, and `R-SKL-001` is the demand one
skill over.
