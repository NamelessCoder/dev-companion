---
id: D-ANS-033
date: 2026-08-03
status: open
---

# D-ANS-033 — The review server is read anonymously, and the answer says what that leaves out

**`typo3_gerrit_lookup` reads review.typo3.org without a credential, so an empty
answer means nothing public names the issue rather than that nobody fixed it.**

The question is asked before every core task and answerable from no checkout.
What made it a tool rather than a recipe is `D-FBK-027`; what this entry settles
is the shape of the answer and the boundary it stops at.

## Evidence

- Four sessions in one week answered it by hand: `feedback/2026-08-02-144511`,
  `144848`, `145217`, `145230`. The cost is two round trips before anything can
  be read — the search, then the `)]}'` prefix the API opens with so a browser
  cannot execute the response as a script.
- Verified live while building. `issue: 110348` answers with change 95040,
  `[TASK] Deprecate AssetCollector media handling`, MERGED on `main`;
  `change: 89011` resolves to the phpunit raise of 2025-04-09.
- `issue: 105403` answers **empty**, and the checkout it was asked from carries
  a patch for exactly that issue. That patch was pushed `%private`, which an
  anonymous read cannot see. The empty answer is therefore about the review
  server rather than about the world, and a caller that reads it as "nobody has
  fixed this" has been misled by a true statement.
- The second crawler is what showed there should be one. `Manual\Documentation`
  carried a `curl` block, and this source was written as a copy of it before
  either was used.

## Decided

- Anonymous, and read-only. No credential is asked for and none is stored, so
  what a reviewer sees, what a private change carries, and every vote and CI
  result stay outside. `server-scope.json` names those rather than the sentence
  it carried before — "The server talks to nothing over the network" — which had
  not been true since the manuals were indexed.
- Three answers, not two. `answered`, `empty` where the server answered and
  knows nothing, and `unavailable` split into `source-not-answering` and
  `source-not-parseable`: a captive portal returns 200 with HTML, and skipping
  to the first bracket would parse a login page as a review.
- `message:<issue>` is the query. The issue number lives in the commit message,
  where `Resolves:` and `Related:` put it, so this asks "has somebody already
  fixed this" rather than "is there a change called this".
- The reading moves to `Http\Fetch`, which both outside sources now go through:
  three seconds to connect, eight in total, three redirects, and this server's
  own user agent. It is a policy rather than a duplicated block, and the agent
  is the part that matters — bot protection challenges browser-shaped agents and
  lets a plain client through, which is what the third source will need.

## Assumed

- That the anonymous REST API stays open. It is what the project's own web UI
  reads, so an outage is likelier than a policy change; both come back as
  `source-not-answering`, which is honest but says nothing about which happened.
- That `message:` matches what a caller means. A change that only mentions the
  issue in a comment rather than in the message is not found, and neither is one
  whose author forgot the trailer.
- That the XSSI prefix stays `)]}'`. It is matched as a fixed string; a Gerrit
  release that changes it makes every answer `source-not-parseable` rather than
  wrong, which is the failure direction worth having.

## Wrong if

- A run reads an empty answer as "no patch exists" and acts on it. The sentence
  is in the tool's own text and in this entry; if it is still read that way, the
  answer needs to carry the private-change caveat as data rather than as prose.
- The review server starts rate-limiting or requiring a credential for the
  searches this makes. Then the tool answers `source-not-answering` often enough
  that the call buys nothing, which is `D-FBK-027`'s own **Wrong if**.
- A second host needs a fetch policy this one cannot express. `Http\Fetch` has
  one timeout pair and one agent for everybody, and the first host that needs
  otherwise is where that stops being a policy and becomes an obstacle.
