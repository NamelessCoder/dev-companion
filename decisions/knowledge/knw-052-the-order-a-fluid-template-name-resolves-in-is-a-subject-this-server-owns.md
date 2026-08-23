---
id: D-KNW-052
title: 'The order a Fluid template name resolves in is a subject this server owns'
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::theFileNameFallbackIsStatedAsOncePerRootPath
  - HintsTest::theFluidFileExtensionIsWithheldWhereItDoesNotResolve
---

# D-KNW-052 — The order a Fluid template name resolves in is a subject this server owns

**The corpus states that the Fluid file-name fallback chain is walked once per
root path, so a root registered later overloads an earlier one.**

Which extension each of the two files carries decides nothing there, and inside
one directory `.fluid.html` wins over `.html`. `fluid-templates` already says
that a bare `.html` still resolves on v14. What no answer here says is which of
two files that both exist is the one rendered, and an extension that forks a
core template ships exactly that pair: its own `Login.html` beside the core's
`Login.fluid.html`. Whether the fork is picked up at all is the first question
an audit of it asks, and the reporting session settled it by reading the
resolver out of a vendor tree.

## Evidence

- The naming half of the ask is answered and is struck from the feedback.
  `knowledge/hints/fluid.json` carries "Template files carry the `.fluid.html`
  extension. The bare `.html` form still resolves, so a directory of `.html`
  templates is the predecessor rather than a mistake to fix on sight" with
  `since: 14`, written in `1d49c912` on 2026-08-02 — the day before this
  feedback.
- It is reachable on the path the audit had in hand. `bin/cli hints:probe`
  returns `fluid-templates` first for `Resources/Private/Layouts/Login.html`,
  `appliesTo(26) + text(128)`, and again for
  `Resources/Private/Layouts/Login.html fork of the core backend login layout`;
  `Resources/Private/Layouts/` is one of its `appliesTo` needles and
  `.fluid.html` is another. The conformance skill asks for one
  `typo3_hint_lookup` per surface with that surface's concrete paths, so
  delivery and routing both work, and the feedback's "that rename appears in no
  hint returned for Fluid or template paths" does not reproduce.
- The claim about TYPO3 holds and its source is in the checkout:
  `Documentation/Changelog/14.0/Feature-108166-FluidFileExtensionAndTemplateResolving.rst`
  in `.checkouts/14.3` at `faf60eea22`. It states the chain for one root path in
  six numbered candidates — `myTemplate.fluid.html`, `myTemplate.html`,
  `myTemplate`, then the same three with the name uppercased — and that
  `*.fluid.*` is "preferred over files without the new extension if both files
  exist in the same folder".
- The per-path half is in that same entry under *Consequences for template
  overloading*: the chain "will be executed *per template path*", and both
  directions are spelled out — an extension shipping `*.html` can be overloaded
  by a sitepackage using `*.fluid.html`, and an extension shipping
  `*.fluid.html` can still be overloaded by a sitepackage using `*.html`. That
  is the sentence the audited pattern turns on and no answer here has.
- The rename is real in the tree.
  `.checkouts/14.3/typo3/sysext/backend/Resources/Private/Layouts/` holds
  `Login.fluid.html`, `Module.fluid.html`, `LinkBrowser.fluid.html`,
  `ElementBrowser.fluid.html` and `ElementBrowserWithNavigation.fluid.html`;
  `.checkouts/13.4` holds the same five under the bare names. So a fork taken
  from v13 keeps a file name the core no longer uses, and the file still
  resolves — which is why the verdict needed the order rather than the rename.
- The same entry carries three further facts an audit needs and no hint has:
  that `*.fluid.*` is **not supported** in an extension still supporting TYPO3
  below 14; that the spelling the caller asked for is tried before the
  uppercased variant, so a template file no longer has to begin with a capital;
  and that the chain does not run at all where the requested name carries its
  own extension, so `<f:render partial="MyTemplate.html" />` inside a
  `.fluid.json` template has to be written out in full.
- Step 1a, and not 2, 3 or 4. Nothing below `knowledge/` states the order.
  `bin/cli hints:probe "which template root path wins override order"` reaches
  nothing out of 78 candidates; `templateRootPaths order override core template`
  returns `content-elements`, `page-cache-flushing`,
  `content-rendering-templates` and `frontend-records` on text alone.
  `sitepackage-templates` speaks of shadowing only where a shared `Layouts/`
  root has no subdirectories and `fluid_styled_content` ships a `Default` of its
  own; `fluid-backend-view` names `templateRootPaths` and says a partial is
  referenced by name rather than by path, neither of which says which path
  answers.

## Decided

- Step 1a of the ladder on the second half of the ask, queued rather than closed
  on the spot. What is missing is a statement about TYPO3 with a version
  boundary on it, and the reading it needs is a resolver this repository does
  not have checked out.
- The priority is `normal` and the judgement is what set it. One session
  reported it, which is not the several that would earn `high`. What lifts it
  off the floor is that a forked core template is the pattern the feedback calls
  common and fragile, that whether the fork renders at all is undecidable from
  the corpus today, and that the session answered it out of installed vendor
  source rather than from anything here.
- The naming half is struck rather than queued. It is stated, it is current, and
  it is the top hit on the audited path.
