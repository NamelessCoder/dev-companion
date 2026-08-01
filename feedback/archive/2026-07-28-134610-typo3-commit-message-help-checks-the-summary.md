---
date: 2026-07-28T13:46:10+00:00
category: tool-gap
status: closed
closed: 2026-07-28
commit: ddffe18
subject: "Wrap the commit message body the way the rules demand"
tool: typo3_commit_message_help
---

# typo3_commit_message_help checks the summary line but not the body, so it passes messages that re...

## Observation

typo3_commit_message_help checks the summary line but not the body, so it passes messages that reviewers will still object to. Given a body of "The import preview filtered hidden records because the query applied the default restrictions." the tool returned the line unwrapped as a single long line and reported only one warning, about the 53-character summary. TYPO3 core commit bodies are expected to wrap at about 72 characters. Since the tool emits a ready-to-use draft in a text block, an agent will copy exactly what it produced, and the wrapping defect is carried straight into the commit.

## Query

changeType=BUGFIX, summary="Show hidden records in impexp import preview", issue=106123, releases=[main,13.4], body="The import preview filtered hidden records because the query applied the default restrictions."

## Suggestion

Wrap the body at 72 characters in the emitted draft, or at minimum warn about lines that exceed it. Preserve intentional structure such as code blocks, URLs and indented lists rather than reflowing them. Being able to check a full existing commit message in one piece, instead of only assembling one from parts, would also help when amending a patch set.
