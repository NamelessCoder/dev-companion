---
id: D-DOC-043
date: 2026-08-22
status: open
restsOn: [D-DOC-041, D-DOC-042]
---

# D-DOC-043 — A test is what holds an entry to the code it points at

**`bin/cli decisions:check` names the entries pointing at this repository's code
that name no test under **Covered by**, and fails on none of them.**

A test named there fails when the behaviour moves, and
`DecisionsTest::everyTestADecisionNamesExists` fails when the test goes with it.
Nothing else couples the two.

## Evidence

- Read on 2026-08-22. Of 443 entries, 236 carry **Covered by**; 348 name a
  `Class::member` this repository declares, and 89 of those name no test.
- The three entries found stale that day carry none. `D-SCO-007` stood `open`
  while the call its statement describes was replaced twice, `D-ANS-045`
  recorded a closed list of thirteen directory names against a method that reads
  the directory, and `D-EVI-003` named a tool renamed weeks earlier.
- The two whose named code had moved under them and which were right both carry
  one. `TermSearchTest::aStemRunsPastItsOwnEndAndACuratedWordDoesNot` holds
  `D-ANS-050`'s claim and
  `SkillTest::aDraftIsWhatDeclaresItselfOneUnderThisServersKey` holds
  `D-SKL-027`'s. `Text` and `Installer` both changed on 2026-08-18 and neither
  entry needed a reader.
- So the signal proposed first was the wrong one. "The statement names code that
  has changed since" would have reported exactly those two and none of the
  three.
- The other coupling is weaker. 156 decision ids are named from `src/` and 266
  from `tests/`, and 155 entries are named in neither — but an id in a comment
  fails nothing. `D-SKL-027` is named nowhere in the code and is safe;
  `D-FBK-004` is named nowhere and is not.

## Decided

- A report beside the outgrown one in `bin/cli decisions:check`, most references
  first. `Decisions::uncovered()` is the reading and
  `DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOutRatherThanFailedOn`
  holds it.
- Nothing fails. Most entries here decide something about process and no test
  could keep them, which the format says in as many words — a demand for
  **Covered by** would be answered with a test name chosen to satisfy it, and
  that is worse than the silence it replaces.
- `Upkeep\Sources` is where the PHP this repository declares is read, because
  this is the second caller: `RecordsTest` had the same scan and now asks for
  it. The scan happens once per process, since both readings run over the whole
  corpus.
- The count is of classes named rather than of references, so an entry
  mentioning one class ten times ranks below one that reaches into five.

## Assumed

- That a **Covered by** names a test that would catch the entry's own claim
  moving. The format asks for exactly that and nothing measures it, so an entry
  naming a test about something else reads here as covered.
- That an entry naming none of our classes is not at risk in this way. It may
  still describe behaviour in prose without naming the class it belongs to, and
  nothing here sees that.

## Wrong if

- The number stands still while entries go on going stale, which would mean the
  report is read as a property of the corpus rather than as work. It is 89.
- An entry gains a **Covered by** naming a test that does not hold its claim, to
  leave the report. The name would resolve, the count would fall, and the entry
  would be no more coupled than before.
- An entry with a test goes stale anyway, because the test held a narrower claim
  than the statement. That is the assumption above failing, and a fourth stale
  entry carrying **Covered by** would show it.

## Covered by

- `DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOutRatherThanFailedOn`
