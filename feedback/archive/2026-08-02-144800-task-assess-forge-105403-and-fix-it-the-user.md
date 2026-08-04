---
date: 2026-08-02T14:48:00+00:00
category: missing-knowledge
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: assess Forge #105403 and fix it. The user had to correct my assessment twice before the wor...

## Observation

Task: assess Forge #105403 and fix it. The user had to correct my assessment twice before the work went anywhere, and both corrections were about the same mistake.

Forge #105403 is Closed, fixed_version "next-patchlevel", closed by a core maintainer with "closing as lack of feedback and alternatives possible", and carries a second maintainer's note that the reporter "must not" use f:image for non-FAL resources. A third maintainer had closed the adjacent #100696 with "We won't do something here in the near future".

I treated all of that as a verdict on whether the underlying need is real. My first assessment called the issue "teilweise valide", said the maintainers' position was correct, and my first committed patch was an improved exception message — that is, a politer way of telling the reporter they were holding it wrong. The user rejected this twice: first "nur weil es jemand ausgeschlossen hat, bedeutet es ja nicht das der Bedarf nicht da ist", then "wir reden immer noch über die Lösung für das Issue, wir geben uns nicht damit zufrieden es einfach abzutun".

Only after the second push-back did I re-read the report and see what was in it from the start: the reporter had wrapped f:uri.resource inside f:image's src *because they wanted the cache buster*. The reported code was a workaround for a capability the ViewHelper lacked. The exception was a symptom; the missing cache busting was the cause. That reframing is the entire patch, and I reached it only because the user refused my framing.

Two further signals I had misread. "Lack of feedback" over sixteen months looked like the reporter conceding; it is at least as consistent with the answer given being unusable for their case, which it was — the suggested replacement drops width, height and cropping. And an issue whose own author is a core maintainer, as this one is, should have raised rather than lowered my prior that the report is competent.

## Query

Task text: "evaluate Forge issue 105403, check whether it is valid, find similar issues, find patches that already fixed it, then create a patch". typo3_task_guide task="Fix f:image ViewHelper failing when src contains a cache busting query string", changeType=bugfix, area=fluid, targetVersion=15.0

## Suggestion

Trimmed on 2026-08-03 by the run that judged this against the server as it is now. The judging phase is supported since `D-SKL-005`: the `typo3-core-patch-development` skill opens with "Establish the issue before you believe it", treats the issue as a report rather than a specification, and says that the maintainers' comments on it can be product judgement rather than an API fact. That is one of the four readings asked for here. The placement asked for is not where it went and is not being pursued: typo3_task_guide at changeType=bugfix returns "Confirm the target TYPO3 core branch and issue context" and "Reproduce the bug first" and nothing more, and a skill is where an order is kept.

What stays open is the two readings that actually turned this session's conclusion, neither of them anywhere on the server. A closure reason records what the conversation did rather than what the report is worth — "closed for lack of feedback" over sixteen months is as consistent with an unusable answer as with the reporter conceding. And a named alternative closes an issue only if it does what the reported code did, so what it drops is checked and said: here it dropped width, height and cropping. The remaining reading of the four, that the snippet is a hypothesis about the cause and the question is what the reporter was trying to achieve, belongs to `feedback/2026-08-02-145128`, which kept it as its own step 8.
