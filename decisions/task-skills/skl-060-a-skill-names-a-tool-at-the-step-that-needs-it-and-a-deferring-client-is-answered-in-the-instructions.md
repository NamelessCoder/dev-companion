---
id: D-SKL-060
date: 2026-08-18
status: open
---

# D-SKL-060 — A skill names a tool at the step that needs it, and a deferring client is answered in the instructions

**A skill names a tool at the step that needs it, and a client that defers tool
schemas is answered in the `instructions`.**

A session reported paying one schema fetch in front of every first call and
asked for the tools a workflow ends in to be listed together near its top. The
names were all delivered, in order, before the calls that needed them.

## Evidence

- `feedback/2026-08-18-074627`, a repair on a DDEV installation of `t3g/blog`.
  Four schema fetches: one batching the four tools `skills/base.md` opens with,
  then `typo3_changelog_lookup`, `typo3_commit_message_guide` and
  `typo3_feedback_record`, each on its own and each in front of the call it was
  wanted for. The session's own account is that none of the three was
  unforeseeable and that the batching was its to do.
- The names were delivered. `skills/base.md` names `typo3_changelog_lookup` as
  step 5 of the order the session had read, and
  `skills/typo3-development-installation/SKILL.md` names
  `typo3_commit_message_guide` in the last step of **Prove it**, which the
  feedback says stood several screens before the moment it was needed. What the
  session skipped was step 5, not a name it never saw.
- Nothing is missing from the corpus.
  `bin/cli hints:probe "every tool arrives deferred so each first use costs a schema fetch the skill could have batched"`
  on 2026-08-18 matches no hint and returns the index — the subject is a
  client's own mechanics, which `knowledge/` is not about.
- The placement it would reverse was decided from measured runs.
  [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md)
  put a guide id on the step that reaches the moment,
  [`D-SKL-030`](skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md)
  the lookup on the surface that can be answered by it, and
  [`D-SKL-014`](skl-014-the-commit-step-is-named-where-a-skills-workflow-ends-in-a-change.md)
  the commit guide where a workflow ends in a change.
  [`writing-a-skill.rst`](../../documentation/contributing/writing-a-skill.rst)
  states the same rule for the call that reads a whole procedure: name it once,
  where it is needed, and not at every mention.
- A second mention is already read as something else. `typo3_server_scope` is
  discharged by the installation workflow, and
  `SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere` fails on a
  skill that names a discharged tool again —
  [`D-SKL-055`](skl-055-a-call-a-skill-names-in-order-not-to-make-it-is-written-as-a-discharge.md).
  A fetch line listing that workflow's tools therefore either fails the suite or
  is a fetch line that deliberately leaves one out.
- The channel that survives deferral is the `instructions`, which is what
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point.md)
  established and what `feedback/2026-08-18-113308` reports again from its own
  side: the tool descriptions never arrived and the `instructions` did, in full,
  from the first turn. They name six of the tools this server declares —
  `typo3_project_describe`, `typo3_task_guide`, `typo3_component_lookup`,
  `typo3_icon_lookup`, `typo3_label_lookup` and `typo3_server_scope` — and none
  of the three this session fetched late.
- The corpus carries schema deferral twice, from two directories. Beside this
  one, `feedback/2026-08-18-113308` reports a whole session in
  `/home/benji/projects/bootstrap_package` that called nothing at all under the
  same client property, and asks for what to call for what in the
  `instructions`. That is a report about the same property and a different
  lever, and its judgement is not this one.

## Decided

- **Step 5 of the ladder in
  [judging.rst](../../documentation/records/judging.rst).** The answer was here,
  it was in the file the session was working from, it was in the right skill and
  it was worded as a step of an order. What the feedback measures is what naming
  a tool where it is used costs a client that fetches a schema per first call.
- **No skill gains a fetch line.** It is a second copy of the routing at the top
  of a file no release of this server corrects, in a project where a step that
  later routes elsewhere leaves the list saying the old thing; it is a mention
  the discharge rule already gives another meaning; and it states a property of
  one client in a file the other clients read too.
- **Proposed, and the question goes up.** Whether to pay that anyway is not
  something this process may decide on its own, and the card carries the
  question with what building it would cost.
- **The `instructions` are named as the candidate and not changed here.** They
  are the one surface a deferring session gets whole, and they are what
  `feedback/2026-08-18-113308` is about; a judgement of this feedback that
  rewrote them would decide that one without reading it.
- **Nothing is trimmed from the feedback.** Its observation is a cost report and
  stands whole; what waits is its suggestion.

## Assumed

- That a list at the top of a skill would be acted on. The session had the
  order, which is the same list in the sequence it is needed in, and batched
  four of it — so what the list adds is that the later names stand before the
  work rather than inside it, and nobody has watched a session use one.
- That the cost is the three fetches. The same session says the server was cheap
  and that its expensive loops were not server calls at all, so the round trips
  this entry weighs are the ones it counted and not the ones it paid.
- That a sentence in the `instructions` can spare them. It reaches a session
  that read the block, and whether a session batches off it is behaviour nothing
  here has measured.

## Wrong if

- A session that read the `instructions` still reports a fetch in front of every
  first call. Then the channel was not what was missing, and the fetch line in
  the skills is what is left to try.
- A session reports batching its fetches off a list at the top of a skill. Then
  the list is delivery after all, and the cost this entry weighs it against is
  what has to be paid for it.
- The count comes back much larger than three. A client that defers charges per
  first use, so a workflow whose steps name ten tools pays ten, and a session
  reporting that is reporting a different order of cost than the one judged
  here.
