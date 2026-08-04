---
date: 2026-08-04T18:00:10+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3-extension-cleanup, typo3-extension-conformance
directory: /home/benji/projects/site-new
---

# Task: "fix this project" — a ten-item worklist produced by typo3-extension-conformance and worked...

## Observation

Task: "fix this project" — a ten-item worklist produced by typo3-extension-conformance and worked off by typo3-extension-cleanup. This is the half that worked and must not be simplified away.

Step 12 caught a regression I had introduced in the fix itself.

Findings F1/F2 were that config/system/additional.php was owned by DDEV, which decides ownership by searching the whole file for its generated-file signature rather than parsing a header. My fix removed the marker and implemented the documented environment contract — and the explanatory comment I wrote quoted the literal signature while explaining why it had been removed. DDEV therefore still owned the file. I had committed it, run the full suite green, and reported the finding closed.

The re-check is what found it, and only because it re-ran the environment rather than re-reading the file: reading additional.php showed a correct 131-line file. What showed the defect was `ddev restart` followed by a checksum comparison — the file came back as DDEV's 45-line stock template with the entire environment contract gone, silently, no error and no prompt. Had step 12 not existed I would have shipped a security fix that reverts itself on the next `ddev start`.

The second regression it caught in the same pass was smaller but real: Playwright runs a project whose dependency skipped, so the backend specs I had added failed rather than skipped when no credentials were set, turning `npm run test:e2e` red on a fresh clone for something that is not a defect.

Also worth keeping: the conformance checklist's "What a dropped candidate owes". It forced me to write down 13 candidates I had raised and let go — including several that would have been false findings, such as the content elements' <f:layout name="Default"/> which the package's own settings.yaml documents as deliberate.</observation>
<parameter name="suggestion">Keep step 12 exactly as it is. If anything is added, add one line saying the re-check has to re-run whatever the finding was about rather than re-read the files — reading showed a correct file here, and only restarting the environment showed the defect. That distinction is what made the difference between a closed finding and a shipped regression.</suggestion>
</invoke>

## Query

typo3-extension-cleanup step 12: "Hand the worked list back to typo3-extension-conformance for the re-check. It kept responsibility for that when it handed the finding over, and a cleanup that grades its own work has no evidence the finding is gone."
