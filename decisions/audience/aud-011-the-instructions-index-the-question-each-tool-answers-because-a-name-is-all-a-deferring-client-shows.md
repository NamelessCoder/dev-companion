---
id: D-AUD-011
date: 2026-08-18
status: open
---

# D-AUD-011 — The instructions index the question each tool answers, because a name is all a deferring client shows

**The `instructions` name, per question, the tool that answers it, and the entry
is paid for out of the same 2048 characters.**

Two sessions on one day called this server nothing at all, in two projects and
at two task shapes, under a client that lists the tools by name and defers their
schemas.

## Evidence

- `feedback/2026-08-18-113308`, a pull request review in `bootstrap_package`.
  All 27 tools arrived as bare names in a system reminder, so the tool
  `description` — the surface a tool is chosen on — was outside the session's
  context, and roughly 130 calls over six turns went to Bash. The `instructions`
  did arrive in full, which is what the session says must not be broken, and it
  asks for five to eight lines mapping a question to a tool name.
- `feedback/2026-08-18-080710`, a TypoScript condition repaired in `blog` across
  two majors. Zero calls, and it names the one line that would have caught it:
  the `routing` entry sending a caller onto a major they have not built on
  recently to `typo3_changelog_lookup`, which is the call the session says it
  would make next time.
- Step 2 of the ladder in
  [judging.rst](../../documentation/records/judging.rst). The line exists, has
  stood in `knowledge/server-scope.json` since 2026-07-29 and describes that
  session precisely — and the whole `routing` block sits behind
  `typo3_server_scope`, which is a call, which a session that calls nothing
  never makes.
- The channel was already established.
  [`D-AUD-003`](aud-003-the-instructions-carry-the-entry-point.md) put the entry
  point in the `instructions` because under deferral the descriptions are not a
  channel at all, and
  [`D-SKL-060`](../task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it-and-a-deferring-client-is-answered-in-the-instructions.md)
  named them as the candidate and left them unchanged so this feedback would be
  read before they moved.
- The room is what is scarce. Measured on 2026-08-18, the worst assembled case —
  the stale-publication notice, a caller that excluded every tool it can, and
  the write sentence — stood at 2038 characters of the 2048
  [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
  holds, so five to eight added lines are about six times the headroom there
  was.
- One line the feedback asks for is not written. Its example sends a core TCA
  palette across two majors to `typo3_schema_lookup`, which answers for the
  table as the installation's own container assembles it; the cross-major half
  is `feedback/2026-08-18-113327` and is judged on its own card.

## Decided

- The three `Before …, call …` sentences become an index, and the index gains
  `typo3_changelog_lookup`. The stored text came out three characters shorter
  than it went in, and the worst case measures 2035.
- What paid for the entry: the repeated framing the three sentences carried,
  `against the installation` on the icon line, `its knowledge is English and`
  where the rule and the mitigation both stay, and `the range it does`. A
  displacement rather than an addition is what `R-ANS-013` asks of the next line
  either way.
- Not the five to eight lines. Every further entry costs about a hundred
  characters, and what is left to displace was placed by a decision — the entry
  point, the three runtime lookups, the English rule, the guides sentence.
  `typo3_hint_lookup` is the largest surface left out, and neither feedback
  reports a session that lost anything for want of it.
- No line says anything about a client. An index is worth as much to a client
  that shows descriptions, and a sentence about schema deferral in a file every
  client reads is the objection `D-SKL-060` raised against the fetch line.
- Both feedback are archived by the commit that carries this. Nothing about
  TYPO3 was looked up and no contract moved, which is what
  [judging.rst](../../documentation/records/judging.rst) asks before a feedback
  is closed on the spot. The fetch line in the skills is a different lever, and
  `feedback/2026-08-18-074627` stays open on `D-SKL-060`'s question.

## Assumed

- That a name-only list plus an index is acted on. What is measured is two
  sessions that had the names and the workflow framing and called nothing;
  nobody has watched a session choose off the index.
- That 2048 still binds.
  [`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md)
  measured one client, and a client that keeps more is being sent less than it
  would read.
- That the changelog entry is the most valuable line left. It is the one both
  feedback name; the rest would be reasoning about task shapes nobody reported.

## Wrong if

- A session under a deferring client reads the index and calls nothing anyway.
  Then the channel was not what was missing, and what is left to try is the
  fetch line `D-SKL-060` proposed.
- A session reaches for a tool the index names and reports that the answer does
  not fit the question. Then the line sold a tool it should not have, and
  compression is what did it.
- An entry earns its place and nothing is left to displace for it. Then the
  budget is what to argue about rather than the wording, which is `D-ANS-004`'s
  **Wrong if** and not this one.

## Covered by

- `ScopeTest::theInstructionsIndexTheQuestionEachToolAnswers`
- `ScopeTest::theScopeInstructionsOrientTheClientBeforeItsFirstCall`
- `ScopeTest::theInstructionsFitWhatAClientKeeps`
