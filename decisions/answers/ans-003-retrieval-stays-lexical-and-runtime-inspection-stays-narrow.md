---
id: D-ANS-003
date: 2026-07-30
status: confirmed
---

# D-ANS-003 — Retrieval stays lexical and runtime inspection stays narrow

**No embedding dependency, no semantic index, and no generic SQL, log or
database-schema tool: what version, audience, binding and source decide is not a
gap a semantic match would close.**

The live-documentation source created the point at which the remaining search
and inspection gaps could be measured instead of anticipated.

## Evidence

- `bin/cli hints:coverage` finds every one of the 61 hints from its own title.
  Seven of 25 scenario prompts reach no architecture hint: `CORE-06`, `META-01`,
  `META-04`, `META-05`, `EXT-01`, `EXT-05`, `SITE-03`. They are respectively
  version spread, orientation, structured-only output, installation, an upgrade,
  the explicit testing boundary and effective runtime configuration. Their
  owning route is not an architecture hint, so a semantic match would make the
  report look fuller without answering them better.

## Decided

- No embedding dependency or semantic index is added. The concrete live-docs
  ranking defect was lexical — separated words tied a precise multi-word title —
  and is fixed by weighting adjacent query terms, guarded by
  `DocumentationTest`. Semantic retrieval may nominate candidates in future, but
  version, audience, binding and source still decide what can be returned.
- No generic SQL, log or database-schema tool is added. No feedback feedback or
  new scenario needed one after live documentation and the existing installation
  diagnostics were available. A runtime tool starts with the session that could
  not finish without it, not with parity against another server.

## Wrong if

- Real queries repeatedly miss a present section after short English
  alternatives, or a scenario's diagnosis cannot be completed from project
  files, effective configuration and the caller's own checkout. Record that
  session; it supplies both the tool boundary and the safe result shape.

## Confirmed on 2026-08-02

The retrieval half fired, on the changelog rather than on the prose sections,
and the cause is lexical. Two sessions in different checkouts report it:
`2026-07-31-172753` found `#109438` and `#108345` only because the test suite
printed them, and `2026-07-31-194504` reports six queries returning nothing.
Both reproduce from `.checkouts/14.3` today. `ext_tables.php` and
`ext_emconf.php` reach no entry, while `ext tables extensions` reaches
*ext_tables.php in extensions* and `emconf` reaches *Deprecation of
ext_emconf.php*. `Changelog::entries()` searches the file name and its CamelCase
split, `LabelSearch::terms()` splits the query on whitespace alone, and
`carryingEvery()` asks `str_contains` — so a term carrying `_` or `.` matches
nothing, the entry's own title included. That is a query language the tool does
not implement, which is `R-ANS-004`, and the todo behind it is `390`. On the
live documentation the falsifier did not fire: `2026-08-01-002928` missed the
present *Record objects* page three times, but every query was question-shaped
where the schema asks for short ones, and the two-word `Record API` returns it
third today.

## Confirmed on 2026-08-02

The runtime half fired once, in a recorded run, and what it asks for is narrower
than what was refused. `REVIEW-02` run 5 left the v13 delta-only rule for
`ext_tables.sql` unraised, "since I did not verify each column against the
schema analyzer's TCA-derived output" — the one diagnosis in either run its
project files, its effective configuration and its checkout could not complete.
The boundary is one table's TCA-derived columns,
`Core\Database\Schema\DefaultTcaSchema`, and `E-EXT` has no database, so whether
that class answers without one is what todo `400` settles. Two further sessions
could not complete a diagnosis and neither wanted SQL, a log or a schema:
`2026-07-31-174524` needed the v14 Page module to render an identifier-less
backend layout, and `2026-08-01-002745` needed the preview to render
`{record.header}`. The safe shape comes from the second one, which reached for
all three refused sources and got nothing from any. Its `SELECT` and `INSERT` on
`tt_content` were what the user corrected to DataHandler, the log carried only a
stale entry, and the reflection script it wrote into the webroot was stopped by
the user; the answer came from a manual page.

## Confirmed on 2026-08-02

