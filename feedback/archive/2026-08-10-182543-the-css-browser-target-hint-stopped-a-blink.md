---
date: 2026-08-10T18:25:43+00:00
category: idea
status: closed
closed: 2026-08-11
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# the css-browser-target hint stopped a Blink-only CSS feature from being recommended into a core p...

## Observation

Task: review and then rework TYPO3 core Gerrit change 95163, which makes the backend module docheader sticky and drives its box-shadow with a scroll-driven animation.

This is the thing that worked and must not be broken.

The user asked, mid-rework, whether the scroll-driven animation could be dropped and the shadow simply shown when it is actually needed. My immediate answer was going to be `@container scroll-state(stuck: top)` — it is the purpose-built primitive for exactly that, it would have removed every magic distance from the patch, and it is genuinely the right shape for the problem. I called typo3_hint_lookup id=css-browser-target before writing it up.

Three statements in that answer changed the recommendation:
- "A feature is inside the baseline only where every engine ships it — Blink, Gecko and WebKit. Gecko is usually the last of the three, so it is the one that decides."
- "Existing core usage of a modern CSS feature is not evidence that the feature is inside the baseline."
- "Build/.browserslistrc is what the CSS build's autoprefixer reads. It decides which prefixes are emitted and never rejects a feature, so it is not a gate on what the CSS may use."

The first turned a recommendation into a rejection: scroll-state() is Blink-only as far as I know, so it is outside the baseline regardless of how well it fits. The second pre-empted the argument I was about to build — the patch already uses `animation-timeline: scroll(root)`, and I was one step from citing that as proof the core is fine with this class of feature. The third stopped me reaching for Build/.browserslistrc as evidence; I had already grepped it and would have quoted "last 2 version, Firefox ESR" as if it were a gate.

I reported the constraint to the user in those terms, said plainly that I could not verify Gecko support from the session, and named scroll-state() as the candidate that will replace the block in a few releases rather than as the fix for this patch. Without this hint I would have put a single-engine CSS feature into a core patch under review with a confident rationale attached.

## Query

typo3_hint_lookup id=css-browser-target, called while deciding whether to recommend @container scroll-state(stuck: top) in place of a scroll-driven animation, in a backend CSS patch targeting 15.0 and 14.3.

## Suggestion

Keep this hint as it is worded, in particular the two sentences that anticipate the specific bad arguments — "existing core usage is not evidence" and ".browserslistrc is not a gate". Those two do the work; a hint that only said "target evergreen browsers" would not have stopped me, because I had a call site and a browserslist config in hand and both pointed the wrong way. It is also worth considering surfacing this hint automatically whenever a query establishes the css domain, the way icon and label lookups are pressed in the startup instructions — the moment it is needed is the moment an agent is about to write a feature down, which is not usually a moment it thinks to ask about browser support.
