---
id: D-FBK-018
title: A strength is evidence about a boundary, not about a decision
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
- Two of its claims are looser than the answer. `typo3_project_describe`
  classifies six of ten declared commands as `check` or `change` and three as
  `unknown` — the phpunit suites, which the answer's own prose says it will not
  classify — rather than "every repo command"; and the platform reality it
  credits to that tool comes from `typo3_extension_describe`, whose footer
  reports that the installation was not asked because the host runs PHP 8.3.23
  against a `>= 8.4.0` requirement.
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

- ~~A positive feedback turns out to carry a lever nothing else does — praise
  that names what the session did instead. The ladder would then apply after
  all, and reading a strength as boundary-evidence only would have skipped it.~~
  Fired in **Since then** below and in four readings after it.
- The two cost cards are judged and land somewhere other than the change/state
  boundary. The pairing above would then be a reading of three files rather than
  a property of the corpus.
- ~~Strengths accumulate unread, because closing one leaves nothing anybody can
  point at afterwards. This entry and its commit are the whole record; if
  neither is cited again, the run was a cost with no return.~~ Answered by the
  readings below: each leaves a guard or a judgement on another entry rather
  than a commit message.
- ~~The line a strength's praise implies turns out not to be worth saying —
  `typo3_extension_describe` naming a missing translation on every extension
  that ships none costs more than it buys. Reading what the praise implies would
  then be right about the asymmetry and wrong about it mattering, and
  `R-PRJ-006` is what would need the sentence instead.~~ Answered on 2026-08-02:
  the line costs a word, and `Ships:` says it.
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
`2026-07-31-194825` reports the absences `typo3_extension_describe` answers as
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
from that directory: `typo3_project_describe` answers TYPO3 14.3.5, the project
extension, `main at https://site-new.ddev.site/` with set `bk2k/printworks`, and
six declared commands; `typo3_extension_describe` answers seven content elements
of which five carry a `templateName` and two do not, three icons, and three XLF
files declaring `source-language de`; `typo3_changelog_lookup` with
`type=deprecation, version=14, limit=30` answers 75 entries and shows 30, with
the `FullyScanned` and `PartiallyScanned` tags on them. The extension has grown
two tables since the report, and the answer is now read off a booted
installation, so it says more than it did rather than less.

So does the lever behind the *wrong path avoided*. `typo3_hint_lookup` with the
task *content element with inline children* returns **Registering a Content
Element**, whose hints say that a plugin is a CType like any other and that the
list_type detour is gone at v14, and that the rendering definition is
`tt_content.<CType>` on top of `lib.contentElement`. The task *Extbase plugin
registration and cHash* returns **Extbase Plugins**, **Registering a Content
Element** and **Records in the Frontend Without Extbase**. `site-sets` and
`tca-formengine` are reachable too, from a task naming those subsystems, which
the two calls behind the report did. The core confirms the statement the session
acted on: `14.0/Important-105538-ListTypeAndSubTypes.rst` in `.checkouts/main`
records the removal of the `list_type` field and of the plugin subtype with it.

**A strength is not evidence about which tool answered.** This is the second
corpus in which the credit is misplaced, and both times on the same fact. The
report has `typo3_project_describe` giving *PHP ^8.4 (actual 8.3.23)*; the tool
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
  `typo3_extension_describe` with `printworks_sitepackage` answers
  `Ships: manual none, readme none, tests Functional+Unit`.
- *read the XLF source languages* reproduces: three files below
  `Resources/Private/Language/`, each at
  `source-language de, no translations beside it`.
- *the answeredBy attribution* reproduces. That answer says `installation`;
  `typo3_project_describe`, which reads files and asks nothing, says `packages`.
- *the commands the repository actually declares, with what each does to the
  sources* reproduces by half. The six commands are there, and every one of them
  answers `runs: unknown`. All six are test suites, and
  [`R-PRJ-007`](../../requirements/project/prj-007-a-declared-command-says-whether-running-it-changes-anything.md)
  says a manifest does not cover what the project's own code writes. The
  classification the strength credits classified nothing here.

That fourth one is the mechanism the misplaced-credit reading above saw twice
and left unnamed. What is recited is the answer's own explanatory prose rather
than the answer. `typo3_project_describe` spends a paragraph on what a check is
and what a change is, ahead of the six `unknown`s, and the report hands that
paragraph back as a result it received. Its conclusion — that the repository
declares no check scripts — is right, and it is read off the list rather than
off the classification. So a recitation is evidence about the file where the
file is a rule, and evidence about nothing where the file is an answer
explaining itself.

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
and drops the text. `ProjectTest::whatAnExtensionDoesNotShipIsAnswered` asserts
the present case in both halves of the answer and the absent case in the data
alone, which is why nothing failed.

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
name — typo3_architecture_lookup was renamed to `typo3_hint_lookup` at `7553cb3`
afterwards.

That sentence was false when it was praised, and `f8be448` deleted it two days
later. `typo3_project_describe`, started in the very checkout this review was
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
`typo3_project_describe` "correctly identified the checkout as a TYPO3 core
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
[`D-SKL-005`](../task-skills/skl-005-core-contribution-earns-two-task-skills.md)'s
`typo3-core-patch-review`, which states this feedback's boundary in the working
form. The companion note it points at is `feedback/archive/2026-08-01-121847`,
from the same review and closed on 2026-08-02.

