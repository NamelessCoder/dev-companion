---
id: D-KNW-055
date: 2026-08-04
status: open
---

# D-KNW-055 — The first check a standalone extension repository gets is a gap this server owns

**The corpus states how an extension's coding standards are set up, beside the
static-analysis hint that is the only half of that layer it holds today.**

Six feedback, one session, one task: "add a code style fixer to a standalone
TYPO3 14.3 extension repository". Three of them are the corpus gap seen from
three sides — no statement on the subject, no route to the subject, and no way
to learn from a near-miss answer that a neighbouring subject exists. The fourth
is the number the task ended on, which the first call of every workflow reports
everything but. The last two report what worked, and what they mark is the same
boundary the fourth one costs at.

The directory is the reading no single card carries. `ext-guidedtour` holds
eight of the ten open feedback, and the earlier cluster is a standalone
extension repository being given the installation it had none of
([`D-SKL-012`](../task-skills/skl-012-bringing-a-development-installation-into-existence-earns-a-task-skill.md)).
This one is the same repository being given the first check it had none of. The
domain is a package with no infrastructure at all, and this server answers its
analyser and nothing else in that layer.

## Evidence

- **One session, six cards, four minutes.** `bin/cli feedback:list` on
  2026-08-04 reports 10 open in three directories, 8 of them in
  `/home/benji/projects/ext-guidedtour`. The six judged here are `055420`,
  `055626`, `055638`, `055658`, `055715` and `055741`, all `claude-opus-5[1m]`,
  all naming the same task, and all read whole for this entry. One session is
  what they weigh, and it is why nothing here is `high`.
- **Nothing below `knowledge/` states the subject.**
  `bin/cli hints:probe "coding standards php-cs-fixer setup for an extension"` —
  the feedback's own query — returns `extension-repository-layout` alone, on
  text at 167 and no `appliesTo` needle. `"php-cs-fixer"` reaches nothing out of
  81 candidates, `"code style fixer extension"` returns `public-assets` on text,
  and `"editorconfig"` returns six hints whose strongest match is 26. A grep
  over `knowledge/` for the packages finds them in `catalog/references.json` and
  in the core-scripts document, neither of which is an answer about an
  extension.
- **The one hint that exists is the analyser's.** `extension-static-analysis` in
  `knowledge/hints/testing.json` was read whole: six statements, all PHPStan —
  the configuration's place, `tmpDir`, `bootstrapFiles` for `LF` and `CR`, the
  `ext_emconf.php` exclusion, level 5, the baseline. Its `appliesTo` needles are
  `phpstan`, `phpstan.neon`, `static analysis`, `analyse`, `baseline`,
  `phpstan-baseline`, `tmpDir`, `result cache`, `set up static analysis`,
  `level`. Not one of them is a word a caller asking for a fixer would use.
- **The skill routes to a corpus that answers half of what it names.**
  `skills/typo3-extension-testing/references/static-quality.md` names
  `friendsofphp/php-cs-fixer` driven by `typo3/coding-standards` and
  `editorconfig-checker` under *Coding standards*, and under *Static analysis*
  it hands the configuration question over: "`typo3_hint_lookup` with
  `id=extension-static-analysis` answers where the file belongs … ask it rather
  than recalling a configuration from another project". There is no such
  sentence for the fixer, because there is no such id.
- **The guide recognizes the task as nothing.** Re-run through
  `bin/typo3-dev-companion` from this worktree on 2026-08-04, with the session's
  task and its paths at `targetVersion: 14.3`: `intents` is `[]`, `skills` is
  `[]`, the checks are `runTests.sh` suites, and the checklist is the core patch
  shape. `knowledge/task-intents.json` carries no entry this task's words reach
  — the nearest, `tests`, matches `test`, `coverage` and `regression` — so the
  guide cannot name the skill that owns the work, which is what `D-SKL-013` put
  it there to do.
