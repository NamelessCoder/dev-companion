---
date: 2026-07-29T09:43:23+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 825854c
subject: "Serve the commit conventions without the Gerrit trailers"
tool: typo3_commit_message_guide
---

# Two issues, one of which affects core work too. First, the changeType enum offers only BUGFIX, FE...

## Observation

Two issues, one of which affects core work too. First, the changeType enum offers only BUGFIX, FEATURE, TASK and DOCS, but [SECURITY] is a real core keyword — I counted 33 subjects matching ^\[SECURITY\] in the last 3000 commits on origin/main — and it is in active use in this project as well, for example "[SECURITY] Update typo3/cms-* (#62)". There is no way to draft or check such a message with this tool. Second, the tool is unusable outside core for a reason that has nothing to do with formatting. The subject line convention is identical in this repository — the git history is [TASK], [BUGFIX], [FEATURE], [SECURITY] throughout — so this is the one piece of core process that genuinely does transfer, and the 72-character body wrapping was correct and useful. But the tool appended "Resolves: #ISSUE_NUMBER" and "Releases: main" unconditionally and returned a hard error, missing-issue: "A Forge issue is required." This project tracks work in GitHub pull requests, references them as a "(#66)" suffix on the subject, and has no Forge issues and no release branch trailers at all. So the one transferable capability arrives welded to trailers that are wrong here and gated behind an error that cannot be satisfied.

## Query

typo3_commit_message_guide{changeType:"TASK", summary:"Add sponsor logo field to sponsor record", body:"The sponsor records need an additional logo field ..."}

## Suggestion

Add SECURITY to the changeType enum; it is a keyword the core itself uses and its absence is a plain gap regardless of the caller's context. Then separate formatting from core policy: keep the current behaviour as the default, and add an argument such as mode:"core"|"generic" (or trailers:false) where the generic mode still validates the keyword prefix, the imperative subject and the 72-character wrapping, but drops the Resolves/Releases trailers and demotes missing-issue from error to a note. That turns the guide into something a site project can adopt wholesale rather than something it has to imitate by hand.
