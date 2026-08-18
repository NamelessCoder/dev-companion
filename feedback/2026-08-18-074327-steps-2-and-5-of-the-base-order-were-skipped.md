---
date: 2026-08-18T07:43:27+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_changelog_lookup, typo3_hint_lookup, typo3-development-installation
directory: /home/benji/projects/blog
---

# Steps 2 and 5 of the base order were skipped and nothing noticed, on a task that changed code

## Observation

Task: "ich habe das blog setup ausgeführt, aber das frontend zeigt immer noch 404" on t3g/blog, TYPO3 14.3.6. The skill's references/base.md lays out a five-step order. I followed step 1 and step 3 and skipped steps 2, 4 and 5. The server has no way to see this, so I am reporting it.

Step 2, typo3_extension_describe for each extension in scope: never called. typo3_project_describe had already named blog as the project's own extension, and the task read as an installation problem, so I went straight to the checkout — ls, cat composer.json, grep Classes/. By the time the session had turned into changing that extension's ViewHelper, service, controller and tests, the step was long behind me. What it would have given me is exactly what I then established by hand and slowly: which test layers exist, and what the extension ships beside its code.

Step 4, typo3_hint_lookup per subsystem: never called standalone. The base.md text says to read whether the task_guide brief carried everything it matched or stopped short and named ids it left. The brief returned five hint groups and an empty omittedHints array, and I took that as the step being discharged without reading for the sentence base.md describes. I cannot now tell whether it said either thing. If omittedHints being empty is the machine-readable form of "these are everything", that is not what base.md tells the caller to look for — it tells you to read a sentence and explicitly warns not to read the populated hints key.

Step 5, the deprecation sweep: skipped, and this one I think was wrong. The exemption in base.md is for tasks that produce no change. This task produced three commits touching PHP in Classes/ViewHelpers, Classes/Service, Classes/Controller and Configuration/DataHandler. base.md is explicit that a review asked to make the change starts the order again holding the files it is about to write. I never re-ran anything when the task turned from operations into a patch, and I owed one call per declared major per tag. Whether it would have found anything I do not know, which is the honest state.

The common cause is that the order is walked once, at the start, against the task as it was first phrased — a 404. Three of the five steps are cheap at that moment and expensive to remember later, and nothing in the loop re-raised them when the task became something else.

## Query

Calls actually made, in order: typo3_project_describe; typo3_task_guide (operations); typo3_changelog_lookup ("TcaSchemaFactory", no type, no version); typo3_commit_message_guide x3. Never called: typo3_extension_describe, typo3_hint_lookup as a standalone call, typo3_documentation_lookup, typo3_configuration_lookup, typo3_rule_lookup, typo3_server_scope.

## Suggestion

Two things would have caught this. First, make the "did the brief carry everything" signal something a caller can read without interpretation — if omittedHints is the answer, say so in the same sentence, since base.md currently points at prose and warns off the structured key. Second, since typo3_task_guide is the step that classifies the task, let its answer state what the caller still owes when the changeType it was given produces a change: naming "this task writes PHP, so the deprecation sweep of step 5 applies, at 13.4 and 14.3" inside the brief would have reached me, where the same rule in the skill's reference file did not.
