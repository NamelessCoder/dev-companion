---
id: D-KNW-090
title: 'The corpus names the PHP type a record arrives as'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::theRecordAndItsTransformedColumnsAreNamedAsPhpTypes
---

# D-KNW-090 — The corpus names the PHP type a record arrives as

**What `{record}` and a transformed column are as PHP types is a second question
about the variable the corpus already describes, and it is queued as step 1a.**

[`D-KNW-020`](knw-020-what-a-preview-template-is-handed-is-stated-on-both-majors.md)
states how a field is read off the record — the container branch, and the five
relational TCA types. A session declaring the variable with `f:argument` needs
the class name instead, and no statement carries one.

## Evidence

- The miss reproduces. `bin/cli hints:probe` on the feedback's subject reaches
  `preview-record-variable`, `content-element-preview`, `frontend-records` and
  `fluid-object-access`; every statement in them describes the record by what it
  does, and none names a class.
- Nothing below `knowledge/` or `skills/` contains `f:argument`,
  `RecordInterface` or `TypolinkParameter`. In this repository the three appear
  only in the feedback, in the card serving it, and in `D-KNW-014`'s evidence.
- `content-element-preview` reaches the neighbouring sentence and stops. It
  tells a preview to draw "a link field as its label beside the URI
  f:uri.typolink resolves for it", without saying that the value reaching the
  ViewHelper is an object rather than the stored string.
- Both claims hold on `.checkouts/14.3`. `Core/Domain/RecordInterface.php` and
  `Core/LinkHandling/TypolinkParameter.php` are there, and
  `RecordFieldTransformer` line 184 builds the second for a link column with
  `TypolinkParameter::createFromTypolinkParts()`.
- The `f:argument` half is not readable from a core checkout. 14.3 pins
  `typo3fluid/fluid: ^5.3.1` and carries no `ArgumentViewHelper` of its own, so
  what that ViewHelper validates and when has to be read in an installation's
  `vendor/` — the reach `D-KNW-014` already needed for `getByPath()`.
- One debrief rather than a domain. All 17 open feedback come from one directory
  on one afternoon, so `bin/cli feedback:list` counts this once. The second
  report is in the archive: `2026-08-01-002745` asked what a relation resolves
  to on the same variable, from a backend preview template rather than from a
  frontend partial.

## Decided

- Step 1a, and queued. The statements land beside the ones `D-KNW-020` put on
  `preview-record-variable`, so no capability is missing and no contract moves.
- `normal`, where the card arrived at `low`. One debrief argues for leaving it
  there; two things argue past it. The cost is counted — five round trips for
  two class names — and a wrong `f:argument` type fails at render time only,
  which is the shape
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  weighs.
- Not step 4. There is no sentence to reword: what the corpus states about
  reading a field off the record is right and complete, and what to declare it
  as is a different question about the same variable.
- Not step 2 or 3. The session reached the hints that own the subject and read
  them, and named them in its report.
- Not closed on the spot. Which TCA types transform into a value object is a
  reading of `RecordFieldTransformer`, and the `f:argument` half needs an
  installation, so both sides of the answer are established elsewhere.

## Assumed

- The types belong on `preview-record-variable`, beside the resolution
  statements, rather than on a Fluid hint. What a column arrives as is a
  property of the record transformation, and `fluid-object-access` already sends
  the container branch to that hint.
- A frontend partial and a backend preview meet the same transformed values. The
  reporting session declared one type in both and says it has no evidence for
  the preview side, which is why the todo settles that first.

## Wrong if

- The transformation depends on the rendering context, so a link column is a
  `TypolinkParameter` in one and the stored string in the other. One statement
  is then wrong in the half it does not name, and the failure stays at render
  time.
- `f:argument` is not what raised the reported exception. A statement about
  declaring types would then send a caller after the wrong mechanism.
- The set of columns that transform into a value object is open-ended enough
  that naming it dates on the next major — the way `D-KNW-020`'s five relational
  types can, and for the same reason.

## Confirmed on 2026-08-18

Both assumptions held and each was read rather than reasoned. The record a
preview template is handed and the one a frontend partial is handed are the same
object, built through the same factory on both majors — so one statement answers
both contexts, and the reporting session's undecided half is decided.

The strict argument declaration is what raised the exception: the validation
runs on render and not before, it accepts an interface name, and it rejects an
object for `array` unless it is traversable, which this one is not. Read from
the release the major pins rather than from an installation, because that is a
source the next session can produce.

The third **Wrong if** moved: the set does grow, so the statements carry a bound
per column rather than one for the set.
