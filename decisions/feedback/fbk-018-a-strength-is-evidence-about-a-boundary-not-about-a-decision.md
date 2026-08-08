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
- A keep-request is refused on a re-run and the sentence turns out to have been
  right after all, so that reading a strength against today's file lost
  something the session knew and this repository had forgotten.
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
