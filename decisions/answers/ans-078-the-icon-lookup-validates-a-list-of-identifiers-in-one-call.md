---
id: D-ANS-078
date: 2026-08-12
status: open
---

# D-ANS-078 — The icon lookup validates a list of identifiers in one call

**`typo3_icon_lookup` takes several identifiers at once and answers each one
registered or not, beside the ranked search it already answers.**

Validating three identifiers read out of one Fluid template cost three calls,
and each of them answered the question in one field and spent the rest of the
payload ranking neighbours nobody had asked about.

## Evidence

- `feedback/2026-08-11-055257` counts it: one exploratory call, which was the
  right shape, and three existence checks after it. Each answered
  `exactMatch: true`; `actions-open` carried 22 suggestions with it.
- The cost is this server's own rule. The `instructions` sent at initialize say
  to call this tool before choosing or emitting a backend icon identifier, and
  four skills repeat it, so a change touching three icons pays three calls
  because we told it to. That is `D-FBK-027`'s "paid repeatedly" in its sharpest
  form: the repetition is not the session's habit, it is our instruction.
- The tool has no shape for it. `query` is one string,
  `Icons::looksLikeIdentifier()` decides per call which mode the answer is in,
  and two identifiers in one query fall to the ranked search, which is the wrong
  answer rather than a slower one.
- A second, list-valued argument beside the free-text one is already how this
  server takes several of something: `typo3_hint_lookup` and `typo3_task_guide`
  both take `paths` beside `task`, each entry placed on its own.
- The repeated `scope` paragraph the feedback also reports is answered by the
  same change and not by dropping it. It is on every answered lookup on purpose
  — the tool is handed a query rather than a task, so an identifier handed over
  without it is usable in a frontend template, where it is wrong — and one call
  carries one copy of it.

## Decided

- Built, as a list-valued argument beside `query` rather than as a second tool.
  The subject and the source are the same; what differs is the shape of the
  answer, which is what an argument is for.
- A validation answers per identifier and carries no ranking for the ones that
  hit. What is registered is the answer, and neighbours of a correct identifier
  are noise.
- A miss keeps its suggestions. `D-ANS-016` is why: an identifier that is not
  registered is the case where the next step is the answer, and this is the tool
  whose misses look most like hits.
- What is still open is the schema, and it is the card's first step: whether the
  validated identifiers come back in the `icons` list the tool already declares
  with a per-entry verdict, or in a section of their own. The first keeps one
  result set and is the recommendation; the second is what a client that renders
  `icons` as matches would need.

## Assumed

- That a caller with several identifiers has them all before the first call. The
  reported case read four out of one template, and an identifier discovered by
  the answer to the previous one would not batch.
- That the ranking stays worth its place for a hit nobody asked to validate.
  Nothing measures which of the two modes callers reach for more often.

## Wrong if

- Callers keep passing one identifier at a time with the list argument, which
  would say the batching was never what cost them and the shape of the answer
  was.
- The per-identifier verdict is read as a ranking anyway — a client rendering
  the list without the verdict field would hand a caller a missing identifier
  that looks registered, which is the failure `D-ANS-006` and the
  suggestion-versus-match split already exist to prevent.
