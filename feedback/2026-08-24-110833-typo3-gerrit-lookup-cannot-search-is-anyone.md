---
date: 2026-08-24T11:08:33+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup cannot search: "is anyone already working on this file" went to the REST API ...

## Observation

Task: triage a bug report about EXT:impexp against the core checkout — establish whether it is already reported on Forge and whether a patch for it is already under review — then write the fix.

typo3_gerrit_lookup takes an issue number or a Change-Id/change number and nothing else. The question a triage actually starts with is neither: it is "is there an open change touching typo3/sysext/impexp, and has anybody ever tried to fix this before". I read the description, concluded the tool could not answer it, and went to https://review.typo3.org/changes/ with curl myself:

- status:open + file:^typo3/sysext/impexp/.* — 21 open changes, which is what let me say in the review that none of them touches writePages(), writePagesOrder() or flatInversePageTree().
- full-text "flatInversePageTree" and "writePagesOrder" — zero changes, ever. That negative is what established that nobody has attempted this fix, and no tool here could have told me.
- full-text "impexp translation" — which found change 71374/81822, the merged patch that introduced the flat pagetree markers.

The assumption that the tool could not do it held: nothing in its schema takes a query or a path. Once I had the Forge issue (#93470) the tool was exactly right — typo3_forge_lookup returned the change numbers hanging off the issue, and typo3_gerrit_lookup on my own Change-Id correctly answered "nothing on the server", with a caveat about anonymous reads that I used verbatim in the review report.

So the gap is only the search direction, and it is the direction a triage and a pre-push review both open with.

## Query

Not a call to this server — what I ran instead: curl -s "https://review.typo3.org/changes/?q=status:open+file:%5Etypo3/sysext/impexp/.*&n=50&o=CURRENT_REVISION", then the same endpoint with q=flatInversePageTree, q=writePagesOrder, q=impexp+translation, q=impexp+pagetree. The calls I did make: typo3_gerrit_lookup change="I55fced4b84048a812adc7dca6d7f66261ef147b5" (empty, correct), typo3_forge_lookup issue="93470" (which carried the review changes).

## Suggestion

A search argument on typo3_gerrit_lookup beside issue and change: free text over commit messages, and a path or file filter. Gerrit's own query language already does both (file:^path, plain terms, status:open), so this is passing a query through rather than new knowledge. Two answers would have replaced my three curl calls: "open changes touching this path" for the review surface the checkout cannot see, and "every change that ever mentioned this identifier" for the negative that says nobody has tried. If the search stays out, the description saying so in one line would still help — I had to infer it from the input constraint.
