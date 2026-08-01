---
date: 2026-07-30T17:44:23+02:00
category: tool-gap
status: closed
closed: 2026-08-01
tool: typo3_project_scope, typo3_extension_scope, typo3_task_guide, typo3_architecture_lookup, typo3_documentation_lookup
---

# Extension releases need a preparation and publication workflow

## Observation

The server can assess an extension, test it, document it and draft a project
commit, but it does not carry an extension maintainer from a release candidate
to a verified release artifact. Release readiness spans owners that no current
task skill coordinates: supported TYPO3 and PHP constraints, extension metadata,
version consistency, changelog and migration documentation, licensing,
dependencies, generated assets, archive exclusions, quality commands, tags, and
the requirements of the intended package registries.

A generic conformance audit does not establish that the exact archive to be
published contains the intended files, omits development and secret material,
installs from its declared dependencies, and passes the same checks as the
checkout. Preparation and external publication also have different safety
boundaries: building and inspecting an artifact is local and reversible;
creating tags or publishing to a registry changes shared state and requires an
explicit request and confirmed target.

## Query

Prepare this TYPO3 extension for its next release to its package registries.
Build and verify the exact artifact, tell me what is still blocking the release,
and do not tag or publish anything until I explicitly ask.

## Suggestion

Add a `typo3-extension-release` skill that starts with project and extension
scope, selects the intended registries and version, and composes the existing
conformance, testing and documentation workflows into one release gate. Derive
registry requirements from current official documentation instead of retaining
them in the skill. Build the artifact through the repository's own release
command where one exists, inspect its file list and metadata, install or validate
it in a clean environment, and run the declared quality commands against the
release candidate.

End preparation with the artifact path, checksum, included and excluded
material, verification results, unresolved blockers and the exact publication
steps not taken. Treat tag creation, pushing and registry publication as a
separate explicitly authorized phase with confirmed repository, version and
credentials. Add a forward scenario that fails on an artifact containing
development files or secrets even when the checkout itself is green.

## The query, re-run 2026-07-31

Verbatim in `E-EXT` — `/home/benji/projects/bootstrap_package`, seven commits
past its `16.0.0` tag — against server `8983a3c`, client `claude-code` 2.1.220,
sessions `d8a3529d` and `65c4868d`, with the six skills of that revision
published into the checkout first.

**No skill activated and no tool was called.** Forty-one `Bash` calls, three
`TodoWrite`, one `ToolSearch` — and that one selected `TodoWrite`. It is not a
wiring accident: the server connected over stdio in 92 ms, and all six skill
names and every tool name stood in the session's context. This client defers
tool schemas, so reaching a tool costs one `ToolSearch` that the session never
spent, while the skills were offered unabridged and went unused. The release was
carried out of `git`, `composer`, `npm` and `ddev` instead: tag distance,
`.gitattributes`, both workflows and the changelog read; the frontend build run;
lint, CGL, PHPStan, unit and functional tests run through DDEV; `typo3/tailor`
installed and both artifacts built.

The run was stopped there, before its answer, once no skill would activate. What
it had established by then is what the paragraph above asks a scenario for, and
it holds in that checkout:

- `git archive HEAD` honours `/.vscode export-ignore` and ships 1558 files.
- `tailor create-artefact 16.0.0`, the path `publish.yml` takes from the full
  tree `actions/checkout` produces, ships 1559. The two extra are
  `.vscode/launch.json` and `.vscode/settings.json`, both tracked. Tailor
  filters by its own `conf/ExcludeFromPackaging.php` and never reads
  `.gitattributes`.

So a green checkout hands the two registries different file sets, and nothing in
the repository says so.

The second session took the same road and was stopped at its fourteenth call —
nine `Bash`, five `Read`, no skill, no tool: `git`, `composer.json`,
`ext_emconf.php`, both workflows, then `test:php:lint`, `cgl:ci` and `phpstan`.
Twice out of twice, an under-specified release request is answered entirely from
the checkout.

**Why nothing activated.** The choice was offered and declined, not missed: the
session is handed all six skills with their full descriptions. None of them
carries a word of the task — not *release*, *publish*, *registry*, *Packagist*,
*TER*, *artifact*, *archive* or *tag*. The nearest is
`typo3-extension-conformance` with "readiness" and "report what is wrong with it
in priority order", which covers "tell me what is still blocking" and says
nothing about building and verifying the thing that ships. The skill this note
asks for is the missing trigger surface, not a better one.