One thing was found unguarded and is guarded now, which is what the third
**Wrong if** asks a closed strength to leave behind. The narrowing this report
credits was held twice over; the invocation was held by nothing. Dropping
`targeted` from every entry of `knowledge/test-suite-hints.json` broke no test,
and `catalog:check` verifies the `-s <suite>` of a command against
`.checkouts/`, never its options.
`HintsTest::theTargetedInvocationKeepsWhatMakesItRunnable`
names them on the paths the report is about. No statement about TYPO3 was
established for it: all three are read in
`.checkouts/main/Build/Scripts/runTests.sh`, where line 6 branches on `CI`,
`shift $((OPTIND - 1))` hands what follows `--` to the tool, and `-n` sets
`CGLCHECK_DRY_RUN`, which `cgl` turns into `--dry-run --diff`.

The feedback is closed by this commit and nothing is queued.

## Confirmed on 2026-08-03

The first **Wrong if** fired again, and this time the lever is a clause the
strength appends to its own praise. `feedback/2026-08-01-115115` credits
`typo3_project_describe`, `typo3_rule_lookup` and `typo3_commit_message_guide`
from a core patch review in `/home/benji/projects/typo3-cms`, asks that all
three stay as they are, and ends: *the compound rule_lookup queries failed but
the single-term ones worked*. That names no workaround and quotes no file, so it
is neither of the two shapes above — it is the report's own qualification of
what it is praising, and it is the only sentence in the file nothing else in its
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
[`D-ANS-037`](../answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers.md),
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
`typo3_extension_describe`'s `deprecatedFiles` be extended to the other
file-level predicates an extension can trip — naming `ext_tables.php`,
`#109438`, which the same call has checked since `a886a2d`.

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
[`D-ANS-009`](../answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-the-file.md),
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
`PackageSourcesTest::theTagListTravelsWithAHit`. `tags` is not among the keys
`ChangelogLookup::outputSchema()` requires and the hit path was in no assertion,
so the list a sweep's second and third call are read off could have been dropped
without a failure. The other two are held already — the `deprecatedFiles` shape
by `D-ANS-009`'s three tests, and the module attribution by `extension` being a
required key of the answer.

The feedback is trimmed to the wording half and stays open behind the card that
carries it. No statement about TYPO3 was established here: the re-runs read this
server's answers, and the covered set was read in
`Extension::deprecatedFiles()`.

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
`HintsTest::theTargetedInvocationKeepsWhatMakesItRunnable`
and `ForgeTest` on the extraction. The other two are sentences inside a document
and a skill, reachable only as strings, and both rested on nobody rewriting the
file.

Two guards were written, which is the keep-request in the only form this
repository has for one.
`KnowledgeTest::theMovesTheCommitRulesStopAreStillStated` holds the clause
refusing the demand and the one refusing the branch list; the obligation beside
them was held twice over already, and it is the refusing half that a summarising
rewrite drops. `SkillTest::aTriageIsHeldToWhatItsMeasurementsActuallyMeasured`
holds the triage's three measurement rules and the sentence that sends a
reproduction to be shown red. Five reports credit that block — this one and
`2026-08-05-033954`, `2026-08-07-065401`, `2026-08-07-130037` and
`2026-08-07-233418` in the archive — and its third bullet is itself the answer
to the last of those, so a rewrite could have taken out four sentences the
corpus asks for without a failure. What that costs is now the last **Wrong if**
above.

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
that clause is added to `whichGlobalsAConditionCanReadIsBoundToItsMajor`. The
two failure modes the report would not change are guarded already —
`PackageSourcesTest::theTagListTravelsWithAHit` for the tag list, and
`KnowledgeTest::anUnknownDocumentIdNamesTheOnesThereAre` for the miss that lists
every document id.

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

## Confirmed on 2026-08-22

Eight readings held the rule above and changed nothing in it, so each is a line
here rather than a section of its own. What every one of them settled is the
boundary it names and the guard it left. `D-DOC-041` is why they were judged.

- `2026-07-31-194823` on 2026-08-02. The boundary is what an answer carries
  where somebody wrote it against what it carries where it indexes something.
  `HintsTest::shippedContentIsAnsweredPastThePointWhereTheFileExists` holds the
  sentence it asked be kept.
- `2026-07-31-194825` on 2026-08-02, the second reading of the strength above.
  The todo it queued was worked, and `Ships:` renders the language files the way
  it renders the manual, in the text as well as in the data.
- `2026-08-02-144456` on 2026-08-03. The boundary is between an obligation and
  the rule that discharges it, which a task about a bug never reaches.
  `HintsTest::aViewHelperPatchIsToldWhichTestItOwesAndWhichChangelogType` holds
  the two sentences.
- `2026-08-08-224455` on 2026-08-09. The boundary is a rule against an instance:
  a rule arrives from the domain the task is already in, and an instance is
  reachable only by an identifier the caller holds or by the words written in
  it. `KnowledgeTest::theReleaseTargetsAnswerRefusesTheBranchListInTheCheckout`
  and two assertions beside it hold what it asked be kept.