The runtime half was read a third time and did not fire. `2026-07-31-174526`
asks for a lookup that says whether a registration is still consumed in the
installed major. `bootstrap_package` registers two
`$GLOBALS['TYPO3_CONF_VARS']['FE']['contentRenderingTemplates']` entries naming
`bootstrappackage/Configuration/TypoScript/`, a directory the move to site sets
removed, and the session read `SysTemplateTreeBuilder` and
`TreeFromLineStreamBuilder` out of its own vendor tree to settle that they are
inert. That is the **Wrong if** met and answered the other way: the diagnosis
*was* completable from the caller's own checkout, and what it cost was one file
read, not a source refused here. The key is read in exactly those two classes on
13.4, 14.3 and `main`, and in them plus `TypoScriptParser` and `TemplateService`
on 12.4. Every use is a membership test deciding whether
`FE['defaultTypoScript_<type>.']['defaultContentRendering']` is appended while a
legacy `EXT:<key>/Configuration/TypoScript/…` static include is resolved.
Nothing deprecated it: `fluid_styled_content` registers itself the same way on
every branch, and no changelog entry names it.

What the feedback asked for is a detector — registrations flagged as inert, with
the version range in which they are. That wants a per-version model of which
`TYPO3_CONF_VARS` keys the core still reads, and under which conditions, kept
current across four branches. It would state a fact that has not moved since
12.4. It would also key on the wrong signal: this API is not deprecated, so
`deprecated-apis` and the Extension Scanner both answer "fine", and what makes
the entry dead is that the path it names is gone.

The cheap lever is where the session's own question already lands.
`typo3_architecture_lookup` with `paths: ["ext_localconf.php"]` and that
question as the task returns `extension-files`, whose hints say nothing about
this key, and `bin/cli hints:probe` reaches nothing for
`contentRenderingTemplates`. The placement works and the sentence is missing,
which is step 1a of the ladder rather than a tool boundary. Re-running
`typo3_extension_scope` for `bootstrap_package` also showed the answer telling
the session there was nothing more to read: `notReadStatically` is `[]` because
`ext_localconf.php` is not among the five files it is drawn from. Both are
queued as todos of their own and the feedback stays open behind them.

## Since then

The second of those two is settled, and `notReadStatically` keeps its five
files. It is a contingent field — `Configuration/Backend/Modules.php` returning
a literal is read and not listed — and it promises that the booted installation
answers for what is in it. `ext_localconf.php` fits neither half: it is never
read in either mode, so it would be listed for every extension that ships one,
and booting answers only the tables, content elements and icons it adds. Listing
it would have moved the false signal rather than removed it, from an empty list
to a named file a reader would then try to boot away.

What over-claimed is what was narrowed. The schema said *an empty list here
means every file that exists was read*, and the rendered caveat excluded
registrations made in `ext_localconf.php` *with a PHP call* — where the
`bootstrap_package` entries are plain assignments into `$GLOBALS`. Both now say
that `ext_localconf.php` and `ext_tables.php` are named among the registration
files and read by nothing here, that a hook, an RTE preset or a global Fluid
namespace one sets is in no list, and that the installation answers only the
tables, content elements and icons. Checked against
`/home/benji/projects/bootstrap_package` on 2026-08-02, where `bk2k` is
registered in `ext_localconf.php` and `fluidNamespaces` is `[]`, because that
extension ships no `Configuration/Fluid/Namespaces.php`. `R-ANS-012` is what it
stands under and `ProjectTest::theFilesThatRegisterByRunningAreSaidToBeUnread`
holds it. The feedback stays open behind the first todo, the missing hint for
`contentRenderingTemplates`.

## Since then

The live-documentation reading of `2026-08-01-002928` holds, and what it left
open is now measured. *Record objects* ranks 28th, 13th and 11th of 1230 pages
for those three queries against a `limit` of 6, so the section was present and
reachable and no semantic index stood between the session and it. What decided
each query was a word with nothing to do with the subject — `has` carried by 3
pages, `get` by 5, `relati` by 1 — against `api` at 635 and `record` at 32. So
the falsifier fails for a sharper reason than the queries being question-shaped:
the ranking was right and the caller was told nothing about it. `D-ANS-021` is
where that goes, and it is a judgement about wording rather than about
retrieval.

## Confirmed on 2026-08-03

