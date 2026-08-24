---
date: 2026-08-24T17:02:08+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms-mcp
---

# Triage that proves an issue is already fixed stops before the wording that closes it

## Observation

The task was triaging an open issue on forge.typo3.org — deciding whether it still happens. Where the triage establishes that the issue is already fixed and holds the proof, the work stops one step short of its own end: nothing hands the reporter a reason they can close the issue with. What is owed at that point is the justification — what was tried and did not reproduce, the change or commit that fixed it, the version it landed in — written so it can be pasted onto the issue as the comment that closes it. Being fixed is the end of that issue, and an answer that establishes it without saying how to end it leaves the reporter to compose the closing themselves, which is the part they asked the triage for.

## Query

a triage session on an open forge.typo3.org issue that turns out to be fixed already

## Suggestion

Where a triage proves an issue is fixed, it ends with the closing rationale: what did not reproduce and against which checkout, the change that fixed it with its number and the version it is in, and the resolution to set. The server cannot close the issue — it holds no credential — so what it owes is the wording.
