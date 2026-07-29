---
date: 2026-07-29T23:43:39+00:00
category: tool-gap
status: open
tool: typo3_architecture_lookup, typo3_reference_list, typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# Nothing in the server answers "which extension point do I use for X" when the answer is that a su...

## Observation

Nothing in the server answers "which extension point do I use for X" when the answer is that a subsystem still has an SC_OPTIONS hook instead of a PSR-14 event. Concretely: prefilling an EXT:form field from a request parameter. The semantically right entry point is $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['afterFormStateInitialized'], which EXT:form uses itself for file upload handling and which survived the v14.0 round that converted beforeFormCreate, beforeFormSave, beforeFormDelete, beforeFormDuplicate and initializeFormElement into PSR-14 events. It is not deprecated, so nothing warns about it, but registering into SC_OPTIONS is not what new project code should do — and deciding that requires knowing that this one hook was left behind, which is only visible by grepping FormRuntime.

Establishing the alternative also needed source reading that no lookup shortens: the PSR-14 event that actually works is AfterCurrentPageIsResolvedEvent, and it works because of where it sits in FormRuntime::initialize() — after initializeFormStateFromRequest(), before processSubmittedFormValues(). That ordering is the whole correctness argument (a submitted value has to win over the prefilled one, and it does because a default value is only the fallback in FormRuntime::getElementValue()). It is also the only form event carrying both the runtime and the request; AfterFormIsBuiltEvent has the FormDefinition but no request at all.

Two smaller misses on the way: typo3_changelog_lookup for "prefill" returns 0 across every version, so there is no path from the task word to the mechanism. And EXT:form's variant conditions look like a declarative answer until you read FormConditionFunctionsProvider and find only getFormValue() and getRootFormProperty() — no request access, and a variant switches on a condition rather than carrying a value, so it cannot do this at all. Ruling that out cost as much as finding the answer.

## Query

typo3_changelog_lookup query="prefill" (0 matches across all versions); typo3_architecture_lookup id="events-extension-points"

## Suggestion

Two things. First, in the events-extension-points hint (or a new one), name the SC_OPTIONS hooks that outlived the PSR-14 conversions per subsystem and say what to use instead — a short "these are the leftovers" list is exactly the kind of fact that is cheap to carry and expensive to derive. ext/form/afterFormStateInitialized is one; the answer should also say that AfterCurrentPageIsResolvedEvent is the PSR-14 stand-in for prefilling and why the ordering inside FormRuntime::initialize() makes it safe.

Second, consider a lookup that answers "which extension point for this intent" rather than "what changed in this version" — the changelog is organised by release, so a task-shaped word like "prefill", "override a form field", or "modify a query before it runs" finds nothing even when the mechanism is documented. Failing that, having typo3_architecture_lookup match on intent words like "prefill" would at least route to the right hint.
