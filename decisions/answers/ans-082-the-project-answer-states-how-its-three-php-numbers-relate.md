---
id: D-ANS-082
title: The project answer states how its three PHP numbers relate
date: 2026-08-18
status: open
---

# D-ANS-082 — The project answer states how its three PHP numbers relate

**The project answer states how its three PHP numbers stand to each other:
whether the declared floor clears what the core requires, and whether any
configured environment runs it.**

The three numbers have been in one answer since `D-KNW-055`, each described
against the other two, and the comparison between them has been the caller's. A
session that had all three declared a floor no environment it configured ever
executed, which is a claim nothing tests and every check passes.

## Evidence

- **The three numbers are there and the relation is not.**
  `typo3_project_describe` re-run on 2026-08-18 through
  `bin/typo3-dev-companion` against `.environments/e-site-14.3`: the opening
  line reads "PHP unconstrained declared and 8.4 in DDEV, and the installed core
  requires ^8.2 — the lowest a package here may declare", and
  `structuredContent` carries `phpConstraint: null`, `corePhpConstraint: "^8.2"`
  and `environment.php: "8.4"`. Each field's description names the other two to
  say which number it is *not*; none of them says how the values stand to each
  other.
- **The reporting session read that answer and narrowed anyway.**
  `feedback/2026-08-17-211157` declared `^8.3` in a project whose core requires
  `^8.2` and whose container runs 8.4 — above the floor it could have declared,
  below the interpreter every command was run on, so no line of that package was
  ever executed on the version it claims to support. It says it chose the number
  out of habit and that nothing prompted a reconciliation.
- **That is the third Wrong if of `D-KNW-055`, in a variant.** The entry named
  the failure as declaring the container's PHP as the supported minimum; what
  happened is a third number belonging to neither source. Both are the same
  event: the number landed, and what the manifest declares did not change.
- **The comparison is machinery this repository does not have.**
  `Versions::admits()` reasons in TYPO3 majors —
  `'^', '~' => $major === $stated` — so `^8.3` against `^8.2` is a question it
  answers "no" for the wrong reason. Reading a PHP constraint at minor
  granularity is new code, which is what puts this on the queue rather than in
  this commit.

## Decided

- **Taken on, queued.** It touches `src/`, the derived text and the tool's
  declared `outputSchema`, which is the line `documentation/records/judging.rst`
  draws around closing on the spot.
- **What the statement may claim.** What is declared, against what the installed
  core requires, against what the configured environment runs — and, where a
  floor is declared that no configured environment executes, that this is so.
  Nothing here ran anything, so it is not evidence the package works on its
  floor, and it is not a check that was performed.
- **On the tool rather than in a skill.** `D-KNW-055`'s Wrong if reads the
  failure as the workflow not saying where to look. A workflow step can say
  "reconcile the three", but the reconciliation is the work, and the one place
  that holds all three values is the answer that already prints them — a caller
  charged one context per call (`D-FBK-020`) should not pay a round trip to
  compare numbers it was just handed.
- **Priority `normal`, set here.** One session reported it, so not `high`; the
  cost is already counted — a supported range narrowed by two minors with every
  check green — so not the `low` a card arrives at.

## Assumed

- **That reading a PHP constraint at minor granularity covers the spellings a
  manifest writes.** `D-VER-004` made that assumption one level up and one
  spelling in the wild broke it, `>= 8.1 < 8.5` with spaces. The same corpus is
  what the minor-granular reading is owed against.
- **That the relation is worth a sentence every project pays for.** It is stated
  even where the three agree, for the reason `ProjectDescribe::floor()` already
  states the core's number where it repeats the project's own: a line the answer
  drops when nothing is wrong cannot be told from one it never computed.

## Wrong if

- The line lands and a session declares a floor it never runs anyway, now with a
  sentence in the answer saying so. Then the answer bought a claim rather than a
  reconciliation, and what was missing was a step in the workflow that writes
  the manifest.
- The comparison answers wrongly on a spelling in the wild — a bare `8.2.*`, an
  operator with a space, a hyphen range — and states the wrong relation with the
  answer's authority. That is worse than the missing line, because a caller
  cannot tell it from a correct one.
- Every project the corpus reaches turns out to declare a floor its environment
  runs, and the derived line is always the consistent case. Then it says nothing
  and costs a sentence on the first call of every task.

## Since then

Built on 2026-08-18, and the first **Assumed** holds, measured rather than
carried over. The corpus is every `require.php` below
`.checkouts/{12.4,13.4,14.3,main}` and the vendor trees they installed: 556
constraints in 36 distinct spellings, each one asked of `Versions::floor()` and
of composer/semver, the second by asking it for the lowest major.minor it admits
any release of. All 36 agree, `>= 8.1 < 8.5` included — the spelling that broke
`D-VER-004` one level up, and the reason the space-collapsing that entry added
is shared here rather than written twice.

Two shapes answer "no floor" instead of a number, and both are the second
**Wrong if** being avoided rather than met. `*` names no floor to compare. So
does Composer's hyphen range: `8.1 - 8.4` read as the comparators it splits into
would answer 8.4, its ceiling, and `D-VER-004` found that shape in none of 3179
constraints and left it unread. A spelling this will not claim to read costs the
caller the whole `phpRelation` object, which is a sentence missing rather than a
wrong relation carrying the answer's authority.

The reading is `Versions::floor()`, one level below the `admits()` beside it and
sharing its comparator pattern so the two cannot drift apart on a spelling. What
it feeds is `R-PRJ-010`.
