---
id: D-KNW-027
date: 2026-08-02
status: confirmed
---

# D-KNW-027 — Which caches a change invalidates is a gap this server owns

**How a change is cleared from an installation's caches is inside this server's
boundary and missing from it, so the feedback is queued rather than closed.**

The corpus names `typo3 cache:flush` once, as the last step of an upgrade,
behind a condition that excludes work on the code. A session that had just
edited a Fluid template reached none of it and deleted a cache directory
instead.

## Evidence

- The feedback's own query reaches nothing. Run in this checkout on 2026-08-02,
  `bin/cli hints:probe "clearing the fluid_template (and code) caches after template changes"`
  classifies it `fluid`, offers 22 candidates and matches none of them.
- The nearest hint the corpus has answers a different question. "how do I clear
  the TYPO3 caches" reaches `caching` in `php.json` at
  `appliesTo(5) + text(55)`, whose statements are about declaring a cache: the
  `cacheConfigurations` entry, which frontend a payload wants, injecting
  `cache.<name>` instead of asking `CacheManager`.
- The one place the command is stated is fenced off from this task.
  `typo3 cache:flush` is the fourth step of `installation-upgrade` in
  `general.json` and of the matching entry in `task-intents.json`, whose
  `condition` reads "only if the task is about an installation rather than about
  the code in it".
- The one hint that mentions flushing from a template task assumes the reader
  knows how. `frontend-page-rendering` says of `config.sendCacheHeaders` that
  "flushing the server cache changes nothing", which is the symptom and not the
  command.
- No skill says it either. The single occurrence of "cache" below `skills/` is
  PHPStan's result cache directory, in
  `typo3-extension-testing/references/static-quality.md`.
- The commands exist and are a family. On `.checkouts/14.3` at `faf60eea22`,
  `CacheFlushCommand` takes `--group` with `system`, `pages`, `di` or `all` and
  defaults to `all`; `CacheFlushTagsCommand` takes tags; `CacheWarmupCommand`
  takes the same groups. The first one's own help is "Useful after code changes
  during development or after deployments."
- What the deletion reaches is one cache of one group.
  `DefaultConfiguration.php` declares `fluid_template` as a `FluidTemplateCache`
  over `SimpleFileBackend` in group `system`, so its directory is a real path;
  `pages`, `hash` and `rootline` are `Typo3DatabaseBackend` in group `pages`,
  which no file deletion reaches. `cache:flush` dispatches `CacheFlushEvent`
  over every group and `CacheManager::handleCacheFlushEvent()` turns that into
  `flushCachesInGroup()`.
- Whether a template change needs any of it was deliberately not established
  here. The identifier a compiled template is stored under belongs to standalone
  `typo3fluid/fluid`, which no checkout vendors, and that answer decides whether
  the statement is a command or a correction.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3, read across the covered majors, and this run has
  read nothing but this repository and three core checkouts.
- Not step 2, so the sentence is not moved. The one that names the command is
  bound to an installation task by an explicit `condition`, and carrying it to a
  template task would carry the upgrade order with it. What a code change needs
  is a different statement.
- Inside the boundary rather than out of it. `doesNotCover` sends "running an
  installation: server and container setup" to the manual, and that line runs at
  the container and the webserver: `installation-upgrade` already states two of
  the core's own console commands under scope `project`.
- The category is not the answer. It arrived as `tool-gap` and needs no tool —
  `typo3_architecture_lookup` would answer it from a hint that does not exist.
- The feedback's **Suggestion** is not copied down. It asks for "the one correct
  way", and whether there is one way or one per kind of change is what the
  reading settles.

## Assumed

- That the answer is one statement rather than three. A template, a TypoScript
  and a TCA change may invalidate different groups, and the reading may split
  it.
- That `var/cache/code/fluid_template` was a real path in that installation. It
  is where a `SimpleFileBackend` PHP cache writes, and nothing here saw the
  project the session was in.

## Wrong if

- The reading shows a changed Fluid template invalidates its own entry. The gap
  is then why the change did not show rather than which command clears it, and
  the statement belongs beside the `sendCacheHeaders` one.
- Which caches a change invalidates turns out to be per installation rather than
  per major. `cacheConfigurations` is overridable in a project's settings, and a
  general statement would describe a default that installation does not run.
- The statement lands on the `caching` hint in `php.json`. That is where a cache
  is declared, which is a core-development question, and the session that needed
  this asked from a template.

## Covered by

- `HintsTest::aChangeIsToldWhichCacheGroupHoldsItsOldOutput`
- `HintsTest::clearingACacheAndDeclaringOneAreDifferentQuestions`

## Confirmed on 2026-08-03

The statement held and the gap is filled: `page-cache-flushing` in
`page-rendering.json`, with `R-KNW-059` and two cases in `HintsTest`. The
reading was `typo3fluid/fluid` at the three releases the branches pin —
downloaded, because no checkout vendors it — plus the three checkouts at
`31f881a212`, `1d104f3b95` and `faf60eea22`.

The first **Wrong if** fired, and the entry's own instruction for it was the
right one. `TemplatePaths::createIdentifierForFile()` hashes the file path
together with `filemtime()`, identically in 2.15.0, 4.6.1 and 5.3.1, and
`sysext/fluid` overrides it on none of the three — so a changed template,
partial or layout is written under a new identifier and the stale entry is
simply never read again. The deletion the feedback reported reached the one
cache that was already correct. What answers with the old page is the `pages`
cache: `PrepareTypoScriptFrontendRendering` builds its identifier from the page
id, type, user groups, site, language base, route arguments, `sys_template` rows
and the TypoScript condition lists, and no template file enters it. So the
statement is a correction *and* a command, and it sits beside the
`sendCacheHeaders` one as this entry said it would.

The second **Wrong if** was avoided rather than disproved. What the statement
names is the group and the command, both of which survive a project overriding
`cacheConfigurations`; which cache sits in which group is stated as the default
it is.

Both **Assumed** resolved. The answer is one hint and several statements rather
than one sentence — a template change needs group `pages` for the rendered page
and nothing for itself, TypoScript needs `pages` where it is a file and nothing
where it is a record, TCA needs `system`. And `var/cache/code/fluid_template` is
the path: `SimpleFileBackend::setCache()` picks `code` over `data` on the
frontend being a `PhpFrontend`, which `FluidTemplateCache` is.

Two findings the entry did not anticipate, both in the statement. `di` is not a
cache group like the others — it has no `cacheConfigurations` entry at all,
`Bootstrap::createCache()` synthesises it, and `CacheFlushCommand` flushes the
container backend outside the event, for `system` and `all` as well. And on 14
only, `fluid_component_definitions` keys a component on its namespace and view
helper name and on nothing else, so a component whose compiled template clears
itself keeps a stale argument and slot declaration until group `system` is
flushed.

The todo's own aim was off by a file and an entry: the `sendCacheHeaders`
statement is `page-cache-headers` in `page-rendering.json`, not
`frontend-page-rendering` in `fluid.json`. The neighbour it named is what
decided the placement, so the new entry went beside it.
