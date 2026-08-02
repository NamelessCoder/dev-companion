---
date: 2026-08-02T14:43:15+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403) and write the commit me...

## Observation

Task: fix a TYPO3 core bug in the Fluid image ViewHelpers (Forge #105403) and write the commit message for it.

The tool's own description promises that "fenced code, indented blocks, list structure, and long URLs left intact". It did not keep that promise for a plain "Executed commands:" block. Every one of the four command lines was reflowed into a single running paragraph:

  Executed commands: CI=true ./Build/Scripts/runTests.sh -s functional --
  typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php
  CI=true ./Build/Scripts/runTests.sh -s cgl -n CI=true
  ./Build/Scripts/runTests.sh -s phpstan

The commands are no longer separable — "-s cgl -n CI=true" reads as one invocation. I called the tool twice in the session (once for the initial message, once for the final amended one) and had to hand-restore the block both times, which defeats the point of asking for a ready-to-commit draft.

Secondary observation on the same call: checks returned {"level":"info","code":"no-issues-found"}. When I committed the hand-restored message, the core commit-msg hook rejected it with "The maximum line length of 72 characters is exceeded" — my indented continuation line was 73 characters. The 72-character rule and the "indented blocks left intact" rule are in direct conflict for any block whose lines are long, and the tool silently resolves that conflict by destroying the block instead of reporting it.

## Query

changeType=BUGFIX, summary="Hint at public URIs passed to f:image src", issue=105403, relatedIssues=[105953,100696], releases=[main], body ending with an indented block: "Executed commands:\nCI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php\nCI=true ./Build/Scripts/runTests.sh -s cgl -n\nCI=true ./Build/Scripts/runTests.sh -s phpstan"

## Suggestion

Preserve hard line breaks inside a block that is indented or that follows a "...:" lead-in line, and wrap only the prose paragraphs. Where a line inside such a block cannot fit 72 characters, do not reflow it silently — emit a check at warn level naming the line, so the caller can shorten the path or split the command themselves. A draft that is described as ready to commit should also be one the core commit-msg hook accepts; running the same length rule the hook runs, over the exact text being returned, would have caught the 73-character line before the commit was attempted.