- `2026-08-13-214838` on 2026-08-14. The boundary is what this corpus answers to
  a handle the caller holds against what it answers to a description of what is
  wanted. `LabelSearchTest::twoLabelsOfOneKeyAreToldApartByTheResourceEachIsIn`
  and `KnowledgeTest::theFetchDirectionNamesTheRemoteTheChangeRefIsOn` hold the
  two.
- `2026-08-18-070515` on 2026-08-18. The praised absence is exhaustive, and its
  axis was read off `SetupService::getAvailableDistributions()` rather than
  supplied by the reader. The boundary is what an answer hands the caller to
  check against what it asks the caller to believe.
  `aBootBriefCarriesTheTestThatDecidesABranch` and three assertions beside it
  hold what it asked be kept.
- `2026-08-18-074305` on 2026-08-18, after five corpora in which the credit was
  misplaced and the first in which it was not. The boundary is what a session
  can settle when the answer arrives against what it would have to be reminded
  of later, and its caveat prices `D-SKL-051` rather than contradicting it.
  `ProjectTest::everySiteIsNamedWithTheBaseAndTheRootPageItCarries` and
  `theSetupItemsArriveUnderTheirGuard` hold the two.
- `2026-08-18-080743` on 2026-08-18. The praised exclusion is true, which the
  reading of `2026-08-01-121852` above requires be checked rather than assumed.
  The boundary is what an orientation answer is worth to a caller that has
  already decided, and the same answer carried the routing line that would have
  changed an action.
  `ScopeTest::theExclusionForPhpSourceKeepsItsQualification`
  holds it.

## Confirmed on 2026-08-25

The 2026-08-03 section reads a strength that proposes a feature against the
feature. This is the mirror of it: a keep-request for a field that had been
taken off the very calls the report names, six days before the report was
written.

`feedback/2026-08-24-140340` credits five hints with one extension version
serving v13.4 and v14 in `/home/benji/projects/ext-usercentrics`, with both test
layers and a browser-verifiable installation. It asks that the ids stay stable,
that the sentences predicting a failure stay in them, and that `availableHints`
and `omittedHints` not be dropped.

The quotations are checked before the boundary is. The reporting checkout is
another project, so all of it was re-run on 2026-08-25 through
`bin/typo3-dev-companion` from this repository's own `.checkouts/14.3`, and
every sentence comes back verbatim:

- `project-configuration-files` — the first start in a clone whose dependencies
  are not installed leaving no `additional.php`, exception 1396795884, the
  detection running before the post-start hooks, and the regeneration that
  overwrites the file while restoring an `/additional.php` ignore entry.
- `extension-test-site` — the absent `SiteBasedTestTrait` with the
  export-ignored `Tests/` behind it, `$this->get(SiteWriter::class)->write()`,
  the file-backed caches and the `CacheManager::flushCaches()` that makes the
  class deterministic, and the green `--filter` run that is evidence of nothing.
- `project-extension-tests` — the five `typo3Database*` variables, and the
  account that has to create a database per test class, which under DDEV is
  root.
- `extension-repository-installation` — `extra.typo3/cms.app-dir` moving
  nothing, with Composer's own message and the `config/` and `var/` that land in
  the versioned tree.
- `extension-manifest` — the two fields whose absence still evaluates
  `ext_emconf.php`, and the suite running `failOnDeprecation` that surfaces it.

**Half the keep-request had already been refused.** `1a22d214` withheld
`availableHints` from a call that names an id on 2026-08-18; `D-ANS-075` is the
order it is printed in and `R-ANS-030` why the count survives where the list
does not. All four id calls the report's own Query line names answer with an
empty index today — 103, 101, 103 and 103 neighbours withheld, each under the
sentence naming the parameter that asks for them. The report says every hint
answer lists the field. Which revision that session's client launched is
recorded nowhere here, so what is settled is narrower: against this server the
field is not where those ids can have come from.

**The other field holds, and it is the one that did the work.** An operations
brief for this task shape names `project-configuration-files` in `omittedHints`,
beside `installation-setup`, `project-build-and-scripts`, `installation-boot`
and `browser-tests-outside-core`. The task text is this run's own, since no
feedback records the one that was sent, so what reproduces is the mechanism
rather than the call.

The praise implies more than it quotes. It puts the whole value in an id that
could not be guessed from its name, and names the two phrases it would have
searched instead. Both rank `project-configuration-files` first — on its
statements, where neither phrase is in its title — and a call that matches
carries the index in full, because withholding it is what naming an id does. So
the reachability credited to the index is the matcher's. That is the sixth
corpus in which a strength misplaces its credit, and the first in which the
misplaced half is the half asked to be kept.

The boundary is **what the corpus states about an artifact that exists against
what it would have to state about a shape somebody has to build.** Every
sentence credited here is a behaviour readable off something already in front of
the session: DDEV's settings generator, the `Tests/` the core package
export-ignores, the database `Testbase` creates per class, Composer's message
about `app-dir`, the predicate that decides whether `ext_emconf.php` is read.
The cost side is the same session three minutes earlier.
`feedback/2026-08-24-140317` names three of these five ids and reports that none
of them says how a page comes to render for an extension that ships no page
TypoScript, and what it ended at is three designs the user rejected in turn
rather than a behaviour anybody could have read. Five predictions landed because
there was something to predict; the sixth question had nothing behind it.

