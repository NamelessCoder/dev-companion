---
id: D-AUD-003
title: The instructions carry the entry point, because the tool descriptions never arrive
date: 2026-07-31
status: confirmed
coveredBy:
  - ScopeTest::bothCallsOfTheEntryPointAreToldInTheImperative
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
  site package. Identify the most important concrete problems, risks, or missing
  safeguards". Its body would have met both criteria the run failed: step 1 is
  `typo3_project_describe`, and it hands fixes to the testing and documentation
  skills.

## Decided

- The `instructions` open with `typo3_project_describe` as the first call of any
  task and name reviewing alongside upgrading and writing code, the working-tree
  sentence stops being a disclaimer and becomes a division of labour, and the
  review shape gets a `routing` entry of its own. The skill description leads
  with the open request — review a repository and say what is wrong with it, in
  priority order — instead of promising compatibility, which described one of
  the seven things that run actually found. The tool descriptions are left
  alone: they are not the channel that failed, because under deferral they are
  not a channel at all.

## Wrong if

- The second `REVIEW-01` run still reaches for Bash alone. Then the wording was
  never the obstacle, and what is left to suspect is the skill's name —
  `extension-conformance` for a site project — and, past that, the possibility
  that a repository review genuinely needs nothing this server has except the
  installed version, the icon and label registries and the component contract.
  That answer would be worth having; it is much smaller than the current surface
  implies.

## Confirmed on 2026-07-31

The second `REVIEW-01` run did not reach for Bash alone: it ran against the
commit that applied this entry, activated the skill first and called
`typo3_project_describe` second, so both channels carried and the wording was
part of the obstacle. What it did not do is follow the skill past step 2 —
thirty-eight of 45 calls were still Bash — which is the order rather than the
entry point, and `D-SKL-001` owns it. The second suspicion falls with the first:
run 4 called eight tools fifteen times and found the two things three runs had
missed.

## Since then

The same channels failed on a task that builds rather than reviews, in another
client and a much smaller model, so it is not the **Wrong if**. What the
sighting adds is a cause this entry did not name: a skill description that names
one side of a domain it owns both sides of reads as somebody else's work. All
seven were read for that shape, two carried it, and both were rewritten with
`R-SKL-010` holding the pair. Half the sighting was later withdrawn by its own
author — the session may have activated a skill after all — and the finding does
not rest on it, because the descriptions were read off the files here.

`REVIEW-03` settled the delivery on 2026-08-03: the instructions and all seven
descriptions are in the transcript's own attachments, against 23 calls that are
22 `Bash` and one `Read`. An entry point carries a task where a skill is there
to receive it, and in `E-CORE` there is none. `D-SKL-005` carries that half.
