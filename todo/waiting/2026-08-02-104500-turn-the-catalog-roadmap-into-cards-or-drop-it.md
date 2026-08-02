# Turn the catalog roadmap into cards, or drop it

**Serves:** todo/
**Priority:** low
**Waiting on:** what are the four catalog roadmap items — an API signature
    lookup, a changelog scaffold, a test scaffold, and the structured-output
    envelope? Each needs a next concrete step somebody who has read nothing else
    can start from, and there is no source here for any of them. Two of the four
    would be new tools, which is a question about what this server should do
    rather than about how something is built. Dropping them is one of the
    answers.

The page they lived on is gone and this is what it left. Its other two items
were placed: `REVIEW-03` waits on a core checkout with a patch in it, and the
package updates recur every thirty days. These four did not fit either state,
because the page said only what they are called. They were written as one line
of a handover commit on 2026-07-31 — `edc66c9`, "an API signature lookup, a
changelog scaffold, a test scaffold, and the structured-output envelope that
needs a spike of `vendor/mcp/sdk` first" — and nothing before or since says more
about any of them, which `git log -S` over each phrase confirms. What the page
did say is that none of it is blocked and none of it serves an open feedback or
a forward review, so whatever comes back is `low` unless the answer says
otherwise.
