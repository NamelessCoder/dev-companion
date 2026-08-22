---
id: D-KNW-100
title: 'How an extension extends a TypoScript condition is a gap'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::whatAnExtensionMayBuildBehindAConditionProviderIsStated
---

# D-KNW-100 — How an extension extends a TypoScript condition is a gap

**Nothing below `knowledge/` says what an extension may build behind
`Configuration/ExpressionLanguage.php`.**

So the choice that decides every such fix is read out of core: a variable object
gives the dotted syntax and sees no condition variables, a function provider
sees them and cannot carry a dot. The feedback is queued at `normal`.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  matched nothing on
  `"extension registers its own TypoScript condition ExpressionLanguage provider"`,
  on `"TypoScript condition provider ExpressionLanguage extension registers"`
  and on `"Configuration/ExpressionLanguage.php typoscript context provider"`;
  the first two routed to the `typoscript` domain and returned its 21 candidates
  as the index.
- The subject is absent rather than thin. `ExpressionLanguage` occurs nowhere
  below `knowledge/` or `skills/`, `knowledge/hints/` has no file for
  TypoScript, `knowledge/task-intents.json` carries no condition intent, and
  `knowledge/catalog/references.json` lists nothing under
  `typo3/sysext/core/Classes/ExpressionLanguage/`.
- One neighbour answers and stops short.
  `"condition provider constructor injection makeInstance public service Autoconfigure"`
  reaches `dependency-injection` and `di-service-not-found`, and the second
  states the rule — an extension point resolved by class name needs a public
  service — with the page title providers as its worked case. It names neither
  the condition provider nor the attribute, and it is reached only by a query
  that already carries the diagnosis.
- The two channels are exactly as reported, on `.checkouts/14.3`.
  `AbstractProvider` offers `getExpressionLanguageProviders()` and
  `getExpressionLanguageVariables()` and nothing else, `Resolver::__construct()`
  instantiates every provider through `GeneralUtility::makeInstance()` and
  merges both before a caller variable arrives, and
  `Typo3ConditionFunctionsProvider` reads `$arguments['request']`,
  `$arguments['site']` and `$arguments['siteLanguage']` inside its evaluators.
- Core states the consequence and not the remedy. `AbstractProvider`'s docblock
  says runtime-related variables must be provided by the caller to the
  `Resolver` directly, which is why a variable object cannot fetch request
  state; what an extension does instead is written nowhere.
- The public-service requirement is the `makeInstance` line.
  `GeneralUtility::makeInstance()` consults the container only where
  `$constructorArguments === [] && self::$container->has($className)`, so a
  private provider falls through to `new $class()` and fatals on its own
  constructor.
- That requirement is unbound and its spelling is not. `DefaultProvider` carries
  `#[Autoconfigure(public: true)]` on `.checkouts/13.4`, `14.3` and `main`, and
  on `12.4` the same class is made public by a `Configuration/Services.yaml`
  entry instead.
- The subject was reported twice out of one task.
  `feedback/2026-08-18-080532-nothing-says-what-is-reachable-at-typoscript.md`
  is the evaluation-time half — which variables the matcher hands over — where
  this one is the registration half, and both name a hint on TypoScript
  conditions as what was missing.

## Decided

- Step 1a, taken on, and a todo rather than the spot. What the statement says
  about TYPO3 was read here as evidence; writing it is the curation, the version
  bindings and the test, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the todo's
  side whatever its size.
- `normal` rather than the `low` the card arrived at. The subject has no
  coverage at all, so a caller who arrives on it is answered by nothing.
- Not `high`. One session reported it, and what is missing is statements rather
  than a capability.
- A hint curated on the condition provider rather than a sentence added to
  `di-service-not-found`. That hint is reached by a caller who already suspects
  a container problem, and the session that filed this arrived holding
  `Configuration/ExpressionLanguage.php` and a condition that had stopped
  matching.
- The public-service half is stated there too, and `di-service-not-found` gains
  a neighbour line rather than the statement. A second hint curated on the same
  container words would compete with the one that already answers them.
