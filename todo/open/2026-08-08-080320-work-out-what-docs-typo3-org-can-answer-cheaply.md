# Work out what docs.typo3.org can answer cheaply, and what to ask its team for

**Serves:** R-DOC-001
**Priority:** normal

Reuse the connection across the reads one lookup makes. `Http\Fetch` opens a
handle per call and closes it, and one search fetches four indexes and up to six
pages — ten connections to one host. Measured with `curl(1)` on 2026-08-08:
twenty reads over twenty connections is 1.96 s, the same twenty over one
keep-alive connection is 415 ms, 98 ms against 21 ms each. **Whether that
survives PHP's curl is what the step has to establish first** — a handle kept on
the `Fetch` instance and reused, or `curl_share`, and the answer is a
measurement through this class rather than through the binary. `Fetch` is one
policy for every host this server reads, so what it holds open it holds open for
the tracker and the review server too.

What was taken already, on this branch: `Http\Fetch` asks for compression and
returns the `ETag`, and `Manual\Documentation` searches the `objects.inv` each
manual publishes, held under that tag and revalidated, instead of the links of
the rendered root. What it cost and what it bought is `D-ANS-065`, and
`D-ANS-032` carries what the new corpus did to the dilution reference.

## What is still there to take

- **Forward changelog coverage.** The installation ships every changelog down to
  7.0 and nothing above its own major, so a session upgrading to a version it
  has not installed cannot read the entries it is upgrading to.
  `Changelog-<major>` in the inventory of `cms-core` names 3800 entries over 55
  version directories up to 15.0, each with its file name and its stated title —
  the two fields `Changelog::entries()` searches on disk — and
  `_sources/<page>.rst.txt` gives back the RST of a matched entry byte for byte,
  `.. index::` included, so `Changelog::read()`, `identifiers()` and `removal()`
  run on it unchanged. That is the gap `skills/typo3-extension-upgrade/SKILL.md`
  names in its own words. It is a piece of work of its own and probably its own
  todo.

## What to ask the documentation team for

Nobody has been asked, and whether to ask is not something this repository can
answer. Ordered by what it costs them, cheapest first.

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

## What is not established

- Whether keep-alive survives PHP's curl the way it survived `curl(1)`. That is
  the step above.
- What any of this costs at 14.4 and other majors. `robots.txt` names only 13.4,
  12.4, 11.5, 10.4 and main in its Allow list; for `User-agent: *` that list is
  irrelevant, but it says something about which versions the host expects to be
  read.
- Whether the documentation team wants any of it. The asks are written from
  measurements and from nothing else.
