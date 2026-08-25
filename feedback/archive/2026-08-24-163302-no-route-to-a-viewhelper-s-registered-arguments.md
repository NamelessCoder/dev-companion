---
date: 2026-08-24T16:33:02+00:00
category: missing-knowledge
status: closed
closed: 2026-08-25
model: claude-opus-5[1m]
tool: typo3_fluid_namespace_list, typo3_component_lookup
directory: /home/benji/projects/typo3-cms
---

# No route to a ViewHelper's registered arguments or to how an unregistered one reaches the tag

## Observation

Task: fix Forge #106584 — register "href", "src" and "alt" as arguments on f:asset.css, f:asset.script and f:image, which currently read them out of the tag attributes.

The entire patch turned on one mechanism that no tool here answers, and I read five vendor files to establish it:

1. vendor/typo3fluid/fluid/src/Core/ViewHelper/AbstractTagBasedViewHelper.php — only UNREGISTERED arguments land in $this->additionalArguments and are pushed into the TagBuilder by initialize(). This is the whole reason the patch is not a one-liner: registering "href" makes $this->tag->getAttributes()['href'] return null, so the ViewHelper would silently stop finding the file. I could not have known this from the issue or from the ViewHelper source alone.
2. Same file — initialize() runs BEFORE render() and TagBuilder emits attributes in insertion order, so registering "alt" on f:image moves it from first to last in the rendered <img>. That is the one visible output change in the patch and the reason it needed a changelog entry at all.
3. StrictArgumentProcessor.php and LenientArgumentProcessor.php — which one is active, and what each does to a 'string'-typed argument.
4. ViewHelperInvoker.php:61-62 — process() runs before isValid(), so scalars and Stringables are cast to string before validation. This decided the backport question: I had told the user "main only, because the new type check could break templates on a release branch", and reading these two lines showed that objection was wrong and 14.3 could have it. I had to retract a statement I had already made to the user.
5. Schema/ViewHelperMetadataFactory.php — to find that prepareArguments() is what the XSD generator reads, which is how I built the test that actually fails before the fix.

I never called typo3_fluid_namespace_list. I passed it over on its name: I read "namespace_list" as "which xmlns prefixes are registered", not "what arguments does this ViewHelper take". If it does carry argument definitions, the name hid it from me. If it does not, this is a genuine gap.

I also never tried typo3_component_lookup. Its routing line says "backend markup or a CSS class", and a Fluid ViewHelper is arguably a component, but "component" plus "backend markup" read as backend HTML/CSS, not Fluid. Unverified assumption on my part.

Version context: TYPO3 main, BRANCH 15.0. Confirmed against origin/14.3 and origin/13.4 by git show, since I also had to decide backport targets — 14.3 carries identical ViewHelper code and the same StrictArgumentProcessor, 13.4 differs (useNonce instead of csp, no ArgumentProcessor at all).

## Query

Nothing was asked of the server here — this is the part of the task I took to the checkout instead. The question I would have asked: "for f:asset.css on TYPO3 15.0, which arguments are registered, and how does an argument that is NOT registered reach the rendered tag?"

## Suggestion

A ViewHelper introspection lookup, keyed by the Fluid tag name and a targetVersion, answering: the registered arguments with type, required flag and default; whether the ViewHelper is tag-based; and — the part that actually mattered — the note that on a tag-based ViewHelper any unregistered attribute is passed through to the TagBuilder by initialize() before render() runs, so registering an argument removes it from getAttributes() and changes its position in the emitted attribute order.

That last sentence is the whole finding. It is not discoverable from the ViewHelper's own source, it is stable across versions, it is what makes "just register the argument" a behaviour change rather than a cosmetic one, and it is the kind of thing a knowledge server exists to hold. Anyone who takes on #106584, or any of the other arguments removed by TYPO3/Fluid commit aa378389, walks into exactly this.

If typo3_fluid_namespace_list already answers part of it, the name is the problem: something like typo3_fluid_viewhelper_lookup would have been found from the tool list, which is where I chose every tool I did use.
