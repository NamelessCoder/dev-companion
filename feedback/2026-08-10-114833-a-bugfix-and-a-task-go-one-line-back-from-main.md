---
date: 2026-08-10T11:48:33+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide, typo3_rule_lookup, typo3-core-patch-review, typo3-core-patch-development
---

# a bugfix and a task go one line back from main; older lines are earned by severity

## Observation

Task: none of a session's own — the maintainer of this repository stated the rule directly while reading what the server says about release targets, so this is the maintainer's own report and not something a session established from a source here.

What the server states today. knowledge/documents/core/contribution/commit-messages.md: "A bug fix goes to every maintained line that carries the defect, which is what makes it the change type the trailer is worth checking on." skills/typo3-core-patch-review/SKILL.md repeats it as the reason a reviewer checks the Releases: trailer at all. Knowledge\ReleaseLines::releasable() answers main, 14.3, 13.4 today. A caller reading those together writes all three lines into the trailer of an ordinary bug fix and believes the server told it to.

What the core actually does. A [BUGFIX] and a [TASK] are backported one release line back from main and no further — main and 14.3 today. An older maintained line is reached only where the severity of the defect earns it: a priority bug fix, a grave or security-relevant defect. Whether the older lines carry the defect is still evaluated every time, on every one of them; that half of the current statement holds. But carrying the defect is not on its own what puts a branch in the trailer. The severity is what decides the backport, and the evaluation is what decides where the defect is.

So the statement is not incomplete, it is over-broad in the one direction that costs other people work: a trailer naming 13.4 asks a core merger to cherry-pick onto a line the change was never meant for, and a reviewer reading that patch has no rule in this server to refuse it with — the server's own sentence is the argument for it.

## Query

No tool call produced this. It is a correction to what the server states about release targets: commit-messages.md "Release Targets", the Releases: bullets of typo3-core-patch-review, and what typo3_commit_message_guide with workflow="core" reports for an omitted trailer.

## Suggestion

Split the one rule into the two steps it actually is, in the three places it is written: the Release Targets section of knowledge/documents/core/contribution/commit-messages.md, the Releases: bullets in skills/typo3-core-patch-review/SKILL.md, and "Release Branches and Backports" in knowledge/documents/core/contribution/gerrit-workflow.md.

Step one, unchanged: read which maintained lines carry the defect, on each of them, rather than counting trailers on other commits.

Step two, new: a [BUGFIX] and a [TASK] are released on main and the one line back from it. An older maintained line is named only where the severity earns it — a priority bug fix, a grave or security-relevant defect — and that is a judgement the author states, not a consequence of the defect being present.

typo3_commit_message_guide has to say the same where it names branches for an omitted trailer: ReleaseLines::releasable() is which lines can take a patch at all, never which lines this patch belongs on, and a finding that reads as the second is the one a session will paste.

Verify the wording against the core's own contribution documentation before it goes into knowledge/, and record in the decision what evidence it rests on: this feedback carries the rule from the maintainer, not from a source the next session can re-read.