- **The scope states one half of the layer and is silent on the other.**
  `knowledge/server-scope.json` lists "setting up static analysis for an
  extension" under `covers`, and no line of `doesNotCover` mentions coding
  standards, a fixer or repository tooling. `055715` reports calling neither
  `typo3_server_scope` nor `typo3_task_guide` and paying a `hint_lookup` to find
  the coverage out; had it called them, neither would have said.
- **The near-miss reproduces exactly.** Both calls of `055626` re-run through
  `bin/typo3-dev-companion` on 2026-08-04: the topical query returns
  `extension-manifest`, `extension-repository-layout` and `extension-boot-files`
  with `availableHints` empty, and `id=extension-static-analysis` returns that
  hint with `availableHints` empty. `HintLookup` documents the index on the `id`
  parameter — "Every answer that returns no hint lists the ids there are" — and
  `MatchedHints` fills it on a miss alone. So the answer that is wrong in the
  most ordinary way is the one that withholds the means to correct itself, and
  the session reached the neighbouring id only because a skill file names it in
  prose.
- **The PHP floor claim holds, and the number is not derivable from the major.**
  `.checkouts/14.3/typo3/sysext/core/composer.json` requires `"php": "^8.2"`,
  and so does the mono-repo root; `13.4` is `^8.2`, `12.4` is `^8.1`, `main` is
  `^8.5`. The file the session actually read is the installed one, and
  `.environments/e-site-14.3/vendor/typo3/cms-core/composer.json` says `^8.2`
  there too. `Instance::typo3Version()` already locates that package directory
  to read `Classes/Information/Typo3Version.php` out of it, so the floor is one
  further read at a path the server has resolved.
- **The strength and the cost are the same boundary.** `055658` credits
  `typo3_project_scope` for two things: the environment block naming the
  container's interpreter against the caller's shell, which sent every command
  through DDEV, and the explicit empty answers — `commands: []`,
  `artifacts.tests: []` — which turned "add a fixer" into "establish the first
  check". Both are already what must hold: `R-PRJ-008` holds the first with
  three `ProjectTest` methods, `R-PRJ-006` the second with one. Twelve seconds
  earlier, from the same debrief, `055638` reports the one number that same call
  does not carry. That is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)'s
  shape: the strength says where the boundary runs and the cost says where it
  stops.
- **The skill's own gap is one sentence wide.** `static-quality.md` says "Keep a
  formatting pass in its own commit, apart from behavioural change" and says
  nothing about the order. `055741` is the case that needs it: introducing
  `ci:editorconfig` onto a tree whose XLF files still hold tabs produces a first
  commit that fails the check it adds, and the session inverted the split and
  verified by running the checks at the new HEAD.
- **The package the corpus would be written from is in no checkout.**
  `typo3/coding-standards` is required by neither the core mono repo — which
  carries its own `Build/php-cs-fixer/` and requires `friendsofphp/php-cs-fixer`
  directly — nor by any of the four environments below `.environments/`. So the
  reading this gap needs is not a `.checkouts/` read, and that is the todo's
  first step rather than a copy of the feedback's account.

## Decided

- **Step 1a on the subject, taken on.** Nothing states it, the skill that routes
  here names the packages and hands the configuration question to a corpus that
  has none for them, and the reporting session read the answers out of an
  installed vendor tree. What is written is what the reading establishes about
  `typo3/coding-standards`, not the feedback's summary of it.
- **The route is part of the same work rather than a second judgement.** The
  session's own sentence is "the vocabulary gap and the coverage gap were the
  same gap": a corpus that spells the layer "static analysis" and covers only
  PHPStan under it is unreachable by every word a fixer task uses. The intent in
  `knowledge/task-intents.json`, the `covers` line, and the hint are one change.
- **Step 2 for the near-miss index, queued rather than closed on the spot.** The
  ids exist, they are computed on the path that already runs, and they are
  withheld exactly where a caller needs them. It is not step 4: the parameter
  description is accurate about today's behaviour, and rewording it would
  document the dead end rather than remove it. It touches `src/` and what every
  answer of a tool carries, which is the line
  [judging.md](../../documentation/records/judging.rst) draws around the spot.
