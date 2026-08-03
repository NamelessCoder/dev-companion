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
forked from in the installed version, with that file's current path and content —
turning a fragile fork into a diff, and making a staleness signal for forks
derivable rather than hand-assembled. The audit behind it forks the core backend
login layout and registers it by appending a layout root path while an event
runs, and its own report calls the hand-made diff the single most productive
artifact of the run.

The three round trips it counts were bought by the file name rather than by the
mapping. `diff` against `Login.html` failed, a `find` produced `Login.fluid.html`,
and the third call was the diff — and the rename together with the fact that a
bare `.html` still resolves is in `fluid-templates` with `since: 14`, written the
day before this feedback and returned first for the audited path. With that in
hand the mapping is the Fluid roots `typo3_extension_scope` already reports plus
the per-root-path fallback chain, and the diff is one command in a tree the
auditor has open — which is where `D-FBK-027` draws the line, and the same
counting as the precedent sweep above. So the **Wrong if** is answered the other
way a second time: the diagnosis completed from the caller's own checkout, and it
produced the two findings the report ranks highest.

What is missing is a sentence and not a shape, so the lever moved to `D-KNW-052`:
nothing here states that the chain is walked once per root path, which is what
decides whether a forked `Login.html` overloads the core's `Login.fluid.html` at
all. The **Wrong if** of that entry names the one thing that would bring this
half back — a root path order that can only be read from the running
installation.
