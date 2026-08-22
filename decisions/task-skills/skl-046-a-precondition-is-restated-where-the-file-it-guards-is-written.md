---
id: D-SKL-046
title: 'A precondition is restated where the file it guards is written'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
---

# D-SKL-046 — A precondition is restated where the file it guards is written

**A precondition the instructions state is restated in the skill whose workflow
writes the file it guards, because the instructions arrive once and the file
arrives hundreds of calls later.**

The instructions name three lookups that fail only at runtime when they are
guessed. One session made all three calls' worth of mistakes available and made
one of them, and the difference between the call it made and the two it skipped
was where the rule was written.

## Evidence

- `feedback/2026-08-17-210001` reports one build session — a sitepackage and a
  distribution extension — against the three preconditions the `instructions`
  carry. `typo3_icon_lookup` was called three times and one call caught three
  unregistered identifiers out of twelve proposed, before any of them reached a
  TCA file. `typo3_component_lookup` was never called, and four backend design
  tokens were written into a preview stylesheet from memory, each behind a CSS
  fallback, and shipped unvalidated. `typo3_label_lookup` was never called
  across roughly fifty new trans-units.
- `typo3_component_lookup` was named in exactly one published skill,
  `typo3-backend-module-development`, which routes it *before writing buttons,
  status markers, cards, tables, or other backend markup*.
  `typo3-content-element-development` asks for *a useful backend preview for a
  custom CType* and for element-only CSS loaded through the AssetCollector, and
  its evidence step routed documentation, labels and icons and not that.
- The icon and label preconditions were both in that skill, in one bullet. The
  icon rule fired and the label rule did not, so restating a rule where the task
  passes is what carried the icon call and is not on its own what carries a
  call.
- What the reporting session did instead is stated: it read *reuse is scoped to
  the XLF resource used at the consuming code* as saying a new extension has
  nothing to ask about. That scoping is the whole of what either channel says
  about labels — the `instructions` sentence ends *a match in another resource
  is not reusable there*, and `LabelLookup::description()` opens *Reuse is local
  to the translation resource already used at the consuming code*. Neither
  covers the case that failed, a core label reference lifted out of another
  extension into the session's own TCA, which its code consumes rather than
  reuses.
- The `instructions` are not the channel with room in it. Assembled in this
  checkout they are 1830 characters of the 2048 `D-ANS-004` fixed as the budget,
  and the exclusion prefix and the installer notice both go in front of them and
  are the caller's size rather than ours.
- The `routing` block already carries all three preconditions, and it sits
  behind `typo3_server_scope` — `D-AUD-003`'s own finding that a tool has to be
  called to learn that tools should be called.
- `feedback/2026-08-17-212218`, from the same session and still unjudged,
  reports the same shape from another symptom: a prescription phrased as a
  section in the middle of a document supplies a caller no reason to stop, and a
  guide delivered at session start is not what is read at the end.

## Decided

- **Step 2, delivery, closed on the spot** for the component half. The rule
  exists and the workflow that writes backend preview markup and CSS did not
  carry it; the bullet is placement, and its wording is the sibling skill's
  because the two guard the same surface. `ROUTING_SKILLS` records it, which is
  what holds it.
- **Step 4, wording** for the label half. The rule was delivered in the same
  bullet as the one that fired, so what is left is what it says: the bullet now
  states that a label reference copied out of another extension is consumed by
  the copying code, and that an extension with nothing of its own to reuse still
  has those to check.
- The `instructions` are left as they are. There is no room for a fourth clause,
  and a longer sentence at session start is the channel that has already been
  measured not to reach the moment the file is written.
- `LabelLookup::description()` is left alone too. It is accurate about reuse,
  and what was missing is a step in a workflow rather than a property of the
  search — the mistake of writing a workflow into a description a client caches
  is `D-SKL-043`'s, one file further out.
- Nothing about TYPO3 was looked up and no contract moved. The skill's
  `description` and the ownership boundary it closes on are unchanged, which is
  the test `D-SKL-043` set for closing a skill edit in the judging run.

## Assumed

- That a bullet in a skill's evidence step is still in reach when the preview
  stylesheet is written sixty files later. The icon call is the only evidence
  for it, and it sat in the same list as the label rule that did not fire.
- That the backend preview is the only surface in this workflow the component
  lookup owns. A content element's frontend markup is the project's own and the
  catalog says nothing about it.
- That the two skills now routing the lookup are the two that consume the
  component contract. `typo3_component_lookup` appears in no other skill. The
  two core-patch workflows name backend markup as a layer that decides which
  suites run, and a patch that changes a component is where the contract comes
  from rather than a caller of it.

## Wrong if

- A session working through `typo3-content-element-development` reports skipping
  the component lookup and writing preview CSS from memory anyway. Then the
  evidence step is as far from the file as the instructions were, and what is
  left is a step where the stylesheet is actually written.
- A session calls the component lookup for a backend preview and gets nothing it
  can use. Then the placement was right and the curated catalog is what does not
  carry the surface, which is a catalog question and not a skill one.
- The label bullet is followed and a copied reference still goes unchecked. Then
  the trigger is the act of copying rather than the wording, and it belongs
  where the TCA is written rather than in the evidence step.
- A third published skill turns out to write backend markup without routing the
  lookup. Then this is not per-skill placement but something `skills/base.md`
  owes every workflow, and it is stated once there instead.
