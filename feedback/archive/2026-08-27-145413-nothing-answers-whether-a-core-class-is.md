---
date: 2026-08-27T14:54:13+00:00
category: tool-gap
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_commit_message_guide, typo3_component_lookup
directory: /home/benji/projects/typo3-cms
---

# Nothing answers whether a core class is @internal, which is what settled the breaking check

## Observation

Task: "please search for 1 workspace bug in forge and fix it" — I fixed Forge #97614 by adding an optional `?int $language` parameter to the protected method `WorkspaceService::getMovedRecordsFromPages()`.

typo3_commit_message_guide returned the `breaking-not-assessed` check, and it was right to: "Enumerate the public and protected members the diff removes, and the ones whose signature it narrows or widens: a parameter added to a method widens that signature whether or not the parameter is optional." My diff does exactly that — it widens a protected signature. By the letter of that rule I owed a [!!!] and a Breaking changelog entry.

It is not breaking, because `TYPO3\CMS\Workspaces\Service\WorkspaceService` carries `@internal` on the class. The rule as stated has no branch for that, and the server has no way to check it: the answer comes "from: knowledge", and the tool never sees the diff or the file.

So I ran `sed -n 30,60p typo3/sysext/workspaces/Classes/Service/WorkspaceService.php` and read the docblock myself. That single line — `@internal` — settled three questions at once: no [!!!], no Breaking entry, and (combined with typo3_task_guide's changelog rule) no changelog entry at all. It was the highest-leverage fact in the session and I got it from `sed`.

The same fact would have been needed for the changelog decision independently. typo3_task_guide's checklist gave me the rule — "A bugfix owes a changelog entry only where it changes what an installation renders, is configured by, or has documented" — which is correct but is stated about installations, not about API surface. Whether a widened protected signature on an internal class "is documented" is exactly the case the rule does not address. I resolved it with the @internal line.

I did not call typo3_component_lookup for this. Reading its description and the server's instruction block ("backend markup or a CSS class: typo3_component_lookup with the targetVersion") I took it to be about backend UI components — markup, classes, icons — not about PHP API classification. That assumption may not have held; I never tested it, and if that tool would have answered "WorkspaceService is @internal" then its description is steering me away from an answer it has.

## Query

typo3_commit_message_guide(workflow: "core", changeType: "BUGFIX", summary: "Respect language filter for moved records in workspaces", issue: "97614", releases: ["main","14.3","13.4"]) — returned check code "breaking-not-assessed". Diff added an optional ?int $language parameter to protected WorkspaceService::getMovedRecordsFromPages().

## Suggestion

Two options, either would have saved the step.

1. A lookup that, given a core FQCN or path, answers its API classification: `@internal` on the class or the member, `@deprecated`, whether it is in the extension-scanner configuration, and whether it is `final`/`readonly`. It reads the installed core, which this server already does elsewhere. Call it from typo3_commit_message_guide's own breaking check, or let me call it directly.

2. Failing that, amend the `breaking-not-assessed` check text to name the exemption: "...a parameter added to a method widens that signature — unless the class or member is marked @internal, in which case it carries no API promise. Read the class docblock before classifying." One sentence, and I would not have had to work out that the rule had an unstated branch.

Also worth clarifying in typo3_component_lookup's description whether PHP API questions are in or out of its scope. Right now the instruction block's one-liner makes it read as backend-markup-only, and I passed it over on that basis.
