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

## Since then

The empty search sends the caller back into the loop it is already in, and
`feedback/2026-08-24-110926` is what shows it. A session establishing whether an
impexp defect was already on the tracker spent eight `query` calls on eight
wordings: two matched nothing, six returned issues about something else, and the
one relevant issue arrived as a relation of a hit rather than as a hit. What
settled it was the ninth call — `open: "stale"`, `category: "import export"`,
`limit: 50` — which answered all 26 open Import/Export issues at once.

Re-run on 2026-08-24 against the tracker.
`query: "writePagesOrder importNewIdPids"` still answers empty and offers two
things to do next: ask again in the reporter's words, or pass a person as
`reportedBy` or `assignedTo`. Neither is the enumeration. The same enumeration
the session ended on answers 26 of 26 in one read, and it carries the pointer
the other way — "query the words as well where the question is about a subject"
— so the cross-reference between the two ways in is written only in the
direction where the caller is not stuck. Two of the feedback's own wordings no
longer reproduce, because the session filed
#110524 afterwards and the tracker now matches them.

That is `R-ANS-006` not reaching this tool. A miss says what there would have
been to find and what it names can be asked for outright; the hints, the labels,
the changelog and the prose corpus all hold that, and what this miss names is a
rewording, which is not a call. The first **Wrong if** above covers the other
half and held — the session never read the empty answer as nobody having
reported it. It went round eight times instead. What moved is the second
**Assumed**: the caller's question here was which issue is the duplicate rather
than which issues mention it, and `query` is not what answered it. `D-ANS-054`'s
first **Wrong if** is this order reversed and did not fire — the enumeration was
reached last rather than first, and its filters carried the question once they
were asked.

Step 4 of the ladder, wording, and queued rather than closed on the spot,
because the sentence is in `src/` and the `category` description is a declared
schema. What the wording owes is the route and not the count the feedback asks
for: naming "Import/Export (T3D) has 26 open issues" is a second read of the
tracker on every miss, while naming `open` with `category` is a call the caller
can compose from what it already holds. `feedback/2026-08-24-163235` is the
unjudged neighbour on the same three sentences, reporting a multi-term `query`
that answers nothing where one of its terms answers five; whether the two are
one rewrite is that judgement's to make.

## Since then

Written on 2026-08-24. The miss of a `query` now names `open` with `category` as
a call to compose — `"stale"`, the area in the caller's own words, `limit: 50` —
and the `category` description carries the duplicate question beside the two
browse questions it already had.
`ForgeTest::aMissNamesTheEnumerationAsACallToCompose` holds it through
`Registry::call`, which is what `Forge::useReader()` was added for: the tool
builds its own `Forge`, so a test of its text half had nowhere to hand a
transport in. The count is still not in the answer, for the reason the section
above gives.

The neighbour feedback is half of this rewrite. `163235` reports
`query: "file renderer RendererRegistry FileRendererInterface"` answering
nothing where `RendererRegistry` alone answers five, and asks for the combining
rule to be said. Measured against the tracker on 2026-08-24: `RendererRegistry`
answers 5, and the same query with one invented word answers 0. So every word
has to be in one issue, and both the `query` description and the miss say so —
with the term nobody would have written named as what empties an answer, which
is the identifier query `110926` says it would not make again.

What is left of `163235` is its second half and it is trimmed to that. The
tracker takes the AND off: `all_words=` with an empty value answers 5 where the
same URL without it answers 0, so an any-word re-read is one further call rather
than one per term. `all_words=0` and `all_words=false` do not do it — Redmine
reads any value that is present as true. Whether a miss spends that call is the
judgement `163235`'s own card carries, and it is the shape this entry's third
**Wrong if** watches: a search growing an answer of its own.

## Since then

**A miss asks each of the caller's words on its own; the any-word re-read the
section above proposed was measured and answers something else.**

That measurement was taken on a query one of whose two words nobody had written,
where the union of the two is the one that reaches. On `163235`'s own four words
it does not hold. Measured on 2026-08-25 against the tracker:
`file renderer RendererRegistry FileRendererInterface` with `all_words=` answers
**14673** where the same URL without it answers 0. The union is ordered by issue
number, so its first page is the newest issues carrying `file` — #110525,
#110524, #110522 — and none of the five the caller was after is in it. The size
is the commonest word's: `file` alone reaches 13944. So the call is spent and
the question is not answered, and `all_words=0` and `all_words=false` still do
nothing.

One read per word is what answers it, which is what `163235` asked for in the
first place. The same four words asked one at a time: `file` 13944, `renderer`
1173, `RendererRegistry` 5, `FileRendererInterface` 0. That names the word that
emptied the query and the word to ask alone, and it is the half of a miss no
advice on this side can supply — the miss already says an identifier empties an
answer, and both of those are class names. Read as advice it would have thrown
away `RendererRegistry`, which is the term that reaches.

Bounded at both ends, because the cost is a read of about two and a half seconds
apiece on the path that answered nothing. One word is not asked — its count is
the zero the caller is holding — and a query of more than a few words is not
asked either, because the answer to that one is to pass fewer words, which the
miss says without reading anything. Each probe is held like any other read, so a
session rewording around one term pays for it once.

A hit spends nothing. `163235`'s second case is the test of that: six common
words answering one irrelevant issue, and asked one at a time they reach between
337 and 4661 apiece, which names no better call. What that case wants is the
enumeration, and the miss already names it — the case never reaches the miss,
because one hit is not a miss.

## Since then

**The miss delivers what the empty answer owed and promises more than the call
it names can carry.**

[`feedback/2026-08-24-225214`](../../feedback/2026-08-24-225214-no-way-to-establish-that-an-issue-has-not-been.md)
is the same shape as `110926` one step earlier: a session establishing whether a
defect was already on the tracker before filing it, three wordings, all empty,
and a negative it could not rely on. It asked for two things — the enumeration
named in the answer, and the empty answer saying what it does and does not
establish. Both were built by the two sections above, before it was judged.

Re-run on 2026-08-27 in the feedback's own words.
`query: "LocalizationController getContent permission check"` still answers
empty, and now opens with "which is not that nobody reported it", counts each
word — `LocalizationController` 16, `getContent` 87, `permission` 1198, `check`
10086 — names `LocalizationController` as the one to ask alone, and names `open`
with `category` as the call to compose.
`query: "localization wizard endpoints missing permission"` answers the same
way, with `endpoints` at 18 as the narrowest that reaches. So the two calls the
feedback says it wasted are recovered on the first one, which is the measure it
set for itself. The third wording now answers #110533, because the user filed it
after the session; that is the tracker having changed rather than this search,
the same way two of `110926`'s wordings stopped reproducing.

What is left is the sentence under them. "Reading those subjects is what settles
whether somebody already reported this" was written from `110926`, whose area
held 26 open issues, and it does not hold on the area this feedback was in:
Backend User Interface has 437, both orderings point at the neglected end, and
#110533 carries no Category at all. `D-ANS-116` is that half, taken on, and it
carries the measurements.

The first **Wrong if** held again — the session never read its empty answers as
nobody having reported it, and said so in its review in as many words.

The third **Wrong if** is engaged and not fired. `terms` is a further field on
the answer, which is growth, but it is the third instance of a shape this tool
already has: `categories` corrects a word that named no area,
`people[] .candidates` corrects a name that named nobody, and this corrects a
word that named no issue. `R-ANS-006` is what all three serve, and it is the
requirement the section above found not reaching this tool.
