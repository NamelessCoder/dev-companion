---
date: 2026-08-24T10:04:58+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# The forge-to-gerrit chain reached an abandoned patch that settled the design question

## Observation

Task: review Gerrit change 95375, then rework it. This reports what worked and must not be broken.

The single most valuable thing this server did was let me walk from a change to an issue to a second issue to an abandoned patch, and land on a maintainer's own reason for not doing what I was being pushed toward.

The chain, four calls:
1. typo3_gerrit_lookup(change=95375, messages=people) returned one comment by Josua Vogel naming review 95015 as making the patch "superfluous".
2. typo3_gerrit_lookup(change=95015) returned its commit message, which resolves TWO issues: #110331 and #107080. The second was invisible from the change under review.
3. typo3_forge_lookup(issue=107080, notes=people) returned "Form prototype not selectable with blank form" plus a 2025 note by the same person: "Creating an empty form always refers to the default prototype and the BlankForm.yaml template. I don't think this template is necessary. I would therefore remove this fixed dependency." It also listed a review I had not seen: change 90176.
4. typo3_gerrit_lookup(change=90176, messages=people) returned status ABANDONED and an unresolved comment: "theoretically the initial form shouldn't contain a renderable... big problem: the form editor js heavily depends on this, e.g. if the root element is selected, the first page is shown. IMO this should be resolved (maybe on the long way)".

That is the answer to the question the user had asked me twice — why a blank form needs a template at all. Somebody had already tried removing the dependency and abandoned it, with a stated blocker. Nothing in the checkout records that: 90176 is abandoned, so it has no commit on any branch, and #107080 is not referenced from #110493 or from the patch.

I did not take the comment on trust. I verified the blocker in the checkout and found it still live on main at Build/Sources/TypeScript/form/backend/form-editor/core.ts:1329, which reads formDefinition.get('renderables')[0] when the root element is selected and dereferences it four lines later — undefined without a Page.

Two other things that held and were load-bearing:
- typo3_rule_lookup(query="changelog entry review readiness") returned "A casual bug fix owes no entry... Demanding one of a BUGFIX that removes nothing public is a review defect of its own." Without that I would have reported a missing changelog entry as a finding.
- typo3_hint_lookup(id="javascript-unit-tests") said tests run against built output and a package without a tests/ directory is discovered as nothing. Both exactly right; both saved a confused debugging round when I added the first test under Build/Sources/TypeScript/form/.

Across 16 typo3_* calls in this session there were zero errors, zero empty results, and zero calls I had to retry with different arguments.

## Query

typo3_gerrit_lookup(change="95375", messages="people") then typo3_gerrit_lookup(change="95015") then typo3_forge_lookup(issue="107080", notes="people") then typo3_gerrit_lookup(change="90176", messages="people"). Also typo3_rule_lookup(query="changelog entry review readiness") and typo3_hint_lookup(id="javascript-unit-tests", targetVersion="15").

## Suggestion

Keep all of it. Specifically keep: comments and their unresolved flag on gerrit_lookup; the reviews array on forge_lookup, which is what exposed abandoned change 90176; abandoned changes being returned at all rather than filtered as dead; and the full commit message on a change, which is what revealed the second Resolves: line.

One small addition would have shortened it: when a change's commit message names issues, gerrit_lookup could list them as it lists the relation chain, so step 2 to step 3 does not depend on me reading the message body carefully. I nearly missed the second Resolves: line, and the whole chain hung on it.