- Not step 1b, and the lookup the feedback asks for is not built. A tool that
  takes an extension-relative template path and returns the core file it shadows
  is the fifth reading of the `D-ANS-003` runtime **Wrong if**, recorded there:
  `typo3_extension_describe` already reports the Fluid roots, and with the chain
  stated, which core file a fork shadows is those roots plus that chain, after
  which the diff is one command in a tree the auditor already has open. What the
  session actually paid three round trips for was the file name, which the
  corpus had.
- The sibling `feedback/2026-08-03-164805` asks for the identifier half of the
  same "read it out of the installed packages" family — does this method exist
  here, is it deprecated, docblock or attribute. Its card carries it and it is
  not folded in here: a different question with a different answer, and neither
  one would have told this session which of two files renders.
- Whether `typo3_extension_describe` should say that a Fluid root was read off
  the directory rather than off a declaration is already queued as
  `todo/say-that-the-fluid-roots-were-read-off-the-directory`, from the same
  audit. It is the provenance of the roots and this is the order they are walked
  in; neither answers the other.

## Assumed

- That the changelog describes what the installed resolver does.
  `typo3fluid/fluid` is not in `.checkouts/` — the core clone carries only
  `TYPO3\CMS\Fluid\View\TemplatePaths`, which overrides the three setters,
  `getTemplatePathAndFilename()` and `ensureAbsolutePath()` and leaves
  resolution to its parent — and `.checkouts/14.3/composer.lock` pins
  `typo3fluid/fluid` 5.3.1. So the chain was read from the entry that announced
  it and not from the code that runs it, which is the reading the todo owes.
- That the ordering an author sees is the ordering the resolver walks. The core
  setters sort on integer keys through
  `ArrayUtility::sortArrayWithIntegerKeys()`, while the feedback reports
  `resolveFileInPaths()` walking `array_reverse($paths)` — so "the last one
  wins" is the highest key rather than the last call, and an event listener
  appending a root path without giving it a key is exactly the case where the
  two could come apart.
- That a statement about resolution order reaches a session auditing a fork. The
  hint it belongs in is reached on the template paths today, which is evidence
  about the placement and not about this sentence.

## Wrong if

- The statement lands and an audit of a forked template still cannot say which
  file renders. Then what was missing is which root paths the running
  installation registers and in what order, which is a runtime answer and step
  1b after all, back at the boundary `D-ANS-003` draws.
- The chain turns out to be walked per name rather than per path — every root
  tried for `X.fluid.html` before any is tried for `X.html`. The changelog says
  the opposite in prose and by example, and the resolver was not read from here.
- `.fluid.html` turns out to be expected rather than optional on a v14-only
  extension. The entry calls it "entirely optional", so a conformance finding
  written against a bare `.html` name would rest on nothing.

## Confirmed on 2026-08-03

The resolver was read where it runs rather than where it was announced.
`typo3fluid/fluid` is in no checkout, but every environment this repository
creates has one installed, and `.environments/e-site-14.3` carries the 5.3.1 the
14.3 lock file pins. `TemplatePaths::resolveFileInPaths()` there builds its
candidate list `foreach (array_reverse($paths) as $path)` and appends all six
candidates for that path before moving to the next, so the chain is walked per
path and the second **Wrong if** does not hold. `.fluid.html` is one candidate
of six rather than a requirement, so neither does the third. The first stays
open: it is about an audit and only an audit can read it.

Both assumptions are settled, and the second one was the reading the todo owed.
The ordering an author sees is the ordering the resolver walks, but not for the
reason either side of the question suggested: every root path list in TYPO3
passes through `TYPO3\CMS\Fluid\View\TemplatePaths`, whose three setters sort
through `ArrayUtility::sortArrayWithIntegerKeys()` on 12.4, 13.4 and 14.3 alike,
and `RenderingContextFactory` is the only way a core view is built. So the
`array_reverse()` walks a list that is already in ascending key order and the
highest key wins. The event-listener case comes out the same way rather than
differently — a path appended with no key of its own takes `max + 1` and sorts
last — and what does come apart is the case nobody named: that sort is skipped
for the whole list as soon as one key in it is a string, and then the array's
own order decides. Read against the installed trees, `[20 => B, 10 => A]`
resolves out of `B` through the core class and out of `A` through the plain
Fluid one, and adding one string key to the list moves the answer back to `A`.

The same reading settles what the changelog entry says and what it leaves out.
The chain, the preference inside one directory, the per-path walk and the
uppercase fallback are all in the code as the entry describes them, and the
fallback is `ucfirst()` on the requested name — the spelling that was asked for
is tried first, which is what makes a lowercase template file resolve. What the
entry frames as a template that "needs to be adjusted" is narrower than it
reads: a name carrying its own extension is tried bare as the third candidate,
so `<f:render partial="Foo.html" />` from a `json` template finds a `Foo.html`
file and only fails against a `Foo.fluid.html` one.

The two majors below it were read at the same time and are the other half of the
statement. Fluid 2.15.0 and 4.6.1 walk the paths with `array_pop()`, which is
the same order, and try `Foo.html` and then a bare `Foo` and nothing else; the
action name is capitalised before the lookup rather than inside it. So the order
between root paths carries no version boundary and the chain inside one does.
