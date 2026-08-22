---
id: D-ANS-068
title: A change answer carries the ref that fetches the patch set it names
date: 2026-08-09
status: open
coveredBy:
  - GerritTest::theAnswerCarriesTheRefThatFetchesThePatchSetItNames
  - GerritTest::aChangeWithoutARevisionSaysSoRatherThanInventingOne
---

# D-ANS-068 — A change answer carries the ref that fetches the patch set it names

**`typo3_gerrit_lookup` hands over the ref that fetches the patch set it names,
derived from the change number and patch set it already answers with.** Two
sessions held a complete description of a patch set they could not fetch, and
both wrote the sharded ref themselves out of Gerrit trivia the answer never
states.

## Evidence

- `feedback/2026-08-08-224354` is a review of change 95179. The answer carried
  number, patch set 1, commit `0b18ff0af75`, project `Packages/TYPO3.CMS` and
  the review URL, and the session built `refs/changes/79/95179/1` from prior
  knowledge that Gerrit shards by the last two digits of the change number.
- `feedback/2026-08-08-224352` is a triage of Forge #82228, a different task
  shape and the same reconstruction:
  `git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/19/53819/3 && git show FETCH_HEAD`.
  That diff is what settled the triage — the 2017 proposal reinterpreted `width`
  plus `height` as the fit-into-box the `m` modifier already does — and the
  session names it as the read this server had no way to give it.
- Re-run on 2026-08-09, both against the review server. `change: "95179"` and
  `change: "53819"` answer number, subject, status, branch, patchSet, commit,
  project, updated and url, in the text half and in the data half alike, and
  neither half names a ref or a fetch.
- Every input to the string is in that answer, and the derivation is checkable
  against a field beside it. Measured on 2026-08-09,
  `git ls-remote https://review.typo3.org/Packages/TYPO3.CMS refs/changes/79/95179/1 refs/changes/19/53819/3`
  resolves to `0b18ff0af75d3dbae5a28f92d0abf9a4a1be7870` and
  `0271db52b1d088b4b5ac33f1f7a5e15833e08cd0` — the two commits the answer
  already carries.
- The form is in the corpus and reached neither session.
  `knowledge/documents/core/contribution/gerrit-workflow.md` carries the ref,
  the sharding and the remote asymmetry under *Fetch a Change Into This
  Checkout*, and `D-SKL-021` measured on 2026-08-05 that the fetch is not
  guessable from what the review server offers a reader.

## Decided

- This is step 2 of the ladder, delivery. Nothing is missing from the corpus:
  the document states the ref exactly, and the answer that reaches the moment
  the patch has to be fetched states nothing. The lever is the answering side of
  the tool the session did call, which is what `D-ANS-061` settled for a
  document uri and `D-ANS-064` for a change reference sitting in prose.
- The ref is a field of the change entry rather than a sentence in the text
  half, so a caller composing a command reads it as data.
- The remote goes with it. A ref on its own repeats the failure `D-SKL-021`
  measured — `git fetch origin refs/changes/…` reports that the ref does not
  exist in the very checkout whose push would reach the change, because a core
  clone fetches from the mirror. What the answer names is the review server it
  is fetchable over, which is `Gerrit::HOST` and the `project` field already in
  the entry.
- It stays a string in an answer. Nothing is fetched, and nothing is started on
  the caller's machine as a side effect of a lookup — `R-DIS-006`.
- The ref follows the patch set the entry names, which is always the current
  one: the query asks for `CURRENT_REVISION` and no parameter reaches an older
  patch set. Asking for an earlier one is a separate capability and is not
  decided here.
- Where the server named no patch set, `patchSet` is `0` and there is no ref.
  The field is null then rather than a string carrying a zero, which is the
  shape `unavailable` and `indistinguishable` already have on this tool.

## Assumed

- The sharding is Gerrit's own rule rather than this instance's configuration.
  Two changes nine years apart resolve under the last two digits, which is
  consistent with it and is not a reading of Gerrit's documentation.
- The padding holds below ten. Both measured change numbers are five digits, and
  what a change numbered under 10 shards to was not measured.
- One field is enough. `D-ANS-061` assumed the same thing about naming a
  document, and nothing yet shows a session acting on a name this server put in
  front of it.

## Wrong if

- A session fetches the ref this answer gave and gets a revision other than the
  `commit` beside it, which would say the derivation is not the rule, or not the
  rule on this server.
- The fetch is reported as noise on an issue search answering ten changes, which
  would say it belongs on a change lookup alone.
- A session reads the ref and fetches over `origin` anyway, which would say
  naming the remote in the same answer was not enough.

## Since then

Built on 2026-08-09 as a `fetch` field on each change entry, carrying `ref` and
`remote` and null where the server named no patch set. The text half prints
`Fetch: git fetch <remote> <ref>` under the patch set line, and says once per
answer that the fetch goes to the review server rather than to `origin`.

Both assumptions the entry rested on are settled, and neither moved anything.
The sharding is Gerrit's own rule: its access control reference states
`refs/changes/<last two digits of change number>/<change number>/<patch set number>`
under *Special references*, and `RefNames.shard()` is the `%02d` that writes
those two digits. A change numbered under ten is therefore
`refs/changes/04/4/1`, and that case cannot arise here — change numbers 1 to 10,
50, 200, 500 and 1000 answer nothing on review.typo3.org, and 2000 is the lowest
of the probed ones that answers. The padding is reached by every change whose
number ends below ten anyway: `refs/changes/00/2000/2` resolves there and
`refs/changes/0/2000/2` does not.

The first **Wrong if** was measured rather than waited for. On 2026-08-09
`git ls-remote https://review.typo3.org/Packages/TYPO3.CMS` resolved the three
refs this answer now derives — `refs/changes/79/95179/1`,
`refs/changes/40/95040/3` and `refs/changes/11/89011/4` — to the commits the
same answers carry.

### 2026-08-12 — two review sessions fetched what the answer named

`feedback/2026-08-11-055157` reviewed change 94686 from a core checkout. The
session read `patchSet`, `commit` and the ref out of one answer, saw that the
checkout's `HEAD` was a different commit, and fetched `refs/changes/86/94686/3`
before reading a line of the diff — its own account is that it would otherwise
have reviewed `main` and reported nothing. `feedback/2026-08-12-092654` is
another task shape a day later and the same use: change 95169 fetched at
`refs/changes/69/95169/2` in one command, first try.

Neither confirms this entry. A strength is a session's account of its own run,
which `D-FBK-018` declines to read as a measurement. What the two do bear on is
the third **Wrong if**, and from the other side: both fetched over the remote
this answer names rather than over `origin`, which is the failure `D-SKL-021`
measured before the field existed.

Re-run on 2026-08-12: `change: "94686"` answers patch set 5 at
`cac873e95dc59bc48bea0bc5dd396bf57a84d638`, with `fetch.ref`
`refs/changes/86/94686/5` and the review server as `fetch.remote`. The change
has merged since that review, so the ref follows the current patch set exactly
as this entry says it does — and a session holding the old one has to read the
number beside it.
