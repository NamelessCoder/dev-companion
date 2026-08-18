---
description: >-
  How a TypoScript condition verdict is proven against a running installation: the marker only the guarded branch produces, the negative control that makes one result evidence, and what stands between two runs.
whenToUse: >-
  When a TypoScript condition has to be shown to have matched in the frontend, or to have stopped matching — a repair judged before and after, or a template swap that may never have fired. What a condition is handed at evaluation time and how an extension registers one are hints instead.
hints:
  - typoscript-conditions
---

# Proving a TypoScript Condition Verdict

A verdict is a boolean nobody prints. The condition matcher records it, the
include tree keeps or drops the branch it guards, and the response says nothing
about either. So the verdict is proven from what that branch did to the rendered
page, through a marker that is derived rather than guessed at.

## What Does Not Answer It

The backend's TypoScript module lists the conditions of a tree and evaluates
none of them. `IncludeTreeConditionAggregatorVisitor` collects the condition
tokens, and `IncludeTreeConditionEnforcerVisitor` then applies the ones the
editor ticked, which are remembered per backend user in the module data. So the
tree it renders is the one those ticks describe, and a condition nobody ticked
is inactive there whatever the frontend makes of it.

No other core surface prints a verdict. The list of them is built inside the
request, goes into the page cache identifier, and is rendered nowhere.

## The Marker Only the Branch Produces

A marker proves the verdict when the guarded branch is the only thing that can
produce it. Deriving one is therefore a diff of the two branches, and what is
shared between them is what a marker may not be built on.

Where the branch swaps a Fluid template — `page.10.templateName` from one name
to another — the wrapper markup is usually shared. Two templates under one
`templateRootPaths` normally use the same layout and the same partials, so
everything those render is identical on both sides and only what the templates
themselves add differs. A container class taken from the template the condition
selects is therefore in the output either way, and finding it proves nothing.
That is the trap, because the wrapper is what the markup offers first.

Where the branch assigns a value in place, the marker is that value in the
output, under the same rule: take the one the other branch cannot render.

## A Marker Put There on Purpose

Where the TypoScript may be edited, stop deriving. Copy the condition line
verbatim below the block under test, into the same setup file, and have it
render something the installation has nowhere else:

```typoscript
[blog.isPost()]
page.headerData.9999 = TEXT
page.headerData.9999.value = <meta name="x-verdict" content="isPost">
[END]
```

`page.headerData` is rendered into the `<head>`, so one request and one string
comparison settle the verdict, with nothing to discriminate. Copy the line
rather than retyping it: an expression carrying a constant is evaluated after
substitution, and a differently spelled constant is a different condition.
Delete the block when the question is answered, and check with `git status`
that the file is back as it was.

## Which URL Is Requested

`typo3 site:list` prints each site's identifier, its root page id and its base
URL, which is what a page id is turned into a request with. The rest of the path
is the page's own `slug` column, so the pages table answers it in one query
through the installation's own client — `ddev mysql` where DDEV runs it.

Request it without a backend session. A logged-in preview disables the page
cache and renders hidden records, so a browser tab that is signed in and a
`curl` are two different requests, and only one of them is what a visitor gets.

## The Negative Control

A page carrying the marker is also what a condition that matches everything
would produce. The evidence is the pair: the page the condition must match
carries the marker, and a page it must not match does not. Where the question is
a repair rather than a rule, the pair is the same URL before and after, with the
marker first absent and then present. One green run on its own establishes
nothing.

## What Stands Between Two Runs

The verdicts are part of the page cache identifier, as `constantConditionList`
and `setupConditionList` — the condition expressions mapped to what each
evaluated to. They are computed on every request, a fully cached one included,
because the identifier cannot be built without them. A verdict that flips
therefore lands on another identifier, and no flush is needed to stop the old
page being served.

What does go stale is the parsed TypoScript, and only where it comes from a
file: an `@import` target or an `include_static_file` set is keyed on the file
name, so an edited `.typoscript` file keeps its include tree. Adding the probe
above is exactly that case. Two ways out:

- `&no_cache=1` on the request, which disables the page cache and the TypoScript
  cache together — the cache is handed to the TypoScript factory only while
  caching is allowed, so the include tree is rebuilt from the files. Such a
  request also writes no page cache entry, so it cannot poison the next one. It
  is ignored where `FE/disableNoCacheParameter` is on, which it is not as
  shipped; `config/system/` is where an installation would turn it on.
- `typo3 cache:flush --group=pages`, which is the step where that parameter is
  on, and where the run has to be the one a visitor would get.
