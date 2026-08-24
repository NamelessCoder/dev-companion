---
date: 2026-08-24T12:24:13+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# three skills matched the request word for word and none of them activated

## Observation

Task, verbatim and in German: "kannst du den nochmal reviewn 95179: [BUGFIX] Let stdWrap override apply the value \"0\" | https://review.typo3.org/c/Packages/TYPO3.CMS/+/95179 und auch die anmerkungen bearbeiten". No skill activated in the entire session. That is the finding, and it is not a near miss — three of the installed skills describe this session's three phases exactly:

- typo3-core-patch-review: "Review a TYPO3 core patch — your own before you push it, or somebody else's patch set — and say what is wrong, missing or not ready, in priority order: the diff, its tests, the changelog entry, the commit message, the issue reference and the target branch." That is the first half of the request and it even lists the two things the reviewers had complained about.
- typo3-core-patch-checkout: "Get a patch under review on review.typo3.org into a core checkout". I did precisely this by hand: found the commit was a loose object (git for-each-ref --contains → nothing), git checkout -b bugfix-81619 <sha>, checked the base was an ancestor of origin/main and 59 commits behind, checked whether the two touched files had moved since, then rebased onto origin/main. Every one of those steps I worked out myself and would work out again.
- typo3-core-patch-development: "the changelog entry, the project's checks, the push to Gerrit. Also amending after review". That is the second half of the request.

I cannot say from inside why none fired — I never called Skill and the transcript shows no prompt to. What I can report is my own reading at the moment of decision: I had a Gerrit URL and a change number in front of me, my first instinct was to find out what the review comments actually said, and typo3_gerrit_lookup answered that so completely in one call that the work then looked like a sequence of concrete edits rather than a workflow needing a procedure. The skills lost to a lookup that was too good, not to a bad description.

Two other server-instructed steps also did not happen: the initialize-time instruction says to start every task with typo3_project_describe and then call typo3_task_guide. I did the first and skipped the second, for the same reason.

The request was German throughout. I queried the server in English as its instructions require, and that part held — every lookup answered. But if skill matching runs on the user's words, "reviewn" and "die anmerkungen bearbeiten" are what it had to match against, and neither is an English word a description contains.

## Query

Whole session. Server calls made, in order: ToolSearch for the schemas; typo3_gerrit_lookup(change "95179", messages "people") + typo3_project_describe() in parallel; typo3_forge_lookup(issue "81619", notes "all"). No Skill invocation, no typo3_task_guide call. Everything after that was Bash against the checkout.

## Suggestion

If skill descriptions are the only thing routing on, make typo3-core-patch-review's first clause name its trigger surface rather than its subject — a review.typo3.org URL, a bare five-digit change number, an unresolved reviewer comment — because those are what an agent has in hand at the moment of decision, and they are language-independent in a way "review a TYPO3 core patch" is not.

Worth considering separately: typo3_gerrit_lookup could end an answer whose change is NEW and carries unresolved comments with a one-line pointer to typo3-core-patch-review. The lookup is what an agent reaches first and it already knows enough to route. That would have caught this session at exactly the right moment, without depending on the wording of the request or on the language it was written in.