The runtime half was read a fourth time and did not fire.
`feedback/2026-08-03-144457` asks for an idiom-precedent lookup: given an
attribute, a class or a call — `#[Autowire(lazy: true)]`, `SingletonInterface`,
`#[AsEventListener]` — return the core call sites with their paths and lines,
read from the installed core package the way `typo3_extension_scope` reads an
installed extension. What a review buys with it is a proposed alternative that
carries precedent instead of taste.

The session that asks for it had already finished without it, and its own report
says the checkout is the honest fallback. Both readings reproduce in
`.checkouts/main` at `c71b2bdb2f` today, at one grep each:
`typo3/sysext/backend/Classes/Routing/UriBuilder.php:199` constructs
`new RequestContext('/typo3/')`, and `#[Autowire(lazy: true)]` stands in the
three files the feedback names — `core/Classes/Site/Set/SetRegistry.php:43`,
`form/Classes/EventListener/DataStructureIdentifierListener.php:68` and
`form/Classes/Domain/Configuration/PersistenceConfigurationService.php:41` —
five occurrences across them. So the **Wrong if** is tested and answered the
other way for the second time: the diagnosis completed from the caller's own
checkout, and the **Decided** bullet holds that a runtime tool starts with the
session that could not finish without it.

`D-FBK-027` reads the same evidence from the other side. What does not qualify
is a fact the caller reads once from its own checkout, and a precedent sweep is
one grep in a tree the reviewer already has open — against the four round trips
and the challenge page that bought the Forge lookup. The lookup would also have
to say which core it answers for: a review reads a working tree at a revision,
and an installed package answers for the release it is pinned to, which is the
`unavailable`-shaped half of the same entry.

What that session lacked is the sentence saying in advance that this question
has no home here. That is `D-SKL-004`'s and is queued there.

## Confirmed on 2026-08-03

The runtime half was read a fifth time and did not fire.
`feedback/2026-08-03-164749` asks for a lookup that takes an extension-relative
template, layout or partial path and returns the core file it shadows or was
forked from in the installed version, with that file's current path and content
— turning a fragile fork into a diff, and making a staleness signal for forks
derivable rather than hand-assembled. The audit behind it forks the core backend
login layout and registers it by appending a layout root path while an event
runs, and its own report calls the hand-made diff the single most productive
artifact of the run.

The three round trips it counts were bought by the file name rather than by the
mapping. `diff` against `Login.html` failed, a `find` produced
`Login.fluid.html`, and the third call was the diff — and the rename together
with the fact that a bare `.html` still resolves is in `fluid-templates` with
`since: 14`, written the day before this feedback and returned first for the
audited path. With that in hand the mapping is the Fluid roots
`typo3_extension_scope` already reports plus the per-root-path fallback chain,
and the diff is one command in a tree the auditor has open — which is where
`D-FBK-027` draws the line, and the same counting as the precedent sweep above.
So the **Wrong if** is answered the other way a second time: the diagnosis
completed from the caller's own checkout, and it produced the two findings the
report ranks highest.

What is missing is a sentence and not a shape, so the lever moved to
`D-KNW-052`: nothing here states that the chain is walked once per root path,
which is what decides whether a forked `Login.html` overloads the core's
`Login.fluid.html` at all. The **Wrong if** of that entry names the one thing
that would bring this half back — a root path order that can only be read from
the running installation.

## Since then

The runtime half was read a sixth time on 2026-08-18, and this is the first
reading where the diagnosis was not completable from the caller's own checkout.
`feedback/2026-08-18-074124` wrote against five core APIs in a package declaring
`^13.4.15 || ^14.3 || 15.*.*@dev` with 14.3.6 installed, and the checkout it had
was at the wrong major: it settled the 13.4 half by curling from
raw.githubusercontent.com and then by installing a second core. So the **Wrong
if** fired in the direction it names, and what the session supplies is not the
boundary this entry refused. What was missing is a branch rather than a reader —
another session the same day answered the same class of question at one
`git show origin/13.4:<path>` per file from a checkout that carried it
(`feedback/2026-08-18-080710`), and a third report of that session asks for the
`doesNotCover` entry standing on this decision to stay exactly as firm as it is
(`feedback/2026-08-18-080743`). The lever moved to `D-VER-007`, which is the
procedure that names the reading and reads no core source here.

## Confirmed on 2026-08-21

