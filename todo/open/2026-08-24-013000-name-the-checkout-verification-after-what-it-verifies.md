# Name the checkout verification after what it verifies

**Serves:** decisions/catalog/
**Priority:** normal

Split `catalog:check` by subject and rename what is left for what it is. It runs
eight checks and four of them read nothing below `knowledge/catalog/` — the
Fluid engine each branch pins, the `typo3/testing-framework` release, the suites
`runTests.sh` offers, and the majors a translation domain arrived in. Its
subject is what this repository verifies against `.checkouts/`, and `catalog` is
the one word in it that names nothing.

## What the word covers today

`knowledge/catalog/` holds three unrelated data sets — `components.json`,
`references.json`, `system-extensions.json` — plus a checklist and a meta file.
One word over three subjects is what `AGENTS.md` calls a name that does not name
its own subject, and it is why a reader cannot tell from `catalog:check` what
ran.

The tool layer already made the split: `typo3_component_lookup`,
`typo3_reference_list` and `typo3_system_extension_lookup` each name their
subject, and only `typo3_catalog_scope` carries the collective word. So the
decomposition is not a proposal — it exists, and the upkeep side did not follow
it.

## What it costs

`CatalogCheck` is 980 lines and `RepositoryCheck` runs it. The recurring todos
and `documentation/contributing/` name the command by its current name, and
`bin/cli` subjects are what people write down, so a rename is read by more than
this checkout. Whether `typo3_catalog_scope` moves with it is the harder half:
it is a tool name, which `AGENTS.md` says outranks prose because clients
installed months ago still call it.

## Where it came from

Read on 2026-08-24 while `components:derive` was being written, and named by the
maintainer: the catalog can be several things, and `catalog:check` says nothing.
Kept out of that commit because a rename of a command eight checks hang from is
not the same change as deriving a class's position — `D-CAT-008`.
