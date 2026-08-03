---
date: 2026-08-02T14:51:28+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: assess Forge #105403 and fix it. Collecting the method that assessment actually required, a...

## Observation

Task: assess Forge #105403 and fix it. Collecting the method that assessment actually required, as one procedure, so a later session does not rediscover it. I have filed separately about maintainer statements contradicting the code, about closure status not being a verdict, and about the CMS premise; what follows is the rest, and each step below either changed my conclusion this session or would have if I had taken it earlier.

Trimmed on 2026-08-03 by the run that judged this feedback. Two of the nine steps are answered and are removed here rather than counted twice. Step 1, reproduce against the target branch before describing the bug, is the fourth sentence of "Establish the issue before you believe it" in the typo3-core-patch-development skill, which was written from this cluster. Step 8, separate the symptom from the goal, is R-GUI-008 and now opens the checklist of every task brief — verified by re-running this session's own typo3_task_guide call. The knowledge those steps were used to establish is in the hint fluid-resource-uris, which states where cache busting is applied and that f:image and f:uri.image are not on the System Resource API. What is left below is the assessment method that no order here carries. Its numbering is the original one, so the steps can still be matched against the session that filed them.

2. Read the related issues, especially the one that introduced the behaviour. #105403 says "in version 12 the same code works" without saying why. The why is in #100696, which added useCacheBusting to f:uri.resource in v13 — the regression cause sat in a related ticket, not the reported one. The Redmine API exposes this as the relations field, and a Forge search on the feature wording finds the rest.

3. A deferred decision has a date and a stated blocker; check whether the blocker still holds. The reason given in 2024 for not adding cache busting to the image ViewHelpers was that it "needs to be handled via getPublicUrl() event, we also have non-public storages". By v15 the System Resource API exists and CacheBustingUri::fromFile() does precisely that. The objection had expired, and treating it as standing was my first error.

4. Look for the same inconsistency inside one version — it is the strongest available argument. "The same file gets a cache buster through f:uri.resource and none through f:image, on the same branch" is a defect report a reviewer can act on. "A user would like cache busting" is a wish. Hunting for a place where the system already does the right thing is what converts one into the other.

5. Read the area's history before designing anything. git log --oneline -- <path> surfaced the whole v15 System Resource migration and a commit documenting the very pitfall the issue is about, in one call. It also would have warned me the area was under active change: a commit landed mid-session that removed the method my first implementation was built on and invalidated it entirely.

6. Search Gerrit by issue number before writing code. review.typo3.org/changes/?q=message:<issue> returning nothing is itself the answer to "has someone already fixed this", and it is one request.

7. Weigh who reported it. #105403's author is a core maintainer, which should have raised my prior that the snippet was considered rather than naive. I reasoned about it as a beginner's mistake for the first several turns.

9. Treat the blast radius as part of the assessment, not as work discovered afterwards. This fix changed rendered output in about 141 expectations across 23 files, which is what decides whether the change is a quiet bugfix, needs an Important changelog entry, or is breaking. I found that out incrementally, long after I had already characterised the change.

## Query

Whole session: "evaluate Forge issue 105403, check whether it is valid, find similar issues, find patches that already fixed it, then create a patch". The assessment phase preceding typo3_task_guide task="Fix f:image ViewHelper failing when src contains a cache busting query string", changeType=bugfix, area=fluid, targetVersion=15.0

## Suggestion

Add an issue-assessment procedure for a bugfix, distinct from the implementation checklist that already exists, containing the steps above in roughly that order. The implementation side of core work is well supported here; the judging side is not supported at all, and it is where a session commits to the wrong deliverable before any code is written.

Rewritten on 2026-08-03 by the run that judged this feedback, in two places. The venue is the typo3-core-patch-development skill rather than typo3_task_guide: a guide answers one call and hands back a checklist, and an order that decides what a session commits to is read in sequence. D-SKL-010 carries that with its evidence. And two of the steps above are round trips this server already answers — typo3_forge_lookup returns #105403's status as it stands today, its relations and its notes in one call, and typo3_gerrit_lookup with the issue number is the "has somebody already fixed this" of step 6. Neither is named by any order a session writing a patch follows. Step 2 is answered better than it was filed: the relations of #105403 carry #99203, whose changelog entry is what gave f:uri.resource its useCacheBusting argument, so the introducing change is one lookup away rather than a Forge search on the feature wording.