Three keep-requests rested on nobody rewriting the file and are held now.
`HintsTest::aTestSiteIsWrittenWithWhatMakesTheClassDeterministic` holds the four
sentences the report credits its first green functional run to;
`aSuiteThatWillNotStartIsAnsweredBeforeTheHarnessIs` gains the credentials and
the account, which its own docblock named and nothing asserted; and
`theFileAnExtensionNoLongerNeedsIsCoveredWhereItsFilesAre` gains the predicate
and `failOnDeprecation`, without which the entry states a rule and not the
failure it is met by. The other two were held already —
`theDdevSettingsAnswerSaysWhenThatFileIsWritten` for the exception and the hook
ordering, `installingTypo3BeneathTheExtensionNamesTheInertKey` for the message
and the ignored directories. The ids need no guard of their own: each is named
in assertions a rename fails.

The feedback is closed by this commit and nothing is queued. No statement about
TYPO3 was established here: the re-runs read this server's answers, and each
sentence guarded above was verified where it was written.

## Confirmed on 2026-08-25

`feedback/2026-08-24-173151` is the first strength whose praise is a **chain**
rather than an answer. It credits no single call: `typo3_forge_lookup` on #35069
listing change 76606 under `reviews`, then
`typo3_gerrit_lookup(change="76606", messages="people")` answering `ABANDONED`
with one inline comment, and asks that the whole path stay reachable.

The quotations are checked before the boundary is. Both calls were re-run on
2026-08-25 through `bin/typo3-dev-companion`, and every one of them comes back
word for word:

- `reviews` on #35069 carries change 76606 at patch set 2, named 2022-11-15. It
  now carries 95399 beside it, which is the reporting session's own push, twelve
  minutes before it filed this.
- Change 76606 answers `ABANDONED`, last moved 2024-12-12, with the one comment
  on `/PATCHSET_LEVEL` — Benni Mack, `wrong approach :(` — and a log whose four
  human messages are the upload, `Patch Set 1: Code-Review-1`, a second upload
  and the abandon two years later. `messages="people"` held two `core-ci`
  pipeline reports back.

The first **Wrong if** did not fire. Every counterfactual is about a patch the
session did *not* write, which is an action an answer stopped rather than a step
it had to take instead, and its one suggestion is judged below rather than
skipped.

**The boundary is what a handle costs to follow against what following it
returns.** Everything credited sits on the far side of one further call — the
status, the inline comment, the review log — and what the Forge answer carries
is the handle alone. That is
[`D-ANS-069`](../answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)'s
decided line, and the same debrief reports the cost side of it:
`2026-08-24-173116`, filed 35 seconds earlier from the same triage, counts one
live issue per five candidates with each dead one paid for by a code read.

Two sessions have now walked this chain from two task shapes, which is what
makes it a property of the corpus rather than one reader's route.
`2026-08-24-183447` reviewed change 91127 and jumped to the abandoned 85224 for
Benjamin Franzke's `-1 for now`, which is the argument that made the change
under review defensible.

**What the praise implies is checkable and half of it is not held.** Of the four
things asked to stay, three rest on assertions already: the comment thread with
its `/PATCHSET_LEVEL` file and null line on `aCommentCarriesItsThread`, the
`messages` filter on `theReviewLogIsAskedForAndTheServiceUsersHalfIsSeparated`,
and `reviews` on `aReviewChangeIsLiftedOutOfTheProseThatCarriesIt` and on being
a required key of `ForgeLookup::outputSchema()`. That an **abandoned** change is
answered at all rested on nothing, and `changesMatching()` already takes a
`status:open` narrowing, so the shape that would drop it is in the class.
`anAbandonedChangeIsAnsweredWholeByBothWaysIn` holds it now, on the handle path
and on the batched query a backlog row is filled from.

One thing the report does not claim was found beside it. `o=DETAILED_LABELS` is
asked on every named change, and 76606 came back with no label state at all, so
the Code-Review-1 exists only as a message body — no `Outdated Votes` line marks
its removal, which is the mechanism `D-ANS-079` names. Two of the four abandoned
and merged changes read that day answer the same way. It is not a rule about
abandonment: 85224 is abandoned and carries its `Verified` votes. What it means
for a caller is that on a change that stopped, `messages` is where the negative
review is, and it is opt-in.

The suggestion is queued rather than closed, and the finding under it is one the
entry it bears on never priced. `Forge::reviewed()` already makes the review
query and already receives `status` per change; it keeps `number` and `url`.
What the feedback asks for therefore costs nothing on the enumeration path,
while on the issue path — where `reviews` is lifted out of the journal and the
review server is not asked at all — the decided line holds unchanged. The
verdict itself moves neither way: it is the inline comment, one
`/changes/<n>/comments` per change, and the session's own account says that, not
the status, is what stopped it. `D-ANS-069` carries the reading and the card
carries the work at `normal`, which the corpus above set. The feedback is
trimmed to that half and stays open behind it.

No statement about TYPO3 was established here: the re-runs read this server's
answers, and the discarded field was read in `Forge::reviewed()`.

