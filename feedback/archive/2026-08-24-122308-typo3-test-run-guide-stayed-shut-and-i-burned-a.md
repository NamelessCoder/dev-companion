---
date: 2026-08-24T12:23:08+00:00
category: idea
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# typo3_test_run_guide stayed shut and I burned a container run on runTests argument order

## Observation

Task: review Gerrit change 95179 and work off its review comments; that ended in running the frontend functional suite against a data provider I had edited.

Trimmed on 2026-08-24. The question this opens with is answered: typo3_test_run_guide does cover argument order, and re-run with this session's own suite and path it returns `-s functional -d sqlite -- <path/to/Test.php>` as the targeted form and `-s unit -- --filter fixPermissionsSetsGroup <path>` as a worked example. The description line the suggestion asks for is refused in D-KNW-112, because this session had the tool as a bare name and never fetched the schema the line would sit in — D-AUD-003. That the review skill names this tool at the verification step and did not open is feedback/2026-08-24-122413's card. What is left is what no source here states:

- Where runTests.sh stops reading its own options. `getopts` stops at the first non-option word on all four covered branches, so a path before `--` reaches phpunit along with everything after it and phpunit reads `--filter` as a test file. The corpus says what happens after the separator and nothing about a word before it, while the sibling failure in the same block is named by the line it prints.

I wanted to run one data-provider test in one file. I wrote:

  CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite typo3/sysext/frontend/Tests/Functional/ContentObject/ContentObjectRendererTest.php -- --filter stdWrap_override

and got 'Test file "--filter" not found', a failed suite and a wasted container spin-up. The working form puts the path AFTER the `--` block:

  CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- --filter stdWrap_override typo3/sysext/frontend/Tests/Functional/ContentObject/ContentObjectRendererTest.php

I guessed the first form from the repository's AGENTS.md, which shows `-s unit <file>` and `-s unit -- --filter someTestMethodName` as two separate examples and never combines them. That is exactly the case where the rule and the case in front of me diverge.

typo3_test_run_guide was in my deferred tool list the whole time and I never loaded its schema. I read the name as "which suite should I run for this change" — a routing question I already had answered from AGENTS.md — not as "what does the argument order of this dispatcher have to be". So a tool that exists stayed shut on the one question it might have settled. I am reporting the assumption rather than the fact, because I still do not know whether it covers argument order.

Everything else in the run was clean and cheap: -s checkRst SUCCESS, the filtered run 13 tests / 13 assertions, the whole ContentObjectRendererTest.php 727 tests / 840 assertions in 10.7s on sqlite, -s cgl -n 0 of 6318 files. No complaint about the dispatcher itself.

## Query

Never called. The failing command was CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite <path> -- --filter stdWrap_override; the succeeding one CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- --filter stdWrap_override <path>.

## Suggestion

If typo3_test_run_guide already answers argument order, say so in its first description line in those words — "the order arguments have to stand in after -- , and where a path goes relative to a filter" — because an agent that has AGENTS.md open reads the current name as suite routing and skips it. If it does not answer it, it should: the single most common failure mode of runTests.sh for an agent is putting the file path before the `--` separator, and it costs a full container cycle each time. A worked example combining -s functional, -d sqlite, --filter and a path in one line would have removed the whole detour.
