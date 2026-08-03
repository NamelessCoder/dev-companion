---
id: D-SKL-006
date: 2026-08-03
status: open
---

# D-SKL-006 — The site-new cluster earns the route into the skill that owns the task

**The seventeen open `site-new` feedback say what reaches a skill rather than
what a skill says, so the cluster earns the route and no second skill.**

[`D-SKL-005`](skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md)
read the other cluster the same way and turned 35 feedback into two skills. This
one arrives at the opposite answer, and the answer is worth as much: a domain
that already has its skill, a session that never loaded it, and fourteen
judgements of which not one reached the rung where a skill is built.

## Evidence

- **The corpus is larger than the card that asked for this reading.** 89
  feedback carry `directory: /home/benji/projects/site-new` — 72 archived and 17
  open. Seventeen rather than the eighteen the todo names, and nothing was
  archived on 2026-08-03, so the count was already wrong when it was written. By
  day: 37 on 07-29, 5 on 07-30, 24 on 07-31, 23 on 08-01.
- **The seventeen are two sessions, and they ran opposite ways.** Three are a
  conformance audit of `printworks_sitepackage` on 2026-07-31 between 19:29 and
  19:48. It loaded `typo3-extension-conformance`, and its own account is that
  the skill "fit the task perfectly — nothing to drop" (`192945`). Fourteen are
  the testimonials build of 2026-08-01 between 00:29 and 00:39, which loaded no
  skill at all and called `typo3_task_guide` never (`003356`). One project, one
  client, `opencode/deepseek-v4-flash-free` filed in both, five hours apart.
- **The skill covers the failed task in its own words.**
  `typo3-content-element-development` names a custom backend preview in the page
  module, editor-facing data models, inline child records, TCA, Fluid templates
  for the frontend and for the preview, localization, and content-element tests.
  That is the testimonials session's whole task. It was never reached, so
  nothing in this corpus says what the skill lacks.
- **Its description was already rewritten for this shape, after the session.**
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point.md)
  read all seven descriptions on 2026-08-02 and found two naming one side of a
  domain they own both sides of. The preview moved from ninth of eleven to
  second, and `SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement`
  holds the pair. No run has been through the new wording.
- **Fourteen independent judgements, and not one landed on 1b for a skill.**

  | Feedback                        | Rung                  | Where it was judged                       |
  | ------------------------------- | --------------------- | ----------------------------------------- |
  | `003929`, `003927`, `003937`    | 1a                    | `D-KNW-022`, `D-KNW-023`, `D-KNW-027`     |
  | `003938`, `003313`, `003925`    | 2                     | `D-KNW-026`, `D-ANS-024`, `D-ANS-027`     |
  | `003533`, `003356`              | 3                     | `D-KNW-017`, `D-SKL-001`                  |
  | `003933`                        | 4                     | `D-SKL-004`                               |
  | `192945`, `193005`              | 5                     | `D-SKL-001`, `D-FBK-018`                  |
  | `002951`, `003103`, `003634`    | mapped, not walked    | `D-FBK-021`                               |
  | `003931`                        | no rung               | `D-FBK-024`                               |
  | `003000`                        | a query word too short | `R-DOC-003`                              |
  | `194826`                        | 1b                    | `R-GUI-006`                               |

  The single 1b is a tool's answer shape — the brief `typo3_task_guide` hands a
  task that changes nothing — and it is in hand on another branch.
- **The route was measured on this exact task.**
  [`D-SKL-001`](skl-001-the-order-a-task-starts-in-is-one-file.md) re-ran
  `typo3_task_guide` from `site-new` on 2026-08-02 with the testimonials task,
  area `sitepackage`, version 14, change type `feature`. It matches the
  content-element and test intents, and returns the `record` hint that session
  spent its evening guessing at. Its "Next lookups" name seven tools. None is a
  skill, and `src/Tool/TaskGuide.php` contains no skill name at all.

## Decided

- **No skill is built for this cluster, and that is the decision rather than a
  deferral.** A corpus filed by a session that loaded nothing says what reaches
  a skill, not what one should say. A second skill over a domain the first one
  owns would be loaded by the same nothing.
- **What the cluster names is one question, and two cards ask it.**
  `todo/waiting/2026-07-31-192945` asks it from inside a skill and
  `2026-08-01-003356` from outside one: does `typo3_task_guide` name the skill
  that owns the task? Both now name this entry, so the reading behind them is
  not made a third time. The third card the todo expected, `193005`, asks a
  neighbouring question about a self-reported call log and is left as it is.
- **The judgement sets an order rather than a number.** The eight queued cards
  keep the `low` their own judgements gave them; each is a fact whose home a
  decision already fixed, and this reading adds no rung to any of them. The
  route outranks all eight, because a session that reaches no skill reaches none
  of the eight corpora either — and the route is not queued at all, it is
  waiting on a person.
- **Nothing is archived and no card is added.** Every one of the seventeen still
  has exactly one todo serving it, so
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant holds unchanged.

## Assumed

- That the two sessions are comparable. The same project and the same model
  filed in both, but the audit ran partly on `nemotron-3-ultra-free`, neither
  prompt is recoverable, and neither is a recorded run. Two accounts, not two
  transcripts.
- That the fourteen judgements read their own feedback right. This entry counts
  the rungs they reached; it did not re-walk the ladder for any of them.
- That a task named after a skill is what loaded it. The audit's own title is
  "TYPO3 extension conformance audit" against a skill called
  `typo3-extension-conformance`. Nothing here measures whether the name is why
  it fired.

## Wrong if

- A run in `site-new`, in the same client and model and with the 2026-08-02
  descriptions installed, loads `typo3-content-element-development` and files
  this cluster anyway. Then the skill was reached and did not carry the task,
  and the corpus was about what a skill says after all.
- `typo3_task_guide` is made to name the skill and the next build session in
  that project still reaches none. Then the route was not the obstacle. What is
  left to suspect is whether that client passes `instructions` and the skill
  listing to the model at all, which `D-SKL-001` records as unmeasured.
- One of the eight facts turns out to be what a loaded skill would still have
  lacked. Then it was filed as knowledge and belonged in a skill body, and the
  cluster carried a skill statement after all.