## Confirmed on 2026-08-25

`feedback/2026-08-24-183447` is the strength the section above named as the
second session on the same chain, and its own lever is a proposal the data
refutes. It credits four answers from a review of change 91127 and asks for one
addition: that a change with several comment threads say which are unresolved
**and** top-level.

The 2026-08-03 section reads such a proposal against the feature. Here the
feature is absent, as in the 2026-08-21 section, and reading the ask against the
data is what disposed of it: the pair it names selects a settled thread on 91127
and misses the open one on 85224, and the answer prints two disagreeing counts
of the same thing. Neither is in the report. `D-ANS-079` carries the
measurement, where its third **Wrong if** fired.

**A proposal is checked against the data the reporting session derived it
from.** Reading it against the feature says whether the thing exists; reading it
against the data says whether it would have been right — and a strength's
proposal is a reading the session made and this repository can redo, which is
what separates it from the account of the run around it.

The quotations are checked before the boundary is, re-run on 2026-08-25 through
`bin/typo3-dev-companion`. The `reviews` array of #103215 still lists 85224
beside 91127, now with the backport 95409; Benjamin Franzke's inline comment on
85224 comes back word for word and his `Code-Review-1` is gone from the labels
and readable in the log alone, which is `D-ANS-079`'s own mechanism; the
gerrit-workflow document returns whole with all four sentences the report used;
and `typo3_project_describe` in `.checkouts/main` answers `core-checkout`,
`15.0.0-dev`, no extensions.

The boundary is **what an answer states against what it hands over to be worked
out**. Everything credited is stated: the ref with the remote it is on, the
`reviews` array, the patch set with its commit, the kind of checkout. The one
cost is the one reading the session had to make itself, and it made it wrong.

Three keep-requests rested on nobody rewriting the file and are held now.
`KnowledgeTest::theFetchDirectionNamesTheRemoteTheChangeRefIsOn` gains the dated
measurement, which the report names as what made it trust the page over its own
habit without testing the claim; the remote and the ref were held already.
`theCarryOntoCurrentCodeNamesTheBranchItLandsOnAndTheUndo` holds the rest of
what it used — the https URL that needs no account, the `review/<change>` branch
with where it starts, and the `git branch -D` undo with the reason `-d` will not
do. Nothing asserted that section of the document at all, and `D-SKL-041`, whose
subject it is, declared no test.

The feedback is trimmed to the ranking half and stays open behind its card.

## Confirmed on 2026-08-27

**What a negative is worth is what the set behind it is worth.**
`feedback/2026-08-24-225153` credits five answers from a review of change 93177
in `/home/benji/projects/typo3-cms`, and one of them is an answer it read as a
negative. Every cost from the same debrief is a negative too, and none of those
could be read.

The quotations are checked before the boundary is. The reporting checkout is
another session's work, so all five were re-run on 2026-08-27 through
`bin/typo3-dev-companion` from this repository's own `.checkouts/main`:

- Change 93177 answers patch set 12 at `fd57c988fde`, `MERGED` on `main`, where
  the report saw patch set 10. It has moved twice more since, which is the
  report's own point made again. `messages="people"` holds 10 service messages
  back of 35, nine of the 25 that are left carry Gerrit's `Outdated Votes` line,
  and `issues` names #109233 as `resolves` and #110365 as `related` with both
  subjects. Its four threads are all resolved now.
- `typo3_rule_lookup` with the bare query `changelog entry` answers
  `The Changelog Entry a Message Announces` first at 100%, and the sentence the
  report acted on twice is its lead, word for word.
- `typo3_project_describe` lists 20 guides with `any/testing/browser-check`
  among them, last in the answer and in both halves of it.
- The invocation note comes back whole, the failure line
  `Test file "--filter" not found` included.
- `typo3_changelog_lookup` with `type=deprecation, version=15, limit=100`
  answers the same five entries, by the same issue numbers.

**The praised negative is exhaustive, and the set is the answer's own.** The
2026-08-03 section asks that of a praised absence, and the axis is written down
here rather than supplied by the reader: five entries were shown against a limit
of 100, so the caller sees the set and draws the conclusion off it.
`.checkouts/main` at `d7b21b717b` carries exactly those five `Deprecation-*.rst`
files below `typo3/sysext/core/Documentation/Changelog/15.0/` and no sixth.

The four costs from the same session are the other side. `2026-08-24-225214`
spent three Forge searches on a zero and says why it bought nothing — a hit is
conclusive and an empty is not, over a set nobody enumerated. `225129` read a
`matchCount 0` that meant the resource was never resolved, which calls for the
opposite move from the one it made. `225022` read a page that carries the
browser environment and is silent on driving the backend, and took the silence
for coverage. `225243` built a second error surface beside four `error:`
branches that were declared and switched off. Each is on a card of its own and
what fills it belongs there.

**The first Wrong if fired, and the lever is one event filed twice by one
session.** Item 3 credits the changelog rule with settling two decisions hours
apart; `2026-08-24-225243`, filed 50 seconds later, reports the second of those
as a cost — the entry written from precedent, matched against neighbours, run
through `checkRst`, then deleted. Both accounts are of the same `Important` file
and both are true. The rule arrived, was quoted approvingly, and did not decide
the moment it was about.

