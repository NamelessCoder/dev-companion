---
id: D-ANS-070
title: 'A document is handed over by the call that reads it'
date: 2026-08-09
status: open
coveredBy:
  - KnowledgeTest::aCutAnswerNamesTheHeadingsOfThePageItLeft
  - KnowledgeTest::anAnswerCarryingEveryHeadingOfAPageSaysThatToo
---

# D-ANS-070 — A document is handed over by the call that reads it

**A cut answer says what it left of the page, and every place that tells a
session to read one whole names the call rather than the address.**

The handover built on 2026-08-07 reached the next session that needed it. It
named the document, the session read the name, and it searched anyway.

## Evidence

- `feedback/2026-08-08-224406` is a core patch session that held the guide ids
  from `typo3_project_describe`, knew the `documentId` route from the parameter
  description, ran one `typo3_rule_lookup` query and read no document whole. Its
  own account of why: the search answered, and answering is indistinguishable
  from answering completely.
- Re-run here on 2026-08-09 with that feedback's own arguments —
  `query="bugfix changelog entry obligation and target branches"`,
  `targetVersion="15"`. The answer is `Changelog Files` and `Release Targets`,
  both cut from `core/contribution/commit-messages`, and it closes with *Each
  excerpt above is one section of a longer document … read the page —
  typo3_rule_lookup with documentId*. So what `R-ANS-028` built was in the
  session and did not take.
- That page has nine `##` sections and the answer returned two of them. Nothing
  in the answer says which number it is: the same line stands under an answer
  that matched most of a page and under one that matched a ninth of it.
- Three surfaces already name the route, and the session names all three: the
  `guides` tail of `typo3_project_describe`, the `documentId` parameter
  description, and the foot of every `Result\Prose` answer.
- The one surface that tells a session to read a page whole names an address
  instead. `skills/typo3-core-patch-development/SKILL.md:177` and `:182` name
  `typo3://guides/core/contribution/commit-messages` and `…/gerrit-workflow` at
  the two steps that say to read them once rather than a section at a time, and
  `skills/typo3-core-patch-checkout/SKILL.md:37` names the second. The same file
  at `:118` already names the security procedure as
  `documentId="any/security/reporting-a-vulnerability"`.
- `documentation/clients/writing-a-skill.md:214` is the rule those follow, and
  it is older than the route it should name: it was written for `D-AUD-007` on
  2026-08-04, and `documentId` did not exist until 2026-08-07.

## Decided

- The judgement is **step 4 of the ladder**, wording. Every document these
  sessions wanted exists, the route to it exists, and it was named in three
  places the session read.
- Both halves are **queued rather than closed on the spot**: one touches
  `src/Result/Prose.php`, the other two published skills, which
  [judging.md](../../documentation/records/judging.rst) puts on the reviewed
  side of that line.
- The skill half is the stronger lever on this evidence, because it is the only
  place that says to read a page whole — an imperative that hands over an
  address a client which lists no resources cannot act on. The rule in
  `writing-a-skill.md` moves with the two skills, since it is what they follow.
- What the answer half says is left to the todo. A share of the page and the
  headings the search did not return are both candidates, and a count owes
  [`D-ANS-008`](ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md)
  what it counted.
- Recorded as its own entry.
  [`D-ANS-061`](ans-061-an-answer-that-names-a-document-hands-it-over.md)
  decided that naming a document is the handover, and this is the first evidence
  about whether the naming took.

## Assumed

- That a session told how much of a page it left will read the page. Nothing
  here shows that; what is shown is one session acting on none of three namings.
- That this session is the common case rather than one careful reporter. It is
  the fourth on the same ground and the first after the fix, and all four come
  from one model in one checkout, which is the same weakness `D-ANS-061`
  carries.

## Wrong if

- A session that received the share still reads only the matched sections. Then
  the answer side is not a lever at all and only the skill sentence was.
- A session reports reading a page because a skill named the call, and a later
  one reports the same miss from a step no skill covers. Then the imperative was
  the whole of it and the corpus answers need nothing.
- The share turns out unstateable — a document whose sections a query
  legitimately covers most of, answered as *2 of 9* and read as a reproach. Then
  what was missing is the headings and not a number.

## Since then

On 2026-08-11, that answer came back: the second of the three, recorded as
[`D-ANS-076`](ans-076-a-search-matching-one-page-answers-with-the-page.md).
A third wording was priced against it and refused, because it would have rested
on one session's own phrasing of a line two sessions had already been handed.

## Since then

The answer half is built and says both: the share and the headings the search
did not return, one line per page under the offer to read it. The headings are
what the todo asked for first, because a session picks its next query out of
them, and the number costs the four words around them.

The count is of headings rather than of section lines, which is what `D-ANS-008`
asks of it: a reader checking the number against the page counts headings and
agrees, where a count the search can never return two of would not. The list
beside the number is the second way to check it.