- Neither archived nor trimmed. No part of the mechanism is stated anywhere
  today, and the one neighbour that answers covers a different extension point.
- The other feedback keeps its own card. A feedback is given one card and never
  a second — `R-FBK-014` — so what the two halves owe each other is read off the
  two files rather than settled here.

## Assumed

- That the two halves make one hint rather than two. Both feedback came out of
  one task, and whether the next caller arrives on registering a provider or on
  what a condition can see is not something this run can know.
- That the statement holds unbound apart from the attribute. Both channels, the
  `Resolver` constructor and the `makeInstance` line were read on `14.3` alone,
  and only `DefaultProvider`'s registration was compared across the four
  checkouts.
- That an event listener is the route an extension actually takes to feed a
  variable object. The feedback says so and `AbstractProvider`'s docblock agrees
  that the caller has to provide such a variable; which event, and whether it is
  dispatched before condition matching on every covered version, is unread here.

## Wrong if

- A covered version offers a provider a second way to reach the condition
  variables — a constructor argument the `Resolver` passes, a tag, an interface
  it checks. The two-channels statement would describe a subset and read as the
  whole.
- The hint lands and the next session with a condition provider reports that
  what it needed was which event feeds the variable object. The gap would have
  been the evaluation-time half, and the registration statement would not have
  closed it.
- A query naming `makeInstance` and a public service stops reaching
  `di-service-not-found` once this hint is written. The boundary between the two
  would be wrong, and the public-service half belongs where it already is.
- The public-service requirement turns out not to bite an extension: its own
  `Services.yaml` already registers its classes as public, or it ships none at
  all and `makeInstance` never consults the container. Half the entry would be
  describing core's habit rather than a rule an extension author is held to.

## Confirmed on 2026-08-18

The gap is filled by `typoscript-condition-providers` in
`knowledge/hints/typoscript-condition-providers.json`, unbound, and the three
queries measured above now answer with it first.

The two halves became two hints, and not because the assumption was tested. Both
feedback were claimed as todos on the same day and worked on two branches at
once, so the evaluation-time half is written under `D-KNW-101` and this hint
states the registration half alone. What that costs is a caller who arrives on
one and needs the other, and what makes it survivable is that the two are asked
in different words: this hint is curated on the provider and the registration
file, and gives up the bare phrase `typoscript condition` to the half that
answers a condition which stopped matching.

The first **Wrong if** was read on all four checkouts and does not hold.
`ProviderInterface` declares the two getters and nothing else, `Resolver.php` is
byte-identical from `12.4` to `main`, and its constructor instantiates each
provider with no argument at all — there is no tag, no interface it checks and
no constructor argument a provider could take from the `Resolver`.

The third does not hold either. The container query still reaches
`di-service-not-found`, now third where it was second, behind
`dependency-injection` and this hint; the neighbour keeps its statement and
gains a line naming the new hint.

The fourth is answered by where the requirement bites rather than by whether it
does. A provider with no constructor arguments is built by `new` and never
consults the container, so nothing is asked of an extension that ships one — and
the moment it takes a dependency, the default `public: false` under `_defaults`
is what makes `makeInstance` fall through. That is the case the report arrived
with, and it is the case the statement is written for.

The **Assumed** on the version bindings is settled and the one about the
attribute was too narrow. `#[Autoconfigure]` is honoured on `12.4` as well —
`Frontend\Cache\MetaDataState` and
`Backend\Hooks\DataHandlerAuthenticationContext` carry it there — so what is
bound is core's own spelling for `DefaultProvider` rather than what an extension
may write, and the hint states the rule and both spellings without a binding.

The **Assumed** about the event stays where it is, and is `D-KNW-101`'s. This
hint states only that a variable object has to be given its request-time state
from outside, and what makes that possible: the provider is instantiated while
`IncludeTreeConditionMatcherVisitor` builds its `Resolver`, so a service filled
earlier in the request is readable through constructor injection at that moment.
