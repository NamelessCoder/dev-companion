# Work out what docs.typo3.org can answer cheaply, and what to ask its team for

**Serves:** R-DOC-001
**Priority:** normal
**Branch:** todo/work-out-what-docs-typo3-org-can-answer-cheaply
**Claimed:** 2026-08-08

Take the one change that is measured and carries no open question: teach
`Http\Fetch` to ask for compression, and rebuild the index
`Manual\Documentation` searches from `objects.inv` rather than from the links of
the rendered manual root. The first is one curl option against a host that
answers `Content-Encoding: gzip` and a page that is 169 kB plain and 19.9 kB
compressed. The second gets what `R-DOC-001` already calls a table of contents
out of a documented artefact instead of out of parsed HTML, with the stated
title of every page beside its path and a conditional GET to revalidate it.
Measure both against the four manuals `Documentation::DOCUMENTS` names before
anything is written into the requirement. What to ask the documentation team for
is below and is deliberately not the step: it is written down so the ask, when
it is made, is made from numbers rather than from a wish.

## What was measured, 2026-08-08

All of it against `docs.typo3.org` from one machine, with the plain `curl/8.5.0`
agent `Http\Fetch::PLAIN_AGENT` already uses for its retry. Sizes are what went
over the wire.

### What the host already publishes

| Artefact                     | Wire      | What it is                                 |
| ---------------------------- | --------- | ------------------------------------------ |
| `objects.inv`                | 603 kB    | Sphinx inventory, zlib, every page + title |
| `objects.inv.json`           | 641 kB gz | The same as JSON, 3.9 MB unpacked          |
| `_sources/<page>.rst.txt`    | 3.7 kB    | The page's own RST, `.. index::` included  |
| Rendered entry page          | 51 kB     | The same entry as HTML                     |
| `Changelog-14-combined.html` | 145 kB    | Every 14 entry as a link with its title    |
| `singlehtml/`                | 3.3 MB gz | The whole manual, 33.5 MB unpacked         |

`objects.inv` is there for every manual and every version tried:
`reference-coreapi` at main 290 kB and at 13.4 264 kB, `reference-typoscript` 92
kB, `reference-tca` 43 kB, `typo3fluid/fluid` 12 kB.

`_sources` gives back the file the page was rendered from, byte for byte. For
`Changelog/15.0/Breaking-109998-RemovedJQuery` that is the RST this repository
already parses, `.. index:: Backend, PHP-API, TSConfig, NotScanned, ext:backend`
included — so `Changelog::read()`, `identifiers()` and `removal()` run on it
unchanged. It is not HTML being scraped, which is what the first **Wrong if** of
`D-ANS-034` is about; it is the same artefact from a second place.

### What one question costs

- `objects.inv` for `cms-core` at main: **224 ms** to fetch, **18 ms** to
  `zlib_decode` and parse in PHP. It yields **3800 changelog entries over 55
  version directories up to 15.0**, each with its file name and its stated title
  — the two fields `Changelog::entries()` searches on disk.
- **The connection dominates everything else.** Twenty RST sources over twenty
  connections is 1.96 s; the same twenty over one keep-alive connection is **415
  ms**. 98 ms per entry against 21 ms. `Http\Fetch` opens a handle per call and
  reuses nothing.
- Compression is offered and not asked for. `Vary: Accept-Encoding` and
  `Content-Encoding: gzip` on every artefact tried; the TYPO3 Explained root is
  169 kB plain and **19.9 kB** compressed. `Http\Fetch` sends no
  `Accept-Encoding`, so this server takes the 169 kB on every manual lookup.
- Revalidation works. Every artefact carries an `ETag` under
  `Cache-Control: public, no-cache`, and `If-None-Match` answers **304 with a
  zero-byte body**. So a held index costs one round trip and no payload.

### What the inventory carries beyond page names

| Manual               | std:doc | std:label | std:title | std:confval |
| -------------------- | ------- | --------- | --------- | ----------- |
| `cms-core` main      |    3876 |      3958 |      5161 |           4 |
| `reference-tca` main |      92 |      1602 |       360 |         742 |

The TCA reference indexes **742 configuration properties as addressable
objects**, 737 of them with a real anchor — `columns-config-type` resolves to
`Columns/Index.html#confval-columns-config-type` and carries `type` as its
display name. Five point at `404.html` and were not chased. That is a semantic
index of TCA, published, versioned, and reachable in one 43 kB request.

