---
id: D-ANS-034
title: A source outside this package answers JSON, or it did not answer
date: 2026-08-03
status: open
---

# D-ANS-034 — A source outside this package answers JSON, or it did not answer

**The lookups that reach a host read JSON and nothing else, so the fields they
hand back are the same on a hit, on a miss and on a failure.**

Two halves of one rule. What comes in is JSON or it is not an answer; what goes
out is the declared schema, so nothing downstream parses anything.

## Evidence

- The tracker's bot protection answers **200 with a 7.5 kB HTML challenge page**
  — measured on 2026-08-03 with a `Mozilla/5.0 …` agent, where this server's own
  and a plain `curl/…` both got JSON. A reader that accepts whatever arrives
  would have taken that page for an answer, and the session that hit it by hand
  spent a round trip finding out (`feedback/2026-08-02-145217`).
- The review server prefixes every response with `)]}'` so a browser cannot
  execute it as a script. Both sources were stripping and decoding for
  themselves, in two places, before this was one function.
- What the caller pays is calls, not tokens (`D-FBK-020`). A tool that answers
  in prose for one case and in fields for another costs a second reading every
  time, and the reading is the caller's model rather than a parser somebody
  wrote.

## Decided

- `Http\Fetch::decode()` is the one reader: it takes the XSSI guard off where
  there is one, decodes, and returns null for anything that is not an array. A
  page, a portal, a login form and an empty body are one answer to a source —
  the question was not answered — and which of them it was is not worth a
  branch.
- No HTML is scraped and no source is added that offers no JSON API. Where the
  answer would have to be read out of a page, it is not a lookup this server
  has; the recipe belongs in `knowledge/` and the reading stays with the caller.
- The answer's shape is the same in all three states. `status` is `answered`,
  `empty` or `unavailable`; the payload field is present and null where there is
  nothing; `unavailable` carries a `cause` from a closed list and a reason in
  words. A caller reads one field to know which of the three it has, and never
  the text.
- The failure vocabulary is two causes, not one. `source-not-answering` is a
  host that said nothing, `source-not-parseable` is a host that said something
  which was not the API — and the second is the interesting one, because it
  arrives with a 200.

## Assumed

- That JSON is what these hosts will keep offering. Both are the APIs their own
  web UIs are built on, so a format change would break more than this.
- That two causes are enough. A rate limit, an outage and a policy change all
  arrive as `source-not-answering`, which is honest and says nothing about which
  happened; where that turns out to matter, the cause list grows rather than the
  status.

## Wrong if

- A source worth having answers only HTML. Then this rule is what keeps the
  answer out, and the choice is between a parser nobody can hold true and a
  recipe in `knowledge/` — which is the trade this entry says to make, and the
  first real case is where it gets tested.
- A caller reads the text of one of these answers to decide something the data
  half already says. Then the shape is not carrying what it promises, and the
  fix is in the schema rather than in the prose.
