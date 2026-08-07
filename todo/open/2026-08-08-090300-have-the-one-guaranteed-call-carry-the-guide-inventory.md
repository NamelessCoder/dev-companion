# Have the one guaranteed call carry the guide inventory

**Serves:** feedback/2026-08-07-231203-the-typo3-guides-resources-were-never-listed-to.md
**Priority:** normal

A fourth session finished without learning which guides exist. Its client
surfaced `ListMcpResourcesTool` and two siblings as deferred tools and rendered
no resource listing; `typo3_server_scope`'s schema was loaded in the opening
`ToolSearch` and the tool was never called, for the reason two sessions gave
before it — the task looked legible without orientation. So it assembled a whole
"does a twenty-year-old frontend bug still reproduce" procedure by hand and says
it will reconstruct it again next session.

`D-ANS-061` already decided the lever is the tool the session does call, and
built it into the three that return a document section. This is the same
argument one step earlier: `typo3_project_describe` is the call the instructions
open every task with, and the one call this session did make. Have it return the
guide inventory as titles plus ids, so discovery stops depending on a client
feature or on an orientation call that agents skip precisely when they feel
oriented. `typo3_server_scope` stays the right home for the detail. Price the
size against `R-ANS-013` and against what `project_describe` already carries —
an inventory that pushes the installation facts down the answer has traded one
discovery problem for another.