### What is not there

`llms-full.txt`, `sitemap.xml`, a per-manual `llms.txt`, `searchindex.js`,
`search.html`, `genindex.html` and `_static/searchtools.js` all answer 404. So
there is **no full-text index and no search a client may call**: `/search/` is
disallowed by `robots.txt`, and the Sphinx search index that would answer the
same question offline is not published. Everything above indexes names, titles
and anchors — never the text of a page.

### The contradiction worth naming

`llms.txt` at the root, dated 2025-03-22, is a licence and crawler declaration
rather than a map. Its "Recommended Paths for AI Use" closes with `/singlehtml/`
versions "for holistic understanding". `robots.txt` disallows `*/singlehtml/*`
for `User-agent: *` and allows it only for the nine named AI bots.

So a client that reads `robots.txt` and identifies itself honestly — which is
what `Http\Fetch` does and why its docblock says so — is steered away from the
one artefact the AI declaration recommends. And that artefact is the most
expensive thing on the host: 33.5 MB unpacked for `cms-core` at main, against
603 kB for the inventory and 3.7 kB for a single source file. The cheap paths
are allowed, undocumented, and named nowhere.

That is the whole shape of the problem: **agent-friendly and resource-friendly
point the same way here.** Every artefact that makes an agent cheaper to serve
also makes it better answered, and the only thing standing between the two is
that nobody has written down which paths those are.

## What this repository could take today

Four things, none of which needs anybody outside to agree:

1. `CURLOPT_ENCODING` in `Http\Fetch`. One line, 8.5× less payload on the manual
   lookup, and less egress for the host.
2. `objects.inv` as the index `Manual\Documentation` searches, instead of the
   links parsed out of the rendered root. Same corpus, documented format, plus
   the stated title of every page.
3. Handle reuse across the reads one lookup makes. The excerpt phase fetches up
   to six pages per call today, each on its own connection.
4. Forward changelog coverage, evaluated on 2026-08-08 in this session: the
   installation ships everything down to 7.0 and nothing above its own major, so
   `objects.inv` plus the RST of the matched entries answers a target version
   that is not installed. That is the gap
   `skills/typo3-extension-upgrade/SKILL.md` names in its own words.

## What to ask the documentation team for

Ordered by what it costs them, cheapest first. Nobody has been asked yet.

1. **Name the cheap paths in `llms.txt`** — `objects.inv`, `objects.inv.json`,
   `_sources/<page>.rst.txt`. A text edit, and it steers every well-behaved
   client off the 33 MB page onto artefacts that are 1/50th the size and easier
   to parse correctly.
2. **Resolve the `singlehtml` contradiction** — either allow it for
   `User-agent: *` or stop recommending it. As it stands the honest clients get
   the worst of both.
3. **Publish the Sphinx `searchindex.js`.** It is a build artefact of the same
   run that already produces `objects.inv`, and one fetch per manual would give
   clients full-text search without a single request reaching `/search/`. This
   is the one that would remove load rather than shift it.
4. **Long-lived cache headers on what cannot change.** A published changelog
   entry is immutable; `public, no-cache` forces a revalidation round trip on
   every read of it. `immutable` with a long `max-age` on
   `Changelog/<released>/` would cost the host nothing and save it the round
   trips.
5. **A per-major changelog index as JSON**, if anything beyond the above is
   wanted. Lowest priority: `objects.inv` already answers it, and asking for a
   new endpoint before using the existing one is the wrong order.

## What is not established

- Whether the title `objects.inv` carries is as good as the link text
  `Documentation::links()` reads today. Step one of the paragraph above is what
  measures it, and `R-DOC-002` is what the answer has to keep true.
- Whether keep-alive survives PHP's curl the way it survived `curl(1)`. The 415
  ms was measured with the binary, not through `Http\Fetch`.
- What any of this costs at 14.4 and other majors. `robots.txt` names only 13.4,
  12.4, 11.5, 10.4 and main in its Allow list; for `User-agent: *` that list is
  irrelevant, but it says something about which versions the host expects to be
  read.
- Whether the documentation team wants any of it. The asks above are written
  from measurements and from nothing else.
