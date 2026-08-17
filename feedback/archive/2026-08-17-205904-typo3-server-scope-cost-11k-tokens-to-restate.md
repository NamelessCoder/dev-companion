---
date: 2026-08-17T20:59:04+00:00
category: idea
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_server_scope, typo3_project_describe
directory: /home/benji/projects/site-demo
---

# typo3_server_scope cost 11k tokens to restate what typo3_project_describe had already answered on...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, starting from an empty directory. Reporting a cost measurement, not a wrong answer.

typo3-development-installation prescribes typo3_server_scope before anything is created, "for whether an installation and a console can be reached at all", and allows skipping it only where the base's installation lookup already described a booted installation. My first call had been typo3_project_describe, which answered {"unsupported":{"cause":"no-installation", ...}} with the searched paths. The prescribed skip condition did not apply — there was no booted installation — so I made the call. Its installation block returned found:false with the same searched paths. It changed no decision I took.

That answer was 39,926 characters, roughly 11,000 tokens: the largest single tool result of the session by a wide margin, larger than any hint, larger than the task guide. I measured the session afterwards from the client transcript: 148 API requests, and this call landed at roughly the tenth. Because a tool result stays in context and is re-read from cache on every subsequent request, its amortised cost at Opus 5 list prices was about $0.88 — near 3% of the whole session's $29.59 — for one call that duplicated the previous one.

The content is not wrong and the routing table in it is genuinely good. The problem is that the workflow makes a full capability catalogue mandatory at the point where the only open question is binary, and the tool that answers that question binary had already been called.

I am reporting this because the server cannot see it: it sees a call that succeeded. Response size is a first-class design parameter for a server used inside long agentic sessions, in a way it is not for one-shot use — every payload is paid for again on every later turn.

## Query

typo3_project_describe (answered no-installation), then typo3_server_scope as the typo3-development-installation workflow prescribes, on an empty directory

## Suggestion

Two options, either would do. Widen the skip condition in typo3-development-installation so that any conclusive typo3_project_describe answer discharges it — including the no-installation answer, which already carries found/searched and is what the step is asking for. Or give typo3_server_scope a way to answer the narrow question cheaply, so the workflow can prescribe the small form and leave the full catalogue for the case it is actually written for: a caller who does not know whether the server can answer a question at all. Worth checking the other always-large answers on the same basis — what a step needs to decide is usually a fraction of what the tool returns.
