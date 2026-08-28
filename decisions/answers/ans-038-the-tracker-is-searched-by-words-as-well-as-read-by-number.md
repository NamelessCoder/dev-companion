---
id: D-ANS-038
title: The tracker is searched by words as well as read by number
date: 2026-08-03
status: open
coveredBy:
  - ForgeTest::aMissNamesTheEnumerationAsACallToCompose
  - ForgeTest::aMissNamesWhatEachWordReachesOnItsOwn
  - ForgeTest::aMissOutsideTheBoundAsksTheTrackerNothingFurther
  - ForgeTest::aSearchThatAnsweredIsNotCountedWordByWord
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

A feedback was judged against this entry and its own card retired into it: the
tracker half of the cluster, all of it answered here except the search. The
re-run says so — one call answers what the feedback established in four,
including the three notes it called decisive, and the route it found to the
patch when one field was empty. The tool never requests that field, so the trap
it warned about is not reachable.

The rest needed no entry, being the fetch policy's rather than this answer's.

## Since then

The empty search sends the caller back into the loop it is already in: a session
establishing whether a defect was on the tracker spent eight wordings, two
matching nothing and six returning something else, and the one relevant issue
arrived as a relation of a hit. What settled it was the ninth call, an
enumeration that answered all 26 open issues of that area at once.

Re-run, the empty query offers two things to do next and neither is the
enumeration, while the enumeration carries the pointer the other way — so the
cross-reference is written only in the direction where the caller is not stuck.

## Since then

Written on 2026-08-24: the miss of a search now names the enumeration as a call
to compose, in the caller's own words, and the area's description carries the
duplicate question beside the browse questions it already had. The test holds it
through the registry, which is what the reader seam was added for — the tool
builds its own client, so a test of its text half had nowhere to hand a
transport in.

The neighbour feedback is half of this rewrite: it reports four words answering
nothing where one of them answers five, and asks for the combining rule to be
said.

## Since then

**A miss asks each of the caller's words on its own; the any-word re-read the
section above proposed was measured and answers something else.** That
measurement was taken on a query one of whose two words nobody had written. On
the four words of the reporting session it does not hold: the union answers
14673 where the same URL without it answers none, ordered by issue number, so
its first page is the newest issues carrying the commonest word and none of the
five the caller was after is in it.

So the call is spent and the question is not answered. One read per word is what
answers it, which is what the feedback asked for in the first place.

## Since then

**The miss delivers what the empty answer owed and promises more than the call
it names can carry.** A session establishing whether a defect was on the tracker
before filing it tried three wordings, got three empties, and had a negative it
could not rely on. It asked for the enumeration to be named and for the empty
answer to say what it does and does not establish; both were built by the two
sections above, before it was judged.

Re-run in the feedback's own words, the empty answer now opens with "which is
not that nobody reported it", counts each word, names the narrowest, and names
the call to compose.
