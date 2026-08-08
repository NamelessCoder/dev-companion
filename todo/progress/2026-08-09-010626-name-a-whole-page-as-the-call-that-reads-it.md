# Name a whole page as the call that reads it

**Serves:** feedback/2026-08-08-224406-the-guides-are-reachable-only-through-project.md
**Priority:** normal
**Branch:** todo/name-a-whole-page-as-the-call-that-reads-it
**Claimed:** 2026-08-08

Judged as wording rather than a gap — `D-ANS-070`. The two steps that tell a
session to read a procedure once rather than a section at a time hand it a
`typo3://guides` address, which is delivery to a client that renders a resource
list and to no other (`D-ANS-061`). Rewrite them as the call:
`skills/typo3-core-patch-development/SKILL.md:177` for
`core/contribution/commit-messages`, `:182` for
`core/contribution/gerrit-workflow`, and
`skills/typo3-core-patch-checkout/SKILL.md:37` for the second. The shape is
already in that first file at `:118`, where the security procedure is named as
`documentId="any/security/reporting-a-vulnerability"` and the uri stands beside
it.

`documentation/clients/writing-a-skill.md:214` is what those two follow and
moves with them: it says to name the `typo3://guides` resource, and it was
written on 2026-08-04, three days before `documentId` existed.

Then the assertion, next to
`SkillTest::everyResourceASkillNamesIsOneTheServerServes`, which holds a uri in
a published skill to a document this server serves and not to being reachable:
every `typo3://guides/<id>` a skill names is also named as that `documentId`. A
skill is installed into somebody else's project, where no release of this server
corrects it.

**Run:** `bin/cli todo:next`

## What this does not touch

The answer side, where a search that matched two of nine sections says it cut a
page and not how much of it it left. That half is
`todo/open/2026-08-09-010627-say-what-a-cut-answer-left-of-the-page.md`.
