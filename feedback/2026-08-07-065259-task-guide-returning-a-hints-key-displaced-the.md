---
date: 2026-08-07T06:52:59+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# task_guide returning a hints key displaced the prescribed per-subsystem hint_lookup call

## Observation

Task: verify Forge 109572 against a 15.0.0-dev core checkout.

references/base.md of typo3-core-issue-triage prescribes step 4 as typo3_hint_lookup, once per subsystem, with concrete paths, and says explicitly that a single broad query is not subsystem evidence. I never made that call in the entire session — not once.

The cause is traceable in the transcript. typo3_task_guide, called at step 3 with the three Extbase persistence paths, returned a populated "hints" array, and I read that as step 4 already answered and moved on. The hints it returned were off-target: datahandler-basics, system-extension-boundaries, extbase-domain-mapping, fal-storages-drivers. Only extbase-domain-mapping was even adjacent; none touched datetime storage or null semantics; fal-storages-drivers has no bearing on a bug in the Extbase query parser. So the substitution cost me the one lookup that might have carried the fact the issue turned on (filed separately as missing knowledge), and the substitute looked plausible enough that I never noticed the step had gone missing.

This is worth reporting because the failure is silent from the server's side: it sees a task_guide call and no hint_lookup call, and cannot tell that its own answer is why.

## Query

typo3_task_guide(task: "Verify a bug report claiming an Extbase repository query cannot filter for IS NULL / IS NOT NULL on a nullable date field", paths: ["typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php", "typo3/sysext/extbase/Classes/Persistence/Generic/Query.php", "typo3/sysext/extbase/Classes/Persistence/QueryInterface.php"], changeType: "audit", targetVersion: "15") — returned hints: datahandler-basics, system-extension-boundaries, extbase-domain-mapping, fal-storages-drivers

## Suggestion

Either task_guide should stop returning hints and instead return the typo3_hint_lookup calls still owed with the subsystem arguments already filled in, so the next step is a call rather than a reading; or its hints key should be labelled in the payload as a non-exhaustive preview that does not discharge the per-subsystem call. Separately, worth checking why paths under extbase/Classes/Persistence/Generic/Storage select fal-storages-drivers at all — that match is what made the set look broad enough to be complete.
