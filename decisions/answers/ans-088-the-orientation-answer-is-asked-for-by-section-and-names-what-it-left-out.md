---
id: D-ANS-088
date: 2026-08-19
status: open
---

# D-ANS-088 — The orientation answer is asked for by section, and names what it left out

**`typo3_server_scope` takes a `sections` argument, answers only the parts a
caller names, and says which parts it left out.**

It answers whole where nothing is named, which is the call this tool exists for.
What the argument is for is the other caller: one that already knows which part
of the orientation it is missing.

## Evidence

- `feedback/2026-08-17-205904` is the filed session the card said did not exist.
  It called this tool from an empty directory, measured the answer at roughly
  11,000 tokens — the largest single result of a build that cost $29.59, about
  $0.88 amortised over the 148 requests it stayed in context for — and reports
  that it changed no decision the session took. What it needed was whether an
  installation and a console could be reached at all.
- `feedback/2026-08-18-080743` is the second session on record that called it,
  and reports the answer as accurate while naming nothing it decided. Both
  sessions read the whole catalogue for a question that is a part of it.
- Measured with `bin/cli tools:measure` on 2026-08-19: 93,994 characters over
  one call, the heaviest single call this server has. The recorded pair against
  `.checkouts/14.3` is 94,198 characters whole and 7,734 for
  `sections: ["installation"]`, twelve times less.
- `D-ANS-083` measured the part the session was after at 389 characters, under
  1% of the answer it arrived in, and its third **Wrong if** is this entry.
- [`D-FBK-020`](../feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)
  prices calls rather than tokens, which is what makes the default the whole
  answer and the argument a narrowing rather than a paging scheme. A caller that
  has to ask twice has paid more than it saved.

## Decided

- **The sections are the payload's own field names**: `covers`, `doesNotCover`,
  `checkoutDiscovery`, `routing`, `versions`, `answersFrom`, `installation`. A
  caller asking again names the field it is already holding, so there is no
  second vocabulary to guess at, which is what
  [`D-GUI-015`](../guides/gui-015-a-cases-own-prompt-reaches-less-than-the-brief-that-stands-in-for-it.md)
  measured the cost of.
- **Naming none is the whole answer.**
  [`D-ANS-087`](ans-087-the-project-answer-stays-whole-because-a-call-is-what-costs.md)
  holds for the caller this tool is written for: one that does not know what the
  server covers cannot name the part it wants, and would choose in its least
  informed moment. A caller that can name one is the caller `D-ANS-083`
  measured.
- **No selection reaches what
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md)
  protects.** The purpose, the initialize instructions and the exclusion report
  are outside the section list and travel with every answer, so the argument
  cannot reopen through a parameter the hole the exclusion list is kept away
  from. So does the sentence telling the agent to query in English, which is the
  whole mitigation for a lexical matcher over an English corpus.
- **A withheld section is absent rather than empty, and `withheld` names it.**
  An empty `covers` reads as a server that covers nothing, which is the failure
  the installation diagnostic was moved into the payload for. `withheld` carries
  what each missing part would have held, so a narrowed answer cannot be read as
  the whole one.
- **The console is not resolved where `installation` was not asked for.** It is
  the one part of this answer that costs seconds rather than bytes — two
  `ddev describe` calls, 0.869s against a stopped project on 2026-08-04.
- **The output schema requires three fields where it required eight.** A whole
  answer still carries every field it did, so only a caller that passes
  `sections` meets the difference, and it met it by asking.
- **Rejected: calling the argument `topics`.** Every `covers` and `doesNotCover`
  entry already carries a `topic`, and a caller passing one of those as a
  section name would be making a different call than it thought.
- **`typo3-backend-module-development` is the first caller**, and it was one
  before this: its step asked for the knowledge depth available and any tool the
  caller excluded, which is `covers` and what no selection can take away. It now
  names `sections: ["covers"]` and reads about a twelfth of what it did.
- **Nothing changes in `typo3-development-installation`.** Its
  `typo3_server_scope` step is discharged by `typo3_project_describe`, and
  `D-ANS-083` records why that survives a cheap form: the answer arrives in the
  first call's own payload, so the second one fetches what the caller holds.

## Assumed

- That a caller who needs one part names it. Nobody has watched a session do it;
  what is measured is two sessions that took the whole answer for a part of it.
  `D-AUD-011` carries the same assumption about its index.
- That the purpose is worth its 1,318 characters in a narrowed answer. It is the
  largest thing this entry keeps always-on, and what it buys is a text half that
  stands on its own whatever was named.
- That the whole answer is unchanged. It is asserted rather than assumed for the
  text, which is byte-identical, and the data half gained `withheld` alone.

## Wrong if

- A session names a section, is answered, and reports it needed a part it did
  not name. Then the answer is cut in the wrong places, and what it names is the
  evidence this entry lacks.
- A session narrows and then reasons about the boundary from what came back.
  Then `withheld` is not doing the work and the whole answer is the only safe
  shape.
- Nobody but the skill passes `sections` over the sessions that follow. Then the
  argument is a surface nobody chooses off, `D-ANS-087`'s reasoning covered this
  tool too, and what a skill prescribes is the whole of what it bought.

## Covered by

- `ScopeTest::aCallThatNamesOneSectionIsAnsweredWithThatSectionAlone`
- `ScopeTest::noSelectionHidesWhatTheCallerExcludedOrWhatThisServerIsFor`
- `ScopeTest::namingNoSectionAnswersEverythingTheToolHas`
