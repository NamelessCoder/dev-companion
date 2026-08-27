---
id: D-ANS-087
title: The project answer stays whole because a call is what costs
date: 2026-08-19
status: open
---

# D-ANS-087 — The project answer stays whole because a call is what costs

**`typo3_project_describe` keeps answering everything it knows in one call
rather than letting the caller select parts.** A session is charged per call, so
what a selection saves is bytes nobody is billed for and what it risks is a
second context.

## Evidence

Measured on 2026-08-19 against `.fixtures/installation`, and against the
recorded answers `bin/cli tools:measure` reads.

- The whole answer is 3267 characters of text and 2464 of data. Among the 24
  tools it is fourteenth of twenty by recorded weight, at 14,338 bytes over two
  calls.
- Half of it is not about the repository: lines 17 to 32 of the text and 1409 of
  the 2464 data characters are `guides`, the same fifteen procedures in every
  answer. The other half — version, the four PHP numbers, Node, extensions,
  sites, declared commands — is what no other call answers.
- `typo3_server_scope` is 86,061 characters in one answer, twelve times this
  one, with `covers` at 22,813 and `doesNotCover` at 7851. That is where an
  answer's weight actually is.
- `D-FBK-020` is `confirmed` and measured the cost model: one context per call,
  the context a call re-reads at 82k on average. Against that, trimming 1409
  characters is not a saving that can be observed, while a fetch for what was
  trimmed costs a whole context.

## Decided

- **No selection parameter.** This is the first call of a session, so a caller
  choosing sections chooses them in its least informed moment — the failure
  `D-GUI-015` measured four times the same day, where the wording that reaches
  an answer is the wording somebody already has the answer for.
- **The guides listing stays, though it is the largest single part.**
  `D-ANS-061` put it here because three core sessions held a resource list and
  read none of it, and `D-AUD-011`'s index entry now routes to this answer
  rather than to `typo3://guides`. Cutting it undoes both.
- **The weight question moves to `typo3_server_scope`**, where a selection is
  also the honest shape: an orientation answer has topics, and a caller asking
  for one of them knows which.
  `todo/open/2026-08-19-014500-weigh-the-scope-answer-against-what-a-caller-asks-it-for.md`
  carries it.
- **Nothing is measured against a client's context window here.** What was
  measured is bytes and the call model; whether any client truncates this answer
  is not known and is the first thing that would reopen this.

## Assumed

- That the fixture installation is the ordinary size. A project with forty
  extensions and eight sites grows the half that is about the repository, which
  is the half nobody proposed to cut.
- That a caller reads what it is handed. `D-ANS-061` is the record of that
  failing for a resource list, not for an inline answer.

## Wrong if

- A filed session reports this answer crowding out its task, or a client
  truncating it. Then the size is a client property rather than a byte count,
  and the first candidate is the guides listing this entry keeps.
- The guides half turns out to be read by nobody. Then `D-ANS-061`'s own **Wrong
  if** fires first, and the listing moves rather than shrinking.
- A session asks for one part of the project answer by name. Then the selection
  is wanted after all, and what it names is the evidence this entry lacks.

## Since then

The weight question this entry moved to `typo3_server_scope` was settled on
2026-08-19, and the evidence the card said did not exist was in the archive:
`feedback/2026-08-17-205904` called that tool, measured it at roughly 11,000
tokens and near 3% of the session's cost, and reports that it changed no
decision the session took. So the answer here is not that a call is what costs
and the bytes are free — it is that both are true at different sizes, and 94,000
characters is where the second one starts to be paid.

What it did not change is the third **Decided** above. The orientation answer
gained a selection under
[`D-ANS-088`](ans-088-the-orientation-answer-is-asked-for-by-section.md)
and the project answer stays whole, because what separates them is not their
size: an orientation has parts a caller can name, and a caller that knows it
wants the boundary is not choosing blind. The project answer is one repository
described, at a fourteenth of the weight, and its parts are not questions
anybody asks apart.

### 2026-08-27 — the listing this entry kept has doubled

**The guides half now carries a sentence per page, and the answer went from
22,075 characters over two recorded calls to 41,701.** `D-GUI-012` was answered
that way on 2026-08-27: an id and a title were what six sessions read from four
surfaces before opening none of the pages, so each entry says what the caller
has to be doing for it to be the one to read.

That is this entry's own reasoning applied again rather than against it — bytes
nobody is billed for, against the second context a fetch costs. What it does
sharpen is the second **Wrong if**: the listing is now three quarters of the
answer, and if the pages still go unopened, the question stops being how they
are named.
