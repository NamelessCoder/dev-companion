---
date: 2026-08-08T22:43:33+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# the triage skill says how to list a backlog but not which row is cheap to settle

## Observation

Task, verbatim and in German: "bitte suche dir ein altes issue und fixe es" — find yourself an old issue and fix it. I activated typo3-core-issue-triage, which fitted, and its ordering was right end to end.

The step it left me was choosing which of the candidates to spend the session on. `typo3_forge_lookup(open="stale", tracker="Bug", updatedBefore="2019-01-01")` gave 37 total with 30 in the page, and the skill is clear that age is not a finding and that picking is the user's call — with an escape hatch for "just find me something", which this request was, so I picked. But neither the skill nor any lookup says what separates a candidate that a checkout can settle in one session from one that cannot, and that is the whole decision when the answer has to end in a patch.

I worked out a criterion of my own and would work out the same one again: prefer a report whose mechanism sits in one class, where a test layer can hold it, and whose subsystem still exists on the branch. Reaching it cost three round trips of three `typo3_forge_lookup(issue=…)` calls each — nine issues read — plus a `git fetch` of the abandoned Gerrit change 53819 out of the review server to rule one of them out. Of the nine, four were decided cheaply once read and only because of things the reading happened to surface: #83913 died on a maintainer note in the comments, #82228 on an abandoned patch whose diff showed the proposed semantics were what the `m` modifier already does, #81102 on being browser-only, #83848 on the report never carrying enough to try.

That reading was not wasted, but three of the four were decidable from signals the answer already carries — `reviews` being non-empty, the comment count, a category naming a subsystem that no longer exists — and I had to open each issue whole to see them.

## Query

typo3_forge_lookup(open="stale", tracker="Bug", updatedBefore="2019-01-01", limit=30), then typo3_forge_lookup(issue=...) for 82228, 85456, 81102, 79571, 83848, 83913, 75145, 58705, 81853

## Suggestion

Give the triage skill a short section on what makes a stale candidate settleable, so a session picking from a backlog does not re-derive it: a mechanism reachable in one class beats one in an interaction of subsystems; a suite layer that can hold the behaviour beats a browser-only symptom; a category naming a subsystem that has since been removed is a verdict before the report is read; an issue whose reproduction is a TypoScript snippet or a Fluid template is cheaper than one whose reproduction is a multi-tree installation. Two of those signals are already in the tracker answer, so `typo3_forge_lookup(open=…)` could carry them per row without a second call — whether the issue has any change on the review server, and how many human comments it has — which is what would let a session narrow thirty rows to three without reading nine issues whole.
