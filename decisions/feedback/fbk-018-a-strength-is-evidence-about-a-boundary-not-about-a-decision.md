---
id: D-FBK-018
date: 2026-08-02
status: confirmed
---

# D-FBK-018 — A strength is evidence about a boundary, not about a decision

**A feedback that reports no gap is not a confirmation; what it carries is where
a boundary runs, read against the costs other feedback report at the same one.**

The ladder has no rung for a report of what worked. Every step names something
missing, misplaced or misworded, so the one question has to be asked from the
other side: what is the strength evidence of?

## Evidence

- Three feedback from one debrief, five seconds apart, same directory
  (`/home/benji/projects/bootstrap_package`) and same model. `2026-07-31-174529`
  reports `typo3_changelog_lookup` as what made the review's first finding
  provable; `2026-07-31-174524` reports the same tool as unable to say whether
  the v14 Page module renders a backend layout without column identifiers; and
  `2026-07-31-174526` reports that no lookup says whether a
  `contentRenderingTemplates` registration is still consumed.
- The strength reproduces. Re-run on 2026-08-02 through `bin/typo3-cms-mcp` from
  that directory: `ext_tables.php` reaches *14.3 Deprecation: ext_tables.php in
  extensions* (#109438), `UpgradeWizard` reaches the 14.0 deprecation of the
  moved interfaces (#106947), `addPiFlexFormValue` reaches its 14.0 deprecation
  (#107047), and all three `.rst` files are in `.checkouts/main`.
- Two of its claims are looser than the answer. `typo3_project_scope` classifies
  six of ten declared commands as `check` or `change` and three as `unknown` —
  the phpunit suites, which the answer's own prose says it will not classify —
  rather than "every repo command"; and the platform reality it credits to that
  tool comes from `typo3_extension_scope`, whose footer reports that the
  installation was not asked because the host runs PHP 8.3.23 against a
  `>= 8.4.0` requirement.
- Nothing here can say which `base.md` that session read. Both installed copies
  of the skill in that repository were rewritten after the report — `.claude/`
  at 18:01 and `.agents/` at 20:04 against a 17:45 feedback — and the step 5
  deprecation sweep had landed 81 minutes before it.

## Decided

- The feedback is closed by this commit. There is nothing to queue: keeping
  something is not work, and the two costs it points at are on the board with
  cards of their own.
  [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes "nothing to do" the close answer rather than a special case.
- A strength does not confirm a decision. It is a session's account of its own
  run, which is what [judging.md](../../documentation/feedback/judging.md)
  already refuses to assess in the other direction — the session was there and
  the reader was not. `D-SKL-001` is confirmed by recorded runs with timings in
  them, and a self-report cannot be read against its **Wrong if** the same way.
  Nothing was added to it here.
- What the strength is evidence for is where the corpus stops. It and the two
  costs are one boundary from both sides: the changelog answers what **changed**
  at a version, and both costs asked what still **holds** at one. A change
  carries an issue number and a state does not, which is why the same tool is
  precise in one direction and silent in the other.
- The gap is named and not the fix. What fills the state half belongs to the two
  cost cards, which are in hand elsewhere; naming their answer from here would
  be the copy-down that judging.md warns produces a guess with a reading's
  authority.

## Assumed

- That the three came from one session. They share a directory, a model and five
  seconds, and nothing in a feedback records a session.
- That a strength is worth a judging run at all. This one cost a re-run and
  bought the boundary above, which none of the three files states on its own —
  but the cheaper alternative, archiving it unread, would have looked identical
  from outside.

## Wrong if

- A positive feedback turns out to carry a lever nothing else does — praise that
  names what the session did instead. The ladder would then apply after all, and
  reading a strength as boundary-evidence only would have skipped it.
- The two cost cards are judged and land somewhere other than the change/state
  boundary. The pairing above would then be a reading of three files rather than
  a property of the corpus.
- Strengths accumulate unread, because closing one leaves nothing anybody can
  point at afterwards. This entry and its commit are the whole record; if
  neither is cited again, the run was a cost with no return.
- The line a strength's praise implies turns out not to be worth saying —
  `typo3_extension_scope` naming a missing translation on every extension that
  ships none costs more than it buys. Reading what the praise implies would then
  be right about the asymmetry and wrong about it mattering, and `R-PRJ-006` is
  what would need the sentence instead.
- A strength is found whose praise implies a property the answer does not have,
  and checking its quotations reaches that property anyway. The step added below
  — read what the praise implies, not only what it quotes — would then be one
  this entry did not need.
- The text is not where an absence has to be said, because the clients that
  matter render the data. `R-ANS-002` assumes the opposite client, and no
  recorded run here shows which of the two a session is.

## Since then

The first **Wrong if** fired on the next strength judged,
`feedback/2026-07-31-192945`. Read as boundary-evidence only it says the
conformance skill's order is a good one, which nothing here doubted. What it
actually carries is a lever, and the lever is the praise itself.

The strength recites the order it followed, and the recitation is this server's
order with one step gone. That can be checked against a file, which is what the
self-report this entry refused to assess could not offer. A recitation is an
artifact; an account of a run is not. So the rule established here holds with
one addition. Where a strength quotes something this repository owns, the
quotation is evidence about the file rather than about the session, and it is
read before the boundary is.

The judgement is on
[`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md),
whose **Wrong if** is where the omission lands. The rest of this entry stands.
The run that judged it established nothing about TYPO3 and named the gap rather
than the fix, and it left the feedback open behind an answer it may not give.

## Confirmed on 2026-08-02

The same reading held on a second strength, at a second boundary, and the first
**Wrong if** did not fire. `2026-07-31-193050` reports what this server saved a
conformance audit of `printworks_sitepackage` from, in
`/home/benji/projects/site-new`. It names nothing the session did instead: every
counterfactual in it is *I would have had to read those files by hand*, which is
what the ladder cannot be walked from. The half that does name it was filed
nineteen seconds later — `2026-07-31-193109`, same directory, same model, same
list of tools, titled *what I had to establish elsewhere* — and that one is on
the board with a card of its own.

The pairing is wider than the one above, which is what makes it a property of
the corpus rather than a reading of one debrief. Two more feedback from the same
evening, and two other models, sit on the same line in the same project:
`2026-07-31-194825` reports the absences `typo3_extension_scope` answers as
first-class results, and `2026-07-31-194510` reports that the same tool says
what an extension registers and not what it ships. Both directions are reported
about one tool, by sessions that never saw each other's runs.

The boundary is **what is registered against what is written in the files**.
Everything the strength credits is a registration or a convention: the tables,
the content elements and the icons the installation attributes to the extension,
its Fluid roots, its requires, its site sets and declared commands, and the
version-bound hints and changelog entries. Everything the cost lists is a file's
contents or an absence outside that surface: the bodies of eight test classes,
the shipped `Initialisation/data.xml`, a `Configuration/Form/…/config.yaml`
nobody registered, a `PageTitleProvider` that is missing, the runtime PHP
version. `R-PRJ-005` states the first half as what is answered; neither it nor
`D-ANS-003` states the second half as the boundary it is.

The strength reproduces. Re-run on 2026-08-02 through `bin/typo3-cms-mcp` from
that directory: `typo3_project_scope` answers TYPO3 14.3.5, the project
extension, `main at https://site-new.ddev.site/` with set `bk2k/printworks`, and
six declared commands; `typo3_extension_scope` answers seven content elements of
which five carry a `templateName` and two do not, three icons, and three XLF
files declaring `source-language de`; `typo3_changelog_lookup` with
`type=deprecation, version=14, limit=30` answers 75 entries and shows 30, with
the `FullyScanned` and `PartiallyScanned` tags on them. The extension has grown
two tables since the report, and the answer is now read off a booted
installation, so it says more than it did rather than less.

So does the lever behind the *wrong path avoided*. `typo3_architecture_lookup`
with the task *content element with inline children* returns **Registering a
Content Element**, whose hints say that a plugin is a CType like any other and
that the list_type detour is gone at v14, and that the rendering definition is
`tt_content.<CType>` on top of `lib.contentElement`. The task *Extbase plugin
registration and cHash* returns **Extbase Plugins**, **Registering a Content
Element** and **Records in the Frontend Without Extbase**. `site-sets` and
`tca-formengine` are reachable too, from a task naming those subsystems, which
the two calls behind the report did. The core confirms the statement the session
acted on: `14.0/Important-105538-ListTypeAndSubTypes.rst` in `.checkouts/main`
records the removal of the `list_type` field and of the plugin subtype with it.

**A strength is not evidence about which tool answered.** This is the second
corpus in which the credit is misplaced, and both times on the same fact. The
report has `typo3_project_scope` giving *PHP ^8.4 (actual 8.3.23)*; the tool
answers `PHP ^8.4` and nothing else, and its own cost sibling says the runtime
version was found with `bash`. That number was also wrong, which
`2026-07-31-193611` reports: the tests run in DDEV and the host is not where the
version is. The looser claim beside it is *filtered v14 deprecations to 30
relevant entries* for a call that passed `limit: 30` against 75 matches. Neither
changes what the boundary is, and both are why an account of a run is re-run
before it is read.

The feedback is closed by this commit and nothing is queued. What the two costs
ask for — a `ships` list, a lookup for cross-file logic — belongs to their own
cards, and naming their answer from here is the copy-down that judging.md warns
about. The entry's second **Wrong if** is still untested: those cards are
unjudged, and where they land is what says whether a strength pairs with a cost
at a boundary or only reads that way.

## Confirmed on 2026-08-02

The first **Wrong if** landed the day this entry was written.
`feedback/2026-07-31-193005` reads as a strength — thirteen numbered steps of an
extension audit, "the tool chain works well", one suggestion and nothing
reported as broken. Read as boundary-evidence it says what its four siblings
from the same debrief already say, and closing it there would have been correct
by this entry. What it actually carries is the numbered order those calls came
in, which no other feedback in the corpus does, and read against
`skills/base.md` that order is the base outrun: step 3 skipped, and the checkout
read before the conventions lookups rather than after them. The praise is what
names it — the session graded the run as working and asked for the file reading
to be blessed with a batch-read hint, which is the one thing both the base and
the conformance skill refuse.

So a strength is not closed on the strength of being one. The ladder is walked
over what it reports it *did*, and only the part that reports how well it went
is read as a boundary. Judged onto
[`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md),
whose **Wrong if** it satisfies, and the third **Wrong if** above is now the one
still open: this entry has been cited by each of the three judgements above, one
of which disagrees with half of it. All three were worked at once, in sessions
that could not see each other, which is why they read as three arrivals at one
entry rather than as one account continued.

## Confirmed on 2026-08-02

The reading held a fourth time, and this is the first strength whose quotation
could be checked against the file it credits. `2026-07-31-194823` comes from the
debrief of the section above, two seconds before the `typo3_extension_scope` one
and in the same project. It reports that `typo3_architecture_lookup` said only
`rootPageId` is remapped when a shipped site configuration is imported, and that
other uid references are not. The session credits that with turning a wrong
finding into the right one, about a hardcoded `t3://page?uid=2` error target.

The first **Wrong if** did not fire. The strength names one thing the session
did besides calling the tool: it confirmed the answer against the vendor source.
It reports no cost for doing so, and a corroboration of an answer that was right
is not the *did instead* the ladder is walked from. Nothing else in the file
names a workaround, a repeated call or a fact established elsewhere.

The quotation is evidence about a file, and the file holds it.
`bin/cli hints:probe` on the feedback's own query reaches
`sitepackage-initial-content`, and the sentence is in that hint verbatim — the
import "remaps the root page id to the page that was actually imported, and
nothing else", with an error handler naming `t3://page?uid=...` as the example
that ships stale. Re-run on 2026-08-02 through `bin/typo3-cms-mcp` from
`/home/benji/projects/site-new`: `typo3_architecture_lookup` with the task the
feedback names returns *Shipping Initial Content with an Extension* whole,
answered for TYPO3 v14. The core confirms the statement.
`ImportSiteConfigurationsOnPackageInitialization` loads the configuration,
assigns `$configuration['rootPageId']` and writes it back, and touches no other
key. The file is byte-identical in `.checkouts/14.3` and `.checkouts/main`.

The boundary is what an answer carries where somebody wrote it, against what it
carries where it indexes something. The other side is from the same debrief:
`2026-07-31-194821` reports that the removal version of a deprecation sat in the
`.rst` body, and that the session had to open the file to read it.
`typo3_changelog_lookup` returns the type, the version, the issue, the title,
the tags and an `EXT:` path, and
[`D-ANS-016`](../answers/ans-016-a-miss-names-the-query-that-would-have-hit.md)
priced its peel on the observation that it "reads file names and never opens an
entry". So the deciding fact is inside the answer where the corpus is prose this
repository authored, and behind a pointer where the answer indexes files it did
not write. One session reported both directions, having seen neither
implementation.

One change was made, and it is the keep-request in the only form this repository
has for one. `R-KNW-005` holds the general property that a silent failure is
named rather than left to the rule, and
`HintsTest::shippedContentIsAnsweredPastThePointWhereTheFileExists` already
names the sibling sentence about `ReferenceIndex::getRelations()`. The
site-configuration sentence was in no assertion, so what the feedback asks be
kept rested on nobody rewriting the hint. That test names it now. No statement
about TYPO3 was established to do it: the sentence was already in `knowledge/`,
and what was added is that it stays.

The feedback is closed by this commit and nothing is queued. What the changelog
side of the boundary owes belongs to its own card, which is in hand elsewhere,
and naming its answer from here is the copy-down judging.md warns about. The
third **Wrong if** is the one this run bears on: a guard is the first thing a
closed strength has left behind that something other than a commit message can
point at.

## Confirmed on 2026-08-02

`feedback/2026-07-31-194825` is the strength this entry and
[`D-ANS-014`](../answers/ans-014-the-extension-answer-enumerates-registrations-not-files.md)
both cite without judging. Read as boundary-evidence it says what that entry
already says, from the side it calls the strength half, and closing it there
would have been correct by this one as it stood. It carries a lever instead, and
it is neither of the two shapes above. Not what the session did instead, which
it never names; not a recitation of a file this repository owns. It is the
praise itself, read against the thing praised.

The **Since then** above says a quotation is checked before the boundary is.
Four of them are here, re-run on 2026-08-02 through `bin/typo3-cms-mcp` from
`/home/benji/projects/site-new`, the directory it was written in:

- *no manual, no README, which test layers exist and which do not* reproduces.
  `typo3_extension_scope` with `printworks_sitepackage` answers
  `Ships: manual none, readme none, tests Functional+Unit`.
- *read the XLF source languages* reproduces: three files below
  `Resources/Private/Language/`, each at
  `source-language de, no translations beside it`.
- *the answeredBy attribution* reproduces. That answer says `installation`;
  `typo3_project_scope`, which reads files and asks nothing, says `packages`.
- *the commands the repository actually declares, with what each does to the
  sources* reproduces by half. The six commands are there, and every one of them
  answers `runs: unknown`. All six are test suites, and
  [`R-PRJ-007`](../../requirements/project/prj-007-a-declared-command-says-whether-running-it-changes-anything.md)
  says a manifest does not cover what the project's own code writes. The
  classification the strength credits classified nothing here.

That fourth one is the mechanism the section above saw twice and left unnamed.
What is recited is the answer's own explanatory prose rather than the answer.
`typo3_project_scope` spends a paragraph on what a check is and what a change
is, ahead of the six `unknown`s, and the report hands that paragraph back as a
result it received. Its conclusion — that the repository declares no check
scripts — is right, and it is read off the list rather than off the
classification. So a recitation is evidence about the file where the file is a
rule, and evidence about nothing where the file is an answer explaining itself.

The lever is in the first two, and it is what the praise implies rather than
what it quotes. An absence answered rather than left to be found holds for three
of the four artifacts and not for the fourth. `ExtensionScope` renders `manual`,
`readme` and `tests` present or absent in one `Ships:` line, and renders the
language files only where the list is not empty. Run against `rte_ckeditor` in
`.checkouts/14.3`, which ships none, the data carries `languageFiles: []`. The
text runs from
`Ships: manual Documentation/Index.rst, readme README.rst, tests Functional+Unit`
straight into the boundary paragraph. No sentence in it says the extension ships
no translations.

[`R-PRJ-006`](../../requirements/project/prj-006-what-an-extension-does-not-ship-is-answered-too.md)
names the XLF files among the four whose absence is answered. `ToolResult` says
the text is the primary answer and the data is what a client composes with.
[`R-ANS-002`](../../requirements/answers/ans-002-the-reason-is-in-the-data-not-only-in-the-text.md)
states the same rule in the other direction, for a client that renders the data
and drops the text.
`ProjectTest::whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut` asserts the
present case in both halves of the answer and the absent case in the data alone,
which is why nothing failed.

Step 4, wording. Not step 1a, because `Extension::artifacts()` already returns
the key; not 1b, because no verb and no order is missing; not step 2, because
nothing has to move — the same builder renders the three artifacts beside it. It
changes `src/`, so it is queued rather than closed on the spot. The feedback is
trimmed to that half and stays open behind the todo *Say the missing translation
the way the missing manual is said*. The other three are held requirements that
reproduce, and keeping something is not work.

Two things are assumed rather than established. First, that the text is where
the absence has to be said. `R-ANS-002` was written for a client that drops it,
the client assumed here is the opposite one, and what `ToolResult` says about
which half is primary is the whole of the evidence. Second, that `rte_ckeditor`
is not a special case. It is the one extension in
`.checkouts/14.3/typo3/sysext/` with no `.xlf` file at all, so the shape was
found in the only place this machine offers it. Both are written into the
**Wrong if** above.

## Confirmed on 2026-08-02

The fourth **Wrong if** is what the todo above turned on, and it did not fire.
The reading it proposes is a line only where a caller could mistake the absence
for a gap. That needs a predicate — whether this extension is one that would
ordinarily carry labels — and nothing in the files answers it. Everything else
in this answer is a fact read from a file, and `R-PRJ-006` names the four
artifacts without conditioning any of them: the `Ships:` line already says
`manual none` on extensions where no manual is normal.

So the cost is what the **Wrong if** was actually about, and it is a term rather
than a sentence. `Ships:` renders the fourth the way it renders the other three
— `language files none`, or their number where there are some, with the per-file
list unchanged below it. Saying it on every answer costs a word.
`ProjectTest::whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut` asserts both
halves of both cases now, which is what `R-PRJ-006` required all along and the
test covered in the data alone.

The last **Wrong if** is untouched by this. Which half of the answer a client
renders is still unrecorded, and this change writes the absence into both.

## Confirmed on 2026-08-03

The reading held a fifth time, on the first strength whose quotations are hints
this repository wrote for the domain the task was actually in.
`feedback/2026-08-02-144456` comes from a core checkout,
`/home/benji/projects/typo3-cms`, and reports a fix in the Fluid image
ViewHelpers for Forge #105403. It credits three answers: that ViewHelpers are
covered by functional tests and why, that a ViewHelper is public API and owes a
changelog entry, and `typo3_project_scope` reporting `15.0.0-dev`.

The quotations are checked before the boundary is. Re-run on 2026-08-03 through
`bin/typo3-cms-mcp` from that directory with the feedback's own arguments —
`typo3_task_guide` with the task *Fix f:image ViewHelper failing when src
contains a cache busting query string produced by f:uri.resource*,
`changeType=bugfix`, `area=fluid`, `targetVersion=15.0` — and both sentences
come back verbatim in the `fluid-viewhelpers` block. The third reproduces by
half. `typo3_project_scope` answers
`core-checkout, TYPO3 15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV`, and names
neither `Changelog/15.0/` nor a `Releases: main` trailer; the session derived
both from the version. The derivation was right — `.checkouts/main` carries
`Documentation/Changelog/15.0/` — which is why nothing in the report reads as
one. That is the third corpus in which a strength misplaces its credit, and the
first where what is credited is the reader's own inference rather than another
tool.

The lever is what the session did instead, and it is one sentence at the end: it
read neighbouring changelog files to decide which of Breaking, Deprecation,
Feature or Important its entry was. Step 2, delivery. The rule is here twice —
the `changelog` intent in `knowledge/task-intents.json` carries the
discrimination, and the `documentation-changelog` hint carries the filename and
the version directory — and neither reached a task about a ViewHelper bug.
`TaskIntents::detect()` matches an intent against the task text, and that text
is about the bug; the sentence saying a changelog is owed arrives from a hint,
after the matching that would have delivered the rest.

The boundary is between an obligation and the rule that discharges it. A hint
states what a change owes because the caller is working in that domain, while
what it takes to discharge it belongs to the domain of the artifact and is
reachable only from a query already about that artifact. Nothing crossed between
the two, and the reading the feedback reports is what that cost.

Three changes, all of them placement and wording of rules already here. The
`fluid-viewhelpers` statement now names the minor-version directory and sends
the type decision to `documentation-changelog`. That hint gains the sentence
which decides the type, read off `Documentation/Changelog/Howto.rst` in
`.checkouts/main` rather than moved on trust: the four types are defined there,
with Important as the last resort and the only one an LTS release may carry.
`HintsTest::aViewHelperPatchIsToldWhichTestItOwesAndWhichChangelogType` holds
the two sentences the feedback asks be kept, which is the keep-request in the
only form this repository has for one.

One thing was rejected. An intent that fires because a delivered hint says a
changelog is owed, rather than because the task text says "changelog", would
answer the crossing itself instead of pointing across it — and it touches
`src/`, which is queued rather than closed on the spot. A pointer costs the
caller one call and this is the one report of the crossing; a second report is
what would make it worth the schema.

What the change rests on is that the discrimination now stands in
`task-intents.json` and in the hint corpus at once. It is wrong if one is
rewritten and the other keeps the old wording, because then this run bought a
delivery and paid for it with two statements of one rule.
