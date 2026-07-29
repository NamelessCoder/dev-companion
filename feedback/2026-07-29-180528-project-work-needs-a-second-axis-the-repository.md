---
date: 2026-07-29T18:05:28+00:00
category: idea
status: open
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# PROJECT WORK NEEDS A SECOND AXIS: THE REPOSITORY, NOT ONLY THE EXTENSION.

## Observation

PROJECT WORK NEEDS A SECOND AXIS: THE REPOSITORY, NOT ONLY THE EXTENSION.

The catalog is organised by subsystem — fluid-templates, tca-formengine, icon-usage, caching. That is a core contributor's model: "I am touching FormEngine, what are the rules." A project developer never asks that. The question is always "where does this go", and the hints that carried this session are exactly the ones that cut across subsystems to answer a project situation instead: sitepackage-layout, sitepackage-initial-content, project-extension-tests, extbase. Those are not subsystems. They are moments in a project's life.

What is answered now is the extension. theme_camino settles templates, layouts, backend layouts, language files, and sitepackage-layout carries that. Nothing answers the repository around it, and the core cannot be the reference there because the core is not a project. Concretely, the things I had to invent in this session with no convention to follow:

- where copied phpunit configurations live (I chose Build/phpunit/)
- where browser tests live and where their config goes (Tests/E2E/ and a root playwright.config.ts, decided by convention from outside TYPO3)
- which composer and npm scripts a project is expected to expose (test, test:unit, test:functional, test:e2e)
- where a throwaway bootstrap script goes (var/transient/, chosen because it is git-ignored)
- the five database environment variables as defaults in the XML so CI can override them
- the .gitignore entries for all of the above

One rule from that does generalise and is worth stating on its own: the PHP tests belong to the extension, the browser tests belong to the project. They live apart because they are about different things — one builds its own instance per test class and tests the extension, the other runs against a deployed site and tests what is actually on it. Merge them and the extension can no longer be tested on its own.

The counter-argument I take seriously: TYPO3 projects differ enormously — with and without Node, with and without DDEV, one site or twenty. A prescribed skeleton that assumes DDEV is wrong for half of them. So the useful form is named places with their reasons, not "copy this repository". The reasons transfer even where the layout does not: Build/ exists because the phpunit configurations must be copied rather than referenced, and the database credentials are defaults in the XML because CI has to be able to override them.

The test of whether a structure holds is whether something new has an obvious home. This session added three new things. A plugin: obvious. A PHP test suite: obvious after reading the testing framework's own header comment. A browser suite: no home at all.</observation>
<parameter name="suggestion">A counterpart to sitepackage-layout that describes the repository level — what sits at the root, what belongs in Build/, where the two kinds of test go and why they are separate, which scripts a project exposes, what is ignored. And make the core-tests / project-extension-tests pattern the rule rather than a one-off: same subject, two answer shapes, selected by the outsideCore flag the server already computes. Where a subsystem hint would read differently for a project, that is the signal it needs its project-shaped twin.

## Query

synthesis of the session — which questions the knowledge base answers well for a project, and which shape of question it has no place for
