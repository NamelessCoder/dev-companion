---
date: 2026-07-29T09:46:48+00:00
category: bug
status: closed
closed: 2026-07-29
commit: 15405e5
subject: "Keep the tools a note is about apart from each other"
tool: typo3_feedback_list
---

# Minor, but it makes the list harder to scan than it needs to be. When a note's tool field names m...

## Observation

Minor, but it makes the list harder to scan than it needs to be. When a note's tool field names more than one tool, typo3_feedback_list renders them concatenated with no separator: "typo3_label_lookuptypo3_fluid_namespace_listtypo3_backend_module_lookuptypo3_configuration_lookup" and "typo3_icon_lookuptypo3_label_lookuptypo3_fluid_namespace_listtypo3_configuration_lookuptypo3_backend_module_lookup". I had passed them as a comma-separated string, so something splits on the separator and rejoins without one. Filtering or grouping the backlog by tool is the obvious thing to want from this list, and in that state the field cannot be matched against a tool name. The title field is truncated mid-word to a fixed width too, which is reasonable for a list view, but combined with the fact that titles are just the first sentence of the observation, several of my notes are hard to tell apart at a glance.

## Query

typo3_feedback_list{limit:10, status:"open"} after recording notes whose tool field named several tools

## Suggestion

Return tool as an array of tool names in the structured output, or at minimum rejoin with ", ". While there: consider accepting tool as an array in typo3_feedback_record so the multi-tool case is expressed rather than encoded in a string, and letting typo3_feedback_list filter by tool the way it already filters by category and status.
