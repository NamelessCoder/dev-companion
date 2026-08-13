---
date: 2026-08-13T21:48:11+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# the review skill specifies the order of findings but not that the report is a file, so a long rev...

## Observation

Task: review Gerrit change 93319 patch set 21 and report.

The skill is precise about the content and the order of a report — five severity bands, what each finding owes, the checklist surfaces marked assessed/unassessed/not applicable, dropped candidates with what dropped them. I followed all of it and the result was a long, structured document. It says nothing at all about where that document goes, so I printed it as my chat answer.

The user corrected me twice in a row: "reviews sollten immer im markdown format ausgegeben werden", then "damit es kopierbar ist". The point is the second sentence. A review of this shape exists to be pasted into the Gerrit change or handed on, and terminal scrollback is not something you can select cleanly — mine ran to a couple of hundred lines with a table in it. I wrote it out to review-93319-ps21.md afterwards.

This is a gap in the skill and not a preference of one user, because the skill itself is what makes the report long. The five bands plus the surface table plus the dropped candidates cannot be short, and nothing in the skill notices that it has specified a deliverable rather than a chat reply. The closing sentence gets close — "A review changes nothing itself, so the report is what it produces" — and then stops without saying what form the thing it produces has.

Same for the neighbouring skills that end in a report: typo3-core-issue-triage and typo3-extension-conformance both produce something a person is meant to carry somewhere else.

## Query

typo3-core-patch-review, invoked to review change 93319 patch set 21

## Suggestion

Say in the Report section that the review is written to a markdown file and the path given in the answer, with the chat reply kept to a short summary plus that path. Name it after what it reviews, including the patch set — review-93319-ps21.md — because a review of PS20 and one of PS21 are different documents and the patch set is the only thing that separates them.

Say where it goes, too: not into the core checkout, since the same skill's own checklist reports untracked files beside the patch as a finding. A scratch directory or a path the caller names.

Worth extending to the other report-producing skills for the same reason: anything whose product is a document should say that it is one.