The runtime half was read a seventh time and did not fire.
`feedback/2026-08-21-074351-cache-configuration-is-answerable-but-the` asks for
the CacheManager beside the configuration array: per identifier, whether it is
registered as well as configured, whether it has been initialized, and the
frontend class in effect rather than the one the entry names. Its argument is
that the two identifier sets differ, so the gap between them is a diagnostic no
configuration read can produce.

They differ by one identifier, and by the same one on every covered line.
`ServiceProvider::getCacheManager()` sets the configured array and then
registers the container's own caches — `core`, `assets` and `di` on 12.4, those
plus `runtime` above it — and all of them but `di` have a
`DefaultConfiguration.php` entry. Nothing else can register at boot: the same
method throws while `boot.state` is incomplete, so an `ext_localconf.php` has no
CacheManager to call `registerCache()` on. What is left of the union is `di`,
which `D-KNW-027`'s reading already found and put into `page-cache-flushing`.

The other half of the diagnostic is laziness rather than health. `createCache()`
runs on the first `getCache()`, so after a boot the registered set is the
container's caches and nothing more, in every installation — configured but not
registered is where every entry stands until somebody asks for it.

So the **Wrong if** is answered the other way again, and by the cheapest of
these readings: the session was comparing this server with another one rather
than failing a task, which is the case the second **Decided** bullet names. It
reports no round trips, and `D-FBK-027` is what would have counted them.

What is missing is a sentence and not a shape. `createCache()` fills each key an
entry omits from `CacheManager::$defaultCacheConfiguration` —
`VariableFrontend`, `Typo3DatabaseBackend`, no options, group `all` — so a
project entry naming only a backend runs in group `all` and the array does not
say so. That and the completeness of the array are two statements on the
`caching` hint, held by
`HintsTest::theCacheRegistryIsTheConfiguredArrayAndTheDefaultsThatFillIt`, and
the feedback is archived with them. Read at `31f881a212`, `fccbd407d8`,
`627949e9dd` and `3a9f0b5e3c`, where the defaulting is identical.

## Confirmed on 2026-08-21

The runtime half was read an eighth time and did not fire.
`feedback/2026-08-21-074351-the-viewhelpers-an-installation-has-are-not` asks
for the ViewHelpers of the running installation — identifier, class, what each
one is for, and the argument definitions with their types and which are required
— searched and batch-validated the way `typo3_icon_lookup` validates an icon.

Its own **Query** names the case the second **Decided** bullet refuses:
*compare this server against another TYPO3 MCP server. No failing call.* It
counts no round trips, and `D-FBK-027` is what would have counted them.

The core half already answers in the shape that was asked for, out of the book
[`D-ANS-026`](ans-026-the-viewhelper-reference-is-indexed-and-a-manual-carries-the-collection-it-is-published-in.md)
indexed. `typo3_documentation_lookup` puts `Global/For.html` first for
`f:for each ViewHelper` at 14.3, and that URL passed back as `page` reads out an
**Arguments** section: `as` string required, `each` array required, `iteration`
and `key` string, `reverse` boolean defaulting to false. Measured twice today.
An argument list costs one call rather than a tool.

The half that is not core is a file the caller already has open. A Fluid
identifier resolves to a class by convention — `be:avatar` is
`backend/Classes/ViewHelpers/AvatarViewHelper.php` in `.checkouts/main`, and
`be:link.editRecord` the `Link/` directory beside it — and
`typo3_fluid_namespace_list` answers the prefix-to-PHP-namespace half the
convention starts from. That is the single read in an open tree where
`D-FBK-027` draws its line. It is also what separates this from an icon: an icon
identifier is registered in PHP across packages and derives no path at all,
which is why `typo3_icon_lookup` has to ask the container.

Nothing in the corpus failed on which arguments a ViewHelper takes. The two
Fluid render failures it carries are about the value passed rather than the
argument set — `2026-07-29-234410` on the missing empty array literal,
`2026-08-13-215637` on `hasItems()` shadowing `items` — and an index of the
installed ViewHelpers would have prevented neither.

What is missing is a sentence, and it is a placement. `fluid-viewhelpers` is
written for somebody authoring one, and `fluid-templates` routes an icon and a
backend component without routing an argument list. So a session writing a
template is told nowhere where that list comes from. The feedback is trimmed to
that half and keeps a todo.