That is the pairing this entry calls the step 5 signal, in a form it has not
carried: not two sessions on one property, but one session on one event, from
both sides. The judgement of it is on
[`D-GUI-012`](../guides/gui-012-the-brief-names-the-guide-the-recognized-work-belongs-to.md),
where the two cards blocked on that question already are.

Two keep-requests rested on nobody rewriting the file and are held now.
`KnowledgeTest::aCommitMessageQueryIsAnsweredWithTheObligationAndNotOnlyThePage`
holds the sentence and the section a `changelog entry` query wins with, which
`D-KNW-111` left behind as prose beside a pointer and a summarising rewrite
takes for a pointer alone.
`HintsTest::theInvocationNoteSaysWhereTheOptionParsingStops` gains the clause
that says where the words go; it held the mechanism and the failure line, and
the report credits the instruction rather than either. The other two are held
already — the patch set and its commit by `R-ANS-021`, the guides inventory by
`ProjectTest::theCallEveryTaskOpensWithNamesTheGuidesThereAre`, which asserts it
is every document there is and that it comes last.

The feedback is closed by this commit and nothing is queued. The one statement
about TYPO3 was read in `.checkouts/main`; the rest of the re-runs read this
server's answers.

## Confirmed on 2026-08-27

**A negative that bounds an obligation is written once and holds; a negative
about the case has to be read off the artifact.** `feedback/2026-08-25-110726`
credits four answers with killing a candidate finding in a review of Gerrit
95392, and both costs from the same debrief are the second kind.

The quotations are checked before the boundary is. The reporting checkout is
another session's work, so all four were re-run on 2026-08-27 through
`bin/typo3-dev-companion` from this repository's own `.checkouts/main`:

- `typo3_rule_lookup` with the compound query `changelog entry bugfix` answers
  `Which Change Owes a Changelog File` first at 100%, with the refusal word for
  word and the three conditions it turns on beside it.
- `typo3_task_guide` with the two changed paths returns `backend-typescript`,
  whose block names both switched-off rules, says neither is a finding, and
  names `Build/eslint.config.mjs` as what decides the rest.
- `typo3_test_run_guide` with the same paths answers `checkGruntClean` at
  `runs: git`, and its `whenToUse` carries the `git add *` sentence, the
  untracked files and the checkout to run it in, all verbatim.
- `typo3_commit_message_guide` on that patch's subject answers
  `summary-length-preferred` at `warning` with the arithmetic the report quotes:
  57 characters, a 48-character summary plus 9 for the keyword prefix.

The boundary is **what an obligation states against what deciding it in this
case requires reading**. Everything credited here is a property of the core's
process, true of every patch and written once: which change types owe a
changelog file, which lint rules are enforced, what a suite does to a checkout,
what a subject line budget is. Both costs from the same debrief are properties
of the artifact in front of the session. `2026-08-25-110753` asks whether this
module is reachable by the JavaScript unit layer at all and answers it in four
reads of the checkout; `2026-08-25-110659` asks whether this patch set is
intact, and what says it is not is a change message the default suppresses.

The two sides meet on over-reporting, which is the failure mode the report
names. Its saves stop it by saying where an obligation stops. `110753` is the
same mechanism failing on the case side: *Frontend modules with state
transitions should have focused JavaScript unit coverage* arrived with nothing
bounding it against this module, and the session reports it as reading like an
instruction to demand a test. One session filed both directions within 27
seconds.

**The first Wrong if fired, in the structural note the report appends to its
four saves.** It says `typo3_project_describe` ends with a guides array, that it
read `core/contribution/gerrit-workflow` whole off that list in the second half
of the session, and that without the array it would not have known to ask for
the page by id. That is
[`D-ANS-061`](../answers/ans-061-an-answer-that-names-a-document-hands-it-over.md)'s
second **Assumed** met for the first time since it was written, and
[`D-ANS-091`](../answers/ans-091-the-project-answer-leaves-the-second-call-to-the-instructions.md)'s
second **Wrong if**. Both are judged there.

**The proposal is checked against the data the reporting session derived it
from, and it covers half of it.** The report reads its saves as one pattern —
each states the negative case explicitly — and asks that the pattern be spread
to the other rule and hint sections a review consults. Two of the four are that
pattern. The `checkGruntClean` warning stops an action rather than a finding,
and the commit arithmetic states no negative at all: what the session credits it
with is ranking a preference against a violation, which the answer does by
carrying `warning` and its own sum. Spreading the pattern would write negatives
into sections whose saves were something else, against the rule in
[AGENTS.md](../../AGENTS.md) that a list of what something is not belongs where
the confusion actually happened.

Where one did happen is the proposal's own second example, and it is on a card
already. `bin/cli hints:probe "which changes owe no test"` reached
`breaking-without-a-moved-member` on the word *owe*, which is about changelog
obligations, and nothing here says which changes owe no test. `110753` from this
debrief is the confusion itself. Naming its answer from here is the copy-down
`judging.rst` warns about, so the card carries it. The proposal's first example
has no report behind it: no session has reported a removal over-reported, and
the nearest negative in the corpus runs the other way — `Changed Signatures`, a
section of the very answer the first save came from, says a member promoted from
protected to public is not a signature change and owes none of it.

