---
date: 2026-08-24T17:31:51+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# reading an abandoned change's inline comments stopped me repeating a rejected approach

## Observation

Task: pick another old open issue from forge.typo3.org, branch, and work it off. I ended up writing the patch for #35069 (addQueryString.exclude should take precedence over config.linkVars).

This is the thing that worked and must not be broken. typo3_forge_lookup on #35069 listed change 76606 under reviews[]. typo3_gerrit_lookup(change="76606", messages="people") came back with status ABANDONED and, decisively, the one inline comment on it: Benni Mack, on /PATCHSET_LEVEL, "wrong approach :(" — followed by Code-Review-1 and an abandon two years later.

Nothing in the checkout could have told me that. The Forge issue's own comments do not carry it; they only show the bot's two "patch set pushed" notes. Without the Gerrit read I would have written a patch in the shape that had already been rejected and handed it to the user as ready for review.

What I did with it: I still fixed the issue, because the reported defect is real and I confirmed it by reading PageLinkBuilder::calculateQueryParameters() — config.linkVars is merged back in with array_replace_recursive after getQueryArguments() has already honoured exclude. But I scoped the change deliberately differently: a single guarded exclusion at the one merge point, gated on addQueryString being active so config.linkVars alone is untouched, plus an extracted excludeQueryParameters() shared with getQueryArguments(). And I told the user in the handover that a prior attempt was rejected with "wrong approach" and that review friction is likely.

The two details that made this usable were the messages="people" filter, which dropped the CI noise, and the fact that comments come back with the change rather than needing a second call.

## Query

typo3_gerrit_lookup(change="76606", messages="people") after typo3_forge_lookup(issue="35069") listed it under reviews[]. Task: pick an old open issue and write a patch for it.

## Suggestion

Keep it exactly as it is: abandoned changes reachable, inline comments included, messages="people" available, and reviews[] on the Forge answer carrying the change numbers that make the jump possible. The chain typo3_forge_lookup(issue) -> reviews[] -> typo3_gerrit_lookup(change, messages="people") is the highest-value path I used all session.

If anything is added, surface the review verdict in the Forge answer itself — an issue whose linked changes were all abandoned with a negative review is a materially different candidate from one nobody has attempted, and right now that costs a second call to find out.
