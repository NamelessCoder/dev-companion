---
date: 2026-08-26T22:32:57+00:00
category: tool-gap
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# nothing answers whether an old open issue still reproduces against the installed core

## Observation

Task: "please find 1 old forge issue and fix it", narrowed to Extbase. Installed core is 15.0.0-dev.

The enumeration side worked well: open="stale" + updatedBefore gave 73 candidates, open="oldest" + category="extbase" gave 99. Picking a *fixable* one is where I was on my own, and it dominated the session.

Of five candidates I read in full and then checked against the checkout, three were dead — and nothing in the tracker said so, because a stale issue's status is by definition untouched:

- #71566 (ObjectAccess + LazyLoadingProxy fails on bool properties, 2015): no longer applicable. ObjectAccess was rewritten onto Symfony PropertyAccess; getPropertyInternal() now routes through isReadable()/getValue(), and the is_callable([$proxy,'getBoolean']) line the report is about is gone. Cost: 5 Bash round trips reading ObjectAccess.php, LazyLoadingProxy.php, GenericObjectValidator.php.
- #78546 (CollectionValidator not auto-assigned to ObjectStorage action parameters, 2016): unimplementable as written. It asks Extbase to read the element type from the @param docblock; ClassSchema::reflectMethods() takes parameter types from native declarations only, so MethodParameter carries no element type at all. Properties still get one via Symfony PropertyInfo from @var, parameters do not. Cost: 6 round trips.
- #78607 (ValidationResults for-attribute, 2016): already fixed. Result::forProperty() is typed ?string today, so the "Illegal offset type" path cannot be reached. Cost: 2 round trips.

That is ~13 round trips of code reading spent to reject candidates, against ~5 to confirm #76202 and write the patch. Roughly two thirds of my exploration was triage of issues that a version-aware check could have discarded up front.

For the record on call volume: the whole session was ~85 tool calls, of which 12 were this server (1 typo3_project_describe, 11 typo3_forge_lookup) and ~72 were Bash reading the checkout. That ratio is the finding — the server told me *which* issues exist and the checkout had to tell me which ones are still true.

I note typo3_task_guide offers changeType "triage" for exactly "deciding whether an open bug report still holds". I loaded its schema in my first ToolSearch and never called it — filed separately.

## Query

typo3_forge_lookup(open="stale", tracker="Bug", updatedBefore="2020-01-01", limit=25, notes="people") then typo3_forge_lookup(open="oldest", tracker="Bug", category="extbase", limit=30), then issue= on 75145, 71566, 78546, 83848, 59822, 76202, 78607, 88464

## Suggestion

Two things would have cut this session hardest, in order:

1. A per-issue staleness verdict on the issue answer itself. Even a coarse, clearly-hedged one: for an issue whose description or comments name classes, methods or files, say whether those still exist at the installed version and whether the named symbol's signature changed. For #78607 "Result::forProperty() exists, signature now (?string $propertyPath)" would have ended it in one call. For #71566 "ObjectAccess::getPropertyInternal() exists; the report names is_callable() which no longer appears in it" would have ended that one. It does not need to decide reproduction — naming which cited symbols moved is enough to rank candidates.

2. On the enumeration (open="stale"/"oldest"), an optional flag that runs the same check across the page and returns a per-row marker — "cites symbols that still exist" / "cites symbols that are gone" / "no symbols cited". A backlog sweep is precisely where this pays, because the cost of reading ten issues is what decides whether they get read at all — the same reasoning the `notes` parameter already uses.

Both stay inside what the server can see: it reads the installation, and the issue text is already in hand.
