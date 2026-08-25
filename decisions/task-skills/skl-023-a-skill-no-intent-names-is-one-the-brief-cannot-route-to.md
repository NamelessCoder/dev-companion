---
id: D-SKL-023
title: 'A skill no intent names is one the brief cannot route to'
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
---

# D-SKL-023 — A skill no intent names is one the brief cannot route to

**Three of the twelve published skills are named by no entry in
`knowledge/task-intents.json`, so `typo3_task_guide` cannot route to them and
routes somewhere else instead.**

## Evidence

- `feedback/2026-08-07-233443`. A session triaging a core bug called the guide
  as `references/base.md` step 3 requires, described the task as "Triage an old
  open core bug report", and got `skills: ["typo3-extension-conformance"]` — a
  workflow for reviewing an extension or sitepackage repository, in a checkout
  `typo3_project_describe` had reported one call earlier as `core-checkout` with
  `extensions: []`.
- Re-run on 2026-08-08 with the session's own arguments: same answer, and the
  checklist that comes with it is patch-review content — "enumerate what it
  removes or renames before judging it", extension-scanner matchers, `[!!!]`
  prefixes, `checkRst` over a core diff. A triage writes no diff. The session
  used none of it.
- The cause is not the `audit` intent choosing badly. `typo3-core-issue-triage`
  is named by no intent at all, and no intent's `match` or `matchWeak` carries
  the word "triage". The same holds for `typo3-core-patch-checkout` and
  `typo3-extension-documentation`: nine of the twelve published skills are
  reachable from the guide and three are not.
- So the guide answered with the nearest intent that did match, which for
  read-only work is `audit`, whose `skill` is the extension conformance workflow
  and whose `skillCore` is the patch review one. Neither owns a triage.
- The session says the call still paid for itself on its suite and option
  blocks, and that a session trusting the routing field over the skill list
  "would have run an extension conformance review inside a core checkout".

## Decided

- A skill this repository publishes and the brief cannot name is a routing hole
  rather than a preference. The client selects on descriptions and the guide
  selects on intents, and a skill present in the first and absent from the
  second is reachable only by a caller who already knew it existed.
- So the set is held rather than curated by memory: every published skill is
  named by at least one intent, and a new skill that arrives without one fails a
  check instead of being discovered by a session that got the wrong workflow.
- What each of the three needs is not decided here. Triage is a task shape with
  its own vocabulary — tracker, Forge number, backlog, "is this still a thing" —
  and is a candidate for an intent of its own; the other two may belong on
  existing entries. That is the todo's reading.
- The checklist is a second finding on the same call and is not the same fix.
  `audit` returns removal, extension-scanner and changelog-file items to a task
  that produces no diff, which the domain withholding already does for hints.

## Assumed

- The three are the whole of it. Measured today against `skills/` and the intent
  file; a skill published under another mechanism would not be counted.
- Routing to the wrong skill costs more than routing to none. The session did
  not follow it, so what is measured is the wrong answer rather than its
  consequence.

## Wrong if

- A skill turns out to be deliberately unroutable — reached only from its own
  description because the guide cannot tell the task apart — which would make
  this a check with an exemption rather than an invariant.
- Giving triage its own intent is reported as pulling patch work into it, which
  would say the vocabulary does not separate the two.

## Since then

The hole is closed and held. `SkillTest::everyPublishedSkillIsNamedByAnIntent`
asserts the whole set at once rather than one skill at a time, and this entry is
what it declares beside `D-SKL-064`; the attribute was missing until 2026-08-23,
so the invariant was guarded and the entry read as held by nothing.

Triage got the intent this entry left to the todo. `triage` in
`knowledge/task-intents.json` carries `skillCore: typo3-core-issue-triage` and
matches on the vocabulary the **Decided** predicted — `triage`, `backlog`,
`forge.typo3.org`, `bug report`, `still reproduce`, `still a thing`. Re-run on
2026-08-23 with the reporting session's own words, "Triage an old open core bug
report" at `changeType: bugfix`, `typo3_task_guide` answers
`typo3-core-issue-triage` and nothing else, where it answered
`typo3-extension-conformance` then.

