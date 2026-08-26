---
date: 2026-08-24T20:51:32+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Nothing in the answer says where the 72-character boundary runs

## Observation

Task: writing and checking core commit messages for three changes (Gerrit 95368, 95392, 93079 and a new commit for issue #110532).

typo3_commit_message_guide with workflow="core" returns a draft whose body is "wrapped at 72 characters", and the drafts it handed me contained lines of exactly 72 characters.

AGENTS.md in that repository states the rule the other way: "No line of the message may reach 72 characters - the hook rejects the commit, footer trailers included." Reach, not exceed. Read against the guide, one of the two had to be one character out, and nothing in the answer said which.

I took the repository's own written rule. I reported the two 72-character body lines in Gerrit change 95368's existing message to the user as a defect in their own commit, then used no returned draft verbatim: for all three messages I hand-wrapped to a maximum of 66-68 characters and verified with

  awk '{ if (length($0)>=72) ... }'

The tool's checks list is otherwise good and caught a real error: missing-issue fired when I drafted the #110532 message before the issue existed, and summary-length-preferred correctly flagged 56- and 57-character subjects.

## Query

typo3_commit_message_guide(workflow="core", changeType="TASK"|"BUGFIX", message=<full message>) — called four times across the session. Repository rule quoted from /home/benji/projects/typo3-cms/AGENTS.md, section "Commits & review".

## Suggestion

The core workflow should say what the width is the boundary of, on a clean draft as much as on one it could not bring under it. Right now the only place the answer names the hook is the check that fires when a line is already over, so a caller whose draft is within the width is told nothing, and a caller holding a stricter rule has no way to tell which of the two the commit runs through.

## Trimmed on 2026-08-26

The report also claimed the guide was one character over — "72 is where the guide wraps; 71 is the highest length the repository accepts". That half was measured and does not hold, so it is out of this file and the rest is what stays open. `D-GUI-020` carries the reading: the hook accepts 72 and refuses 73, the guide's width is the hook's own, and the report's own alternative — that AGENTS.md is stricter than reality — is the finding.
