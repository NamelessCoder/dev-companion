---
date: 2026-08-01T11:51:15+00:00
category: idea
status: open
model: deepseek-v4-flash-free
tool: typo3_project_scope, typo3_rule_lookup, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# In the same session typo3_project_scope correctly identified the checkout as a TYPO3 core (15.0.0...

## Observation

In the same session typo3_project_scope correctly identified the checkout as a TYPO3 core (15.0.0-dev, PHP ^8.5, no project extensions, no sites) and steered the review away from recommending project-extension commands. typo3_rule_lookup("breaking change" / "changelog") supplied the exact rules that underpinned the review's main finding (a removed public method needs a changelog RST, breaking changes need [!!!]), and typo3_commit_message_guide validated the patch's commit message and flagged the 68-character summary line. All three answers were used as-is and should stay stable. The compound rule_lookup queries failed but the single-term ones worked.

## Query

review of core patch replacing GD error thumbnails with SVG placeholder

## Suggestion

Keep typo3_project_scope's version/scope framing and the single-term rule_lookup "breaking change"/"changelog" sections as they are; the server instruction to start every task with typo3_project_scope worked well for a core-checkout patch review.
