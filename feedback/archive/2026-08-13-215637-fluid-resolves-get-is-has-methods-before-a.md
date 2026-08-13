---
date: 2026-08-13T21:56:37+00:00
category: missing-knowledge
status: closed
closed: 2026-08-13
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Fluid resolves get/is/has methods before a public property, so a hasItems() helper shadows the it...

## Observation

Audit of an agent's own accumulated project notes against what this server answers. This one survived, and the lookup for it came back completely empty: hints: [], with fluid-templates, fluid-conditions-and-arrays, fluid-viewhelpers and eighteen others listed as available and none of them matching.

The note, established from the Fluid source while building backend DTOs (SiteOverview/SiteStatus): StandardVariableProvider::getByPath() resolves {obj.foo} in the order getFoo() -> isFoo() -> hasFoo() -> then the public property foo. (vendor/typo3fluid/fluid/src/Core/Variables/StandardVariableProvider.php, around lines 124-148.)

Two consequences, and the second is the one that costs an afternoon:

1. A method named hasItems() or isOk() is NOT reachable as {obj.hasItems} / {obj.isOk} — Fluid looks for getHasItems, isHasItems, hasHasItems and never the literal hasItems(). The core's own convention is therefore getHasMorePages(), read in the template as {pagination.hasMorePages}; SlidingWindowPagination is the worked example.

2. A boolean helper hasItems() SHADOWS a public array property items. For {obj.items} Fluid tries "has" + "Items" = hasItems() BEFORE the items property, so the property never gets read and the template receives the boolean. <f:for each="{obj.items}"> then fails with "argument each ... is of type boolean" — an error message that names the ViewHelper and the type and says nothing about the method that caused it. Same for hasIssues() shadowing issues.

The rule that comes out of it, for any DTO rendered in Fluid: do not name a method has<Property>() / is<Property>() / get<Property>() when a property <property> exists and is accessed in templates. Prefer the property directly, expose counts as get*Count(), and reserve boolean names for cases where no property of that name exists.

This is not an obscure corner. It is a naming rule that bites the ordinary case — somebody adds a convenience boolean to a DTO that already has the array, and a template that worked stops working, with an error pointing at the template.

## Query

typo3_hint_lookup(task: "Fluid object accessor resolution getter isser hasser method before public property") — returned hints: []

## Suggestion

Add a hint on Fluid object access — the resolution order itself, and the shadowing rule as its consequence. fluid-conditions-and-arrays is the closest existing home ("where an expression goes wrong", as fluid-templates describes it), and this is exactly a case of an expression going wrong for a reason nothing in the template shows.

Carry the error string. "argument each ... is of type boolean" is what somebody will be holding when they need this hint, and it is the only searchable thing about the failure.

Pair it with the naming rule for DTOs, since that is the actionable half: the core writes getHasMorePages() rather than hasMorePages(), and SlidingWindowPagination is the precedent to point at.
