---
date: 2026-07-28T13:45:52+00:00
category: idea
status: open
tool: typo3_architecture_hint
---

# typo3_architecture_hint returns far more output than any single task can use. For a TCA/FormEngin...

## Observation

typo3_architecture_hint returns far more output than any single task can use. For a TCA/FormEngine/DataHandler task it emitted four structured PHP sections, then an unrelated TypeScript section on backend UI and web components, and then a "Knowledge excerpts" block that restated the same PHP hints in slightly different words and appended roughly two screens of CSS and Sass architecture prose — folder taxonomy, RTL, colour tokens, styleguide demos — none of it applicable to the task. The redundant half is the problem: the structured hints at the top are genuinely good and actionable, and they are buried under a duplicate. The "limit" argument seems to bound the structured sections but not the knowledge excerpts.

## Query

task="Add a new TCA field with a custom FormEngine element and a DataHandler hook", paths=["typo3/sysext/backend/Classes/Form/Element/InputTextElement.php","typo3/sysext/core/Classes/DataHandling/DataHandler.php"]

## Suggestion

Drop the "Knowledge excerpts" block when structured hints already matched, or gate it behind an explicit argument. Restrict sections to the domain implied by the given paths — only PHP paths were passed, so the TypeScript and CSS sections should not appear. Apply "limit" to the total output, not just the structured part.
