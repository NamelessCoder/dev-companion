---
id: D-AUD-3
date: 2026-07-31
status: tested
---

# D-AUD-3 — The instructions carry the entry point, because the tool descriptions never arrive

**The entry point into this server is stated in the `instructions`, because
under deferral the tool descriptions are not a channel at all.**

`REVIEW-01` ran in `E-SITE` and this server took no part in it: no tool called,
no skill activated, all thirty-five calls through Bash. Reading the client's own
attachments back showed why, and the three channels behaved differently enough
that one fix would have been the wrong fix.

- **Evidence:** the eighteen tools arrived as a `deferred_tools_delta` — names
  only, no schemas and no descriptions, so every tool `description` in `src/Tool/`
  was outside the session's context and `ToolSearch` would have had to be called
  first to see one. The `instructions` did arrive, in full, from the first turn —
  and they opened with a profile caveat, then "not a patch assistant", then "it
  does not read your working tree", and named no entry point for the commonest
  request there is. The twenty `routing` entries, which do name one, sit behind
  `typo3_server_scope`: a tool has to be called to learn that tools should be
  called. The skills arrived with their full descriptions, and
  `typo3-extension-conformance` — "Audit or improve a TYPO3 project, sitepackage,
  or extension … Use for extension reviews, … quality or readiness assessments" —
  did not activate against "Review this TYPO3 project and its site package.
  Identify the most important concrete problems, risks, or missing safeguards".
  Its body would have met both criteria the run failed: step 1 is
  `typo3_project_scope`, and it hands fixes to the testing and documentation
  skills.
- **Decided:** the `instructions` open with `typo3_project_scope` as the first
  call of any task and name reviewing alongside upgrading and writing code, the
  working-tree sentence stops being a disclaimer and becomes a division of
  labour, and the review shape gets a `routing` entry of its own. The skill
  description leads with the open request — review a repository and say what is
  wrong with it, in priority order — instead of promising compatibility, which
  described one of the seven things that run actually found. The tool
  descriptions are left alone: they are not the channel that failed, because
  under deferral they are not a channel at all.
- **Wrong if:** the second `REVIEW-01` run still reaches for Bash alone. Then the
  wording was never the obstacle, and what is left to suspect is the skill's
  name — `extension-conformance` for a site project — and, past that, the
  possibility that a repository review genuinely needs nothing this server has
  except the installed version, the icon and label registries and the component
  contract. That answer would be worth having; it is much smaller than the
  current surface implies.
- **Tested on 2026-07-31:** the second `REVIEW-01` run did not reach for Bash
  alone. It ran against `b85036b`, the commit that wrote this entry and applied
  it. The skill activated as the session's first action, `typo3_project_scope`
  was the second call, and `typo3_extension_scope` followed. Both channels this
  entry changed carried, so the wording was part of the obstacle after all. What
  the run did not do is follow the skill past step 2. Thirty-eight of its 45
  calls were still Bash, and `typo3_task_guide`, `typo3_architecture_lookup` and
  `typo3_documentation_lookup` were loaded through `ToolSearch` and never called.
  That is the order rather than the entry point, and `D-SKL-1` owns it. The
  second suspicion falls with the first: run 4 called eight tools fifteen times
  and was judged `covered`, and the two findings three runs had missed came from
  those calls. A repository review therefore needs more of this server than the
  version, the registries and the component contract. The run stands in commit
  `021eac8`, the two runs after it having overwritten the file.
