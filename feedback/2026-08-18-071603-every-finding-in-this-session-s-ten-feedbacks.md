---
date: 2026-08-18T07:16:03+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_feedback_record
directory: /home/benji/projects/blog
---

# every finding in this session's ten feedbacks was produced by being asked afterwards, and a secon...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository. This feedback is about how the other ten came to exist, which the server has no way of observing.

At the moment the boot finished I had filed nothing and intended to file nothing. The installation worked, my report to the user was accurate, and nothing about the session felt like it had gone wrong. Every one of the ten findings was produced afterwards, by being asked — none of them by noticing at the time.

Which question produced which is worth recording, because the questions are not interchangeable:

- "Which tool calls did the task actually need, in order, and which would you not make again" produced the typo3_project_describe finding. During the session I read the empty no-installation answer as normal — the skill had told me to expect it as the entry condition — and it was only reconstructing the call order that put the two project_describe answers side by side, the second returning from unchanged files everything the first withheld.
- "Which documents did you read whole, and did your client show you that list at all" produced the guides finding. I had not registered that I never saw a resource list; absence of a list is not an event.
- "What did you never put to it" produced the typo3_extension_describe finding. I had passed it over silently and would never have reported a call I did not make.
- "Where did a name not mean what it said" produced the --create-site version-tagging finding, which I had actually noticed and resolved during the session and then forgotten precisely because I resolved it.
- "What did it save you from, be as concrete as about the failures" produced the ddev-restart/additional.php entry. Left to myself I would have filed only complaints, and the paragraph that carried the whole session would be unprotected in the record.

Then the user asked three plain questions — had I reported all unnecessary round trips, were skills missing, was documentation missing — and each found something the structured debrief had not:

- Re-walking every call rather than the notable ones showed I had undercounted one finding (five checkout calls, not two, and two of them returned nothing at all), and found the extension-repository-installation hint I never fetched because the skill files it under a branch I had decided I was not in.
- "Are skills missing" made me read task_guide's own answer again, where it returns two installation intents at strong confidence and one skill. The server had reported the gap in its own output during the session and I had read straight past it.
- "Is documentation missing" is the one I have to answer honestly with nothing: I never called typo3_documentation_lookup once, so I have no evidence about the manual's coverage and will not manufacture any.

Two things follow. First, feedback quality here is a function of the debriefing, not of the session — an identical session with no debrief files zero, and the server cannot tell those two apart. Second, even a careful structured debrief left three findings on the floor until someone asked a blunter question; "which calls would you not make again" is answered from the calls one remembers as notable, whereas "did you report all of them" forces a walk of the actual list.

I also note what shaped what got filed: the tool's own instruction that one feedback carries one subject made me split rather than bundle, and the instruction to name the model made me look it up rather than recall it. Both worked. The instruction not to paste secrets was load-bearing this session — the boot generated an admin password which I reported to the user, correctly, and kept out of all ten feedbacks.

## Query

One boot session against github.com/TYPO3GmbH/blog, followed by a structured debrief prompt (which skills activated, which step no skill carried, which calls were unnecessary, which resources were read, what was never asked, where a name misled, what went wrong, what it saved you from), then a three-clause follow-up from the user: "hast du alle unnötigen roundtrips reported? fehlen dir skills? fehlt dir doku?"

## Suggestion

Treat the debrief as part of the server rather than as something a client may or may not do. Concretely: ship the debrief prompt as a guide with an id of its own, so any client can run it and so the questions that demonstrably produce findings are versioned alongside the knowledge they are about — at minimum the five named above, plus the two blunter ones that caught the rest ("walk every call, not the notable ones" and "read the answers you received again for gaps they reported themselves"). Consider having the workflow skills end by naming it, the way they already name typo3_commit_message_guide at the end of an authoring task. And weigh the sampling bias when reading the feedback store: what is in it reflects which sessions were debriefed, so a subsystem with no feedback is not evidence of a subsystem without problems.
