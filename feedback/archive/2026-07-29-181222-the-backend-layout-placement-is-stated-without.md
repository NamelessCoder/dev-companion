---
date: 2026-07-29T18:12:22+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 0885391
subject: "[BUGFIX] Put the backend layouts where the set can actually deliver them"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# THE BACKEND LAYOUT PLACEMENT IS STATED WITHOUT ITS CONDITION, AND IT TRANSFERS WRONGLY TO A PROJE...

## Observation

THE BACKEND LAYOUT PLACEMENT IS STATED WITHOUT ITS CONDITION, AND IT TRANSFERS WRONGLY TO A PROJECT SITEPACKAGE.

I followed the hint literally: Configuration/BackendLayouts/*.tsconfig at extension level, glob-imported from the set's page.tsconfig. The user asked why the layouts were not in the set, and the follow-up question is the one that settles it: "die werden doch nur mit dem set ausgeliefert und ausgewertet?"

They are. Checked in the working tree: this extension has no Configuration/page.tsconfig, so the glob import inside the set's page.tsconfig is the only path by which those files reach any backend. Delivered with the set, evaluated only where a site depends on the set. Placing them one directory further up is therefore an indirection with no effect at all — not a weaker option, an inert one.

It is also inconsistent with the same hint. sitepackage-layout has the TypoScript split living inside the set — Configuration/Sets/<Set>/TypoScript/*.typoscript, imported from setup.typoscript — and the page TSconfig split living outside it, imported from page.tsconfig. Two files in the same directory, the same kind of decomposition, opposite answers, and nothing says why.

Camino has a reason that a project does not. It is a distributable theme, announced in the hint itself as moving out of the core into a repository of its own, and its set is one of several things the extension ships. There the extension is the unit and the layouts plausibly belong to it. A project sitepackage is the mirror image: one set, one site, the set is the unit of delivery, and everything that only functions with the set belongs in it.

What I changed, and what I would put in the hint: the directory keeps the name — Configuration/Sets/<Set>/BackendLayouts/*.tsconfig — because the name should say what is inside rather than which mechanism carries it, and because it stays recognisable to someone who read the hint. Only the location moves. The name also stays honest when other page TSconfig arrives: a TCEFORM or TCEMAIN block does not belong in a directory called BackendLayouts, and would go into page.tsconfig directly or into a sibling.

The general shape of this is the one I reported separately about a second axis for project work. Here it is concrete: a convention read off a core reference implementation, stated unconditionally, and wrong for the case the reader is most likely in.

## Query

typo3_architecture_lookup id=sitepackage-layout — "One page layout per file in Configuration/BackendLayouts/&lt;Name&gt;.tsconfig, pulled in by the set's page.tsconfig with a single @import over that directory."

## Suggestion

Give that hint its condition. Where an extension ships one set — the normal project sitepackage — the backend layouts belong in Configuration/Sets/&lt;Set&gt;/BackendLayouts/, imported relatively with @import './BackendLayouts/*.tsconfig', for the same reason the TypoScript split already sits there. Where an extension ships several sets or is a distributable theme like theme_camino, Configuration/BackendLayouts/ at extension level is right. Naming the test rather than the layout is what makes it transfer: does anything reach a backend without the set? If not, it belongs in the set.