Item 3 needs nothing.
[`D-ANS-113`](../answers/ans-113-a-suite-whose-mark-warns-the-caller-off-names-what-answers-its-question-instead.md)
names this feedback among the three sessions that read the warning, did not run
the suite and each invented the same replacement, and
`core/contribution/committed-build-output` shipped the day before this
judgement. It is the twentieth id of the guides array the report counted
nineteen of.

Two keep-requests rested on nobody rewriting the file and are held now.
`HintsTest::theLintRulesThatAreSwitchedOffAreNamedAsNoFinding` holds the two
rule names, the clause that makes neither a finding, and the config file beside
them — the names alone read as a list of rules, and the clause after them is
what dropped the finding. `HintsTest::theStagingSuiteSaysWhichFilesItStages`
holds the `git add *` sentence, the untracked files and the checkout to run it
in; `aTypeScriptChangeIsOfferedTheSuiteThatStagesTheWorkingTree` held the mark
and its one-line gloss and
`everySuiteThatRunsGitNamesTheDocumentAnsweringItsQuestion` the way out, so a
trim keeping those two would have left a caller warned without knowing what of
theirs was at stake. The other two were held already —
`KnowledgeTest::aQueryForTheChangelogObligationReachesTheSectionThatStatesIt`
for the refusal and its three conditions, and
`CommitMessageTest::theLengthCheckSaysWhatMadeTheSubjectLong` for the code and
the arithmetic under it.

The feedback is closed by this commit and nothing is queued. No statement about
TYPO3 was established here: the re-runs read this server's answers, and the
absence behind the proposal was read with `bin/cli hints:probe`.

**A strength can reach its judging run after the lever in it has been pulled.**
`feedback/2026-08-25-105203` credits `typo3_gerrit_lookup` and the
`core/contribution/gerrit-workflow` document with carrying a Gerrit review end
to end in `/home/benji/projects/typo3-cms`. `D-ANS-107` had already read it: its
last evidence bullet is this report, and the field it asked for shipped under
two hours after it was filed. What was left for this run is the half that entry
did not need.

The first **Wrong if** fired in its ordinary shape — the report names what it
did instead, `git merge-base --is-ancestor` on the patch set it had fetched.
Read against the data the proposal is refuted, and the measurement is on
[`D-ANS-107`](../answers/ans-107-the-review-backlog-is-enumerated-the-way-the-tracker-is.md).

The quotations are checked before the boundary is. The reporting checkout is
another session's work, so both calls were re-run on 2026-08-27 through
`bin/typo3-dev-companion` from here, and every one comes back:

- Change 95369 answers the fetch ref `refs/changes/69/95369/6` with the review
  server as its remote, `main` as its branch, and #110503 as `resolves` with its
  subject and tracker state. Code-Review and Verified are both satisfied with
  every voter's value beside them, and `releaseLines` names `main` as
  development, 14.3 until 2029-06-30 and 13.4 until 2027-12-31. The backporting
  thread it acted on comes back word for word — Garvin Hicking raising it, Elias
  Häußler answering *I haven't considered a backport for this patch*. It is
  resolved now, above the backports 95418 and 95419 the report recommended.
- The `core/contribution/gerrit-workflow` document returns whole at 13,771
  characters, with both things the report says it would not have got right
  alone: `remote.origin.pushurl` as what a change ref is fetched from, and the
  `Releases:` policy that a bug fix goes to `main` and one line back. So does
  the warning against the contribution guide's cherry-pick page and the
  `review/<change>` branch it prescribes instead.

The credit for the guides list is neither checked nor read here. What the report
says about it is a cost — that `typo3_project_describe` was the only place it
saw the document ids at all — and two other open feedback carry that with cards
of their own.

**The boundary is what the source states against what this answer had lifted out
of it.** Everything credited is a fact `review.typo3.org` states and this answer
forwards, `releaseLines` excepted, which is shipped rather than fetched. The one
thing the session went to git for is stated there too: `mergeable` is on every
row the review server sends unasked, and `Gerrit::change_()` was dropping it
along with five others. So the cost side of this strength is not a boundary of
the source at all.

That is the gap a reporting session cannot see. An answer missing what its
source never had looks short, and the session says so; an answer missing what
its source sends unasked looks complete, and what the session reports instead is
the work it did around it. This one reported the work — four fields it rebuilt
by hand — and proposed a test for the field it did not know was there.

One keep-request rested on nobody rewriting the file and is held now. The report
puts the comments' whole value in what they said, not in that there were three:
its review note and its `Releases:` recommendation were both written against
that thread. `theTextHalfListsOneThreadAtATimeAndSaysWhatEachStandsAt` held the
headings, the counts and the thread states, and nothing asserted that a
comment's message reaches the text at all. It now holds the question and the
reply under the lines naming who wrote them, because a thread read without its
reply is a question standing open. The rest was held already — the ref and its
remote by `theAnswerCarriesTheRefThatFetchesThePatchSetItNames`, the issues by
`aChangeCarriesTheIssuesItsCommitMessageNames`, and `releaseLines` by
`theBranchesThatTakeAPatchStandBesideTheOneAChangeTargets` with its dates.

