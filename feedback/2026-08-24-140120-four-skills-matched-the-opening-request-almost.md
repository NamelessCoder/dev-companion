---
date: 2026-08-24T14:01:20+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-extension-health, typo3-extension-upgrade, typo3-extension-documentation, typo3-extension-patch-review
directory: /home/benji/projects/ext-usercentrics
---

# Four skills matched the opening request almost verbatim and none of them activated

## Observation

Task: bring a TYPO3 extension last released for v10 from two unfinished branches to one version serving v13.4 and v14, in a German-language session.

The user opened with "diese extension ist in einem schweren zustand, wir wollen das bereinigen, wir wollen compat zu typo3 v13 und v14 herstellen in einer version". Over the session I rewrote composer constraints and ext_emconf, replaced removed Fluid APIs, rewrote Documentation/ and README, and judged two incoming branches. I activated exactly two skills, both late: typo3-development-installation and typo3-extension-testing, and only because the user asked for a browser-verifiable frontend and functional tests in a later turn.

Four skills whose descriptions cover what I was doing never opened:

- typo3-extension-health: "look over my repository and fix it". The user's words were "in einem schweren zustand ... bereinigen". I was at that moment running git log and reading composer.json. Nothing routed me to it.
- typo3-extension-upgrade: "adding a new major, dropping one, and proving every version it claims". This is the whole first half. The moment one would have had to activate is the turn "bitte mach einen neuen branch und fixe alles", with composer.json and ext_emconf.php open and the open question whether one version can serve 13.4 and 14.3.
- typo3-extension-documentation: I rewrote Documentation/Configuration, Documentation/Usage, migrated Settings.cfg to guides.xml and rewrote README.
- typo3-extension-patch-review: the first request was to say what two unmerged branches do.

Why they stayed shut, as far as I can reconstruct: the first request read as analysis, so I went to git and grep directly and never called typo3_task_guide. I only called typo3_task_guide much later, from inside typo3-development-installation, which is where the skills list finally reached me. Nothing before that point pointed at the skill catalogue.

The two skills I did activate fitted well and I would keep both. typo3-development-installation correctly sent me to typo3_project_describe first and its "When the task turns to a suite" section did fire: I loaded typo3-extension-testing before writing Tests/Functional, which is exactly what that section says it exists to prevent being skipped.

## Query

Session opening: "diese extension ist in einem schweren zustand, wir wollen das bereinigen, wir wollen compat zu typo3 v13 und v14 herstellen in einer version. bitte prüfe was da ist, was die branches machchen etc" — followed by "bitte mach einen neuen branch und fixe alles". No typo3_ tool was called until several turns in; the first calls were typo3_changelog_lookup and typo3_ter_lookup.

## Suggestion

The skill descriptions are good; what is missing is anything that fires on a repository-wide request before a task_guide call. Two possibilities: (1) have typo3_project_describe's answer name the skills that fit the repository kind it just described — it already returns a guides list, so a skills list costs one more field and lands in the very first call every workflow makes; (2) state in the server's initialize instructions that a request phrased as "look at my repository", "clean this up" or "make it compatible with version N" is a skill call before it is a lookup, naming typo3-extension-health and typo3-extension-upgrade explicitly. Right now the instructions say "start every task with typo3_project_describe: ... Then call typo3_task_guide", and I did neither at the start because the request looked like reading, not changing.
