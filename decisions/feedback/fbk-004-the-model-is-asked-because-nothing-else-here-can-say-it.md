---
id: D-FBK-004
title: The model is asked, because nothing else here can say it
date: 2026-07-31
status: confirmed
---

# D-FBK-004 — The model is asked, because nothing else here can say it

**`typo3_feedback_record` asks the caller which model is recording the feedback,
declares the field required, and writes `unknown` rather than refusing the
feedback when it does not arrive.**

A feedback that reports behaviour is evidence about a model. Nothing on this
side of the connection knows which one: the MCP handshake carries `clientInfo`,
the client rather than the model behind it, and the SDK keeps it in the session,
which a tool handler is not given. So it is asked for, in the one place the
answer is cheap — the call that is already being made.

## Evidence

- The feedback of 2026-07-31 17:21 — the conformance skill's instructions
  loaded, its lookups not run — recorded from a checkout whose published skill
  is byte-identical to this repository's, `references/base.md` included. The
  text the session had said, in three places, to ask before judging. What the
  feedback could not say is who read it, which is the whole of the difference
  between "the instruction is missing" and "the instruction was present and
  walked past", and only the second of those is worked off in the skill.

## Decided

- One field, `model`, required in the schema so every client asks for it, never
  enforced in the write. `unknown` is a legitimate answer and the described one
  for a model that does not know its own identifier, because an invented name
  attributes a habit to a model that never had it and there is no way to find
  that out afterwards. The field is always written, so an unattributed feedback
  is visibly unattributed rather than indistinguishable from one recorded before
  the field existed.

## Assumed

- That a model asked for its identifier answers with the one it was given rather
  than the one it believes it should be. Nothing verifies this and nothing can —
  the string is whatever arrives.
- That the model is the half worth having. The client decides whether a skill is
  offered at all, which the previous day's finding about `chat.useAgentSkills`
  is exactly about, and it is not asked for here: it is knowable without asking,
  once the SDK hands `client_info` to a handler.

## Wrong if

- The recorded feedback come back mostly `unknown`, or carrying names no such
  model has, which would mean the attribution is noise and the field should be
  dropped rather than read. Or the same behaviour is reported by every model
  that is asked, which makes it the instruction's problem after all and the
  attribution merely a thing that was measured on the way there.

## Confirmed on 2026-08-22

Measured over every recorded feedback, 461 of them. 320 carry a `model` line and
141 do not, and the split is the hour the field arrived: the newest without one
is `2026-07-31-172757` and the oldest with one is `2026-07-31-174524`. The field
has been written every time since it existed.

The first half of **Wrong if** does not hold. `unknown` is 7 of the 320, all
seven recorded between 18:55 and 19:06 on that first evening and none since, and
nine distinct identifiers are on record — `claude-opus-5[1m]` 199 times,
`claude-opus-5` 55, `opencode/deepseek-v4-flash-free` 31, and six more. Only
`big-pickle`, three times, is not a released identifier.

The second half is untried, and it is answerable for the first time. It asks
whether every model reports the same behaviour, and until now there was not
enough of more than one to ask.
