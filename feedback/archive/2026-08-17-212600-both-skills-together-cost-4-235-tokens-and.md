---
date: 2026-08-17T21:26:00+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3-development-installation, typo3-content-element-development
directory: /home/benji/projects/site-demo
---

# both skills together cost 4,235 tokens and routed correctly — a counter-example in the same sessi...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. This is about what works and must not be broken later, measured, because I have filed a lot of criticism in this session and this is the part I would defend hardest.

Measured from the client transcript: typo3-development-installation injected 10,605 characters when activated, about 2,945 tokens. typo3-content-element-development injected 4,645 characters, about 1,290 tokens. Together 4,235 tokens for the two workflows that carried an entire project from an empty directory to a verified, reinstallable deliverable.

For contrast, in this same session I invoked one unrelated, non-TYPO3 skill to answer a pricing question whose real content is four numbers. It injected 664,176 characters — about 184,493 tokens. That is forty-three times both TYPO3 skills combined, and more than the entire mass of every tool result in this session from every source (131,136 tokens). It inlined a dozen reference documents to answer something a table row would have answered.

The difference is a design decision, and typo3-development-installation states it in its own opening: keep the skill as routing and workflow, "never retain layout keys, environment defaults, command options or package names — each of those belongs to a tool that releases on its own cycle and none of them can be re-asked from here". That is why it is small, and it is also why it cannot go stale: the version-bound facts arrive from lookups at the step that needs them, and the skill carries only the order and the obligations. content-element-development does the same with its checklist reference, which was the single most useful document of the build — it made me answer the ownership question before writing a field, and that decision produced two child tables instead of a nested-content bodge.

Worth naming precisely what the size buys, because it is not just cost. A 4,000-token workflow is read in full and acted on. A 184,000-token one is skimmed, and the parts that matter are indistinguishable from the parts that do not. Both TYPO3 skills were read completely and their prescriptions were specific enough that when I failed to follow one — the two-id hint step, the five-bullet verification section — the failure is legible afterwards and I could file it. That legibility is a property of the size.

If there is a risk here it is drift in the other direction: the two gaps I filed separately, a project skill and a distribution skill, are exactly the kind of thing that gets solved by growing an existing skill rather than adding one. The measurement says the current size discipline is worth defending against that.

## Query

Measure the injected size of typo3-development-installation and typo3-content-element-development when activated, against the total tool-result mass of the session they steer.

## Suggestion

Keep the routing-and-workflow rule, and treat the injected size of each skill as a number worth watching rather than an accident — 3,000 tokens for a workflow that steers a whole project is the thing that made it usable. Where the two missing workflows I filed separately get written, write them to the same rule: the order, the obligations, the terminal check, and every version-bound fact delegated to a lookup named at the step that needs it. If it is ever useful, the concrete baseline from this session is 2,945 and 1,290 tokens against 4,235 total, steering 248 tool calls and 169 round trips.
