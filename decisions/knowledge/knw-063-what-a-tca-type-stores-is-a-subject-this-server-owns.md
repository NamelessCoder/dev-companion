---
id: D-KNW-063
title: 'What a TCA type stores is a subject this server owns'
date: 2026-08-07
status: open
---

# D-KNW-063 — What a TCA type stores is a subject this server owns

**Nothing here says what column a TCA type produces, whether it is nullable, or
what its empty value is.** One session handed a user a wrong verdict for want of
it.

## Evidence

- `feedback/2026-08-07-065228`. Verifying Forge 109572 turned on one fact: a TCA
  `type=datetime` column that is not nullable never holds SQL NULL, and the
  factory maps its empty value back to `null` on read — so a mapped object
  reports `null` while the row does not. The session established it by reading
  core source across many turns, and names four places: `DateTimeFactory`,
  `DateTimeFieldType`, `QueryHelper` and `Backend::insertObject()`.
- What it cost is stated and is the most expensive outcome in this corpus. The
  session first reported the behaviour as correct, filed the issue as not
  reproducible as written, and told the user the reporter was wrong about
  everything but the symptom. Only after the user pushed back did it find the
  inconsistency. The defect was real and had been dismissed.
- `feedback/2026-08-07-065342` is the same gap from the manual side.
  `typo3_documentation_lookup` at 13.4, three phrasings, six results, none used;
  coverage 0.274 to 0.731 with the top match landing on the word "datetime" in a
  page title. The undocumented surface it names is whether
  `equals($property, null)` is supported API and what it does against a column
  that cannot hold NULL — which is what decides whether a report is a defect or
  a misuse.
- `feedback/2026-08-07-065329` is the same question a third time, as a tool gap.
  The live question was DDL: what column does `type=datetime` produce with and
  without `dbType`, is it nullable, what default does it carry. The session
  answered it by building a six-variant fixture table and measuring it on
  sqlite, mariadb and postgres.
- `bin/cli hints:probe "TCA datetime nullable null empty value stored in the database"`
  reaches `tca-formengine` alone, on `appliesTo(6) + text(66)`. No hint in the
  corpus carries `nullable`, `DateTimeFactory` or the storage side of a TCA
  type. That is the 1a probe and it comes back empty.

## Decided

- This is a gap and it is taken on. Three reports out of two sessions, one of
  them with a wrong answer that reached a user, and the probe confirms nothing
  here answers it.
- The subject is the storage side of a TCA type — the column, its nullability,
  its empty value and what reads that value back — rather than `type=datetime`
  alone. Datetime is where it was hit; the shape of the question is the same for
  every type whose empty value is not NULL.
- What it says about TYPO3 is not decided here and may not be. This run has read
  this repository and no core checkout: `bin/cli checkouts:status` reports all
  four missing in this working directory. Copying the feedback's own account
  into `knowledge/` would put a guess where a reading belongs, which is the one
  failure nothing downstream can detect.
- `typo3_schema_lookup` is a separate finding and not the answer to this one. It
  returns `notnull` and `default` per column and needs a booted installation
  with the table in it, which is a different question from what a type produces.
  Its description not naming those two fields is queued on its own.

## Assumed

- The four places the feedback names are the right ones. They are a session's
  reading of a checkout this run cannot open, so they are where the research
  starts rather than what it concludes.
- The manual has no page here. That is one session's search at 13.4 with three
  phrasings, and an absent page is the hardest thing to establish from a search.

## Wrong if

- The reading finds the manual does cover it, which would make this a routing
  finding about `typo3_documentation_lookup` rather than a corpus gap.
- The behaviour turns out to differ across 12.4, 13.4, 14.3 and main in ways one
  statement cannot carry, which would make it a document rather than a hint.
- Forge 109572 is fixed in a way that changes the storage rule, which would date
  the statement before it is written.

## Since then

On 2026-08-07, the reading was done against `.checkouts/` at 12.4, 13.4, 14.3
and `main`, and `tca-datetime-storage` is what came of it. All four places the
feedback named hold, and two things it did not know are now part of the
statement.

The first is a version boundary. `DateTimeFactory` and `DateTimeFieldType`
arrived in 13 and are absent from 12.4 — but `DateTimeFieldType::isNullable()`
arrived a major later still, in 14, with
`[TASK] Make use of TcaSchema in DefaultTcaSchema`. So the default the feedback
reported for a `dbType` column is `true` only from 14; on 13.4 the class
inherits `isNullable()` and the default is `false` there as well. The session
read it on 15.0.0-dev and reported it flat, which is exactly what a statement
bound to one branch looks like.

The second is that the write side is the same rule read backwards.
`QueryHelper::transformDateTimeToDatabaseValue()` writes `0` for an integer
column and the empty literal for a native one, and
`DateTimeFactory::fromDatabase()` maps that value back to `null` — so the round
trip is null to zero to null, and the row is not NULL at any point.
`Backend::insertObject()` omits a null property from the row on all three
branches, so the column default decides instead.

The second half of the card is settled too, and against the tool rather than
from the reading: `typo3_documentation_lookup` at 13.4, asked twice more with
different phrasings, reaches nothing about `QueryInterface` constraints against
null. Best coverage 0.344, on the Doctrine `QueryBuilder` page and a TypoScript
data processor. So `feedback/2026-08-07-065342` is a corpus gap and not a
routing failure, which is what this entry's first **Wrong if** was watching for.
