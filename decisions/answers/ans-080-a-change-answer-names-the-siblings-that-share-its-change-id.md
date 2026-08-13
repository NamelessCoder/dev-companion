---
id: D-ANS-080
date: 2026-08-14
status: open
---

# D-ANS-080 — A change answer names the siblings that share its Change-Id

**`typo3_gerrit_lookup` answers a change with its own Change-Id and with the
other changes carrying it, whichever handle the caller named it by.** A backport
is a change of its own on the release branch, linked to the original by that id
and by nothing else. Which of the two handles a reviewer holds decides today
whether the answer names it.

## Evidence

- `feedback/2026-08-12-092654` reviewed change 95169 and learned of its 13.4
  sibling 93202 from `typo3_forge_lookup`'s `reviews` array. Nothing on the
  Gerrit side of 95169 named it. The backport lives in a different file,
  `fluid_styled_content/Configuration/TypoScript/Helper/ParseFunc.typoscript`,
  and was pushed before the main patch merged — two review points that exist
  because the other tool happened to be called.
- Re-run on 2026-08-14 through `bin/typo3-dev-companion`. `change: "95169"`
  answers one change and no Change-Id, in the text half and in the data half
  alike. `change: "I4b0290760f14296feec6ab30ad49595899ca08f4"` answers 93202 on
  13.4 and 95169 on `main` in one call, with nothing saying the two are one
  patch on two branches.
- One query stands behind both. `Gerrit::change()` asks
  `change:<what was passed>`, so the handle decides the answer — and the id is
  in the number-form response already: `q=change:95169` carries `change_id`,
  which `Gerrit::change_()` drops before the entry is built.
- Nothing else in the payload relates them. Measured on 2026-08-14, 93202
  carries no `cherry_pick_of_change` and no topic, so the Change-Id is the whole
  of the link.
- The second query cost 0.07 seconds against review.typo3.org on 2026-08-14.
- The corpus states the relation and cannot deliver it.
  `knowledge/documents/core/contribution/gerrit-workflow.md` says a backport
  keeps the `Change-Id` unchanged and that this is what lets Gerrit link it to
  the original, while
  `bin/cli hints:probe "gerrit change backport release branch same Change-Id"`
  reaches `public-api-surface` and `extension-ter-release` and nothing about
  either.
- The handle a review task arrives with is the number. `typo3-core-patch-review`
  routes to the Change-Id in the commit message, and this session was asked to
  review "Gerrit change 95169" — the form a review URL ends with.
- The id is reported missing a second time, from another task.
  `feedback/2026-08-13-214644` reviewed change 93319 and names the change's own
  Change-Id among what the answer does not return. It also reports the
  `changeId` of `typo3_forge_lookup`'s `reviews` entries as empty, which
  reproduces: on 2026-08-14 `issue: "109254"` named both changes with `changeId`
  empty on each, so the forge answer cannot make the hop either.
- The strength half of this feedback is already read. `D-ANS-068` records the
  fetch of `refs/changes/69/95169/2` in its 2026-08-12 section, and the two
  forge fields the report asks be kept are held: `subject` and `reviews` are
  required keys of `ForgeLookup::outputSchema()`, and
  `ForgeTest::aReviewChangeIsLiftedOutOfTheProseThatCarriesIt` asserts the
  entries with their change numbers and patch sets. Both reproduce — issue
  109254 is still titled *No link resolving in RTE table caption* against a
  patch titled *figcaption*, and
  `.checkouts/main/typo3/sysext/core/Tests/Unit/Html/RteHtmlParserTest.php:761`
  persists a table caption as `<figcaption>` inside `<figure class="table">`,
  which is what the session read the contradiction against.

## Decided

- Step 2, delivery. Nothing is missing from a source: the review server returns
  the sibling to the query this tool already runs, and it reaches the caller
  through one form of the `change` parameter and not the other. The lever is the
  answering side of the tool the session called, which is where `D-ANS-068` put
  the same diagnosis for the fetch ref.
- The change entry carries its Change-Id. It is in the response already and
  costs no call, and it is the handle that reaches the sibling, the forge
  journal and the patch after an amend.
