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
  cards of their own. [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes "nothing to do" the close answer rather than a special case.
- A strength does not confirm a decision. It is a session's account of its own
  run, which is what
  [judging.md](../../documentation/feedback/judging.md) already refuses to
  assess in the other direction — the session was there and the reader was not.
  `D-SKL-001` is confirmed by recorded runs with timings in them, and a
  self-report cannot be read against its **Wrong if** the same way. Nothing was
  added to it here.
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

## Since then

The first **Wrong if** fired on the next strength judged, `feedback/2026-07-31-192945`.
Read as boundary-evidence only it says the conformance skill's order is a good
one, which nothing here doubted. What it actually carries is a lever, and the
lever is the praise itself.

The strength recites the order it followed, and the recitation is this server's
order with one step gone. That can be checked against a file, which is what the
self-report this entry refused to assess could not offer. A recitation is an
artifact; an account of a run is not. So the rule established here holds with
one addition. Where a strength quotes something this repository owns, the
quotation is evidence about the file rather than about the session, and it is
read before the boundary is.

The judgement is on [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md),
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

So does the lever behind the *wrong path avoided*.
`typo3_architecture_lookup` with the task *content element with inline children*
returns **Registering a Content Element**, whose hints say that a plugin is a
CType like any other and that the list_type detour is gone at v14, and that the
rendering definition is `tt_content.<CType>` on top of `lib.contentElement`. The
task *Extbase plugin registration and cHash* returns **Extbase Plugins**,
**Registering a Content Element** and **Records in the Frontend Without
Extbase**. `site-sets` and `tca-formengine` are reachable too, from a task
naming those subsystems, which the two calls behind the report did. The core
confirms the statement the session acted on:
`14.0/Important-105538-ListTypeAndSubTypes.rst` in `.checkouts/main` records the
removal of the `list_type` field and of the plugin subtype with it.

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
in, which no other feedback in the corpus does, and read against `skills/base.md`
that order is the base outrun: step 3 skipped, and the checkout read before the
conventions lookups rather than after them. The praise is what names it — the
session graded the run as working and asked for the file reading to be blessed
with a batch-read hint, which is the one thing both the base and the conformance
skill refuse.

So a strength is not closed on the strength of being one. The ladder is walked
over what it reports it *did*, and only the part that reports how well it went
is read as a boundary. Judged onto
[`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md),
whose **Wrong if** it satisfies, and the third **Wrong if** above is now the one
still open: this entry has been cited by each of the three judgements above, one
of which disagrees with half of it. All three were worked at once, in sessions
that could not see each other, which is why they read as three arrivals at one
entry rather than as one account continued.
