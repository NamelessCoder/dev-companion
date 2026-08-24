---
id: D-SKL-064
title: The audit and the work that answers it are one skill
date: 2026-08-19
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
  - SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange
---

# D-SKL-064 — The audit and the work that answers it are one skill

**`typo3-extension-cleanup` and `typo3-extension-conformance` are one workflow,
published as `typo3-extension-health`, and the listing ratchet moves with it
because no fixed total can absorb a thirteenth skill.**

Two skills waited on publication with their reviews given, and the room for
their descriptions was what stopped both. The merge is what the maintainer chose
over raising the ratchet alone, and raising it is what the merge turned out not
to save.

## Evidence

- The arithmetic, measured on 2026-08-19. The twelve published descriptions cost
  3570 characters against the ceiling of 3600. Two step clauses `D-SKL-054`
  priced came out — `typo3-core-patch-development` and `typo3-core-issue-triage`
  — for 162, and the new review skill's own list of its five section headings
  for 44, which left the twelve at 3408 and the entry at 350. Publishing one
  skill put the listing at 3759 and both at 3966.
- `typo3-core-patch-checkout` was left alone deliberately, though it carries the
  third clause. `D-SKL-026`'s **Since then** records a session that stopped
  activating it the last time that clause was cut, and the requests it lists are
  what went back in.
- **The gap is structural rather than a wording.** A ratchet set to what twelve
  descriptions cost forbids a thirteenth at any length, and the merge frees 232
  of the 350 one costs. Merging one pair buys one publication and leaves the
  next skill at the same wall.
- The two bodies were already a cycle. The cleanup skill's step 2 invoked the
  audit and its step 13 invoked it again for the re-check, and the audit named
  the cleanup as where the fixes go. What the merge removes is two activations
  of the same surface list per task.
- `D-SKL-014`'s last bullet is what the merge crosses: a workflow that ends in a
  review changes nothing and commits nothing. The merged skill does both, in
  that order, and the gate between them is the report.

## Decided

- One skill, `typo3-extension-health`. It is named for neither half, because a
  domain named by one of its halves sends the other to whichever skill carries a
  word of it — the reason the description names both and the reason the name
  names neither.
- The gate survives as a step rather than as a skill boundary. The report half
  closes on **A request that asked for a review ends here**, and the commit
  stands after it, which
  `SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange` now holds
  as an order rather than as an absence.
- The ceiling moves from 3600 to 3970, which is what the thirteen cost with room
  for a rename. It stays a ratchet: what it holds is the description mass
  drifting, and how many skills this server publishes is still not decided by
  it.
- Both waiting skills are published in the same commit —
  `typo3-extension-patch-review` and `typo3-distribution-content` — each with
  its intent in `knowledge/task-intents.json` and its workflow in
  `knowledge/server-scope.json`.
- **The baseline run `D-SKL-035` asks of a new skill was not bought.** The
  maintainer published on the review alone, and neither run exists.

## Assumed

- That the two halves are one workflow for a caller and not only for this
  repository. What was read is the two bodies and the cycle between them; no run
  has been given a task that crosses the gate.
- That a name naming neither half is reachable. The triggers are in the
  description, where `D-AUD-003` says the routing happens, and `health` is not a
  word a user types.
- That the ratchet is still worth having at 3970. Nobody has re-measured a
  client's listing budget since 2026-08-08, and `D-SKL-026`'s own **Since then**
  records 9500 characters arriving where it computes 6000.

## Wrong if

- A session is given "review my extension" and comes back with changes made. The
  gate is then a paragraph rather than a step, and what a skill boundary was
  doing is what has to be written back into the body.
- A request for a repository-wide audit reaches no skill, or reaches
  `typo3-extension-patch-review`. Then the name naming neither half cost the
  routing, and `audit` is the word to put back.
- The next skill is blocked by the ratchet again. Then merging bought one
  publication and the question `D-SKL-026` left open — which skills a 200k
  session should be able to see — is the one that has to be answered.
- A published skill turns out to have been wrong in a way a baseline run would
  have shown. That is what `D-SKL-035` buys and what these two did not.