- **Step 1b for the PHP floor.** The answer is available here — one file at a
  path `Instance` already resolves — and there is no way to get it in the form
  the task needed. What it prevents is silent: a package declaring the
  container's `^8.4` against a core that requires `^8.2` narrows its own range
  by two minors with every check still green.
- **`055658` is archived by this change.** It reports no gap, so the ladder has
  no rung for it; what it carries is written above as the boundary, and both
  fields it asks to keep are already requirements held by tests. It is not a
  **Confirmed on** anywhere: an account of a run confirms nothing, which is what
  `D-FBK-018` settled.
- **Four todos for six cards, and the six cards are deleted.** The corpus, the
  index, the field and the skill are four rungs with four readings; folding them
  into one card would queue a paragraph nobody can finish. `055715` is served
  twice because its two halves are two of them, and `055420` and `055741` name
  the todo that took their card over.
- **Priority `normal` on three of them, and one session is what holds it
  there.** `low` is where a card arrives, and all three of these were reported
  with the cost already counted — a layer read out of a vendor tree, a corpus
  neighbour reachable only through a skill file, a number that every check
  passes while being wrong. `high` would say several sessions, and one session
  reported these.
- **The skill card stays `low`, decided rather than defaulted.** Its workable
  half is one paragraph in a reference file, and its other half is a question
  nobody here can answer, so it does not go before the three above.
- **The question about `base.md`'s order goes up rather than being decided.**
  Both `055715` and `055741` ask whether the fixed five-step order may be
  shortened where a skill has already routed the task; that is what is wanted
  rather than what holds, `R-SKL-005` is what a change to it would touch, and it
  is written into the skill todo in the words it was asked in.

## Assumed

- **That `typo3/coding-standards` is the rule set an extension uses.** It is
  named that way by this repository's own skill and by the reporting session,
  and by nothing that was read here — the package is in no checkout and in no
  environment. If the reading finds the practice has moved on, the hint is
  written for what it finds.
- **That the corpus is the place and not the skill.** `static-quality.md`
  already names the packages and deliberately leaves the configuration to a
  lookup, which is the split `extension-static-analysis` was written on. A
  second copy in the skill would be the thing that page avoided for the
  analyser.
- **That an index of the same domains is short enough to attach to every
  answer.** `bin/cli hints:coverage` counts 127 hints over the whole corpus, and
  an answer is already scoped to the domains it matched in, so the list a `php`
  query would carry is a fraction of that. Nothing has measured what it costs a
  caller to read.
- **That the four cards this session's task shape produced are one domain rather
  than four coincidences.** They are read together because one session hit them
  in four minutes on one task; a different session hitting one of them alone
  would have been judged as one card.

## Wrong if

- The hint lands and the next session still reaches it only because a skill file
  names its id. Then the gap was the matcher and the vocabulary rather than the
  corpus, and the statement was written into a place nobody arrives at.
- The reading finds `typo3/coding-standards` does not ship what the session
  describes — the two rule sets, the two setup types, the excludes as literal
  directory names. Then this entry queued a statement about a package from an
  account of it, which is the failure the ladder's steps 2 to 4 exist against.
- `corePhpConstraint` lands and a session still declares the container's PHP as
  the supported minimum. Then the missing half was never the number but where
  the workflow says to look for it.
- `availableHints` is carried on every answer and callers stop reading them, or
  the list is long enough that an answer is worse for having it. Then the index
  was an affordance of the empty case and should have stayed there, and what the
  near-miss needed was a miss threshold instead.
- The answer about `base.md` comes back "the order stays fixed" and the next
  session on a tooling task skips steps 3 and 5 again. Then the prescription
  rather than its wording is what is being skipped, and `R-SKL-005` is the entry
  that has to move.

