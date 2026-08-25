---
date: 2026-08-24T16:32:20+00:00
category: tool-gap
status: closed
closed: 2026-08-25
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Both skills matching this task stayed shut for the whole session; I did their work by hand

## Observation

Task: "bitte suche forge issues im asset renderer bereich", then "suche einen den man einfach fixen kann und es mit tests belegen kann" — find a Forge issue in the asset renderer area that is easy to fix and provable with tests, then fix it. I picked #106584 and carried it to a finished patch with tests, a changelog entry and a commit message.

I activated ZERO skills. Not one Skill call in the entire session. Two listed skills describe exactly the two halves of what I did:

- typo3-core-issue-triage ("Say what is still true about an open issue on forge.typo3.org") — this is precisely the first half: six typo3_forge_lookup searches, then reading four candidate issues to decide which was still live, still unclaimed, and still a defect.
- typo3-core-patch-development ("Write a TYPO3 core patch and carry it to review: the changelog entry, the project's checks, the push to Gerrit") — precisely the second half.

The moment one would have had to activate is the user's second message, verbatim: "ok suche einen den man einfach fixen kann und es mit tests belegen kann". At that point I had no files open beyond the checkout; I went straight to typo3_forge_lookup with open=stale and never considered a skill.

Why they stayed shut, as best I can reconstruct:
1. The first request was worded as a lookup ("suche forge issues"), so I reached for the tool, and never re-evaluated when the task grew into a patch.
2. The server's own startup instructions name tools by function ("typo3_task_guide for the workflow the task belongs to") but I read that as a tool to call, not as a reason to open a skill, and I never called typo3_task_guide either.
3. A system-reminder in this session says "The task skills installed in this project are stale; run typo3-dev-companion update". I did not act on it and it did not make me more likely to open one.

The cost was real and measurable. Because typo3-core-patch-development stayed shut, I re-derived by hand: the changelog RST format (by cat-ing a neighbouring Important-*.rst from Changelog/14.3/ and copying its shape), the reference-label convention with the unix timestamp, the Important-vs-Breaking section rules, the 72-character commit-message limit and footer order, and which runTests.sh suites to run. All of that came from the repository's AGENTS.md and from reading neighbouring files, not from the skill that exists to carry it.

I also got the scope wrong in a way that skill would plausibly have caught — I implemented two of the three ViewHelpers #106584 lists and the user had to correct me (filed separately as "Cover every point a Forge issue lists").

## Query

Session with two user turns: "bitte suche forge issues im asset renderer bereich" and "ok suche einen den man einfach fixen kann und es mit tests belegen kann". Client: Claude Code, VSCode extension. Skills were listed in the system prompt and none was invoked.

## Suggestion

Both skill descriptions are written for a request that names the activity ("triage this issue", "write a patch"). Neither matches how the work actually arrived, which was a search request that grew into a patch. Consider making the descriptions match the entry point rather than the activity:

- typo3-core-issue-triage: add the shape "find me an issue to work on / which of these is still worth fixing / is anyone on this already" — picking an issue out of the backlog is triage, and its description currently only covers verifying one issue you already hold.

Trimmed on 2026-08-25 to the part above, which is D-SKL-076. The proposal for typo3-core-patch-development and the reading of the instruction block were answered against D-SKL-033.
