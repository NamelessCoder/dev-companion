---
date: 2026-07-29T18:05:28+00:00
category: idea
status: closed
closed: 2026-07-30
commit: e8e7f03
subject: "[FEATURE] Give core-only docs and asset rules project twins"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# WHICH SUBSYSTEM HINTS STILL NEED A PROJECT-SHAPED TWIN

## Observation

*Trimmed twice. The repository level is answered by `project-repository-layout`,
and the force of a statement is now data: `binding: "core"` marks what is a
condition of a core patch and a convention elsewhere, on the hint for a whole
subject and on the statement for a single sentence. What is left of the note is
the part that marking cannot do.*

*Trimmed a third time, on 2026-07-30: the pass this note asked for has been
made. Its outcome is below — nineteen of the twenty-two marked hints, all four
marked statements and the marked intent need no twin, and two twins are missing.
What is still open is writing them.*

The catalog is organised by subsystem — fluid-templates, tca-formengine,
icon-usage, caching. That is a core contributor's model: "I am touching
FormEngine, what are the rules." A project developer never asks that. The
question is always "where does this go".

Marking answers "is this mine to follow". It does not answer "then what is mine
to do instead", and that is what a twin is for: `project-extension-tests` exists
because the conventions of `core-tests` transfer and everything around them —
the harness, the paths, what is available at all — does not. Two pairs exist
now, both because a note asked for one.

## What the pass decided, per subject

Marking is enough for nineteen hints, all four marked statements, and the
`submission` intent. The reasons differ enough to be worth having:

The eighteen backend CSS hints that are not about the build — naming, components,
tokens and specificity, state attributes, colour and surface, shadows, z-index,
motion, container queries, light and dark, RTL, accessibility, icon and text
stability, web components, browser target, minimal and reusable — are the
backend's design system, and a project styling a backend module wants them
unchanged. The note guessed this and the guess holds. Two of them look like
exceptions and are not. `css-styleguide-demos` carries an obligation only the
core can meet, since nobody else can add to the styleguide extension, but what
it spends its length on is what a demo has to cover — the variants, the states,
the sizes, both colour schemes, RTL — and that is a description of what to
verify, which holds for anybody building a backend component. `css-bootstrap-
transition` reads like a core roadmap and is not one: treating Bootstrap as
existing infrastructure and not enabling an `@import` that was disabled on
purpose is more binding outside the core, not less, because a project cannot
change what the core ships.

`backend-ui` transfers verbatim. Its four statements are component design advice
— focused custom elements, explicit state transitions, accessibility of the
controls, CSS aligned to the host element API — and the only core-shaped things
on it are the checks, which are already dropped outside the core, and a
`typo3/sysext/backend/` path that matters to nothing when the hint is reached by
topic.

The four marked statements are marked for two different reasons and neither wants
a twin of its own. `core-tests` (a test mirrors its class path in the mono
repository) already has one, `project-extension-tests`. `site-sets` (the site set
resolution tests live under `typo3/sysext/core/Tests/Functional/Site/Set/`) is a
pointer to where the core's own coverage sits — evidence for the reader, not an
obligation. `fluid-viewhelpers` (a new or changed ViewHelper needs a changelog
entry) and `language-files` (marking a label unused is a deprecation, with its
changelog entry and the translator history on translate.typo3.org) do have a
project side, and it is the same one in both cases: an extension's ViewHelper and
an extension's label are public API for whoever installs it, so the obligation is
a version bump and a release note. That is the documentation subject, not the
Fluid or the label one, so it belongs in the twin below rather than in two places
that would each say it half.

The `submission` intent is core-only by its own `condition`, and the project
profile drops the tools it routes to. Nothing to add.

## The two twins that are missing

**An extension's own documentation, twin of `documentation-changelog`.** The note
named this one and it is worse than it looked: the hint's `appliesTo` carries
`Documentation/` and `.rst`, so an extension author asking about their own
documentation is handed the core's changelog filename grammar, its anchor format,
its `.. index::` tags and `checkRst` — marked as not theirs, with nothing in its
place. And nothing is in its place anywhere: `guides.xml`, `Documentation/Index`
and semantic versioning appear nowhere in `knowledge/`, and `docs.typo3.org`
only as an upstream URL in `server-scope.json`. The project side is a real answer
— documentation lives in `Documentation/` with `guides.xml` and `Index.rst` and
is rendered by docs.typo3.org, there is no `Changelog/` artifact and no Forge
issue in the filename, and a breaking change is announced through the extension's
own version and release notes — and it is the place the two statements above land
as well.

**An extension's own asset build, twin of `css-source-build-boundaries` and
`backend-typescript`.** Both marked hints say the same thing about two file
types: the source of truth is under `Build/Sources/`, the generated file is
committed beside it, and the two disagreeing means the browser runs the old one.
Outside the core none of that exists — no `Build/Sources/Sass/`, no
`Build/rollup/`, no `lintScss`, no `build-css`, no committed pair — and both
hints are reachable from an extension: `appliesTo` carries `Resources/Public/Css/`,
`.scss`, `css`, and `Resources/Public/JavaScript/`. What the project side needs is
mostly already written and needs pointing at rather than repeating:
`public-assets` answers how a built file is published and referenced,
`extension-files` answers that backend JavaScript is declared in
`Configuration/JavaScriptModules.php` as an import map. What is missing is the
boundary itself — the extension owns its build and its tooling, nothing hooks
into the core's — and that is one answer for both file types, not two.

One twin for both, and its section is a finding of its own: it does **not**
belong in `css.json` or `typescript.json`. Those two sections are the backend
interface's own and `typo3_architecture_lookup` withholds them when the task
names the frontend — which is exactly what a sitepackage's asset build is. Put
the twin there and it is invisible in the case it was written for. It belongs in
`general.json`, beside `public-assets` and `sitepackage-layout`, and the two
marked hints point at it from their own sections.

## Query

synthesis of the session — which questions the knowledge base answers well for a
project, and which shape of question it has no place for
