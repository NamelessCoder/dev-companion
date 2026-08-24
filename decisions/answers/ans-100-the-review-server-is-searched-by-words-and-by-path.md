---
id: D-ANS-100
title: The review server is searched by words and by path
date: 2026-08-24
status: open
---

# D-ANS-100 — The review server is searched by words and by path

**`typo3_gerrit_lookup` takes a word query and a path beside its two handles,
because which changes touch a file is asked before a patch and no number answers
it.**

`D-ANS-033` built the search over commit messages and `D-ANS-038` the word
search on the tracker. Neither covers the review surface itself: which changes
stand open on a file, and whether anybody ever tried a fix.

## Evidence

- The re-run is a reading rather than a call, because the query that produced
  `feedback/2026-08-24-110833` never reached this server.
  `GerritLookup::inputSchema()` takes `issue`, `change`, `messages` and `limit`,
  and its `oneOf` requires one of the first two. Nothing takes a query or a
  path.
- Measured against `review.typo3.org` on 2026-08-24, anonymously and over the
  path `Http\Fetch` already reads. `status:open file:^typo3/sysext/impexp/.*`
  answers 22 open changes, and `file:` asked without a status answers ABANDONED,
  MERGED and NEW alike — so the path reaches what landed as well as what is
  open.
- A bare term searches, and more than the subject. `AssetCollector` answers ten
  changes, headed by the deprecation 95040 and including 94979, whose subject
  does not carry the word. `writePagesOrder` and `flatInversePageTree` answer
  none at all, which is the negative that says nobody has attempted the fix.
- The reporting session paid three calls for those, by hand and to an endpoint
  it composed itself. Its checkout could answer none of them: a core clone
  carries what landed and says nothing about what is open.
- One of the 22 is that session's own patch, 95393. The surface this search
  answers is the one a triage is trying to see.
- `D-ANS-038` built the same second way into `typo3_forge_lookup`, and its third
  **Wrong if** names this tool as where the split would show first.
- `D-ANS-033` assumes `message:` matches what a caller means. It does where the
  caller holds an issue number, and a triage that starts from a bug report and a
  file has none yet.

## Decided

- Built, and as a third way into `typo3_gerrit_lookup` rather than as a tool of
  its own. One subject, one verb, and the answer is the list of changes the
  issue search already returns.
- Two arguments this server composes into the query — words over the commit
  message, and a path a change touches — rather than a Gerrit query passed
  through. The operators, the anchoring and the escaping stay here, and `query`
  in the answer says what they produced.
- Every state, with open as a narrowing. "Has anybody ever tried this" needs the
  abandoned and the merged ones, and "who is working on this file now" is the
  same search with `status:open`.
- The boundary per hit is the issue search's: number, Change-Id, subject,
  status, branch, patch set, commit and URL. The review, the comments, the chain
  and the issues are what reading one change answers, which is where a hit is
  passed back into.
- Nothing ranks. `impexp translation` answers ten changes of which one is about
  both words, so the order is Gerrit's own and a caller holding a narrow set
  asks again in other words rather than concluding that nothing exists.

## Assumed

- That the anonymous search index stays as it was measured. It is what the
  project's own web UI searches with, and a change to it arrives as a smaller
  answer rather than as an error.
- That a path is what the caller has. It is what a checkout hands it, and a
  subsystem nobody can spell as a path is a word query instead.

## Wrong if

- The three ways into the tool stop being one question. That is `D-ANS-038`'s
  third **Wrong if** happening here, and a search growing its own filters and
  its own answer shape is what would revoke this entry.
- A caller reads an empty path search as "nobody is working on this file". An
  anonymous read cannot see a private or work-in-progress change, which
  `D-ANS-033` states for the issue direction, so the caveat is owed on this one
  too and `indistinguishable` is where it goes.
- The full-text match turns out not to reach the diff. It reaches the commit
  message body, which 94979 shows; whether it reaches the changed lines was not
  measured, and a caller reading a zero as "this identifier appears nowhere"
  needs it to.
- The path search answers a sweep more often than a change about the file. Two
  of the 22 measured are one — a link adjustment across every manual, and a
  tree-wide PHP simplification — and a list where most entries touch the path
  incidentally is a count that says nothing.
