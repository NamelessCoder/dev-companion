---
id: D-AUD-003
date: 2026-07-31
status: confirmed
---

# D-AUD-003 — The instructions carry the entry point, because the tool descriptions never arrive

**The entry point into this server is stated in the `instructions`, because
under deferral the tool descriptions are not a channel at all.**

`REVIEW-01` ran in `E-SITE` and this server took no part in it: no tool called,
no skill activated, all thirty-five calls through Bash. Reading the client's own
attachments back showed why, and the three channels behaved differently enough
that one fix would have been the wrong fix.

## Evidence

- The eighteen tools arrived as a `deferred_tools_delta` — names only, no
  schemas and no descriptions, so every tool `description` in `src/Tool/` was
  outside the session's context and `ToolSearch` would have had to be called
  first to see one. The `instructions` did arrive, in full, from the first turn
  — and they opened with a profile caveat, then "not a patch assistant", then
  "it does not read your working tree", and named no entry point for the
  commonest request there is. The twenty `routing` entries, which do name one,
  sit behind `typo3_server_scope`: a tool has to be called to learn that tools
  should be called. The skills arrived with their full descriptions, and
  `typo3-extension-conformance` — "Audit or improve a TYPO3 project,
  sitepackage, or extension … Use for extension reviews, … quality or readiness
  assessments" — did not activate against "Review this TYPO3 project and its
  site package. Identify the most important concrete problems, risks, or
  missing safeguards". Its body would have met both criteria the run failed:
  step 1 is `typo3_project_scope`, and it hands fixes to the testing and
  documentation skills.

## Decided

- The `instructions` open with `typo3_project_scope` as the first call of any
  task and name reviewing alongside upgrading and writing code, the
  working-tree sentence stops being a disclaimer and becomes a division of
  labour, and the review shape gets a `routing` entry of its own. The skill
  description leads with the open request — review a repository and say what is
  wrong with it, in priority order — instead of promising compatibility, which
  described one of the seven things that run actually found. The tool
  descriptions are left alone: they are not the channel that failed, because
  under deferral they are not a channel at all.

## Wrong if

- The second `REVIEW-01` run still reaches for Bash alone. Then the wording was
  never the obstacle, and what is left to suspect is the skill's name —
  `extension-conformance` for a site project — and, past that, the possibility
  that a repository review genuinely needs nothing this server has except the
  installed version, the icon and label registries and the component contract.
  That answer would be worth having; it is much smaller than the current
  surface implies.

## Confirmed on 2026-07-31

The second `REVIEW-01` run did not reach for Bash alone. It ran against
`b85036b`, the commit that wrote this entry and applied it. The skill activated
as the session's first action, `typo3_project_scope` was the second call, and
`typo3_extension_scope` followed. Both channels this entry changed carried, so
the wording was part of the obstacle after all. What the run did not do is
follow the skill past step 2. Thirty-eight of its 45 calls were still Bash, and
`typo3_task_guide`, `typo3_architecture_lookup` and
`typo3_documentation_lookup` were loaded through `ToolSearch` and never called.
That is the order rather than the entry point, and `D-SKL-001` owns it. The
second suspicion falls with the first: run 4 called eight tools fifteen times
and was judged `covered`, and the two findings three runs had missed came from
those calls. A repository review therefore needs more of this server than the
version, the registries and the component contract. The run stands in commit
`021eac8`, the two runs after it having overwritten the file.

## Since then

The same three channels failed again on a task that builds rather than reviews.
A session in `site-new` wrote a custom backend preview for a TYPO3 14 content
element and called nothing — no tool, no skill, the work done by reading vendor
code (`feedback/2026-08-01-002926-debrief-of-a-typo3-14-backend-content-element.md`).
It ran on 2026-08-01, a day after `b85036b` put `typo3_project_scope` at the
head of the `instructions`, so the entry point this entry added was in the text
and did not fire. That is not the **Wrong if**: it was a different client and a
much smaller model — `opencode` with `deepseek-v4-flash-free` — and neither has
been measured here.

What the sighting adds is a cause this entry did not name. The skill
descriptions arrived in full, as they did for `REVIEW-01`, and
`typo3-content-element-development` opens "Build or refactor TYPO3 **frontend**
content elements" with `previews` ninth of the eleven things it then lists. The
task was a backend preview of a content element, which that skill covers in as
many words — "Add a useful backend preview for a custom CType" — and which
`knowledge/task-intents.json` has matched on `backend preview` since `51e5e5a`
on 2026-07-30. So the description names one side of a domain the skill owns both
sides of, and the other side reads as somebody else's work. That is narrower
than "leads with the open request", and it is checkable against every skill
here: `typo3-backend-module-development` promises "TYPO3 backend UI work" and
means a module, so this task matched a word in each description and belonged
wholly to the first. The rewrite is queued rather than made, because a
description is installed into somebody else's project.

That rewrite landed on 2026-08-02 with the check this entry proposed, and the
check is the part worth keeping: all seven descriptions were read for the same
shape, and two carry it. `typo3-content-element-development` now opens on
content elements with neither side attached and names a custom backend preview
in the page module second in its list rather than `previews` ninth of eleven;
`typo3-backend-module-development` no longer claims the backend beyond the
module it means, says that the preview is not one, and its crossing hands over a
content element "or its backend preview" instead of a frontend one, because a
body naming one side while the description names both is the file disagreeing
with itself where nobody can correct it. The other five do not carry the shape:
conformance, documentation and testing each name a domain by what is done to it
and list surfaces from both ends, and the two that could have read as one-sided
already state the second half — the upgrade skill names dropping an old major
beside adding a new one, and the release skill names the publication step it
stops before, deliberately. `R-SKL-010` is the demand, and
`SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` holds the pair.
What none of it settles is whether the wording was what that model was missing:
the words are what can be changed from here, and only a second run in the same
client says whether they were the obstacle.
