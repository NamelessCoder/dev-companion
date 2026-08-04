# Name the document corpus in the instructions

**Serves:** feedback/2026-08-04-180133-task-fix-this-project-twelve-commits-over-a.md, knowledge/
**Priority:** normal

`D-AUD-007` decided that the `instructions` in `knowledge/server-scope.json`
name what this server ships as prose documents, since a resource list is the
client's to render. Read the instructions whole first — they open with
`typo3_project_describe` and `typo3_task_guide` and are the one text every
client receives — then add the line: that the corpus carries end-to-end
procedures, that they are addressed as `typo3://guides`, and what a session
reaches for one for. Name the index rather than the seven documents, and check
what `typo3_server_scope` already says at `src/Tool/ServerScope.php:337` so the
two do not say it differently. `ScopeTest` holds the scope and the tool list to
each other; run it, and re-read `documentation/resources/readme.md` for what a
picker chooses by before wording the line.
