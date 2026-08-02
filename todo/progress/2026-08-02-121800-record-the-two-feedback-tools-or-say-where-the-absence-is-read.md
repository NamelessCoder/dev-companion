# Record the two feedback tools, or say where the absence is read

**Serves:** documentation/clients/tool-answers/
**Priority:** normal
**Branch:** todo/record-the-two-feedback-tools-or-say-where-the-absence-is-read
**Claimed:** 2026-08-02

Give `typo3_feedback_record` and `typo3_feedback_list` a recorded answer, or
state their absence where a reader meets it. Neither has a page under
`tool-answers/`, `ToolSurface::recorded()` leaves the link out without a word
where none exists, and the head of `tools.md` says every tool below links to its
own — which is false for those two today. `ToolCalls` names why they are out:
the record tool writes, and a table driven by `ToolContractTest` as well has to
be safe to drive; the list tool answers with prose somebody else wrote, and a
truncated tool name in one feedback title reads to `ToolNamingTest` as a tool
that does not exist. Both are answerable — `FeedbackTest` already writes real
feedback below `feedback/` and removes them again, and a recorded list can be
driven against a fixture directory rather than the live one, which also makes
the page the same on every machine. Where either turns out not to be worth that,
correct the sentence in `tools.md` and have a test hold the list of tools with
no recorded answer, so the next one dropping out is visible rather than a link
that silently is not rendered.