The feedback is closed by this commit and nothing is queued. The one statement
about TYPO3 is the ancestry measured on `D-ANS-107`, read in `.checkouts/main`.

## Confirmed on 2026-08-27

**A rule reaches a session at the gate it runs through, not at the place it is
written.** `feedback/2026-08-25-114605` credits two corrections that arrived in
one call, and what it praises is a checkout that could not have supplied either.

The quotations are checked before the boundary is. The reporting checkout is
another session's work, so the call was re-run on 2026-08-27 through
`bin/typo3-dev-companion` from here, with the workflow, the releases and the
issue the report names:

- `summary-length-preferred` comes back word for word — 56 characters, a
  47-character summary, 9 for the keyword prefix, and 43 left. It now carries
  the shortening half after it, which `D-GUI-021` built for this session's own
  sibling, so the answer says more than it did rather than less.
- `older-release-line` comes back as
  `13.4 is maintained, and a BUGFIX is released on main, 14.3`, with the
  severity sentence the report acted on.

Both quotations from the core checkout hold too, and both were read in
`.checkouts/main`. `checkForLineLength()` is `grep -q -E '^[^#].{72}'`, verbatim
as the report has it, and the `AGENTS.md` sentence is
`**No line of the message may reach 72 characters** — the hook rejects the commit, footer trailers included`.
No file in that checkout carries the 52 outside the changelog.

**The praised absence is real, and the axis it is closed over is the checkout
rather than the process.** The report enumerates "the repository's own two
sources of truth" and both are files on the machine. There is a third, and the
first names it: the hook prints a `docs.typo3.org` permalink when it refuses a
commit, and the contribution guide's commit message appendix is what
`knowledge/documents/core/contribution/commit-messages.md` cites as its
`Source:`. So the rule was one hop away and the hop leaves the checkout, which
is the distinction the session was in no position to draw.

The boundary is **what a checkout gates mechanically against what the process
requires**. Every gate in that hook is a regex over the message's own text: is
there a keyword, is there a `Resolves:` line, does the `Releases:` line have the
shape, is any line too long. None reaches a fact outside the message.
`checkForReleases()` takes any `main|[0-9]+\.[0-9]`, so it would have passed a
trailer naming a branch that has not existed for years, and the length gate
measures 72 because 72 is what a regex can count. Both things this server
supplied are on the other side: what 52 is preferred for, and which lines a
`BUGFIX` is released on today.

The cost side is the same session, 90 seconds later, and it is the mirror.
[`feedback/archive/2026-08-25-114735`](../../feedback/archive/2026-08-25-114735-the-mandated-opening-calls-project-describe-and.md)
reports that this server's own initialize instruction — start every task with
`typo3_project_describe`, then `typo3_task_guide` — was in context from the
first token, is quoted back word for word, and produced neither call in five
turns. A rule this server states and cannot enforce lost to the harness prompt,
in the session where a rule the checkout could not enforce was the answer's
whole value. One session filed both directions.

**The first Wrong if fired, and both levers had been pulled before this run
reached them.** The report names what it did instead — it read the hook's regex,
measured every line at 68 or less, and called the message ready.
[`D-GUI-020`](../guides/gui-020-the-commit-guide-states-the-longest-line-the-hook-accepts.md)
took that half on 2026-08-26 with this feedback in its evidence, and the
re-run's `line-length-boundary` states the boundary on a clean draft.
[`D-ANS-117`](../answers/ans-117-the-commit-draft-names-the-workflow-that-owns-the-commit.md)
took the report's own suggestion — that the value would rise if the tool were
easier to reach at draft time — and shipped it hours before this judgement: the
re-run carries `typo3_task_guide` in the text and in `nextTools`. That is the
shape the 2026-08-25 section named, a strength arriving after its lever, and
here it happened twice to one file.

One thing the report does not claim was found beside it, and it is not queued.
The session corroborated `older-release-line` by counting the `Releases:`
trailers of 40 `[BUGFIX]` commits, which
`knowledge/documents/core/contribution/commit-messages.md` refuses in as many
words: counting trailers on recent commits samples what other changes needed
rather than what this one does. It is the second session in the corpus to reach
for the log on that question — `D-ANS-058`'s evidence records the first, which
counted 40 commits too and used them to establish rather than to confirm. That
makes the log the thing a checkout offers where the answer is not in it, which
is this boundary again. It stays evidence: the session accepted the check on its
merits first and the sample changed nothing, so it is the corroboration of a
right answer that `2026-07-31-194823` established is outside the ladder.

One half of the keep-request rested on nobody rewriting the file and is held
now. `aMaintainedLineFurtherBackSaysWhatItClaims` held the level, the older line
and `priority bug fix` — the half that says what to drop. What the session acted
on is the half that says what to write instead, and the change type and the
ordinary lines were in no assertion, so a trim keeping the severity sentence
alone would have left a caller told their trailer was wrong and not what the
right one is. The other half was held twice over already:
`theLengthCheckSaysWhatMadeTheSubjectLong` for the arithmetic, and
`theLengthCheckNamesWhatToCutAndWhoCutsIt` on this session's own subject.

The feedback is closed by this commit and nothing is queued. The statements
about TYPO3 were read in `.checkouts/main`; the rest of the re-run read this
server's answers.
