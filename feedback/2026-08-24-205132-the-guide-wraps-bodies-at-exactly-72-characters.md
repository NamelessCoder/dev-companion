---
date: 2026-08-24T20:51:32+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# The guide wraps bodies at exactly 72 characters, which this repository's own rule forbids

## Observation

Task: writing and checking core commit messages for three changes (Gerrit 95368, 95392, 93079 and a new commit for issue #110532).

typo3_commit_message_guide with workflow="core" returns a draft whose body is "wrapped at 72 characters", and its body-lines-reflowed check says so explicitly. The drafts it handed me contained lines of exactly 72 characters.

AGENTS.md in this repository states the rule the other way: "No line of the message may reach 72 characters - the hook rejects the commit, footer trailers included." Reach, not exceed. A 72-character line is a violation there.

This was not theoretical. Checking Gerrit change 95368's existing message with awk found two body lines at exactly 72:
  "message per inline child, since the access event backing that message is"
  "already, so the form stays readable no matter how many children a record"
I reported that to the user as a defect in their own commit. Then the guide, asked to check a message I wrote for the same repository, produced a draft with the same property. I ended up not using any returned draft verbatim: for all three messages I hand-wrapped to a maximum of 66-68 characters and verified with
  awk '{ if (length($0)>=72) ... }'
which is the check the repository's rule implies and the guide does not perform.

The tool's checks list is otherwise good and caught a real error: missing-issue fired when I drafted the #110532 message before the issue existed, and summary-length-preferred correctly flagged 56- and 57-character subjects.

Note the two rules are not the same rule stated loosely. 72 is where the guide wraps; 71 is the highest length the repository accepts. Every draft that hits the wrap width is one character over.

## Query

typo3_commit_message_guide(workflow="core", changeType="TASK"|"BUGFIX", message=<full message>) — called four times across the session. Repository rule quoted from /home/benji/projects/typo3-cms/AGENTS.md, section "Commits & review".

## Suggestion

Wrap core-workflow bodies at 71 rather than 72, and add a check that fires at length >= 72 rather than > 72.

If the 72 wrap is deliberate for some other consumer, then at minimum the core workflow should emit a check naming every line that reaches exactly 72, the way it already names the lines it could not bring under the width. Right now a message can pass every check the tool reports and still be rejected by the commit-msg hook, which is the one outcome the tool exists to prevent.

Worth confirming against the hook itself rather than against AGENTS.md alone — I did not test what the hook does with a 72-character line, I took the repository's own written rule. If the hook actually permits 72 and AGENTS.md is stricter than reality, that is the finding instead, and it is one the maintainers of this server can settle once for everyone.
