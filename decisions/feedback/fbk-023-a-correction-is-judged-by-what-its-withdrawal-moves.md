---
id: D-FBK-023
date: 2026-08-02
status: open
---

# D-FBK-023 — A correction is judged by what its withdrawal moves

**A feedback that corrects earlier ones is judged by testing each of their
judgements against the premise it withdraws, and not walked down the ladder on
its own.**

Only a judgement that used the withdrawn claim as evidence moves. The ladder is
still owed once, for the correction's own lever, and that lever is rarely the
subject the earlier notes were about.

## Evidence

- `feedback/2026-08-01-003736` withdraws one claim from three notes of the same
  session — that the `typo3-extension-testing` skill "was never activated" and
  that "no skill was activated". Its ground is that the reporting conversation
  begins at an anchored summary, so the skill activation, if there was one, is
  before its window. It keeps the rest: the rating of the skill's workflow
  "remains valid as far as it goes".
- `bin/cli hints:probe` on its `Query` matches nothing and returns 22 hints as
  the index. That is not step 1a. The subject is what one session could see of
  itself, which is neither TYPO3 nor anything `knowledge/` would hold.
- [`documentation/feedback/judging.md`](../../documentation/feedback/judging.md)
  does not assess whether a self-criticism is accurate, so a withdrawal can only
  move a judgement that took the claim as evidence rather than as the report it
  came in. Three siblings, three answers.
- `002926` — archived on 2026-08-02. Its judgement is the **Since then** of
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point.md)
  and [`R-SKL-010`](../../requirements/task-skills/skl-010-a-skills-description-names-every-side-of-what-it-owns.md),
  and it rests on the descriptions themselves: `typo3-content-element-development`
  opened on "frontend content elements" and reached `previews` ninth of eleven,
  while the task was a backend preview the same skill covers in as many words.
  That was read off the files here, and
  `SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` holds the
  rewrite. The withdrawal moves the sentence about what the session called, not
  the finding, and it is written into that entry.
- `003533` — judged as
  [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md),
  step 3, on four hint probes against this repository. That entry already
  records the withdrawal and says the trigger is not its lever. Unmoved, and its
  todo stands.
- `003634` — in hand on `todo/self-rating-against-the-typo3-extension-testing`
  as this is written, so nothing of it was touched here. What the withdrawal
  does to it is the largest of the three: the reason it gives for all five of
  its section scores is "skill never activated via the skill tool", and that is
  no longer evidence. If the skill was active, its Playwright half is a rule
  that was delivered and did not take, which is rung 4 rather than rung 3.
- The lever is this repository's own debrief prompt, in
  [readme.md](../../documentation/feedback/readme.md). It asks for exactly the
  fact the session could not see — "Report the session you just had from your
  own transcript" — and offers the skill question two answers: name it, or "If
  none activated, say so — that is a result". An agent whose context begins at a
  compaction summary has no transcript for the first half of its own run, and
  neither answer is the one it could give.
- The other four bullets ask for the same kind of fact: which calls, in which
  order, how many round trips each cost. So the qualification belongs on the
  lead sentence and not on the skill bullet.
- Nothing about TYPO3 was established here. Every reading above is this
  repository on 2026-08-02.

## Decided

- Rung 4, wording, and closed on the spot. The prompt's lead sentence now asks a
  session whose transcript begins at a summary to say so and answer for the part
  it can see. The rule was already there — "not from how it felt" — and what was
  missing is what the transcript half is worth when there is not one.
- The mapping above is the judgement of the correction and is written here so it
  is not derived a second time. No todo follows it: one sibling is archived, one
  is judged with its todo queued, and the third is being judged elsewhere.
- `typo3_feedback_record`'s parameter descriptions were rejected as the
  placement. They would also reach a session that files without the prompt, but
  they are a declared schema, which `judging.md` queues rather than improvises,
  and one incident is not the evidence for a contract change.
- What must hold from now on is
  [`R-FBK-012`](../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md);
  the feedback is archived by this commit.

## Assumed

- That the window was what the correction says it was. Nobody here has the
  transcript, which is the point: this repository can check neither the claim
  nor its withdrawal, and the change made does not depend on which is right.
- That a sentence in the prompt changes what an agent reports. Nothing has
  measured it, and the feedback filed after 2026-08-02 is where it would show.
- That the three notes are one session. The correction says so and the dates are
  minutes apart, but `002926` reports a different task from the other two.

## Wrong if

- A feedback filed after this reports a skill as never activated from a window
  that began at a summary, without saying so. The prompt is then not the lever,
  and the tool's parameters are the next placement.
- The added sentence buys a hedge on everything, and debriefs arrive unable to
  say what happened in their own session. "If none activated, say so — that is a
  result" is what that costs, and `D-AUD-003` and `R-SKL-010` were both built
  from a report of exactly that shape.
- A sibling's judgement turns out to have rested on the withdrawn claim after
  all. `003634` is the open one.
