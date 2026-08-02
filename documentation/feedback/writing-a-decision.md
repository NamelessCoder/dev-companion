# Writing a decision, and going back to one

What a decision **is** and where it lives is
[decisions/readme.md](../../decisions/readme.md). This is how one is written,
and what a later session does with it.

An entry is written by the commit that implements it. Nothing here is a
proposal stage: by the time a decision exists, the change it describes is in
the code, and what the file carries is the part a commit message cannot — the
assumption, the evidence available at the time, and what would show it wrong.

## What an entry looks like

```markdown
---
id: D-DIS-004
date: 2026-07-29
status: open
---

# D-DIS-004 — The version comes from the core package, not from the console

**The installed version is read from the core package's `Typo3Version` class
rather than asked of `bin/typo3 --version`.**

The catalogs are pinned to one revision and every answer was phrased as
timeless fact, while the server had the other number all along.

## Evidence

- What was measured or read at the time, with its numbers.

## Decided

- What was done, and what was rejected in doing it.

## Assumed

- What the decision rests on that nobody has verified.

## Wrong if

- What would show it to have been wrong, concretely enough that somebody could
  notice it happening.

## Covered by

- `SomeTest::theMethodThatWouldCatchIt`
```

- The **bold first sentence** is the decision. A reader who stops after it knows
  what was settled; everything under it is what settled it.
- The sections are a fixed set, in that order: **Evidence**, **Decided**,
  **Assumed**, **Wrong if**, **Covered by**. Only **Wrong if** is required — an
  entry that cannot say what would falsify it is not a decision worth
  recording. `date` is the day it was decided.
- Each section holds one bullet per item. Half the entries decide more than one
  thing and a fifth rest on more than one assumption, which is why these are
  sections and not a bullet repeating its own label.
- **Covered by** is optional and lists the tests that would catch the **Wrong
  if** happening, one per line. Most entries are about process and nothing runs
  over them; where something does, naming it is what turns the promise into
  something the suite keeps. Every test named anywhere in an entry has to exist
  — `DecisionsTest::everyTestADecisionNamesExists`.

## What a later session adds

A dated section at the foot and nothing else: **Confirmed on `<date>`** where
somebody went back and it held, **Revoked on `<date>`** where it did not, and
**Since then** for what followed without a date of its own. Those carry prose
rather than bullets, because each is an account of one reading.

`status` is one of `open`, `confirmed` and `revoked` — the `DecisionStatus`
enum — and it names the **last** dated section rather than the only one. A
decision has a history: `D-KNW-003` was confirmed by a run on the morning of
2026-08-02 and revoked by the evidence that arrived the same day, and both are
in the file. What a reader relies on is the latest.

The status is not a workflow. `open` does not mean unbuilt — it means nobody
has been back to the **Wrong if** yet.

`revokedBy` is what a revoked entry owes its reader: where to go instead. It
names one decision, only a revoked entry may carry it, and the generated
listing shows it, so nobody has to open a dead entry to find the live one.

## What rests on a decision

A requirement says which decisions it stands on, in its own front matter —
`restsOn: [D-FBK-005]`. That is the one crossing neither directory can see on its
own: a decision is revoked, the requirement written on top of it keeps its
`held` status and its passing test, and the reasoning under it is gone.
`bin/cli backlog:list` reads that out; nothing fails on it, because whether the
requirement still stands is a judgement.

## Going back to one

Most decisions are open and stay that way, which is what makes the state easy
to stop seeing: a **Wrong if** written and never read is a promise, and nothing
says when to keep it. `bin/cli backlog:list` counts them and names the oldest —
not because age disproves anything, but because that is the entry the
repository has moved furthest away from since. Going back to one and adding
**Confirmed on** or **Revoked on** is a legitimate task with no feature behind
it.

`bin/cli decisions:check` holds every file to the shape above, and
`composer test` runs the same check through `DecisionsTest`, except the
listing: that one is generated from every file in a group, so it can only be
true on a checkout that has all of them, and `DecisionsTest` would fail every
branch that adds an entry — [`D-FBK-011`](../../decisions/feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md).
