---
date: 2026-08-25T10:52:03+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# gerrit-workflow read whole plus one gerrit_lookup carried the checkout end to end

## Observation

Task: fetch review.typo3.org change 95369 into a core checkout on main, compare it against local uncommitted work doing the same thing, and merge ours into it.

This is the part that worked and must not be broken.

Before the skill ran I had already gone to review.typo3.org with raw curl twice — `changes/?q=message:110503+project:Packages/TYPO3.CMS` and `changes/95369/detail?o=CURRENT_REVISION&o=CURRENT_COMMIT`. One `typo3_gerrit_lookup(change="95369")` returned strictly more than both of those together and in one round trip: the current patch set number (2) with its commit hash, the fetch ref `refs/changes/69/95369/2` together with the remote it lives on, the target branch, the label votes, the three review comments, the linked Forge issue with its tracker state, and `releaseLines` naming main as development and 14.3 (until 2029-06-30) and 13.4 (until 2027-12-31) as maintained.

The comments were the part I would not have read on my own, and they changed my output: Garvin Hicking had raised backporting, Elias Häußler had answered that he had not considered it, and the thread was still open. My review note and my `Releases:` recommendation were both written against that thread instead of past it.

`typo3_rule_lookup(documentId="core/contribution/gerrit-workflow")` returned the page whole and carried the procedure end to end with no follow-up search. Two things in it I would not have got right alone:

- The fetch ref lives on the review server and not on the mirror the clone fetches from. The page states it flatly, names `remote.origin.pushurl` as what to fetch from, and even dates the measurement. Without it I would have run `git fetch origin refs/changes/...` and got "couldn't find remote ref" — a failure that reads like a stale patch rather than a wrong remote.
- The `Releases:` policy, which corrected an answer I had already given from branch dates alone.

The document also warned against the contribution guide's own cherry-pick page writing onto `main`, and prescribed the `review/<change>` branch instead. I used exactly that.

Reading the whole document rather than searching it was the right instruction and I would repeat it. `typo3_project_describe` is where I learned the document ids exist at all — the `guides` array is the only list of them I saw, and my client showed no MCP resource list.

## Query

typo3_gerrit_lookup(change="95369"); typo3_rule_lookup(documentId="core/contribution/gerrit-workflow"); typo3_project_describe(). Preceded by two hand-rolled curl calls against review.typo3.org that the first of these made redundant.

## Suggestion

Keep `releaseLines` and the comments in the gerrit answer — those two are why the call beat curl, and a thinner answer would have sent me back to the API by hand.

One addition worth considering: the answer told me patch set 2 was current but not that patch set 2 was rebased or how far its parent had drifted from the target branch. I established that myself with `git merge-base --is-ancestor`. A field saying which commit the patch set sits on, and whether that commit is still an ancestor of the target branch, would say "this still applies" before anything is fetched.
