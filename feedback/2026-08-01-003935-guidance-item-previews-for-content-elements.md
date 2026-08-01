---
date: 2026-08-01T00:39:35+00:00
category: idea
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3contentelementdevelopment
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed guidance item: 'previews for content element...

## Observation

Debrief of the TYPO3 14 testimonials session, missed guidance item: 'previews for content elements should show an abstract view of the assigned data'. The backend preview requirement is not just a static label or a duplicate header — the preview should summarize the assigned related data (e.g. list the assigned testimonial groups) so an editor sees what the element shows without opening it. This guidance was stated by the user and never recorded as a design rule.

## Query

backend content preview should show an abstract view of the assigned related data

## Suggestion

Record the preview design rule: a content-element preview should display an abstract summary of the assigned/related data (group titles, counts, references), not a static confirmation label or a re-render of fields the default renderer already shows.
