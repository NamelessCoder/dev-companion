# Ask the documentation team for the cheap paths

**Serves:** R-DOC-001
**Priority:** normal
**Waiting on:** whether to make these asks at all, and who makes them. Nobody
    has been asked. The five below were written from what was measured against
    the host on 2026-08-08 and from nothing else, so they are ready to send;
    what nothing in this checkout decides is whether this server's maintainer
    approaches the documentation team, and under whose name.

Ordered by what it costs them, cheapest first.

1. **Name the cheap paths in `llms.txt`** — `objects.inv`, `objects.inv.json`,
   `_sources/<page>.rst.txt`. A text edit, and it steers every well-behaved
   client off the 33 MB single-file page onto artefacts that are a fraction of
   the size and easier to parse correctly.
2. **Resolve the `singlehtml` contradiction.** `llms.txt` recommends
   `/singlehtml/` "for holistic understanding"; `robots.txt` disallows
   `*/singlehtml/*` for `User-agent: *` and allows it only for nine named AI
   bots. A client that reads `robots.txt` and identifies itself honestly — which
   is what `Http\Fetch` does — is steered away from the one artefact the AI
   declaration recommends, and that artefact is the most expensive thing on the
   host. Either allow it for everyone or stop recommending it.
3. **Publish the Sphinx `searchindex.js`.** It is a build artefact of the same
   run that already produces `objects.inv`, and one fetch per manual would give
   clients full-text search without a single request reaching `/search/`, which
   `robots.txt` disallows. This is the one that would remove load rather than
   shift it — everything published today indexes names, titles and anchors and
   never the text of a page.
4. **Long-lived cache headers on what cannot change.** A published changelog
   entry is immutable; `public, no-cache` forces a revalidation round trip on
   every read of it. `immutable` with a long `max-age` on
   `Changelog/<released>/` would cost the host nothing and save it the round
   trips.
5. **A per-major changelog index as JSON**, if anything beyond the above is
   wanted. Lowest priority: `objects.inv` already answers it, and asking for a
   new endpoint before using the existing one is the wrong order.

The whole shape of it is that **agent-friendly and resource-friendly point the
same way here.** Every artefact that makes an agent cheaper to serve also makes
it better answered, and the only thing standing between the two is that nobody
has written down which paths those are.

Nothing is broken while this waits. The lookup answers today from `objects.inv`
and revalidates against the `ETag`, which is what `D-ANS-065` measured; the asks
would make it cheaper for both sides rather than possible.

## What is not established

- What any of this costs at 14.4 and other majors. `robots.txt` names only 13.4,
  12.4, 11.5, 10.4 and main in its Allow list; for `User-agent: *` that list is
  irrelevant, but it says something about which versions the host expects to be
  read.
- Whether the documentation team wants any of it.
