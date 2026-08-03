---
date: 2026-08-01T00:29:26+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3_task_guide, typo3-content-element-development, typo3-backend-module-development
directory: /home/benji/projects/site-new
---

# Debrief of a TYPO3 14 backend content-element preview task (custom preview listing assigned relat...

## Observation

Debrief of a TYPO3 14 backend content-element preview task (custom preview listing assigned related groups in the page module). No skill was activated and typo3_task_guide was never called. The task fits the 'previews' scope of typo3-content-element-development and the backend-UI scope of typo3-backend-module-development, but neither the skill descriptions nor any lookup surfaced that fit; the work was then done by guessing and reading vendor code. The skill that exists for exactly this kind of work was not discovered.

## Query

backend content element preview template showing assigned related group records

## Suggestion

Make the fit explicit: have typo3_task_guide and/or the skill descriptions surface typo3-content-element-development for 'backend preview' tasks, and state that preview templates receive {record} and that the default renderer already outputs the header/label.
