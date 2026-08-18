---
id: D-KNW-099
date: 2026-08-18
status: open
---

# D-KNW-099 — What a row handed to lib.contentElement owes is a gap this server owns

**What a row handed to `lib.contentElement` has to carry since v14, and that
`RecordFactory` rejects one that does not, is inside this server's boundary and
missing from it.**

The change arrived with no changelog entry, so the corpus is the only source
that could carry it. A caller holding the exception has three words — the class,
its code and the field it names — and none of them reaches anything here or in
the changelog.

## Evidence

- `feedback/2026-08-18-074350` reports a session that never asked, on the
  assumption that a changelog query would not reach the change. The assumption
  holds, and for a reason no query shape fixes: there is no entry.
- The change is real and is a v14 one.
  `fluid_styled_content/Configuration/TypoScript/Helper/ContentElement.typoscript`
  carries `dataProcessing { 1770716912 = record-transformation }` in
  `.checkouts/14.3` and nothing of the kind in `.checkouts/13.4`.
- It shipped as `b0ee153010`, "[TASK] Use f:render.text in fluid_styled_content"
  (#108935, `Releases: main`), six files, none of them a changelog entry. So a
  behaviour change that breaks third-party code is absent from the changelog
  because the commit that made it was not the kind that writes one.
- `IncompleteRecordException` occurs in no changelog entry at all in
  `.checkouts/14.3`, only in `Domain/RecordFactory.php`, its own class and
  `RecordFactoryTest.php`. `RecordTransformationProcessor` occurs in exactly one
  — `13.3/Feature-103581-AutomaticallyTransformTCAFieldValuesForRecordObjects` —
  which is about TCA value transformation and names `lib.contentElement`
  nowhere.
- The corpus is silent in the same words.
  `bin/cli hints:probe "IncompleteRecordException"` reaches nothing, and the
  term occurs nowhere below `knowledge/` or `skills/`.
- The two hints a caller does reach are each about something else.
  `page-content-element-rendering` is the template, partial and layout roots of
  `lib.contentElement` and says nothing about its data processing;
  `frontend-records` names `record-transformation` as a nested processor under
  `database-query` and never as something `lib.contentElement` runs by itself.
- The manual cannot be reached from those words either. It is matched against
  page titles and section paths, so a PHP identifier has no page to be titled
  after — the reading `D-ANS-010` was narrowed by, which is why this is a
  statement to write rather than a routing to fix.
- The mechanism is three guards rather than one, which is what made the
  reporting session iterate. `RecordFactory` throws `1726046917` for a
  language-aware table missing a language field, `1726046918` for a
  workspace-aware one, and `1726046919` for any field a system capability
  declares — `RecordFactory.php:241`, `:267` and `:289` in `.checkouts/14.3`. A
  caller fixing the field one exception names meets the next.

## Decided

- **Queued**, not closed on the spot. The gap is a statement about TYPO3, and
  what the field set actually is has to be read against the checkouts, which
  this judgement did not do.
- The subject is the row rather than the processor: what a synthetic
  `tt_content` row handed to `lib.contentElement` through `f:cObject` owes since
  v14, and what happens when it does not. Which rows a query returns stays with
  `persistence-reading`, what a `Record` hands out stays with
  `record-system-properties`, and the template roots stay with
  `page-content-element-rendering`.
- Nothing changes about `typo3_changelog_lookup`. The feedback's suggestion —
  make the entry reachable from the symptom — has no entry to reach, and the
  identifier search `D-ANS-042` already built would find one if it existed.
- The card goes to `normal`. One session reported it, which does not lift a card
  on its own; what lifts this one is that the changelog cannot answer it at all,
  so a session that does the right thing still ends up reading vendor source.
- Recorded here rather than against `D-ANS-010`. That entry is about a silence
  the manual answers, and this silence belongs to neither corpus: the event
  happened and was never written down anywhere outside the commit.

## Assumed

- That the field set is stable enough across the covered majors to be one bound
  statement rather than a table. The guards are the same three in
  `.checkouts/14.3`; which fields `tt_content` declares capabilities for was not
  read.
- That the reporting session's third failure — `fe_group` read as a string — is
  a property of the transformation rather than of that extension's row. It is
  reported and not verified here.
- That a caller meets this holding the exception rather than the processor's
  name, so `appliesTo` has to carry the class and the three codes.

## Wrong if

- The reading finds `lib.contentElement` runs the processor on 13.4 as well
  through some other route, which would make this a statement without a version
  boundary and the feedback's account of what changed wrong.
- A row that is missing those fields turns out to render anyway on a
  `tt_content` whose TCA a project has trimmed, which would make the guard a
  property of the schema rather than something a caller can be told.
- The core writes a changelog entry for this after all, in a 14.x still to come,
  which would make the gap a delivery question and put the answer back in
  `typo3_changelog_lookup`.
