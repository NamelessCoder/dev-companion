# Say which root path a Fluid template name is resolved out of

**Serves:** R-KNW-063, feedback/2026-08-03-164749-installation-the-extension-ships-resources.md
**Priority:** normal
**Branch:** todo/say-which-root-path-a-fluid-template-name-is-resolved-out-of
**Claimed:** 2026-08-03

[`D-KNW-052`](../../decisions/knowledge/knw-052-the-order-a-fluid-template-name-is-resolved-in-is-a-gap-this-server-owns.md),
step 1a: `fluid-templates` in `knowledge/hints/fluid.json` says a bare `.html`
still resolves on v14 and nothing anywhere says which of two files that both
exist is the one rendered, so an extension forking a core template — its own
`Login.html` beside the core's `Login.fluid.html` — cannot be judged from the
corpus. Add the order to that hint with `since: 14`: the chain per root path is
`X.fluid.html`, `X.html`, bare `X`, then the same three with the name uppercased,
`.fluid.html` wins over `.html` inside one directory, and the whole chain is
walked once **per root path**, so a root registered later overloads an earlier one
whichever extension each file carries. Read it before writing it: the statement
above comes from
`.checkouts/14.3/typo3/sysext/core/Documentation/Changelog/14.0/Feature-108166-FluidFileExtensionAndTemplateResolving.rst`,
not from the resolver, because `typo3fluid/fluid` — 5.3.1 per
`.checkouts/14.3/composer.lock` — is not in `.checkouts/` and the core's own
`TYPO3\CMS\Fluid\View\TemplatePaths` overrides only the three setters,
`getTemplatePathAndFilename()` and `ensureAbsolutePath()`. So read
`TemplatePaths::resolveFileInPaths()` out of an installed vendor tree or from
upstream, and settle there what the entry does not say: whether "the last root
wins" is the highest integer key, since the core setters sort through
`ArrayUtility::sortArrayWithIntegerKeys()` while the feedback reports the resolver
walking `array_reverse($paths)`, and an event listener appending a root without a
key is the case where the two could differ. Three further facts from the same
entry belong in the hint or in a decision saying why they do not: `*.fluid.*` is
not supported in an extension that still supports TYPO3 below 14, a template file
no longer has to begin with a capital, and a name written out with its own
extension in `f:render` leaves the chain. Then measure the reach with
`bin/cli hints:coverage` and add the case to `HintsTest`, which is what turns
`R-KNW-063` from `open` to `held`. Archive the feedback in that same commit.