The first **Wrong if** did not fire in the case it names. The check has exactly
one exemption and it is not a published skill that cannot be routed to: a draft
is out of the set, because a draft reachable by routing is one nobody chose —
`D-SKL-064`. Nothing reports the second one either; no feedback since says an
intent for triage pulled patch work into it.

### 2026-08-25 — the intent names the skill, and the word people call the tracker by reaches it weakly

[`feedback/2026-08-24-173236`](../../feedback/archive/2026-08-24-173236-task-guide-schema-was-fetched-and-never-called.md)
asks for one thing to be re-run: the bare task text "fetch another old issue
from Forge, create a branch, work it off". Its session loaded
`typo3_task_guide`'s schema, never called the tool, and reports that a call
would have routed it to `typo3-core-issue-triage`. Run in this checkout on
2026-08-25, that text matches `triage` weakly and answers `skills: []`. The call
it says it should have made names no skill.

Two gates decide a core-scoped intent, and the brief fails both.

- **The scope.** `Scope::CORE_WORK` is matched with `str_contains`, so the
  tracker's own name is carried as `'forge '` and the trailing space is what
  keeps "forget" out. The comma in "from Forge," sits where that space has to
  be. The same sentence with "from Forge and" reads as `core`, which is the
  whole of the difference.
- **The needle.** `forge` is in the triage intent's `matchWeak`, beside
  `reproduce`, `unresolved` and `old issue`, and a weak intent names no skill.
  So the brief that clears the scope gate is answered with none either: "fetch
  another old issue from Forge and create a branch, work it off" reads as
  `core`, matches `triage` weakly, and answers `skills: []`.

`forge.typo3.org` is a strong needle and "triage Forge 15984" answers
`typo3-core-issue-triage`, so what fails is the brief that names the tracker the
way a contributor does and never states the verb. This entry's **Decided** named
that vocabulary — "tracker, Forge number, backlog" — and the section above
reports the hole closed on a task text that carries the word "core" three times.

**The word is read as the tracker's name on both gates, and the repair is
queued.** `Text::containsWord` is what the intent matcher already uses for this,
and it keeps "forget" out by the rule rather than by a space. Which needles the
triage intent takes is the todo's reading rather than this one's: bare `forge`
in `match` is one shape, the phrases around it — "issue from forge", "forge
issue" — are another, and the second is what `reporting` already carries, so the
choice is about what else starts matching. It is queued because half of it is
`src/Knowledge/Scope.php`, and the card serving that feedback carries it at
`normal`.

Both halves landed the same day.
[`D-SKL-077`](skl-077-the-triage-intent-matches-taking-an-issue-off-the-tracker.md)
is the reading that was left here: `isCoreWork()` matches each marker as a word,
and the triage intent takes "from forge" and "off forge" rather than the bare
name, which was measured to read a patch on one chosen issue as a triage as
well.

**Against the feedback's own suggestion**, which is to move the triage clause to
the front of `typo3_task_guide`'s description so that a session scanning it
calls the tool. What that buys on this brief is the answer above. The
description is also byte-identical to the one seven later sessions in the same
checkout did call the tool on — `feedback/2026-08-24-183319`,
`feedback/2026-08-24-205113`, `feedback/2026-08-24-205223`,
`feedback/2026-08-25-105300`, `feedback/2026-08-25-105324`,
`feedback/2026-08-25-110726` and `feedback/2026-08-25-110753`, each naming an
argument it was called with.

The rest of that report is answered.
[`D-SKL-038`](skl-038-the-change-answer-names-the-skill-that-owns-the-patch-it-describes.md)
built `ForgeLookup::workflow()` on 2026-08-25, after the report: a page of the
backlog now names `typo3-core-issue-triage` and `typo3_changelog_lookup`, which
are the skill and the "sharpest miss" this session says nothing reached it with.
It stands at the moment the feedback names — holding the oldest-bugs list, about
to choose — and the skill hands over to `typo3-core-patch-development` itself,
which is the second skill that stayed shut.
