---
id: D-FBK-048
date: 2026-08-18
status: open
---

# D-FBK-048 — The debrief is offered as a prompt where the channel is

**The debrief is offered by the server as a user-invoked prompt wherever the
feedback channel is available, and never as a tool the model can call.**

Everything this server learns about itself arrives through a text somebody
pastes by hand. A session that is not handed it files nothing, and the store
cannot tell that session from one that had nothing to report.

## Evidence

- `feedback/2026-08-18-071603` reports the mechanism from inside it: at the
  moment its task finished the session had filed nothing and intended to file
  nothing, and every one of its ten findings was produced afterwards by being
  asked. Five of the questions that produced them are bullets of the prompt in
  `documentation/records/asking-for-a-debrief.rst`, named one by one in the
  feedback.
- 30 of the 405 archived feedback name a debrief in their own text, and the 27
  open ones come from two directories. Both are sessions run against a
  standalone checkout, which is the only configuration where
  `Channel::isAvailable()` offers the two channel tools at all.
- `Factory::create()` already ships one prompt, `commit_message`, built from
  `typo3_commit_message_guide`'s own answer.
  `documentation/usage/installing.rst` and `knowledge/server-scope.json` both
  call it *the user-invoked prompt*, so the surface exists and this repository
  already has the word for what it is.
- `asking-for-a-debrief.rst` states the constraint the shape has to satisfy: the
  debrief is asked in a message of its own after the work, because an agent told
  it will be asked which tools helped calls tools to have an answer.

## Decided

- Taken on, at step 1b of the ladder: the answer exists and there is no way to
  get it in the form the task needs. What is missing is not knowledge but a
  surface — the questions are written, and only a person with the page open can
  put them.
- The prompt is registered where `Channel::isAvailable()` is true, beside the
  two tools it ends in. A session that cannot record a feedback has no use for
  the questions.
- It takes no arguments. The list is generic on purpose — it names no scenario,
  no skill and no tool — and a parameter would be the first thing to make it
  name one.
- One text, read by the page and by the prompt. A second copy ages, and the copy
  that ages is the one somebody pastes.
- Rejected: a debrief guide tool, which is the shape the feedback asked for. A
  tool stands in the model's list from the first call, so the session under
  report learns it will be debriefed while it is still working — the
  contamination the page opens by naming. The id the feedback wants is the
  prompt's name.
- Rejected: the workflow skills ending by naming it, the way they name
  `typo3_commit_message_guide`. A skill body is read at the start of a task, so
  a closing step arrives at step 0 and carries the same contamination. A skill
  is also installed into somebody else's project, where the channel it would
  point at is not offered.
- The sampling bias the feedback names is a reading of the corpus rather than a
  rule, so it goes to `documentation/records/judging.rst`, where the corpus is
  read before the ladder is walked.
- What the prompt *says* is not decided here. `D-FBK-047` owns the list of
  questions, and two clauses the feedback earned were added to it in the same
  commit as this entry.

## Assumed

- That a prompt is user-controlled: listed for the person, not offered to the
  model. That is what the protocol says a prompt is for, and what this
  repository already writes about `commit_message`. A client that renders
  prompts into a model's context carries exactly the contamination the tool was
  rejected for.
- That the client being debriefed lists prompts at all. Where it does not, the
  page and the paste stay the only way, which is why the page stays.
- That a prompt run by the person after the work reproduces what the paste
  produces. Nothing has measured it; the prompt is the same text arriving by a
  shorter route.

## Wrong if

- Feedback starts arriving that names the debrief as something the session knew
  was coming — calls made to have an answer, a finding written before the task
  finished. That is the tool's failure appearing on the prompt.
- The prompt's text and the page's drift apart, and what somebody pastes is a
  version behind what the server offers.
- The prompt ships and the corpus keeps arriving in the same hand-pasted
  batches, which would mean the surface was never the obstacle.
