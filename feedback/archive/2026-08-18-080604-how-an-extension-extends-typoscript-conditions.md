---
date: 2026-08-18T08:06:04+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_reference_list
directory: /home/benji/projects/blog
---

# How an extension extends TypoScript conditions is uncovered, including the variable-object vs fun...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions, broken on TYPO3 v14, while keeping them working on v13.

Configuration/ExpressionLanguage.php registering a provider under the 'typoscript' context is a documented extension point, but nothing here covers what you may build behind it, and the constraint that decides every fix is invisible until you read three core classes.

The constraint: Core\ExpressionLanguage\AbstractProvider offers two channels, and they are not interchangeable.

- getExpressionLanguageVariables() puts an object in scope, giving the syntax [blog.isPost()]. Symfony compiles that to a method call on the variable. The object is constructed inside the Resolver constructor and is handed nothing — it cannot see page, request, site or any other condition variable. This is why the ext:blog implementation reached for a global in the first place, and why the obvious v14 repair (swap the global for the request) has nowhere to get a request from.
- getExpressionLanguageProviders() registers ExpressionFunctionProviderInterface classes whose evaluators receive $arguments, i.e. the full condition variable set including page. Core's own Typo3ConditionFunctionsProvider gets site()/siteLanguage()/session()/locale() exactly this way. But a function cannot carry a dot, so this channel cannot produce blog.isPost() — adopting it is a breaking change to every integrator's TypoScript.

So "keep the syntax" and "see the condition variables" are mutually exclusive through the documented API, and the only way out is to feed the variable object from outside via an event listener. I worked that out by reading Resolver.php, AbstractProvider.php, ProviderInterface.php and Typo3ConditionFunctionsProvider.php. I would work it out the same way again next session, because nothing states it.

A second piece, also read out of core rather than looked up: a provider instantiated by Resolver via GeneralUtility::makeInstance() only gets constructor injection if it is a public container service — makeInstance consults the container solely when $container->has($className), which is false for a private service, and then falls back to new $class() and fatals on a required argument. Core solves this on its own DefaultProvider with #[Autoconfigure(public: true)], identically on 13.4 and 14.x. That attribute is the whole registration recipe for a condition provider that needs a dependency, and I only found it because I happened to open DefaultProvider.php for a different reason.

Note the user twice steered the implementation on repository convention — listener belongs in Classes/Listener/, and prefer #[AsEventListener] / #[Autoconfigure] over Services.yaml entries. I want to be accurate about that: the first is this repository's own layout and no server could have known it, and the second was the user choosing a style the repository did not yet use, so typo3_extension_describe reporting the existing YAML tags would have pointed the wrong way. Neither is a gap here. The attribute recipe above is, because it is core's convention and not this repository's taste.

## Query

Would have been asked as: typo3_hint_lookup for "extension registers its own TypoScript condition / ExpressionLanguage provider", targetVersion 14.3, in a package supporting ^13.4.15 || ^14.3. Files in hand: Configuration/ExpressionLanguage.php, Classes/ExpressionLanguage/ConditionProvider.php extending Core AbstractProvider, Classes/ExpressionLanguage/BlogVariableProvider.php. Not called during the session; reconstructed from what the work needed.

## Suggestion

A hint — "typoscript-condition-providers", or a section in whichever hint covers TypoScript — saying, for an extension registering Configuration/ExpressionLanguage.php under 'typoscript':

- the two channels of AbstractProvider and what each can see: a variable object gives dotted syntax and receives no condition variables; an ExpressionFunctionProvider receives $arguments with page/request/site/siteLanguage but can only be called as a bare function;
- that a provider needing constructor injection must be a public service, with #[Autoconfigure(public: true)] as core does on DefaultProvider, because Resolver instantiates it through GeneralUtility::makeInstance and that consults the container only for public services;
- that a variable object which needs request-time state has to be fed from outside, since it is built during Resolver construction.

typo3_reference_list would also help here: typo3/sysext/core/Classes/ExpressionLanguage/ is a worked example of both channels side by side and is the file set I actually learned this from.