- Both handles answer the same set. Named by id the siblings are in the answer
  today and unlabelled; named by number they are absent, and that path pays one
  query more.
- What the sibling is called in the answer belongs to the work. Whether it is a
  field on each change or a list beside them, and what the text half says, is
  written against the schema `D-ANS-068` already shaped.
- Queued rather than closed on the spot. It changes `src/` and a declared output
  schema, which the ladder reviews rather than improvises.
- Priority `normal`. Two sessions on two tasks report the missing id, which is
  what takes the card off the `low` it arrived at; neither lost its task to it,
  which is what keeps it below `high`.
- The review comments and votes of the same tool are not decided here. They are
  `feedback/2026-08-13-214644`'s own card, in hand elsewhere, and the Change-Id
  both reports ask for belongs to whichever of the two lands first.

## Assumed

- That two changes sharing a Change-Id are one patch on two branches. Gerrit
  labels nothing on this pair, and what was measured is one subject on `main`
  and on `13.4`, which is what the backport workflow produces.
- That the extra query is worth what it costs on the number path. It is paid on
  every such call, sibling or not, and 0.07 seconds is one measurement on one
  day.
- That `limit` does not hide one. A Change-Id query is bounded by `n` like any
  other, so a caller passing `limit: 1` receives one of a pair, and nothing has
  met that bound.

## Wrong if

- A reviewer acts on a sibling that is not a backport — a Change-Id reused by a
  change nobody meant to relate, or a cherry-pick abandoned on a line the patch
  is not going to.
- The number path answers a sibling rarely enough that it bought a round trip
  for nothing.
- A session holding the Change-Id reads both changes out of today's answer and
  misses nothing, which would say the number path was the whole gap and the
  label was noise.
- The Change-Id on its own turns out to be enough, because a session that
  receives it asks the second query itself. The answer would then never have had
  to carry the pair.
- A change with siblings on several supported lines makes the answer longer than
  the question it was asked, so that a review lookup has to be bounded where it
  is not today.

## Covered by

- `GerritTest::aChangeNamedByItsNumberIsAnsweredWithTheSiblingsSharingItsChangeId`
- `GerritTest::aChangeIdIsNotAskedAgainWhereItIsWhatTheCallerPassed`
- `GerritTest::theChangeThatWasNamedIsInItsOwnAnswerWhateverTheLimit`
- `GerritTest::aSiblingQueryThatDidNotAnswerLeavesTheNamedChangeStanding`

## Since then

Built on 2026-08-14. `changeId` is a field of each change entry, read from the
`change_id` every response carries. The siblings are entries of `changes` rather
than a structure beside them: a backport is a change with its own number, patch
set, commit and fetch ref, and anything narrower would answer half of it. What
labels the pair is the id the entries share, and the text half says once what
sharing one means.

A change named by its number costs the second query whether it has a sibling or
not, because only the answer says. A change named by its Change-Id costs
nothing: that query already answers the whole family, and it is skipped by
comparing the id in the response with the handle that was passed.

The third assumption is settled and is not what it said. `n` bounds the sibling
query like any other, and Gerrit orders by last activity — measured on
2026-08-14, `change:I4b0290760f14296feec6ab30ad49595899ca08f4&n=1` answers 93202
on 13.4 and not the 95169 that was asked for. So the change the caller named is
put first and `n` is applied after it: `limit: 1` answers one of a pair, and it
is the one that was named.

A second query that does not answer leaves the named change standing rather than
replacing it with nothing. The answer then says nothing about siblings, which is
what it said before this entry — a sibling that was not looked up is not a
sibling that does not exist, and no field claims otherwise.

The pair the report was about is not the only one. Recording the tool page on
2026-08-14 answered `change: "89011"`, a phpunit raise from 2025, with 89012 on
13.4 beside it — a second backport pair through the number handle, on a change
nobody chose for this.

The skills are unchanged. `typo3-core-patch-review` and
`typo3-core-patch-checkout` name what the lookup answers in their own words, and
what the pair is is stated by the answer itself; the review comments of
`feedback/2026-08-13-214644` land in that same paragraph, and one card editing
it is enough.
