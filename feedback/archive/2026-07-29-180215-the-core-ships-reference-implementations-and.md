---
date: 2026-07-29T18:02:15+00:00
category: idea
status: closed
closed: 2026-07-29
commit: 3e4f0ff
subject: "[FEATURE] Say where the core keeps its own worked examples"
tool: typo3_architecture_lookup, typo3_server_scope
directory: /home/benji/projects/site-new
---

# THE CORE SHIPS REFERENCE IMPLEMENTATIONS AND NOTHING INDEXES THEM. This is the single thing I wou...

## Observation

THE CORE SHIPS REFERENCE IMPLEMENTATIONS AND NOTHING INDEXES THEM. This is the single thing I would most like to have known at the start.

Three times this session the actual answer was an existing example inside the core repository, and all three times I reached it by accident or by being told:

- typo3/sysext/theme_camino — the layout of a v14 sitepackage. I invented a directory structure, shipped it, and the user rejected it with "bitte prüfe camino". Reading the extension took ten minutes; the rework took an hour.
- Build/tests/playwright/ — the shape of a browser test suite, including e2e/frontend/ and an e2e/accessibility/ that runs axe. I only found it because the user had that directory open in a second workspace.
- typo3/sysext/extbase/Tests/Functional/Fixtures/Extensions/blog_example — how an Extbase repository is exercised in a functional test, including the ServerRequest/ConfigurationManager setup that a repository test needs. I found it by grepping the core for "extends Repository".

The knowledge base has since gained hints for the first two subjects, which is exactly right and answers those questions. But the pattern will repeat with the next subject that has a reference implementation and no hint yet, and the fix for the general case is cheaper than a hint per subject: an index of where the core keeps its own worked examples, with one line on what each is a reference FOR.

A first list from what I met: theme_camino for sitepackage and theme layout; styleguide for backend components and TCA demos (the CSS hints already lean on it, so the convention exists); blog_example for Extbase domain, repository and controller plus their tests; Build/tests/playwright for acceptance and accessibility testing; Build/phpstan and Build/php-cs-fixer for the static analysis setup a project might copy.

The value is not that these are documentation. It is that they are the version-correct, currently-passing, actually-maintained form of the answer — which a hint, however good, is a summary of. When a hint is thin, "read X" is a better answer than a thin hint, and right now the server cannot give it.

## Query

pattern across the whole session — three times the real answer was "read the reference implementation the core already ships", and nothing pointed at any of them

## Suggestion

Add a lookup — or a section of typo3_server_scope — that maps subjects to the core's reference implementations, with the caveat the sitepackage-layout hint already gets right for camino: read it, do not depend on it. When a hint has a matching reference, name it in the hint too. For an installation without a core checkout the paths are still useful as a pointer to github.com/TYPO3/typo3.
