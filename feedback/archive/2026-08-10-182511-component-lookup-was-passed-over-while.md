---
date: 2026-08-10T18:25:11+00:00
category: idea
status: closed
closed: 2026-08-12
model: claude-opus-5
tool: typo3_component_lookup
directory: /home/benji/projects/typo3-cms
---

# component_lookup was passed over while authoring a new backend CSS class, on an assumption I neve...

## Observation

Task: review and then rework TYPO3 core Gerrit change 95163, which introduces a new backend CSS class `.module-docheader-wrapper` and restructures the `.module` grid, the docheader bars and their custom properties.

The server's own startup instructions say: "Before writing backend markup or CSS classes, call typo3_component_lookup with the targetVersion." I loaded the tool's schema at the start of the session and never called it — not while reviewing the new class, not while adding a `f:be.pageRenderer` line to the docheader partial, and not while inventing and then removing `--module-docheader-box-shadow-range` and `--module-docheader-navigation-height`.

The assumption I acted on, unstated at the time: that component_lookup is a catalogue of discrete UI components — badge, card, panel, input-group, per its own examples — and that the module chrome around every backend module is not in it. I reinforced this by checking the styleguide instead: I listed typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/ and grepped it for "docheader", found nothing, and concluded from the checkout that no docheader demo exists. I then reported "not applicable" against the css-styleguide-demos hint on that basis.

So the assumption may well be right — but I established it from the filesystem, having never asked the tool that the instructions point at. If the docheader and its custom properties are in the catalogue, this session should have found them and did not. If they are not, then the instruction "before writing backend markup or CSS classes" is broader than what the tool actually covers, and an agent following it literally on a layout-chrome change gets an empty answer and learns to skip the step.

Worth noting the counter-case in the same session: the css-* hints, which I did call, were the right instrument for this change and one of them was decisive.

## Query

No call made. The change authored `.module-docheader-wrapper` and the custom properties --module-docheader-navigation-height, --module-docheader-sticky-height, --module-docheader-scroll-offset, --module-docheader-box-shadow-range against targetVersion 15.0, and verified conventions via typo3_hint_lookup and the styleguide templates directory instead.

## Suggestion

Say in the tool description which side of the line module chrome falls on — is the docheader, the module grid, the module body a component this tool answers for, or only the discrete widgets its examples name? Either answer is useful. If chrome is out of scope, the startup instruction "before writing backend markup or CSS classes" should say so, because a change to `.module-*` is backend markup by any reading and this session skipped the step on a guess rather than on the instruction.
