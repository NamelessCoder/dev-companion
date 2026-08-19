---
date: 2026-08-18T07:46:27+00:00
category: idea
status: closed
closed: 2026-08-19
model: claude-opus-5[1m]
tool: typo3-development-installation, typo3_project_describe, typo3_changelog_lookup, typo3_commit_message_guide, typo3_feedback_record
directory: /home/benji/projects/blog
---

# Every tool arrives deferred, so each first use costs a schema fetch the skill could have batched

## Observation

Task: repair a 404 on a DDEV TYPO3 14.3.6 installation of t3g/blog, then fix a v14 rendering crash, add tests, prove it on 13.4 and 14.3, and change the extension's setup service. Reporting the round-trip accounting, which my earlier feedback from this session left out.

In my client every typo3_ tool is deferred: the names are listed, the schemas are not, so a tool cannot be called until a ToolSearch fetches it. I paid that four times. The first was efficient — I read the skill's references/base.md and batched the four tools its order names into one select:. The other three were single-tool fetches made mid-task, each an extra round trip in front of the call I actually wanted: changelog_lookup when the 13.4 question came up, commit_message_guide when I was ready to commit, feedback_record at the debrief.

None of those three was unforeseeable. base.md's own order names typo3_changelog_lookup as step 5 and I fetched it late because I had skipped that step. The installation skill names typo3_commit_message_guide in its "Prove it" section, several screens before I needed it. So the information to batch all of them was in front of me at minute one and the batching was mine to do and I did it for four tools and not for the rest.

No server call in the session was wasted in the sense the debrief asks about: nothing returned nothing usable, nothing had to be repeated with different arguments, nothing only restated a previous answer. typo3_changelog_lookup with "TcaSchemaFactory" came back thin — three entries, one useful — but it was one call and it did settle that the Schema API predates 13.4.

The expensive loops in this session were not server calls at all. Three edit-flush-request-read-log cycles to converge on the missing tt_content fields, two more to converge on the site base, and a full second core installation under var/v13 to prove 13.4. Those cost far more than every typo3_ call together, and both loops trace to knowledge gaps I have already filed separately. Recording the shape here so the two are not confused: the server was cheap, the verification it could not shorten was not.

## Query

Four ToolSearch calls in one session: select:typo3_project_describe,typo3_hint_lookup,typo3_configuration_lookup,typo3_task_guide; then select:typo3_changelog_lookup; then select:typo3_commit_message_guide; then select:typo3_feedback_record. Server tool calls actually made: project_describe x1, task_guide x1, changelog_lookup x1, commit_message_guide x3, feedback_record x9 (2 failed on my own malformed call, not the server).

## Suggestion

Where a skill knows which tools its workflow ends in, name them together near the top as one fetch line rather than scattered through the prose at the point each is used — the installation skill ends in typo3_commit_message_guide and, under base.md's order, in typo3_changelog_lookup, and both could be listed with the four the order opens with. A client that defers schemas turns each late mention into its own round trip, and the skill is the only thing in the session that knows the whole list in advance.
