---
id: D-ANS-038
title: The tracker is searched by words as well as read by number
date: 2026-08-03
status: open
---

# D-ANS-038 — The tracker is searched by words as well as read by number

**`typo3_forge_lookup` takes a word query beside the issue number, because
whether other issues describe the same bug is asked before a patch and no number
answers it.**

`D-FBK-027` built the issue read and `D-ANS-033` the review search, both out of
the same cluster of sessions. What none of them covers is the step those
sessions took between the two: finding the issues nobody had linked.

## Evidence

- Re-run on 2026-08-03 against the server as it is. `typo3_forge_lookup` with
  `issue: 105403` answers subject, tracker, status `Under Review`, target
  version, both relations and all six comments, including the two maintainer
  verdicts the reporting session called the decisive evidence;
  `typo3_gerrit_lookup` answers `empty` with the private-change caveat. The half
  of `feedback/2026-08-02-144511` that asked for those is delivered, and this
  entry is what is left of it.
- Two sessions searched the tracker by words from the same task shape:
  `feedback/2026-08-02-144511` and `145217`, whose task text was "find similar
  issues". Both used `/search.json?q=<terms>&issues=1`, and neither could have
  reached it from an issue number.
- `relations[]` answers a narrower question. It carries what a person linked —
  #105403 names #99203 and #105953, and `145217` reached #100696 through them —
  so an issue nobody linked is invisible to it, which is what a duplicate is
  until somebody recognises it.
- The endpoint answers JSON to this server's own agent, measured the same day:
  `results[]` of `id`, `title` —
  `Bug #105403 (Under Review): f:image and cache busting issue` — `type`, `url`
  and `description`, in an envelope of `total_count`, `offset` and `limit`. A
  query nothing matches answers `total_count: 0`. It clears `D-ANS-034` without
  a parser, and tracker and status arrive without a second call per hit.
- One query is not the answer. `cache busting` returned 15, `f:image` 134 and
  `image cache` 279 on 2026-08-03 — three phrasings of one question — and
  `145217` reports four wordings finding four different sets.

## Decided

- Built, and as a second way into `typo3_forge_lookup` rather than as a tool of
  its own. One subject, one verb, and `lookup` is already the verb whose answer
  is matching entries with nothing being a legitimate one. `typo3_gerrit_lookup`
  carries the same exclusive pair, stated where the call is composed
  (`D-ANS-012`).
- The boundary is identity and triage state per hit: issue number, subject,
  tracker, status and the URL a person reads it at. The description is not
  carried — a caller that wants the issue reads it by number, which is the other
  half of the same tool.
- Nothing here ranks the hits. The order is the tracker's own and the answer
  says which query produced it, so a caller holding a narrow set asks again in
  other words rather than concluding that nothing else was reported.
- Writing stays outside. Commenting, assigning and reopening need a credential,
  and reopening is where that bites: `145217` records that a closed issue has to
  be reopened before a change can be pushed against it, which is a person's step
  rather than a missing call.

## Assumed

- That the anonymous search stays open, like the two endpoints already read. It
  is what the tracker's own UI searches with, so an outage is likelier than a
  policy change, and both arrive as `source-not-answering`.
- That the caller's question is which other issues mention this, rather than
  which one is the duplicate. Nothing here decides duplication, and an answer
  that looked like it did would be believed.

## Wrong if

- A caller reads an empty search as "nobody reported this". It is the failure
  `D-ANS-033` names one source over, and worse here, because a report worded
  differently is invisible to a word match rather than merely private.
- Callers routinely page past the first answer. Then the hits needed ranking, or
  a filter on tracker and open state, and a limit was the wrong knob.
- The two ways into the tool stop being one question. A search that grows its
  own filters and its own answer shape is a second tool wearing the first one's
  name, and `typo3_gerrit_lookup` is where the same split would show first.

## Since then

`feedback/2026-08-02-145217` was judged against this entry on 2026-08-03, and
its own card was retired into it. That feedback is the Forge half of the cluster
— how the tracker has to be operated by hand — and everything it recorded is
answered here except the search.

The re-run is what says so. `typo3_forge_lookup` with `issue: 105403` answers in
one call what the feedback established in four: subject, tracker, status
`Under Review`, target version, the reported TYPO3 version, both relations, and
all six comments. The three the feedback called decisive are among them — the
two maintainer verdicts, Georg Ringer's "closing as lack of feedback" and the
Gerrit bot's patch-set notes, which is the route it found to the patch when
`changesets` was empty. The tool asks for `include=journals,relations` and never
requests `changesets`, so the trap it warned about is not reachable.
`typo3_gerrit_lookup` with the same number answers `empty` with the private
caveat.

The rest of it needed no entry. The user-agent inversion and its HTTP 200
challenge page are `Http\Fetch`'s policy and its plain-agent retry, with
`D-ANS-034` behind them; the reopening of a closed issue before a change is
pushed is the fourth bullet under **Decided** above; and the venue its
suggestion asked for is `knowledge/server-scope.json`, which routes a session
taking an issue on to both lookups in that order. What is left of `145217` is
the word search, carried by `todo/progress/2026-08-03-125637` — the same card
that carries `144511` — and that commit archives both.

The third **Wrong if** named `typo3_gerrit_lookup` as where the split would show
first, and on 2026-08-24 it is where the same shape was asked for.
`feedback/2026-08-24-110833` reports a triage that could ask the review server
neither which changes are open on a file nor whether anybody ever tried a fix,
and answered both with `curl`. `D-ANS-100` takes it on under the rule this entry
set: a further way into one tool, answering the question it already answers, in
the shape it already answers in. So the **Wrong if** is engaged rather than
fired, and what would fire it is that search growing filters and an answer of
its own.
