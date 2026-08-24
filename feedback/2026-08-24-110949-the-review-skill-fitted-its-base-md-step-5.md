---
date: 2026-08-24T11:09:49+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# The review skill fitted; its base.md step 5 exemption is where I silently dropped a step

## Observation

Task: "review this before pushing" for a local core commit in EXT:impexp, one modified class and two new test files.

typo3-core-patch-review was the only skill I activated in the session, and it fitted. What it produced that I would not have produced on my own: establishing the four things a patch is (paths, target branch, message, issue) before judging any code, reading the Forge issue rather than the author's account of it in the commit message, asking typo3_gerrit_lookup with the Change-Id and reporting "not on the server" as a surface rather than as silence, the "what a dropped candidate owes" rule — which is what made me record that the XmlExports fixture location and the sorting assertions were disproved by ImportTest.php:179 and by importPagesAndRelatedTtContent.csv rather than just dropping them — and the instruction to name the suites I did not run, which turned an otherwise clean-looking verification into an honest one.

Where it did not survive contact, and this is mine as much as the skill's: base.md step 5, the changelog deprecation sweep. Its exemption reads "A task that produces no change does not reach this step at all... a triage, a reproduction and a review are illustrations of it", and it then says "A report names the step it did not reach, under either exemption". I took the exemption and did not name the step in the report. That instruction is the last sentence of a nine-paragraph step, after two other conditions, and it is the only one that has to survive into a document written half an hour later. It is also the one thing in base.md a reader of my report cannot detect is missing.

One thing I would keep verbatim: "a review that rewrites what it reviews has destroyed the evidence for its own findings", together with the boundary sentence about remarks on a finding's weight not being an instruction to change the patch. The user asked follow-up questions about my findings during the review ("I do not know whether --update-records is wrong here", "I believe this has nothing to do with our problem") and both were exactly the case that section warns about — a remark that reads like a handover and is not. Having read that section, I answered with a re-ranking and a withdrawal instead of editing the patch, and the withdrawal was correct: --update-records only engages where records with the same uid already exist, so it says nothing about the reported defect, and my rank-2 finding built on that sentence in the issue fell away.

## Query

Skill typo3-core-patch-review, invoked with "Review the local commit 933cce1716e on branch bugfix-impexp-import-page-translations before pushing to Gerrit (Resolves: #110524, Releases: main, 14.3)"; then its references/base.md and references/checklist.md read whole

## Suggestion

Lift "name the step you did not reach" out of the body of base.md step 5 into the report section of the skill, where the other reporting obligations stand — the surfaces table already asks for assessed/unassessed/not applicable per surface, and a skipped order step belongs in the same place a reader looks for it. As it stands the obligation lives in the step that is being skipped, which is the one paragraph a caller taking the exemption reads least carefully.
