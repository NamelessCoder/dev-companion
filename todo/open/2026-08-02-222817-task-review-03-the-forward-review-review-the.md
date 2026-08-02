# Establish whether a core patch review earns a skill

**Serves:** feedback/2026-08-02-222817-task-review-03-the-forward-review-review-the.md
**Priority:** normal

Judged step **1b**, the shape is missing: the answers are here and nothing says
in which order to ask for them, and the skill whose description is this exact
review shape is bounded to project, sitepackage and extension. `D-SKL-005`
carries the evidence and the two things it assumes. What is left is the research
`documentation/clients/writing-a-skill.md` demands before a skill exists at all:
read the two core sessions call by call — `REVIEW-03` in
`scenarios/runs/REVIEW-03.json` and the GD/SVG review behind
`feedback/2026-08-01-115711` — and say what order they each invented, then ask
this server what it already answers about reviewing and preparing a core patch,
with the tools such a skill would route to. The answer that ends this step is
either the order a skill would hold, written from what the runs did, or the
finding that a core review needs nothing here that the existing skills and tools
do not already carry — both are results. What this todo may not do is decide the
first step for it: `feedback/2026-08-02-144350` shows `typo3_project_scope`
answering a core checkout with four `gerrit:setup` commands and no test runner,
and that half is that feedback's work.
