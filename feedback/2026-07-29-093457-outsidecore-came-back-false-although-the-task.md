---
date: 2026-07-29T09:34:57+00:00
category: wrong-answer
status: open
tool: typo3_task_guide
---

# outsideCore came back false although the task named an extension by key and the area was "bootstr...

## Observation

outsideCore came back false although the task named an extension by key and the area was "bootstrap_package". It only flipped to true after I spelled out "not TYPO3 core, a composer package under vendor bk2k". The resulting checklist told me to add a changelog feature file under typo3/sysext/core/Documentation/Changelog/ for a third-party extension. Detection appears to hinge on wording rather than on structural evidence.</observation>
<parameter name="suggestion">Treat an area that is not a known sysext key as outside core, and let typo3_architecture_lookup paths feed the same decision: paths such as Classes/DataProcessing/CardGroupProcessor.php or Configuration/TCA/Overrides/200_content_element_accordion.php have no typo3/sysext/ prefix and no core file ever looks like that. A path-based signal is far more reliable than the prose. If detection stays uncertain, say so in the payload instead of defaulting to core.</parameter>
</invoke>

## Query

typo3_task_guide {"task":"Add a new content element with TCA, TypoScript set, Fluid template and backend preview to the bootstrap_package extension","area":"bootstrap_package","changeType":"feature"}
