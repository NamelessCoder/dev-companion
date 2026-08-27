---
id: D-AUD-011
title: 'The instructions index the question each tool answers'
date: 2026-08-18
status: open
coveredBy:
  - ScopeTest::theIndexNamesNoToolTheCallerExcluded
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - ScopeTest::theInstructionsIndexTheQuestionEachToolAnswers
  - ScopeTest::theScopeInstructionsOrientTheClientBeforeItsFirstCall
---

# D-AUD-011 — The instructions index the question each tool answers

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
  [`D-AUD-003`](aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  put the entry point in the `instructions` because under deferral the
  descriptions are not a channel at all, and
  [`D-SKL-060`](../task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it.md)
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

## Since then

`feedback/2026-08-18-113357`, from the same debrief as the first feedback above,
names an entry this one did not buy. The session wrote six commit messages for a
sitepackage repository and derived their convention from
`git log --oneline -15`, having read `typo3_commit_message_guide` as the core's
Gerrit convention from its name alone. That is step 2 again: the sentence it
needed stands in the tool's own `description` and in the `routing` entry
*Writing or amending the commit message*, and under this client neither is a
channel. So the third **Assumed** above is answered — the reported task shape it
said the remaining lines would lack is on the same board, and
`typo3_hint_lookup` is no longer the largest surface left out.

The name is not the alternative lever the feedback offers beside it. `typo3_` is
on every tool this server declares, so no name distinguishes core work from the
rest, and a tool name is the one surface clients installed months ago have
already written down — `AGENTS.md`.

What blocks the line is the room. Re-measured on 2026-08-18, the instructions
stand at 1912 characters with the stale-skills notice and nothing excluded, and
at 2035 in the worst assembled case, against 2048; an index bullet is about
seventy. Nothing a decision placed is left to displace, which is this entry's
third **Wrong if**.

The room is somewhere nobody placed. The index is prose inside the stored
`instructions`, so it is not filtered by `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`: a
caller that excluded everything it can is still told which four tools to call,
which is both the case that binds the budget and what `Coverage::offered()`
already refuses for `covers` and `routing`.

That step was taken on 2026-08-18. The index is data in
`knowledge/server-scope.json`, `Coverage::offered()` drops the entry of a tool
the caller excluded, and the line the feedback asked for is the fifth entry: the
commit message, in your own repository as much as in the core. The sentence
under the entries names the three that fail at runtime rather than counting
them, since which entry is first now depends on the caller. The heading and that
sentence are rendered with the entries, so a caller left with none is handed no
empty list.

What it measures, the same day: the longest assembly is now the stale-skills
notice with nothing excluded, at 2021 characters of the 2048. A caller that
excluded every tool it can reads 2018, where the room freed is spent naming all
23 rather than counting them, and 1598 where the notice pushes that prefix back
onto the count. So the budget bought the entry and is no longer what blocks the
next one — the third **Wrong if** above is what to argue with instead.

**The first Wrong if fired**, on 2026-08-19. `feedback/2026-08-19-090401` is a
JavaScript dependency update in `blog` under Claude Code's VSCode extension:
every tool arrived as a bare name with its schema deferred, the block arrived
whole, and the session counted the index inside it. It called nothing, and the
block is what it names as the only thing it read from this server. Two feedback
from the same debrief carry what that cost — a backend CSS class borrowed
unverified (`feedback/2026-08-19-090231`) and a commit message derived from
`git log` (`feedback/2026-08-19-090253`).

The index was current. Its sixth entry, the call that reads a whole procedure,
landed in `d8acbbf0` ten hours before the feedback was filed, and the session
still reports finishing without learning the corpus can be enumerated. So the
entry
[`D-AUD-007`](aud-007-the-prose-documents-are-named-where-a-session-already-looks.md)
bought was delivered and read and did not reach this one either.

What separates it from the two feedback this entry was built on is that those
sessions did not know what to call and this one had decided. Its own account: it
was never unclear whether the server could answer, it was confident it could
not, and confidence does not trigger a scope check. That confidence was right
about the third-party library it was upgrading and wrong about the TYPO3 half of
the same task, which is where both costs above came from.

An index answers a session looking for a tool. Nothing in the block answers one
that has stopped looking, and what the feedback asks for instead is the boundary
— three lines of covered and not covered, so a session sure it is off-topic
reads something that contradicts it.

That is this entry's third **Wrong if** rather than the first's remedy. Measured
on 2026-08-21, the longest assembly stands at 2028 characters of the 2048, so
twenty are free against about seventy for one bullet and the 1153 `D-AUD-007`
priced for naming the documents. And the lever this entry named as what is left
to try is closed: `D-SKL-060`'s fetch line was put to the maintainer on
2026-08-19 and the answer was not to.

Two of the feedback's own suggestions are refused on their own ground rather
than on the room. `typo3_reference_list` is not the document list — re-run on
2026-08-21 it enumerates the core's worked examples, `theme_camino` and
`styleguide` — so naming it would not have shown the session the shelf it
wanted. The other is the name, which the paragraph above already answers; re-run
with the session's own subject, `npm build scripts webpack in an extension`,
`typo3_script_lookup` answers with the four topics of the core script notes and
routes elsewhere, so the call the name cost would have returned nothing about
the task.

What goes up is the budget, because every lever left is instruction-block
characters that do not exist. That is
[`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md),
which measured one client on 2026-07-31 and has not been re-measured; the client
reporting here is another one, it delivered the block whole, and where it
truncates only a session inside it can say. `todo/waiting/T-260819-dcaf.md`
carries the question.

**The first Wrong if fired again** on 2026-08-25, this time against the entry
this one bought. `feedback/2026-08-25-105141` is a core commit message written
in the TYPO3 CMS checkout under the same deferring client: the session read the
block, read the fifth entry, and made no call in that turn. Its account of why
is not that the line was missed — the checkout's own `AGENTS.md` carries the
commit rules in full, so a tool named for the same question read as redundant.

What that cost is on the record. The session wrote `Releases: main, 14.3, 13.4`
off the branch dates `git for-each-ref` prints, and a `typo3_rule_lookup` on
`core/contribution/gerrit-workflow` in the next turn corrected it to
`main, 14.3`. The guide answers that without the second call: run on 2026-08-27
for a core bugfix carrying no `releases`, it names the lines that can take a
patch at all and says a bug fix goes to main and 14.3, an older line only where
the severity earns it. So the entry named a tool that was right, and the file
that was wrong won.

The wording is the lever, which is step 4 rather than step 2 or 3: the block
arrived, the entry was current, and the session read it. What the entry did not
say is what the guide settles that a rules file cannot. The branch set is a
project fact that changes and no checkout carries it, which is why
`feedback/2026-08-26-223348` lost it too — a second session, at another task
shape, guessing the set off `git branch -r` and reporting that nothing suggested
this server would know it. That one is judged on its own card.

The entry now reads *the commit message, yours as much as the core's, and its
branches*, and it pays for itself.
`in your own repository as much as in the core` is `yours as much as the core's`
in eighteen characters fewer, which is what the branch clause costs to the
character: measured on 2026-08-27 both assemblies are unchanged at 1948 and 2033
against the 2048. The third **Wrong if** is therefore untouched, and the budget
question stays where `D-ANS-004` has it.

The feedback's other suggestion is refused rather than deferred. It asks that
the three tools a core session opens with arrive with their schemas loaded, and
nothing in the protocol lets a server ask a client for that. The fetch line
[`D-SKL-060`](../task-skills/skl-060-a-skill-names-a-tool-at-the-step-that-needs-it.md)
proposed was the last lever of that kind, and the maintainer answered not to on
2026-08-19.
