---
date: 2026-08-12T09:26:54+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# the forge answer's reviews array exposed a sibling backport and an issue subject contradicting th...

## Observation

Task: review Gerrit change 95169 and say whether it is breaking. Recording what worked, so it is not refactored away.

Two calls, one round trip each, both first-try. No lookup in this session returned nothing usable, none had to be repeated with different arguments, and none merely restated a previous answer.

typo3_gerrit_lookup change="95169" returned the fetch ref refs/changes/69/95169/2 together with the remote. That is the feature added by f609ee69 off an earlier feedback, and it paid: I fetched the patch set with one git command, first try, without guessing the sharded refs path or reading anything to find it.

typo3_forge_lookup issue="109254" was the highest-value call of the session and changed the review twice over, on two fields I would not have thought to ask for separately:

- The `subject` contradicted the patch. The change is titled "[BUGFIX] Add link parsing in RTE figcaption"; the issue is titled "No link resolving in RTE table caption". Without that I would have reviewed a figcaption patch on its own terms and never asked why an issue about tables is fixed in figcaption. It sent me to RteHtmlParserTest.php:761, which shows the core persists table captions as `<figcaption>` inside `<figure class="table">` — the patch is right, but its commit message never says so, and that became a review point I could not otherwise have made. It also made me re-run the probe with the real table markup rather than my invented image markup.
- The `reviews` array named change 93202 on 13.4, same Change-Id, pushed in parallel with 95169. Nothing on the Gerrit side of 95169 mentions it and I had no other route to it. Fetching it showed the backport lives in a different file (fluid_styled_content/Configuration/TypoScript/Helper/ParseFunc.typoscript), and that the backport was pushed before the main patch merged — a process point in the review that exists solely because that field is in the answer.

The `notes` were all four Gerrit bot pings and carried nothing; `notes="people"` would have returned an empty set here. That is fine and not a complaint — the reviews array had already lifted the change numbers out of them, exactly as its description promises.

## Query

typo3_gerrit_lookup change="95169"; then typo3_forge_lookup issue="109254" (one call each, no repeats, no rephrasing)

## Suggestion

Keep the fetch ref on the gerrit answer and keep both `subject` and `reviews` on the forge answer; each removed a concrete wrong path here. One small addition: typo3_gerrit_lookup answered 95169 without mentioning that a change with the same Change-Id exists on another branch, and I only learned of 93202 through the forge. A change answer could carry its siblings by Change-Id, so a reviewer handed one branch's patch knows immediately whether the backport is already pushed — which is itself a reviewable fact.
