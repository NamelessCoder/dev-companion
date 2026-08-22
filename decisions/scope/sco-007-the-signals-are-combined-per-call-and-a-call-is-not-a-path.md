---
id: D-SCO-007
date: 2026-08-01
status: open
---

# D-SCO-007 — The signals are combined per call, and a call is not a path

**`Scope::isOutsideCore()` combines the signals `R-SCO-001` orders over the
whole call: every path is folded into one string and one boolean comes back.
`META-03` is therefore a feature and not a wording change.**

Nothing decides per path, and nothing can say the audience is uncertain.

`META-03` hands the server two paths of different audience in one session and
asks what applies to each. Read against `Scope`, the question was which half of
`R-AUD-002` is missing: the per-path decision, or only the uncertain answer.

## Evidence

- Measured against the contract's own paths.
  `isOutsideCore(['packages/acme_events/Classes/Domain/Repository/EventRepository.php'])`
  is `true`,
  `isOutsideCore(['typo3/sysext/core/Classes/Database/Query/QueryBuilder.php'])`
  is `false`, and the two together are `false` in either order. The
  `typo3/sysext/` short circuit returns before the `packages/` marker is looked
  at, so the extension path is silently given the core verdict — the first
  outcome `META-03` says has to be avoided, verbatim.
- The haystack is `implode(' ', $paths) . ' ' . $text`. After that line nothing
  can tell which path contributed which marker, so no caller of the flag could
  draw the distinction even if it wanted to. `D-SCO-003` already draws the
  payload distinction per line; the audience it draws it from is per call.
- Of the four call sites, two can receive two paths at all — `typo3_hint_lookup`
  and `typo3_test_run_guide` take a `paths` array. `typo3_task_guide` takes one
  `area` string, so the prompt `META-03` is written in cannot be put to it in
  the first place.

## Decided

- Two things are missing rather than one. The unit of decision has to become the
  path, and the answer has to gain a third value for the case `R-AUD-002` names
  — signals that disagree with nothing to resolve them. A `bool` has room for
  neither, so this is the rename `D-SCO-006` predicted: `outsideCore` becomes
  the audience of a path.
- Against a mixed call answering `true` for the whole of it, which keeps one
  verdict per session and merely picks the other side of it, and against a
  second call-wide flag beside `outsideCore`, which is the same verdict under
  one more name.

## Assumed

- `R-SCO-001`'s ordering survives the change. It orders signals about one piece
  of work, and per path all of them are still available; only the free text and
  the kind of installation are per call, and both sit at the weak end of it,
  which is where a path carrying no marker of its own should land anyway.

## Wrong if

- Two paths of different audience turn out to be rare in recorded sessions, and
  what is common is one path against prose that disagrees with it. Then the
  cheap answer is enough — a call whose signals disagree names both audiences
  and applies neither silently, which is `META-03`'s second required outcome
  without the first.
