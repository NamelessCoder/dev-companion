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
- The strength reproduces. Re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from that directory: `ext_tables.php` reaches *14.3
  Deprecation: ext_tables.php in extensions* (#109438), `UpgradeWizard` reaches
  the 14.0 deprecation of the moved interfaces (#106947), `addPiFlexFormValue`
  reaches its 14.0 deprecation (#107047), and all three `.rst` files are in
  `.checkouts/main`.
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
  run, which is what [judging.md](../../documentation/records/judging.rst)
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
- A keep-request is refused on a re-run and the sentence turns out to have been
  right after all, so that reading a strength against today's file lost
  something the session knew and this repository had forgotten.
- The text is not where an absence has to be said, because the clients that
  matter render the data. `R-ANS-002` assumes the opposite client, and no
  recorded run here shows which of the two a session is.
- A keep-request answered with a quoting test refuses the rewrite that improves
  a sentence as readily as the one that drops it. The helper those tests read a
  skill through takes the line breaks out of the comparison and nothing takes
  the wording out, so from then on the test is what an author has to argue with
  rather than the file. That is the cost of the only form a keep has here, and
  it is paid per sentence.

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

The strength reproduces. Re-run on 2026-08-02 through `bin/typo3-dev-companion`
from that directory: `typo3_project_scope` answers TYPO3 14.3.5, the project
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
that ships stale. Re-run on 2026-08-02 through `bin/typo3-dev-companion` from
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
Four of them are here, re-run on 2026-08-02 through `bin/typo3-dev-companion`
from `/home/benji/projects/site-new`, the directory it was written in:

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
`bin/typo3-dev-companion` from that directory with the feedback's own arguments
— `typo3_task_guide` with the task *Fix f:image ViewHelper failing when src
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
## Confirmed on 2026-08-03

The **Since then** above says a quotation is evidence about the file rather than
about the session, and is read before the boundary is. This is the strength that
says what such a quotation is evidence *of*, because here the file held it and
the file was wrong.

`feedback/2026-08-01-121852` reviewed the AssetCollector deprecation in
`/home/benji/projects/typo3-cms` and calls one answer "the single most useful of
the whole review": that this server "never reads, inspects or runs anything
against a TYPO3 core checkout; determine the changed paths yourself and pass
them to typo3_architecture_lookup and typo3_task_guide". The quotation is exact.
It is the first `doesNotCover` entry of `knowledge/server-scope.json` at
`18a371a`, which is the version that was in play on 2026-08-01, down to the tool
name — `typo3_architecture_lookup` was renamed to `typo3_hint_lookup` at
`7553cb3` afterwards.

That sentence was false when it was praised, and `f8be448` deleted it two days
later. `typo3_project_scope`, started in the very checkout this review was
written in, answers
`core-checkout, TYPO3 15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV` with the
four `composer gerrit:setup` scripts it read there. The boundary that holds is
git, not the checkout, and `R-SCO-008` now carries
`ScopeTest::noExclusionDeniesASourceTheServerReads` for it.

So the keep-request is refused, and that is the point worth recording: there is
nothing left to keep. What the session acted on was never the `why` it quotes —
it was the `instead` beside it, *determine the changed paths yourself and pass
them in*, which is true, survived the rewrite, and is what
`typo3-core-patch-review` now opens with. An exclusion is used through its
`instead`; its `why` is read once and believed, and no use of it tests the
claim. That is why the session that called the sentence its most useful answer
is also the one that could not notice it was untrue.

The corpus reports both sides on one day. `feedback/2026-08-01-115115` comes
from the same checkout and the same review week, and its strength is that
`typo3_project_scope` "correctly identified the checkout as a TYPO3 core
(15.0.0-dev, PHP ^8.5, no project extensions, no sites)" — the reading the other
strength praises this server for never doing. Neither session saw the other, and
both were right about what they received.

**A strength's quotation dates a file; it does not verify it.** What it
establishes is that the sentence was there and was read, which is what makes it
evidence at all. Whether it was true is a separate question with a separate
source, and a keep-request is therefore answered against the file as it is now
rather than against the praise.

The rest of the report reproduces. Re-run on 2026-08-03 through
`bin/typo3-dev-companion` from `/home/benji/projects/typo3-cms`, whose DDEV
project is paused, so the console was not reachable and neither tool needs it:

- `typo3_test_run_guide` with the eleven changed paths of `e82b930e6e0`, the
  patch under review, narrows to the php, fluid and docs domains and answers
  `cgl`, `cglGit`, `functional`, `phpstan`, `unit` and `checkIntegrityPhp`.
  Every command carries `CI=true`, `cgl` carries `-n`, and `unit` and
  `functional` carry the `--` passthrough — the three things the report names as
  what ran clean first try.
- `typo3_commit_message_guide` with that patch's message returns it wrapped at
  72 characters and one check: the changelog `.rst` a deprecation owes. No
  readiness is claimed anywhere in the answer, which is the "no false readiness
  flags" the report credits.
- The per-tool attribution it asks be kept is in every topic of
  `typo3_server_scope`, as the `Tools:` and `Source:` lines.

Its suggestion has landed. `1b61d5d` put the review row into `routing` —
`typo3_rule_lookup` per obligation, `typo3_changelog_lookup` for the precedent,
`typo3_test_run_guide` with the changed paths, then `typo3_commit_message_guide`
— and published
[`D-SKL-005`](../task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md)'s
`typo3-core-patch-review`, which states this feedback's boundary in the working
form. The companion note it points at is `feedback/archive/2026-08-01-121847`,
from the same review and closed on 2026-08-02.

One thing was found unguarded and is guarded now, which is what the third
**Wrong if** asks a closed strength to leave behind. The narrowing this report
credits was held twice over; the invocation was held by nothing. Dropping
`targeted` from every entry of `knowledge/test-suite-hints.json` broke no test,
and `catalog:check` verifies the `-s <suite>` of a command against
`.checkouts/`, never its options.
`HintsTest::theTargetedInvocationSurvivesWithTheThreeThingsThatMakeItRunnable`
names them on the paths the report is about. No statement about TYPO3 was
established for it: all three are read in
`.checkouts/main/Build/Scripts/runTests.sh`, where line 6 branches on `CI`,
`shift $((OPTIND - 1))` hands what follows `--` to the tool, and `-n` sets
`CGLCHECK_DRY_RUN`, which `cgl` turns into `--dry-run --diff`.

The feedback is closed by this commit and nothing is queued.

## Confirmed on 2026-08-03

The first **Wrong if** fired again, and this time the lever is a clause the
strength appends to its own praise. `feedback/2026-08-01-115115` credits
`typo3_project_scope`, `typo3_rule_lookup` and `typo3_commit_message_guide` from
a core patch review in `/home/benji/projects/typo3-cms`, asks that all three
stay as they are, and ends: *the compound rule_lookup queries failed but the
single-term ones worked*. That names no workaround and quotes no file, so it is
neither of the two shapes above — it is the report's own qualification of what
it is praising, and it is the only sentence in the file nothing else in its
session states.

The **Since then** holds: the quotations were checked before the boundary was,
and all three reproduce in the report's own words — re-run on 2026-08-03 through
`bin/typo3-dev-companion` from that directory, `15.0.0-dev`, `PHP ^8.5`, no
extensions and no sites; `## Breaking Changes` and `## Changelog Files` at 100%
for `breaking change`; and *the summary line is 68 characters long* on the
patch's own subject. The credit is not misplaced here, which is the third corpus
in which that was worth checking and the first where it held.

The boundary is **what a topic is asked for in**. Everything the strength
credits was asked for as a topic or as an artifact: a directory, a two-word
subject, a commit message. What its last clause reports is the same corpus asked
in a sentence, and the cost side is from the same session — `115109`, archived,
whose compound query `D-ANS-029` measured missing. The judgement of that half is
[`D-ANS-037`](../answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md),
where the mechanism is measured: two words naming the document drop the section
that answers below `Documents::MIN_COVERAGE` while its score stays three times
the winner's. The feedback is trimmed to that clause and stays open behind the
two cards it queues.

The third **Wrong if** is the one this bears on, from the other side. A closed
strength left nothing to point at; this entry has now been cited by five
judgements and read as the reason to check a clause that a boundary reading
would have closed unread.

## Confirmed on 2026-08-03

The **Since then** says a quotation is evidence about the file rather than about
the session, and the section above says a strength's quotation dates a file
without verifying it. Both assume there is a sentence to check.
`feedback/2026-08-03-144316` is the first strength that praises an **absence**,
and an absence dates nothing.

It is the fullest strength in the corpus — five behaviours of a core patch
review of `9f6c6eb9093` (#110359), each with the finding it produced, from
`/home/benji/projects/typo3-cms`. Four of the five reproduce in its own words,
re-run on 2026-08-03 through this package from that directory:

- `typo3_forge_lookup` on 110359 answers `Under Review`, target version 15.0, a
  `## Reported` heading with nothing under it, and the one automated Gerrit
  note. The empty description is still rendered as an empty description.
- `typo3_gerrit_lookup` answers in both directions and names the query it ran,
  `message:110359` and `change:Id53f1068d…`. The issue direction returns exactly
  one change, so no part 3 has been pushed.
- `typo3_changelog_lookup` with the bare query `ResourceFactory` and no filters
  returns all four entries, `Feature-72904` and `Important-107735` among them —
  the precedent and the counter-direction the session built its placement
  finding out of.
- `typo3_test_run_guide` with the seven changed paths and `targetVersion 15.0`
  narrows to php and fluid, returns all nine suites rather than the narrowed
  ones, and carries the `cglGit` worktree caveat unprompted.

The fifth is refused, and the judgement of it is
[`D-ANS-035`](../answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md).
The five matcher rows were read as closed over visibilities, which produced a
finding telling a core reviewer that a matcher cannot exist for a removed
protected method. It can, and the core enters one.

**An absence is not evidence, and a strength that praises one is the report to
check hardest.** A quoted sentence is a claim somebody wrote, so a re-run can
put it beside the file and see whether it still stands. An absence is a claim
about a boundary nobody drew: the reader supplies both the set the list is
closed over and the conclusion, and neither is in the file. So the check is not
whether the words are still there — they were, verbatim, at 100% — but whether
the enumeration is exhaustive over the axis the praise reads into it. Only the
domain answers that, which is why this one needed `.checkouts/main` and the
earlier keep-requests did not.

The order the **Since then** sets is unchanged and gains a rung: read what the
praise quotes, then what the praise implies, then what it praises the file for
**not** saying. A keep-request for an omission is a request to freeze a reading,
and the reading is the part nobody wrote down.

Two things were found that the strength does not report, and both are queued
rather than closed, because both touch `src/`.

The first is in its own item 2. The session credits the Change-Id lookup with
establishing that it was "reading the same patch set that exists on the server",
and that fact is in neither half of the answer. Re-run with `data`, the change
carries `number`, `subject`, `status`, `branch`, `project`, `updated` and `url`
— no revision, no patch-set number, no commit hash. The number it reports, patch
set 1, is in the automated Forge note the *other* call returned. That is the
fourth corpus in which a strength misplaces its credit. It is the first where
what is credited is a question the tool cannot answer as it stands: whether the
checkout is the revision under review. The card carries it.

The second is this file. Its observation is exactly 4000 characters and ends
`the shape that made this work: the skill fixed the or` — cut mid-word by
`Channel::MAX_FIELD_LENGTH`, with no marker and nothing in the tool's answer
saying so. What went with it is the one sentence naming the shape the session
credits, which is the boundary half this entry exists to read. One field in 235
recorded feedback sits on that cap, so it is rare and it is undetectable. The
same class marks a redaction `[redacted: ...]` and says out loud that a report
which was altered has to say so, and `title()` three lines above ends its own
cut in `...`. Only `text()` is silent.

The feedback is trimmed to the patch-set half and stays open behind its card,
which is rewritten from the judging card and titled after the work. The
truncation is a card of its own against `feedback/`.

The third **Wrong if** is answered again rather than tested: this run left a
guard, `KnowledgeTest::theMatcherListSaysWhatItsMissingRowsDoNotMean`, on prose
whose value the reporting session located precisely in it having no such
sentence.

## Confirmed on 2026-08-03

The first **Wrong if** fired again, in a shape none of the readings above
carries: the lever is a **request for something the answer already does**.
`feedback/2026-08-03-164818` reports a conformance audit of `EXT:guidedtour` in
`/home/benji/projects/ext-guidedtour` and credits three answers, then asks that
`typo3_extension_scope`'s `deprecatedFiles` be extended to the other file-level
predicates an extension can trip — naming `ext_tables.php`, `#109438`, which the
same call has checked since `a886a2d`.

The quotations are checked before the boundary is, re-run on 2026-08-03 through
this package from that directory:

- The `ext_emconf.php` finding comes back verbatim: the predicate naming
  `providesPackages` and the two version fields, `#108345`, and the sentence
  that a Composer installation is unaffected because the package artifact skips
  the fallback.
- `typo3_backend_module_lookup` settles all three identifiers with their owning
  package — `web_layout` and `records` at `typo3/cms-backend`,
  `media_management` at `typo3/cms-filelist`, which is the attribution the
  report says turned an identifier check into a dependency finding.
- The tag-bounded sweep reproduces, and its count is read off two calls rather
  than three: `ext:core` answers 30 of the 75 deprecations of v14 and
  `ext:backend` 19, which is the 49 the report states, while `JavaScript`
  answers 6 that are almost all already among them.

The lever is in the suggestion, and what it is evidence about is the answer
rather than the session. Beside the ask stands *the audited package happened not
to ship one, and confirming that took a separate look* — a confirmation the same
answer had already given in its `Registration files:` line. Both are one wording
defect, judged onto
[`D-ANS-009`](../answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-it.md),
whose **Wrong if** is where it lands: the rendered block says *these two entries
whole* on an answer that rendered one, and names neither file, so what was
checked and not found is readable in the schema description and nowhere a caller
looking at the text can see. A strength that proposes a feature is therefore
read against the feature: where it already exists, the ask is evidence that the
answer does not say so.

The boundary is **what an answer qualifies about what it found, against what it
says about what it did not find**. Everything this strength credits is a
qualification travelling with a hit: a predicate with the condition under which
its cost is zero, a module with the package that declares it, a tag list wide
enough to bound the next call. The costs from the same debrief are one shape,
and it is the other side — an answer's silence read as a finding. Five files,
one session, one directory: `164651` reports a `Classes/` subdirectory that is
absent rather than reported, `164805` that a changelog sweep answers what
changed and not what still holds, `164710` a hint naming one of two supported
entry points, `164734` six substring hits that read as "the manual has nothing",
`164749` a forked core file with nothing to say which file it forks. What each
one costs is the reading the caller supplies where the answer stops.

One keep-request rested on nothing and is held now:
`PackageSourcesTest::theTagListTravelsWithAHitAsWellAsWithAMiss`. `tags` is not
among the keys `ChangelogLookup::outputSchema()` requires and the hit path was
in no assertion, so the list a sweep's second and third call are read off could
have been dropped without a failure. The other two are held already — the
`deprecatedFiles` shape by `D-ANS-009`'s three tests, and the module attribution
by `extension` being a required key of the answer.

The feedback is trimmed to the wording half and stays open behind the card that
carries it. No statement about TYPO3 was established here: the re-runs read this
server's answers, and the covered set was read in
`Extension::deprecatedFiles()`.

## Confirmed on 2026-08-09

The reading held a sixth time, on the first strength whose keep-request names
sentences a second session asked to keep as well. `feedback/2026-08-08-224455`
reviewed Gerrit change 95179 in a git worktree of a core checkout,
`/home/benji/projects/typo3-cms`, and credits four answers plus a fifth it calls
worth keeping. Its suggestion is *nothing to change*, and it names the
load-bearing sentences so a later rewrite can recognise what it is dropping.

The quotations are checked before the boundary is. Re-run on 2026-08-09 through
`bin/typo3-dev-companion` from that directory, with the feedback's own
arguments, and all five reproduce:

- `typo3_test_run_guide` prints the preconditions above the suites, with both
  worktree sentences and the literal
  `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`.
  Its `cglGit` entry carries the caveat: git fails, the list is empty, and the
  suite reports SUCCESS having read nothing.
- `typo3_rule_lookup` with the session's compound query returns
  `Changelog Files` at 80% and `Release Targets` at 66%. All three sides of the
  changelog answer are in the first — `A BUGFIX owes none`, `Important` as the
  last resort and the only one of the four an LTS release may carry, and
  demanding an entry of a `BUGFIX` that removes nothing public as a review
  defect of its own.
- The second section refuses the branch list in the checkout, says
  `git branch -r` reaches back to `TYPO3_3-6`, and calls a branch out of regular
  support an error.
- `typo3_forge_lookup` on 81619 answers the 2017 report whole, the sentence the
  finding turned on included: the override is needed *if a result of TypoScript
  condition should be overriden*.
- `typo3_hint_lookup` on the two changed paths returns `core-tests` with the
  paragraph on where output expectations hide.

The first **Wrong if** did not fire. Every counterfactual in the file is an
action the answer pre-empted — `composerInstall` run first, `cgl -n` instead of
`cglGit`, 12.4 left out as correctly excluded ELTS — and the only thing it did
besides calling a tool is probing the TypoScript-condition path to confirm a
finding. `2026-07-31-194823` is where a corroboration was established not to be
the *did instead* the ladder is walked from.

The keep-request is answered against the file as it is now, and two of the five
answers rested on nobody rewriting them. The preconditions were held for the
symptom string, the container and `composerInstall`, and not for the worktree
that is the checkout nobody expects to be missing `vendor/`. `Changelog Files`
was held for the obligation and the last resort, and not for the exception the
session says stopped it writing an `Important` entry to be safe.
`Release Targets` was held by nothing at all: the ELTS error exists as
behaviour, with `unmaintained-release-line` under test, while the sentence that
refuses `git branch -r` was in no assertion. The other two were guarded already
— `KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold` for the
`cglGit` caveat, and `HintsTest` for the expectations paragraph.

Three assertions and one test now name them:
`KnowledgeTest::theReleaseTargetsAnswerRefusesTheBranchListInTheCheckout`, and
the sentences added to `theInvocationNotesNameTheInstallAFreshCheckoutOwes` and
`aQueryForTheChangelogObligationReachesTheSectionThatStatesIt`. The second
session is `feedback/2026-08-08-224426`, from a triage rather than a review,
which names the same two sentences and had run `git branch -r` one turn before
the answer arrived. Its card is in hand elsewhere and this run neither closes it
nor takes it over; what the guards cover is the request both make.

The boundary is **a rule against an instance, and what an instance is keyed
by**. Everything this strength credits is a rule: a precondition of the
environment, an obligation with the counter-rule beside it, a release-target
reading with the source it refuses. Each is reached from the domain the task is
already in — the changed paths, the words of the question — and arrives whole
without being asked for precisely. The fourth is an instance and arrives whole
too, because the caller held its number. The cost side is from the same debrief:
`2026-08-08-224429` wanted an instance it could only describe by shape, a
`BUGFIX` that changed 0-is-empty semantics and got an `Important` entry on an
LTS branch. `typo3_changelog_lookup` answered `matchCount 0` for
`stdWrap override`, and `ls` over `13.4.x` produced the entry. So an instance is
reachable by an identifier the caller holds or by the words written in it, and a
caller holding neither has nothing to ask with.

What would show that reading wrong is where the changelog card lands: a scoring
defect in the matcher rather than a key nobody can supply. That card is in hand
elsewhere, and naming its answer from here is the copy-down judging.md warns
about.

No statement about TYPO3 was established. The re-runs read this server's own
answers, the guarded sentences were already in `knowledge/`, and what was added
is that they stay. The feedback is closed by this commit and nothing is queued.
The third **Wrong if** is what it bears on: what a closed strength leaves behind
here is four assertions rather than a commit message.

## Confirmed on 2026-08-09

`feedback/2026-08-08-224426` is a keep-request for four answers, from a triage
and fix of Forge #58705 in `/home/benji/projects/typo3-cms`. The first **Wrong
if** did not fire. It names no workaround and no fact established elsewhere, and
the one command it reports running — `git branch -r`, one turn before the answer
arrived — is an action an answer stopped rather than one the session had to
perform.

The quotations are checked before the boundary is, re-run on 2026-08-09 through
`bin/typo3-dev-companion` from that directory:

- `typo3_test_run_guide` with the two `GifBuilder` paths at `targetVersion 15`
  opens on **Before a suite can run**, above the suites rather than below them,
  with both preconditions verbatim: the container, and the `vendor/` and `bin/`
  of the directory the script is started from. Every command it then prints
  carries `CI=true`.
- `typo3_rule_lookup` with the feedback's own compound query returns
  `Changelog Files` at 85% of the query terms and `Release Targets` at 51%, both
  whole. The changelog sentence and the `git branch -r` one come back word for
  word.
- `typo3_forge_lookup` on 82228 answers `## Changes on review.typo3.org (1)`,
  naming change 53819 at patch set 3, above three comments that are all Gerrit
  pings. The description ends at the reporter's scenario, so it is the `reviews`
  field that rules the issue out, which is what the report credits it with.

**The credit for the fourth is misplaced, and this is the first time that could
be settled against the file itself.** Both sentences the session attributes to
`references/base.md` are in `skills/typo3-core-issue-triage/SKILL.md`. The copy
installed in that checkout is byte-identical to `skills/base.md` and carries
neither, and neither does the `references/checklist.md` beside it. The opening
section of this entry could not say which `base.md` a session had read, because
both installed copies had been rewritten after the report; here the published
copy is unchanged and the comparison is exact. That is the fifth corpus in which
a strength misplaces its credit, and none of the five was wrong about what it
received.

The boundary is **what a test can reach by name against what it can only reach
by quoting**. The four split on that line exactly. The preconditions are a block
of `knowledge/test-suite-hints.json` and `reviews` is a key
`ForgeLookup::outputSchema()` requires, so both were held already —
`KnowledgeTest::theInvocationNotesNameTheInstallAFreshCheckoutOwes`,
`HintsTest::theTargetedInvocationSurvivesWithTheThreeThingsThatMakeItRunnable`
and `ForgeTest` on the extraction. The other two are sentences inside a document
and a skill, reachable only as strings, and both rested on nobody rewriting the
file.

Two guards were written, which is the keep-request in the only form this
repository has for one.
`KnowledgeTest::theMovesTheCommitRulesStopAreStillStatedAsWellAsTheRules` holds
the clause refusing the demand and the one refusing the branch list; the
obligation beside them was held twice over already, and it is the refusing half
that a summarising rewrite drops.
`SkillTest::aTriageIsHeldToWhatItsMeasurementsActuallyMeasured` holds the
triage's three measurement rules and the sentence that sends a reproduction to
be shown red. Five reports credit that block — this one and `2026-08-05-033954`,
`2026-08-07-065401`, `2026-08-07-130037` and `2026-08-07-233418` in the archive
— and its third bullet is itself the answer to the last of those, so a rewrite
could have taken out four sentences the corpus asks for without a failure. What
that costs is now the fifth **Wrong if** above.

Two of these sentences are asked for twice in one debrief.
`feedback/2026-08-08-224455` reviewed Gerrit 95179 in a worktree of the same
checkout and names the preconditions block and the `Changelog Files` section
again, from a task that is not this one. It is in hand elsewhere; the guards
here cover the sentences both name, and its own halves — the `cglGit` worktree
note, the `core-tests` hint paragraph, the Forge issue that produced a finding —
belong to its judgement.

The feedback is closed by this commit and nothing is queued. No statement about
TYPO3 was established here: the re-runs read this server's answers, and the
attribution was settled by comparing two files on this machine.

## Confirmed on 2026-08-14

`feedback/2026-08-13-214838` reviewed Gerrit change 93319, a Playwright e2e
diff, in `/home/benji/projects/typo3-cms`. It is a keep-request for three
answers, and the first **Wrong if** did not fire: every counterfactual in it is
an action an answer pre-empted — the `git fetch origin refs/changes/…` it did
not run, the `typo3_extension_describe` it did not need, the `typo3_hint_lookup`
a coverage sentence had already answered.

The quotations are checked before the boundary is, and the one about TYPO3 is
checked in `.checkouts/` rather than taken on trust. The reporting checkout is
worked by another session, so the re-run was made from this repository's own
`.checkouts/main` on 2026-08-14, with the feedback's own arguments:

- `typo3_label_lookup` with query `newPage` and extension `backend` answers six
  labels across two resources, which is the report's own count. The two the
  review turned on are `backend.pages_new:newPage` at "Page" and
  `backend.layout:newPage` at "Create new page", byte for byte in
  `.checkouts/14.3` as in `.checkouts/main`. No console runs here — that
  installation requires PHP 8.5 and this machine has 8.3 — so the answer came
  from the packages, and the resource travelled with every hit on that path too.
- `typo3_rule_lookup` with documentId `core/contribution/gerrit-workflow` hands
  the document over as one section, the fetch paragraph in it: the ref is on
  Gerrit and not on GitHub, and what to fetch from is `remote.origin.pushurl`.
- `typo3_task_guide` states its coverage in `omittedHints`, a required key of
  its own schema, with `HINTS_COMPLETE` saying the same thing in the text.

**One implication is refused, and it is the one the strength argues from.** It
credits the skill with sending it to read the document whole rather than search
it, "the section I needed was not the one my words would have matched". Both
*fetch a gerrit change into this checkout* and *fetch patch set refs/changes
remote* return **Fetch a Change Into This Checkout** first. What the instruction
bought is one call instead of a search and a read, not a miss avoided — and the
same corpus asked in four subjects at once did miss, which is the cost side
below.

The third keep-request is the one that asks for something the answer already
has. The report quotes `"omittedHints": []` and then asks for a machine-readable
equivalent of the sentence beside it. It read the field and did not recognise it
as the answer to its own ask, which is the shape `2026-08-03-164818` established
and no rewrite here would change.

The boundary is **what this corpus answers to a handle the caller holds against
what it answers to a description of what is wanted**. Every answer this strength
credits was asked for by a handle: a trans-unit id read out of the core source,
a documentId the skill named, a Forge number off the commit message, the changed
paths. The cost side is from the same session, seconds earlier —
`feedback/2026-08-13-214857`, whose four-subject query returned `matchCount: 0`
and was recovered by picking a documentId out of the miss listing. So the
session that paid nothing for four handles paid a round trip for the one call it
phrased as a description. That card is in hand elsewhere and naming its answer
from here is the copy-down judging.rst warns about.

Two keep-requests rested on nobody rewriting the file, and both are held now.
The resource on a label hit was in no assertion in either half of the answer,
and it is what separates two labels of one key in one extension —
`LabelSearchTest::twoLabelsOfOneKeyAreToldApartByTheResourceEachIsIn` holds the
console path in the text and in the data, and the packages path is asserted
where that fallback already was. The fetch asymmetry was held in
`typo3_gerrit_lookup`'s answer by `GerritTest` and nowhere in the document the
skill sends a session to;
`KnowledgeTest::theFetchDirectionNamesTheRemoteTheChangeRefIsOn` names it beside
the write direction that was guarded already. The third is guarded twice over by
`R-GUI-009` and needed nothing.

One thing was found and left. `resource` is not among the keys
`LabelLookup::outputSchema()` requires, which is where `tags` stood when
`2026-08-03-164818` was judged. Requiring it would touch a declared schema,
which judging.rst reviews rather than improvises, and it would promise less than
it looks: the console path renders `''` where an item carries no resource, and
an empty string satisfies a required key. The assertion is what holds the
sentence, so the guard came first here as it did there.

The feedback is closed by this commit and nothing is queued. The one statement
about TYPO3 in it was verified in both covered majors and written into no
`knowledge/` file, because the corpus already answers it from the installation.

## Confirmed on 2026-08-18

The 2026-08-03 section says an absence is a claim about a boundary nobody drew,
and that the reader supplies both the set the praise is closed over and the
conclusion. `feedback/2026-08-17-212600` is the strength where the file supplied
the set itself, and that is what made the absence checkable.

It measured the two skills that carried a build in
`/home/benji/projects/site-demo` and praises them for what they do not say. The
praise is not the reader's reading of an omission: it quotes the sentence that
declares it,
`never retain layout keys, environment defaults, command options or package names`.
So the axis is written down, in four kinds, and the check is whether each of
them holds and what says so. The sentence reproduces verbatim, and of the four,
none was asserted — `SkillTest` held the routing phrase, a version number and
backend markup. Two have a shape a file can be held to and both hold today; they
are guarded now.

The first **Wrong if** did not fire. Every counterfactual in the file is about a
different skill's size rather than about a step this one had to take instead,
and the failures it reports against these two are filed separately, judged
elsewhere, and already worked into the files.

The boundary is **what a workflow states against what it names a call for**.
Everything the strength credits is on the second side: an order, an obligation,
a lookup at the step that needs it. So is everything the same debrief reports as
a cost, which is why answering six of them added 1,949 characters and no fact
that can go stale. The judgement is
[`D-SKL-052`](../task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md),
where the size the report asks be watched is read as what that rule leaves. The
feedback is closed by this commit and nothing is queued.

## Confirmed on 2026-08-18

The 2026-08-03 section says the check on a praised absence is not whether the
words are still there but whether the enumeration is exhaustive over the axis
the praise reads into it, and that only the domain answers that.
`feedback/2026-08-18-070515` is the first strength that was **handed** that axis
rather than supplying it: the sentence it credits states its own test, the
session ran the test, and the branch it took was the one the test decided.

It booted a clone of the `t3g/blog` extension repository in
`/home/benji/projects/blog` on four calls and credits five things. The first
**Wrong if** did not fire. Every counterfactual in the file is an action an
answer pre-empted — the `ddev restart` it would not have run, the wrong
distribution branch it would have inherited, the database connection it was
about to hand-write, the install-tool password it says it would not have
reported. The one command it ran beside a call,
`find .build/vendor -maxdepth 3 -type d -name Initialisation`, is the test the
answer handed it, which is not the *did instead* the ladder is walked from —
`2026-07-31-194823`. Its second `typo3_project_describe` is a repeated call, and
what caused it is `feedback/2026-08-18-070333`, filed 102 seconds earlier with a
card of its own.

The quotations are checked before the boundary is. Re-run on 2026-08-18 through
`bin/typo3-dev-companion` with the feedback's own arguments —
`typo3_hint_lookup` with `id=installation-boot`, then `typo3_task_guide` with
`changeType=operations` and `paths=[".ddev/config.yaml","composer.json","blog"]`
— and all five come back verbatim. Four of them arrive in that one
`typo3_task_guide` answer, whose `intents` are `installation-setup` and
`installation-operations`, so the brief the report calls "the checklist" is both
of their checklists in one call.

The praised absence is exhaustive, and this is the first keep-request whose axis
could be read straight off the core. `SetupService::getAvailableDistributions()`
walks `getAvailablePackages()` and skips every package that has neither
`Initialisation/data.xml` nor `Initialisation/data.t3d` in its package path.
There is no second predicate — not the Composer type, not a manifest key — so
*shipping one of the two files is the whole test* is closed over the axis the
session read into it. The method is byte-identical in `.checkouts/14.3` and
`.checkouts/main` but for a blank line, and `SetupCommand` honours
`--create-site` in both wherever `$distributions['active'] === []`.

The boundary is **what an answer hands the caller to check against what it asks
the caller to believe**. Everything this strength credits is on the first side,
and each is a different kind of handle: a file path the environment reads
(`Typo3Version.php`), a file whose presence decides a branch
(`Initialisation/data.xml`), an exception number that tells two refusals apart
(1669747685 against 1669747200), a settings key a value silently becomes
(`BE/installToolPassword`), a command that recovers what is not stored
reversibly (`backend:resetpassword`). None of them is a conclusion; each is
something the session could put to its own machine.

The cost side is the same debrief, seven files in three minutes, and every one
of them is a conclusion with no test attached. `2026-08-18-070632` is the exact
inverse of the strength's item 2: `--create-site` carries `until: 13`, which
reads as removed at v14, and the clause that decides it — *read only where no
distribution is active yet* — sits mid-paragraph in the statement beside it, so
the session reconciled two statements and then verified empirically.
`2026-08-18-070423` names the variables `typo3 setup` reads and not the
invocation that carries them into the container, and two round trips went on
`ddev exec -e` and `--raw=false`. `2026-08-18-070358` answers `scope: uncertain`
for an extension key the parameter documentation says counts as a path.
`2026-08-18-070333` promises in a tool description that the answer holds on a
fresh clone, which is the one state it does not. Four cards, four conclusions
the caller had no way to check; five sentences, five tests it could run. One
session reported both directions without seeing them as one line.

What the report proposes generalising — *state the decidable test alongside the
conclusion* — is the same reading, and it is not written as a requirement here.
One debrief is where the shape was found, `070632` is the single instance of the
absence and has a card of its own, and naming its answer from here is the
copy-down judging.rst warns about. What would establish it is a second debrief
reporting the same split from a different task shape.

Four of the five keep-requests rested on nobody rewriting the file. The
additional.php paragraph was held for the timing and not for the path the
detection reads, which is the half the session acted on;
`aBootBriefCarriesTheTestThatDecidesABranchAndNotOnlyItsVerdict` names that
path, the `Initialisation/data.xml` clause and the sentence that leaves the file
to DDEV, all three in the brief the report received. The four password lines
were in no assertion at all —
`theAdminPasswordIsAnsweredWithWhatItAlsoBecomesAndHowItIsRecovered` holds the
report obligation, the generated password, the install-tool half and the reset.
The two exception numbers and what `--force` moves were held by their wording
alone, and the distribution predicate by neither, so both are added to
`anUnattendedInstallIsAnsweredWithWhatTheCommandRefuses`. The fifth, the
`Typo3Version.php` sentence in the hint that owns it, was guarded already by
`theDdevSettingsAnswerSaysWhenThatFileIsWritten`.

One statement about TYPO3 was checked and none was established: the predicate
was read in `.checkouts/`, and everything else is this server's own answers
re-run. The feedback is closed by this commit and nothing is queued. What it
asks be kept is now asserted rather than left standing, which is the third
**Wrong if** asked of a closed strength.

## Confirmed on 2026-08-18

`feedback/2026-08-18-074305` is the first strength that prices a construction
this repository chose knowing it would cost something, and the price it reports
is the one the choice assumed.

It credits three answers from a session that repaired a frontend 404 on a DDEV
installation of `t3g/blog` in `/home/benji/projects/blog` and then wrote three
commits: `typo3_project_describe` as the first call, the `operations` brief, and
`typo3_commit_message_guide`. The first **Wrong if** did not fire. Every
counterfactual in it is an action an answer pre-empted — the generated
`config/system/additional.php` it did not touch, the TypoScript and caches it
did not chase, the round trip it did not spend guessing a commit convention —
and the two commands it ran, it ran *because* of an answer rather than instead
of one.

The quotations are checked before the boundary is. The reporting checkout is
another session's work, so the re-runs were made on 2026-08-18 from this
worktree, with the feedback's own arguments, and from `.checkouts/14.3` where
the answer needs an installation:

- `typo3_task_guide` with the feedback's task, `changeType=operations`,
  `targetVersion=14.3` and its three paths answers `installation-operations`
  strong and `installation-setup` weak. Both credited items come back: the
  `additional.php` ownership paragraph in the hints and again as a checklist
  item, and the seeded installation answering 404 at its own root because
  `Import::processSiteConfigurations()` overwrote the base.
- `typo3_commit_message_guide` with `workflow=project` returns the wrapped
  message and one check, `summary-length-preferred`, which is the code the
  report names.
- `typo3_project_describe` from `.checkouts/14.3` renders the marking and the
  paragraph that explains it, and classifies that checkout's four `gerrit:setup`
  scripts as `unknown`.

**The credit is not misplaced, which is worth saying after five corpora in which
it was.** The report says it ran `composer test:php:lint` and `composer phpstan`
unasked because both were marked `check`, and those two declarations are the two
`ProjectTest`'s own asserted table answers `RUNS_AS_CHECK` for. Its count is
exact where it can be checked and stale by one where it cannot: six guarded
items reproduce as six, and the seventeen it counted are eighteen, because
`94765545` added the fresh-instance item to `installation-operations` five and a
half hours after the report — on the unguarded side.

The caveat is the whole of what this strength adds, and it is the report's own
qualification of what it is praising: six of the items were guarded
`only if the task is setting an installation up rather than working on the code in one`,
which did not hold, "so the guard worked; it is just a lot of answer to read
past". `TaskIntents::confirmed()` never promotes a weak match and states the
condition instead, and `ScopeTest`'s
`aCoreTaskThatNamesNeitherAPathNorGerritKeepsTheSubmissionRules` calls that cost
"a prefix rather than a lookup" over two items. This is the same price paid at
six items in eighteen, by a session that read them, skipped them and calls the
prefix worth keeping. So it prices `D-SKL-051`'s weak `development installation`
rather than contradicting it, and there is nothing to queue: its second **Wrong
if** is about a weak match arriving alone, and here `installation-operations`
matched strongly and carried `typo3-development-installation` in.

The boundary is **what a session can settle when the answer arrives against what
it would have to be reminded of later**. Everything credited is on the first
side: the sites and the command marking arrived at orientation and were acted on
in the same minute, the `additional.php` paragraph and the base item answered
the question the session was holding, the length check answered a message it had
just written. So are the six guarded items, from the other end — their condition
is one the reader settles on arrival, which is why reading past them costs a
prefix. The cost side is the same debrief, and it is everything whose condition
could only become true later: `074226`'s guides list, delivered at orientation
and wanted three user turns afterwards; `074245`'s hand-off, read once before
the work that triggers it existed; `074327`'s five-step order, walked against
the task as first phrased and never re-raised when it became a patch. This
server answers a call and never sees the session again. Each of the three has a
card of its own, and naming what fills that half from here is the copy-down
judging.rst warns about.

Two keep-requests rested on nobody rewriting the file, and both are held now.
The marking was guarded twice over already —
`ProjectTest::aDeclaredCommandSaysWhetherRunningItChangesTheSources` holds the
sentence the report acts on, and `SkillTest` the three values — but nothing held
the guard on the setup items for the case that produced this report:
`theSetupItemsArriveUnderTheirGuardWhereTheInstallationAlreadyExists` names the
confidences, the prefix on every one of the six, and that the password
instruction reaches no other item. The other is the site line the diagnosis
turned on. `base` and `rootPageId` are required keys of
`ProjectDescribe::outputSchema()`, so the data was held and the sentence
rendering them was in no assertion —
`ProjectTest::everySiteIsNamedWithTheBaseAndTheRootPageItCarries` builds the two
sites `074200` describes, one on a host and one on `/`, and reads both back out
of the text.

No statement about TYPO3 was established: the re-runs read this server's own
answers, and the item that made seventeen eighteen was read in this repository's
own history. The feedback is closed by this commit and nothing is queued.

## Confirmed on 2026-08-18

The 2026-08-03 section says an exclusion is used through its `instead`, and that
its `why` is read once and believed. `feedback/2026-08-18-080743` is the
strength where the caller did not use the `instead` either. It had read the
class, on both majors, before the answer arrived.

It is the debrief half of a session that fixed the `[blog.isPost()]` TypoScript
conditions on TYPO3 v14 in `/home/benji/projects/blog` and called this server no
times while doing it. `typo3_server_scope`, called afterwards, agreed with it:
the *PHP source as code* exclusion describes what the session consisted of. The
cost half is `feedback/2026-08-18-080710`, filed 33 seconds earlier, which is on
the board with a card of its own.

The first **Wrong if** did not fire. Reading the class is what the exclusion
prescribes, so it is compliance rather than the *did instead* the ladder is
walked from — `2026-07-31-194823` is where a corroboration was established not
to be one. Every other counterfactual in the file belongs to a sibling: the
session that called nothing to `080710`, the guides nobody listed to the note
named below.

The quotations are checked before the boundary is. The reporting project is
another session's checkout, so the re-run was made on 2026-08-18 through
`bin/typo3-dev-companion` from this repository's own `.checkouts/14.3`:

- The exclusion reproduces verbatim, `Instead: Read the class` included, and the
  three tools it hands the neighbouring questions to are registered.
- Every covered topic carries its `Tools:` and `Source:` line, which is what let
  the session tell a Bash detour with a tool behind it from one without.
- The `answersFrom` block distinguishes the five sources the report names:
  installation with 8 tools, packages 8, knowledge 12, network 4, checkout 2.
- The installation half reproduces in shape and not in its values. This checkout
  answers `core-checkout` with a console via php at 8.3.23; the
  `composer-project` and the ddev console at PHP 8.2 are that machine's.

**The praised sentence is true, which the 2026-08-01 section requires be checked
rather than assumed.** Nothing here reads a class for what it declares.
`Extension::declarationsIn()` tokenises the registration files an extension
ships and takes TCA table names, content elements, plugin signatures and
FlexForm data structures out of them. `PhpArray` and `FluidNamespaces` tokenise
a configuration file for its keys, and `Instance::typo3Version()` matches one
class constant. All of it is a registration or metadata, and none of it is a
signature or an annotation.

The boundary is **what an orientation answer is worth to a caller that has
already decided against what it is worth to one that has not**. Everything this
strength credits is a confirmation: an exclusion that agreed, a discovery that
matched what the session had worked out from `.ddev` and `composer.json`, a
per-topic attribution that made the debrief writable. None of it changed an
action, because the call came after the work. The same answer carries the
routing line that would have changed one — *starting work on a TYPO3 major you
have not built on recently … → typo3_changelog_lookup* — which the cost half
names as the one call it would make next time. One document, one session, and
what separates the two halves is when it was read.

The corpus repeats that split one hint deeper. The report ends on the two
failures the session did have, both in Bash, one of them an unquoted
`--filter A|B` whose pipe the shell ate inside `ddev exec`.
`knowledge/hints/configuration.json` carries that trap and the `bash -c` form
that survives it. So the answer to the one thing that went wrong was written
here, and a session that calls nothing reaches no delivery.

The suggestion is refused, and it is the shape `2026-08-03-164818` established:
an ask for something the answer already does. It asks that covers entries gain
an explicit *and not X* clause of the exclusions' firmness. On 2026-08-18, 26 of
the 28 carry one — *it says nothing about what parseFunc does to a snippet*, *it
runs nothing and starts no installation*, *a miss says the name is not a system
extension there, never that it does not exist* — and the two that do not are the
two shortest topics. What differs is the form: a covered topic renders as one
line of `depth` with the clause at its tail, an exclusion as a heading of its
own with an `Instead:` under it. Giving the clause that form states a boundary
in a second place, and `R-SCO-008` makes `doesNotCover` the exhaustive one. What
a boundary nobody tests costs is what the 2026-08-01 section priced. A second
session reporting it read a covered topic and mistook its limit is what would
change this.

The keep-request is answered against the file as it is now, and it names two
things. The `answersFrom` split is held already:
`SourceTest::theOrientationAnswerGroupsEveryOfferedTool` asserts that every
offered tool stands under a source and that each entry's meaning is the enum's,
and a case cannot be dropped without the tool definitions failing to resolve.
The exclusion rested on nobody rewriting the file. `SkillTest` holds
`typo3-core-patch-review` restating it and nothing held the entry it restates,
so `ScopeTest::theExclusionForPhpSourceKeepsTheQualificationThatMakesItExact`
names the `@internal` half of the topic, the `instead` the caller acts on, the
three tools beside it, and the qualification a summarising rewrite drops first —
*never for a signature or an annotation*, which is what the registration reads
above would otherwise falsify.

The guides half is the third arrival on one line and is nobody's to take over
here. `feedback/2026-08-18-074226` is archived, the instructions have named
those resources since `c62ecbcf` on 2026-08-04, and `feedback/2026-08-18-113425`
reports from another project that naming them without listing them is not
enough. Its card carries that.

The feedback is closed by this commit and nothing is queued. No statement about
TYPO3 was established: the re-run read this server's own answers, and what the
exclusion claims about this server was settled against `src/`.

## Confirmed on 2026-08-21

The 2026-08-03 section says a strength that proposes a feature is read against
the feature, and that an ask for something already there is evidence the answer
does not say so. `feedback/2026-08-19-094315` makes that same ask about the same
field, and this time the feature is genuinely absent. The lever is elsewhere,
and it is in one of the four saves the report counts as a save.

It audited `t3g/blog` before an official v14 release, in
`/home/benji/projects/blog`, and credits four answers plus two failure modes it
would not change. The reporting checkout is another session's work, so the three
hint calls were re-run on 2026-08-21 through `bin/typo3-dev-companion` from this
repository's own `.checkouts/14.3`, with the feedback's own arguments:

- `typo3_hint_lookup id=typoscript-conditions targetVersion=14` returns every
  sentence the report acts on verbatim: the variable list with `page` in it,
  `AfterPageAndLanguageIsResolvedEvent`, and the clause refusing
  `$GLOBALS['TYPO3_REQUEST']` because conditions are matched while the frontend
  request handler has not yet assigned it.
- `typo3_hint_lookup id=extbase-plugin-registration targetVersion=14` returns
  the constraint word for word.
- The paths-and-task call returns `security-sinks` first, with both directions
  the report used — the sink claim, and the hop that hands a value on.
- `typo3_extension_describe extension=blog` was not re-run. It answers from that
  installation and no extension here is one it would answer for; the
  `deprecatedFiles` half reproduced on 2026-08-03 in the section above.

**The first Wrong if fired, in the save the report is proudest of.** Item 3 is
not the corroboration of a right answer that `2026-07-31-194823` established is
outside the ladder. The session says the hint alone would have produced a wrong
finding. It states the constraint on the literal `"CType"` and says anything
else throws; the extension passes
`ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT` everywhere; reading the core is
what told the session those are one thing. What the report calls a pair is a
statement plus the round trip the statement costs.

Step 4, wording. Not 1a, because the constraint is here and it is correct; not
step 2, because it was delivered by id; not step 3, because the lookup fired.
The literal is what the exception quotes —
`Fifth parameter $pluginType has to be omitted or set to "CType"` — and
`PLUGIN_TYPE_CONTENT_ELEMENT` is `'CType'` in `.checkouts/12.4`, `13.4`, `14.3`
and `main`. The hint gains that, and the half beside it that a dual-major
package needs: `PLUGIN_TYPE_PLUGIN` was deprecated at 13.4 by #105076 and
removed at 14.0 by #105377, so a call still passing it is an undefined-constant
fatal rather than the throw the hint describes. The same line is valid on one
declared major and fatal on the other, which is what this audit spent its
session on.

That is closed here rather than queued, against the rule in judging.rst that
sends a TYPO3 lookup to a todo. The reason that rule gives is that the judging
run has read nothing but this repository. `.checkouts/` is step 2 of the
ladder's own cost table and is owed to any feedback claiming something about
TYPO3, which this one does in as many words — *I checked the constant in the
installed core*. Having paid it, the statement is verified on both sides of its
boundary, which is what writing into `knowledge/` requires. Nothing in `src/`,
no schema and no skill moved.

The boundary is **what an answer states about the code as it stands against what
it states about the version that produced it**. Everything this strength credits
is the first kind: a file that is there with the condition beside it, the
variables a matcher was initialised with, the argument a method accepts, the
place a value leaves PHP. Each is a property of the artifact in front of the
session, and each is testable there. The cost side is the same debrief, and all
four of its audit halves ask about history — `2026-08-19-094221` since when a
migration target exists, `094403` what changed at a major tag by tag, `094528`
what has already been published to the TER, `094341` what has already been fixed
on a branch nobody merged. One session filed both directions within three
minutes.

That is the axis the opening section of this entry read from the other side. It
found the changelog precise about what **changed** at a version and silent about
what still **holds** at one, and credited the change half. Here the state half
is what is credited and the history half is what everything cost. So the axis
reproduces across two corpora and which side reads as the strength is decided by
which tool the session reached, not by which side is better served.

The keep-requests are answered against the files as they are now, and two of the
four rested on nobody rewriting them. `security-sinks` was in no assertion at
all — `aSecurityFindingIsHeldToItsSinkAndToWhatIsOnlyOnThePath` now holds the
sink claim, the unverified-report alternative, and the two clauses the report's
second direction lives in, which is the half a summarising rewrite drops. The
plugin-type statement was in no assertion either and is held by the test named
below. The conditions hint was guarded for the variable list, the event and the
silent failure, and not for the clause that refuses the obvious substitution;
that clause is added to
`whichGlobalsAConditionCanReadIsBoundToTheMajorThatPopulatesThem`. The two
failure modes the report would not change are guarded already —
`PackageSourcesTest::theTagListTravelsWithAHitAsWellAsWithAMiss` for the tag
list, and `KnowledgeTest::anUnknownDocumentIdNamesTheOnesThereAre` for the miss
that lists every document id.

`aPluginTypeArgumentIsAnsweredWithTheConstantThatSatisfiesIt` holds what was
added, on both sides of the boundary: the constant on every covered major, and
the removed one named at 14 and absent at 13.

The corpus repeats the delivery point one day back.
`feedback/archive/2026-08-18-080743` is a session that fixed these same
`[blog.isPost()]` conditions on v14, in this same project, and called this
server no times. The hint this report calls the most useful single answer of its
session is the one that session never asked for, and the code was identical.
Nothing changes here on that: it is the same statement reaching one session and
not the other, which is what a delivery finding already is.

**The ask is queued, and what makes it different from the one refused in the
2026-08-03 section is that no answer covers it.** `Extension::deprecatedFiles()`
carries `ext_tables.php` and `ext_emconf.php` and no third file, and the
criterion for a third is written in its own docblock: a deprecation whose
predicate is the file being there, which no changelog sweep of an extension's
vocabulary reaches. Two sessions have now asked for the extension and neither
named an instance, so the question is whether the set is bigger than two. It is
— `.checkouts/main` carries
`Documentation/Changelog/12.4/Deprecation-98093-Ext_iconAsExtensionIconFileLocation.rst`,
whose subject is `ext_icon.png`, `ext_icon.svg` and `ext_icon.gif` at the
extension root, and whose stated impact is a deprecation log entry that becomes
a silent stop. What each candidate actually says is the todo's reading and is
not named here. The feedback is trimmed to that half and stays open behind its
card, which is rewritten from the judging card and titled after the work.

The priority is `normal` rather than `low`, and what set it is the corpus: the
same field asked about by two sessions from two task shapes, and named by this
one as the highest-value answer of a whole audit.

The third **Wrong if** is what this run bears on. What the closed half leaves
behind is three assertions and a test rather than a commit message.