## Covered by

- `ProjectTest::theFloorTheInstalledCoreDeclaresIsBesideTheProjectsOwn`
- `ProjectTest::theAnswerSaysWhatRunsTheProjectAndNotOnlyWhatItDeclares`
- `ProjectTest::anEnvironmentThatIsNotDdevIsSaidToBeUnreadRatherThanAbsent`
- `ProjectTest::whatAnExtensionDoesNotShipIsAnswerdRatherThanLeftOut`
- `HintsTest::theFixerHalfOfTheStaticQualityLayerIsStatedAndReachable`
- `HintsTest::aCodeStyleFixerTaskIsRoutedToTheSkillThatOwnsIt`

## Since then

The reading the corpus todo was queued for was done on 2026-08-04, out of a
`typo3/coding-standards` v0.9.0 installed into a scratch Composer project — the
package is in no checkout, in no environment and in no lock file here. Two of
the three things `055420` described hold and one does not. The two rule sets and
the two setup types are there, with two corrections: the type argument is
optional and detected from `composer.json`, and `--rule-set` defaults to both
sets, so a bare `setup` writes both files.

What does not hold is the trap the report drew, which is what made it rewrite
the finder. `CsFixerConfig::create()` excludes `.build`, `typo3temp`, `var` and
`vendor`, and those are directory names matched at any depth rather than literal
paths: `packages/foo/vendor/` and `Classes/var/` are excluded, and
`PhpCsFixer\Finder` excludes `vendor` and ignores dot files before
`CsFixerConfig` is reached at all. A package whose `vendor-dir` sits under
`.build/` is therefore covered twice over and the shipped `->in(__DIR__)` needs
no correction for it. What does need one is a build directory or document root
that is neither hidden nor one of those names, and the match is case-sensitive,
so `Vendor/` is not excluded. Measured against a fixture tree with the installed
package, and the hint states the reading rather than the account.

**Since then**, on 2026-08-11, the fourth **Wrong if** fired. The measurement
disproves the remedy it names rather than confirming it.

`feedback/2026-08-10-182451` is a session that stopped reading the index. It
names two ids it was shown and never asked for, and counts six calls spent
reconstructing one of them from the filesystem. Its own call re-run here selects
`css`, `fluid` and `typescript`, scores 52 candidates and returns six.
`availableHints` carries the other 46 — 47 of the answer's 126 lines, and 3545
of its 13080 bytes.

So the premise holds and the conclusion does not. `javascript-unit-tests`, the
hint those six calls went around, ranks **seventh of 52**: the matcher admitted
it and `limit=6` cut it by one place. In `availableHints` it stands at position
43 of 46, because `Hints::index()` re-reads the corpus and orders it by file
while the rank `find()` has just computed is discarded. Nothing in the index
separates a hint the matcher ranked seventh from one that answered nothing.
Withdrawing the index to the empty case — what this entry said the firing would
mean — would have removed the only place that id was named at all.

The second id is a different case and separating the two is most of the finding.
`css-tokens-specificity` is sixteenth of the 39 the coverage floor rejected,
tied at a score of 47 with `css-light-dark-mode` and
`css-rtl-logical-properties`. Nothing in the query's words reaches it, so no
ordering of the index would have raised it. That half is the matcher's
vocabulary rather than the index's order, and the feedback's own suggestion —
rank what matched the domains but not the words — would not have surfaced it.

The third **Assumed** is now measured, on one call: the index is a little over a
quarter of the answer and says nothing about what was asked. The count was never
the cost. One thing was read beside it and is recorded because it turns
silently: `Hints::index()` calls `load()` with no target while `byId()` honours
one, so the index is not filtered by version and can name an id the tool would
then refuse. Nothing is wrong today, all 134 hints holding on 15.

What goes up is which of the two the index becomes — ordered by the rank the
matcher already has, or withdrawn to the miss it was built for. Both change
`src/` and what every answer of this tool carries, so neither is made here.
