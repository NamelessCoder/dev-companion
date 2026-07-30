# What was decided, and on what evidence

A feedback note is deleted by the commit that closes it, and the commit message
says what changed and why. What a commit message cannot carry is the part that
may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

That is what this file is for. One entry per decision worth revisiting, newest
first. An entry is not a changelog line — a change nobody would need to
reconsider does not belong here. When an assumption is later disproved, the
entry stays and gains a **Corrected** line: the wrong assumption is the useful
part, because it names the place where the next one is likely to sit.

## 2026-07-30 — Retrieval stays lexical and runtime inspection stays narrow

The live-documentation source created the point at which the remaining search
and inspection gaps could be measured instead of anticipated.

- **Evidence:** `bin/hints coverage` finds every one of the 61 hints from its
  own title. Seven of 25 scenario prompts reach no architecture hint:
  `CORE-06`, `META-01`, `META-04`, `META-05`, `EXT-01`, `EXT-05`, `SITE-03`.
  They are respectively version spread, orientation, structured-only output,
  installation, an upgrade, the explicit testing boundary and effective runtime
  configuration. Their owning route is not an architecture hint, so a semantic
  match would make the report look fuller without answering them better.
- **Decided:** no embedding dependency or semantic index is added. The concrete
  live-docs ranking defect was lexical — separated words tied a precise
  multi-word title — and is fixed by weighting adjacent query terms, guarded by
  `DocumentationTest`. Semantic retrieval may nominate candidates in future,
  but version, audience, binding and source still decide what can be returned.
- **Decided:** no generic SQL, log or database-schema tool is added. No feedback
  note or new scenario needed one after live documentation and the existing
  installation diagnostics were available. A runtime tool starts with the
  session that could not finish without it, not with parity against another
  server.
- **Would falsify it:** repeated real queries that miss a present section after
  short English alternatives, or a scenario whose diagnosis cannot be completed
  from project files, effective configuration and the caller's own checkout.
  Record that session; it supplies both the tool boundary and the safe result
  shape.

## 2026-07-30 — Package knowledge needs a producer before it needs discovery

An installed extension can eventually contribute task guidance for its own API
and workflow, but loading an arbitrary markdown path from every package would
erase the distinction this server already makes between a core rule, a
transferable convention and somebody else's package advice.

- **Decided:** a package contribution, when one exists, is reported with
  `package` and `packageVersion` derived from Composer rather than trusted from
  its file, `authority: "package"`, and `appliesTo` fixed to that Composer
  package by default. A package may augment its own namespace. An explicit
  override target is valid only below the same package authority; matching the
  path or name of a bundled core convention never overrides it.
- **Decided:** no discovery path or general loader is added yet. There is one
  canonical package skill in this repository and no real third-party producer
  whose layout, update cycle or override need has established a common shape. A
  fixture written now would prove only that code can read the format it just
  invented.
- **Assumed:** the first useful contribution will be procedural task guidance,
  not replacement facts about TYPO3 itself. Facts about a package remain its
  documentation; facts about TYPO3 remain versioned live documentation or the
  curated corpus.
- **Would falsify it:** one real extension ready to ship agent material. Add its
  scenario first, record the package and version in every answer, then implement
  the narrowest discovery path its package can actually publish. A second
  producer is what justifies extracting a shared format and override rules.

## 2026-07-30 — The Fluid engine gets no version axis of its own, because the core pins it

A note said Fluid has no empty array literal, and writing that down turned out to
need three engines rather than one: Fluid's major is not the TYPO3 major, and
nothing in `knowledge/` had said which is which. It never came up before because
no Fluid statement had been version-bound.

- **Decided:** no second axis. Each covered branch pins the engine in its own
  `composer.json` — 12.4 on `^2.15.0`, 13.4 on `^4.6.1`, 14.3 and main on
  `^5.3.1` — so the Fluid major is a function of the TYPO3 major, and `since` /
  `until` on the TYPO3 major already carries it. A caller asking with a
  `targetVersion` is asking about an engine, whether they know it or not.
- **Decided:** verifying such a statement means fetching the engine, not reading
  the checkouts. `.checkouts/` has no `vendor/`, and `typo3fluid/fluid` is a
  Composer dependency rather than part of the mono repository, so the parser the
  statement is about is not in the tree the rest of the knowledge is checked
  against. What was done here — one throwaway directory per major with the engine
  required into it, and the behaviour rendered through a probe ViewHelper — is
  the procedure, and it is worth the twenty minutes: the note's own diagnosis
  («`{}` is a string») is what a reading of the source would plausibly have
  produced, and the measurement says null.
- **Would falsify it:** a branch loosening its constraint to span two engine
  majors, or a Fluid minor changing behaviour inside one — the strict argument
  processor arrived in 5 and is injectable, so 5.x turning lenient by default
  would make a `since: 14` statement wrong without any TYPO3 version moving.
  Either one and the engine needs its own field.

## 2026-07-30 — `provenance` is not the third spelling of `binding`, and stays

The plan was one field name for who an answer obliges, because it looked spelled
three ways: `binding: "core"` on the hints, `coreOnly: true` on the task
intents, `provenance: "core-only"` in `knowledge/server-scope.json`. Two of them
are the same axis. The third is not, and reading its values is what showed it.

- **Decided:** the intents move to `binding`, because `coreOnly: true` asks
  exactly the question `binding` asks and answers it in a boolean — which is the
  shape that cannot carry a second audience the day one is needed.
- **Decided:** `provenance` stays as it is. Its values are `core-only`,
  `transferable` and `installation`, and the third is not an obligation at all —
  it says the answer is read from the installation rather than from a snapshot.
  Folding it into `binding` would either drop that value or make "installation"
  something a caller is obliged by. Two fields overlapping on one value are not
  one field with a naming problem.
- **Would falsify it:** a fourth value arriving on either side that reads
  naturally in both — then they are the same axis after all and the merge is
  the entry that was right.

## 2026-07-30 — Three numbers now decide what a lookup answers, and they were measured, not reasoned

Scoring the hint text as well as its keywords is the change; keeping it from
answering everything is the part that took the work. Three constants came out of
it, and each was picked off a sweep over sixteen queries with a known right
answer rather than argued for. That is also what makes them worth writing down:
a number that fell out of one afternoon's corpus is a number the corpus can grow
out of.

- **Decided:** a term nothing in the corpus carries weighs `log(total) / 2`, as
  if the square root of the corpus held it. At zero — the previous behaviour —
  «how do I write a good sonnet» decayed into a query about *writing* and about
  *good*, and something always answers that. At full weight, one unfamiliar word
  sank queries the corpus does answer: nobody wrote «upload», and the storage
  hint stopped being the answer to «file upload storage configuration». The
  sweep was flat between a quarter and three quarters, so the middle was taken.
- **Decided:** a term found in a field longer than the corpus's ordinary field
  counts less, on a log scale. Without it the longest hint — over a thousand
  words — answered at full confidence whenever a query's words all appeared in
  it somewhere. This is the one that carried the precision: with the unknown-term
  weight already in place, turning dilution off put the returned hints back up
  from 33 to 49 for the same recall.
- **Decided:** the reference length is per corpus, not global — 200 words for
  the hints, 400 for the prose. Hint bodies are uncapped and differ by more than
  twenty times, so the mean is the meaningful reference; prose sections are cut
  at `MAX_SECTION_LENGTH`, so a section at that length is ordinary rather than an
  outlier. A single global value was tried first and it broke
  `typo3_script_lookup` on «php-cs-fixer and phpstan», where two sections each
  answer half of a two-word query and sat exactly on the floor.
- **Assumed:** the hint corpus keeps roughly its current shape. 200 is its mean
  body length today, and a corpus that doubles its typical hint makes the
  ordinary hint look like an outlier to this code.
- **Would falsify it:** hints getting longer on average — the dissolution of the
  architecture prose into this corpus is queued and moves in that direction —
  or a query that should hit going missing while its hint is plainly about it.
  Both show up in `bin/hints coverage` once that exists; until then the eight
  queries in `HintsTest` are the only tripwire, and they are a sample.
- **Decided since:** the stopword list is English, so German filler words behave
  like unknown technical terms and sink the whole query. «wie lege ich ein neues
  Content-Element an» reached `content-elements` before this change and does not
  now. It was left open here rather than papered over with a lower floor, and
  the measurement that followed settled it the other way round: the server is
  queried in English and says so to the agent (`R-AUD-6`). A lexical matcher
  over an English corpus has nothing else to match on, so the alternative was
  never a stopword list — it was translating the knowledge base. Twelve German
  queries also surfaced two genuine bugs on the way, both language-independent
  and both fixed under `R-ANS-8b`: a three-letter term matched as a prefix, and
  `appliesTo` matched by plain substring.

## 2026-07-29 — The index of worked examples is curated, and existence is all that is checked

An index of directories inside the core is the kind of answer that rots
silently: a moved path leaves a caller reading a miss as "I looked in the wrong
place". `bin/verify-catalog` therefore re-derives each entry's range from
whether the path is there in every covered checkout, the way the component and
system-extension catalogs are derived.

- **Decided:** the path is machine-checked, the sentence about what it is a
  reference for is not. Existence is what a script can know; whether
  `theme_camino` still demonstrates the layout is a judgement, and a wrong
  judgement here costs one read, while a wrong path costs the caller their
  trust in the tool.
- **Assumed:** the list stays short enough to reread by hand when a major lands
  — seven entries, each a subject a session actually needed. The value is one
  line per directory, not coverage.
- **Would falsify it:** an entry whose path still exists while what is inside
  moved — `Build/tests/playwright/e2e` surviving a rewrite that puts the
  fixtures somewhere else. Then the check has to descend into the entry, and
  the honest form is naming the two or three files that carry the shape rather
  than the directory.

## 2026-07-29 — Why "project work is out of scope" kept coming back

It has been settled several times that the extension author and the site
developer are audiences of this server, and the claim that they are not kept
reappearing. Where it came from, found by reading every place that states a
boundary:

- `knowledge/server-scope.json` carried it as a `doesNotCover` entry — "The
  knowledge base is scoped to contributing to the core" — and that entry is the
  canonical text anyone writing about scope copies from.
- `knowledge/typo3-core-architecture.md` opened its TypoScript section with
  "Configuring an installation with TypoScript is out of this server's scope".
  It is a knowledge document, so the sentence was also handed to callers.
- `Scope::OUTSIDE_CORE_NOTICE` said the server "only knows the core's own
  conventions". Every tool opens with it, so the framing was reinforced in the
  answers themselves.
- `requirements.md` says the opposite in R-AUD-1 and R-AUD-2 and flags the
  conflict — but R-AUD-2 is **open**, and nothing decided which side wins.

- **Decided:** all three prose sources now say what is actually true, and the
  contradiction is guarded: `ScopeTest::whatTheScopeExcludesIsNotWhatTheServerAnswers`
  fails if the declared scope excludes a subject that has a hint.
- **Assumed:** the remaining pull is the name `outsideCore`. It is a scope word
  for what is really a payload rule — "leave out what only the core repository
  has" — and each session that touches it re-derives a scope sentence from the
  name.
- **Would falsify it:** the claim reappearing anyway, in a place the guard does
  not read — a tool description, the readme, a hint. Then the flag has to be
  renamed to what it decides (`coreRepositoryOnly`, or the audience of
  R-AUD-2), rather than the sentences being corrected one at a time.

## 2026-07-29 — A hint about typo3/testing-framework is verified against tags, not against the checkouts

`project-extension-tests` is the first hint whose subject is not the core: the
phpunit boilerplate, the environment variables, the extension-path resolution
and `setUpFrontendRootPage()` all live in `typo3/testing-framework`, a package
with its own release cycle that `.checkouts/` does not contain.

- **Decided:** verify it against the tags that pair with the covered majors —
  7.1.1 (v12), 8.3.3 (v13), 9.6.1 (v14) and `main` (v15) — read from a clone of
  the package repository, and leave the statements unbound where all four agree.
  Only what the core itself decides carries a range: `SiteWriter` against
  `SiteConfiguration`, and the site set that `setUpFrontendRootPage()` drops.
- **Assumed:** those behaviours are stable within a major of the package. They
  have survived four majors unchanged, and the two that would hurt most — the
  hardcoded `clear` flag and the document-root-relative extension paths — are
  identical in all four.
- **Would falsify it:** a testing-framework release that changes one of them
  inside a major. Nothing here would notice: `bin/verify-catalog` re-reads the
  core checkouts, and this package is not one of them. The cheap fix is to teach
  that script the tag-to-major pairing; the honest alternative is to bind the
  statements to the package version instead of the TYPO3 one, which the hint
  format has no field for.

## 2026-07-29 — Sitepackage work is answered from the General category

The hint categories mirror the technical domains: PHP, TypoScript, Fluid,
Backend CSS, Backend TypeScript, General. Building a section of a website
crosses all of them at once — a TCA file, a data processor in TypoScript, a
YAML route enhancer, a Fluid template — and the task text that asks for it
names none of them, so a domain-scoped hint is unreachable exactly when it is
needed.

- **Decided:** `sitepackage-layout` and `frontend-records` go into
  `general.json`, whose category is always selected. A new category file would
  never match: `Domains::hintCategories()` returns a fixed list, and a category
  outside it is loaded and then filtered away.
- **Decided:** "sitepackage" and "content element" also became domain keywords
  for Fluid and TypoScript, and frontend markers besides — so such a task pulls
  the page rendering and site set hints too, and the backend's own CSS and
  TypeScript conventions are withheld from it.
- **Assumed:** the noise this adds is smaller than the miss it removes. Two
  entries in the always-on category is the cost, paid by every query.
- **Would falsify it:** a backend-only task that comes back with the sitepackage
  layout because it mentioned a content element, or a General category that
  keeps growing until it is what every answer is made of. The fix in that case
  is a category for the audience rather than for the domain, which is a larger
  change than this one earns today.

## 2026-07-29 — A catalog entry is bound whole, and the binding is derived

The architecture hints bind one statement at a time. A component entry has no
statements: it is markup, a class list and a custom-property contract that were
read off one revision together, and a caller pastes them together.

- **Decided:** `since`/`until` sits on the entry rather than on its fields. The
  finer split — this variant since v14, that property since v13 — is what the
  Sass sources already say, and it would put four ranges in an answer whose
  reader wants one question answered: can I paste this.
- **Decided:** an entry that does not hold on the stated version is withheld,
  not qualified. A qualified class name still ends up in a stylesheet, and a
  custom property that does not exist fails in a browser without an error.
- **Decided:** the binding is derived by `bin/verify-catalog`, not judged. An
  entry holds on a version when every Sass file it names exists there and every
  class and custom property it names that the newest covered version writes in
  its Sass is written there too — plus, for a custom element, its tag name in
  the TypeScript that defines it.
- **Assumed:** what the core writes in its own Sass is the evidence. Classes
  Bootstrap generates from a state map — `btn-secondary`, `badge-secondary`,
  `callout-warning` — appear in no stylesheet on any covered version and are
  therefore not evidence for or against; including the TypeScript corpus in that
  check made an incidental v14 usage of `btn-secondary` bind Buttons to v14.
- **Evidence:** run against 12.4, 13.4, 14.3 and main. 22 of 25 entries bind:
  13 from v13, where the `--typo3-*` custom-property contract arrives, and 9
  from v14. Avatar, the infobox ViewHelper and the record search box hold
  everywhere.
- **Would falsify it:** an entry whose markup changed while every name in it
  stayed — the derivation reads names, not structure, and would call that
  unchanged. Also a range with a hole in it, which `derivedSince()` reports as
  no binding at all, on the grounds that such an entry needs splitting rather
  than a number.

## 2026-07-29 — The prose is not bound; it says which half it is

The architecture hints now carry `since`/`until` on every statement that
changed inside the covered range. The markdown documents below `knowledge/` are
the long form of the same subjects and carry nothing — the event listener
attribute, the Fluid file extension, the backend tokens and the translation
domains are all described there as the shape, with no range.

- **Decided:** no binding mechanism for prose. It would need per-bullet
  metadata in markdown, a parser, a renderer and a test, for a corpus that is
  read whole rather than filtered, and the same statements are already bound
  where a caller acts on them.
- **Decided:** every prose answer says so instead, in one sentence from
  `Tools::renderSections()`, and names `typo3_architecture_lookup` with
  `targetVersion` as where the bound form is. One sentence in one place, so a
  caller who learns it in a rule answer finds it unchanged in a script answer.
- **Assumed:** a caller that is told the prose is unfiltered will ask the hints
  when the version matters. The alternative — filtering prose sections by the
  ranges of the hints that share their subject — would guess at a mapping
  nobody declared.
- **Would falsify it:** a prose section that misleads on an LTS badly enough
  that the sentence does not save it, which would mean that statement belongs
  in the hints rather than in the document.

## 2026-07-29 — Two profiles, because a third one would have been the same set

The note that asked for this described one packaging: the installation-backed
tools and the transferable domains, without the core contribution surface. The
item written from it named three profiles — `core`, `project`, `all`.

- **Decided:** two, `all` and `project`. A `core` profile would have had to
  leave the installation-backed tools out to differ from `all`, and it must not:
  a core checkout is an installation, and looking an icon identifier or a label
  up in it is exactly what writing a patch needs. Everything else made `core`
  and `all` the same set under two names.
- **Decided:** the tools left out are named per tool rather than derived from
  the `provenance` of the topics they answer. `typo3_rule_lookup` answers two
  core-only topics and two transferable ones, so no formula over the field gets
  it right; the field is the input to the decision, not the decision.
- **Assumed:** what the three omitted tools also carried and what does transfer
  is reachable without them — the commit conventions through
  `typo3_commit_message_guide` with `workflow="project"`, the backend CSS and
  subsystem conventions through `typo3_architecture_lookup`, and every prose
  document verbatim through its `typo3://core` resource.
- **Decided:** the resources are not filtered, only the tools and the maps that
  route to them. A document is read when it is asked for by name; a tool is
  offered to every session whether it fits or not.
- **Assumed:** deriving the profile from the kind of installation is right more
  often than it is wrong, and being wrong costs one environment variable. It
  does sit against R-AUD-2 — the audience is a property of the task, not of the
  directory — and the tool list cannot vary per task, so this is the one place
  the directory decides.
- **Would falsify it:** someone contributing to the core from a session started
  in a site installation, who then has to set `TYPO3_MCP_PROFILE=all` to get the
  rules back — or a deployment with no installation to read at all, which is
  where a profile that leaves out the installation-backed half would earn its
  name.

## 2026-07-29 — A version range is data on the statement, not a sentence in it

The knowledge base covers four TYPO3 lines — 12.4, 13.4, 14.3 and main — and
until now every statement was written to hold on all of them. That was the rule
in `AGENTS.md`: no version numbers, and where a fact is branch-specific, write
the rule that holds everywhere. It bought branch-neutrality by leaving out
everything that is not.

- **Decided:** bind the statement, not the hint, and bind it with `since` and
  `until` fields rather than words in the sentence. A subsystem does not change
  wholesale — one sentence in it does, and the other six are unaffected — so
  per-hint versions would duplicate what did not change.
- **Decided:** a bare string stays a valid statement and means "holds on every
  covered version". The two hundred existing bullets are exactly that, so
  nothing had to be rewritten to introduce the model.
- **Assumed:** majors are granularity enough. `13.4` and `13.3` do differ, but
  the covered lines are one release line per major, and a range that cannot
  express a minor is better than a range nobody maintains.
- **Assumed:** filtering is better than qualifying once a version is known. A
  statement that does not hold is left out rather than shown with a warning,
  because an answer the caller has to filter is an answer they will not filter.
- **Would falsify it:** a statement that is true on 12.4 and 14 but not on 13 —
  the range cannot say that, and it would have to become two statements.

## 2026-07-29 — The version comes from the core package, not from the console

The catalogs are pinned to one revision and every answer was phrased as timeless
fact, while the server had the other number all along: the installation it reads
for icons and labels states its own version. Both were known and never
contrasted.

- **Decided:** read it from the core package's `Typo3Version` class rather than
  ask `bin/typo3 --version`. The version decides whether an answer holds, so it
  has to be available exactly when the console is not — and a console call costs
  a TYPO3 boot, on every answer that carries the catalog pin.
- **Assumed:** the `VERSION` constant in that class stays where it is and stays
  a literal. It has been in `Classes/Information/Typo3Version.php` on every
  branch this was checked against, and a missing or unparseable one yields null,
  which reads as "nothing to compare with" rather than as a wrong version.
- **Decided:** translation domains are the one answer that is withheld rather
  than qualified below a version. `13.4` has no `TranslationDomain*` class at
  all, `14` ships the mapper, so the domain string is syntactically fine and
  resolves to nothing: the label renders empty, at runtime, silently. Everything
  else the catalogs hold is markup and class names, where a qualified answer is
  still worth having.
- **Would falsify it:** a backport of the domain API into a 13.x patch release,
  which would make the constant in `Tools` wrong. It is one number in one place
  for that reason.
- **Would also falsify it:** a caller working on a version other than the
  installation the server found — the second checkout, the backport branch. The
  version is then read from the wrong place, and nothing accepts a stated one
  yet.

## 2026-07-29 — The installation is evidence about the task, and the weakest kind

`outsideCore` was decided by phrases. "bootstrap_package" as the area matched
none of them, so a third-party extension got the core's changelog rules until
the caller wrote "not TYPO3 core" into the prose. Three structural signals are
now consulted, and the installation this server was started in is one of them.

- **Decided:** an ordering rather than a vote. Core work named outright wins,
  then an outside-core marker, then an area the installation knows as somebody's
  extension, then a path in extension layout, and last the kind of installation.
  Each step is more specific than the one below it, so the general signal never
  overrules a statement about the task.
- **Assumed:** in a Composer project, work is not core contribution unless
  something says it is. That is what the checkout is: core patches are written
  in a core monorepo, and a site installation that vendors `typo3/cms-*` is not
  one.
- **Assumed:** a path starting with `Classes/`, `Configuration/` or
  `Resources/` is inside a package. From the core root nothing is named that
  way — `typo3/sysext/<key>/` or `Build/` comes first.
- **Would falsify it:** a core contributor who runs their client from a site
  installation that has the core checked out somewhere else, or who passes paths
  relative to the system extension directory they are standing in. Both then
  read as extension work, and the way out is to say `typo3/sysext/` once.
- **Would also falsify it:** `TYPO3_MCP_ROOT` pointing at a site installation
  for the label and icon lookups while the questions are about the core. The
  variable now moves the boundary too, which it was not introduced to do.

## 2026-07-29 — The frontend is recognised by name, and only the two UI sections go

The backend CSS hints were answered for a Bootstrap 5 theme extension, where
every one of them is inverted: treat Bootstrap as legacy, prefer `--typo3-*`
properties, work in both backend color schemes. The paths gave nothing away —
`Resources/Public/Scss/bootstrap.scss` and `Build/Sources/Sass/_variables.scss`
are shaped exactly like core paths, and the second one is one.

- **Decided:** the task text decides. A frontend marker with no backend marker
  withholds the `Backend CSS` and `Backend TypeScript` categories, and the
  answer says which and why. `Scope::isOutsideCore` was the obvious lever and
  is the wrong one: an extension's backend module has backend CSS, and the core
  renders a frontend.
- **Assumed:** words are enough here although R-SCO-1 says they are not for
  `outsideCore`. The difference is the cost of being wrong: withholding leaves
  a caller with a pointer to docs.typo3.org, while the wrong direction hands
  over four confident instructions to rewrite a working theme.
- **Assumed:** naming the categories `Backend CSS` and `Backend TypeScript` is
  worth the churn in every rendered answer. A category label is read on every
  hit, a boundary note only when it fires.
- **Would falsify it:** a core contributor working on the frontend rendering of
  `fluid_styled_content` who loses the CSS hints they wanted. `styleguide` and
  `backend` are the escape, and they are named in the notice.

## 2026-07-29 — The commit workflow is asked for, not inferred

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::isOutsideCore` reads the
paths and the task text. This one does not.

- **Decided:** an argument, defaulting to `core`. A commit message carries no
  paths, and the one thing it does carry — the subject text — describes the
  change, not the repository it lands in. Inferring from it would be guessing
  from prose, which is exactly what R-SCO-1 exists to stop.
- **Assumed:** the caller knows which repository they are committing in, and
  the pointer at the end of every answer is enough for them to find the other
  mode. The default is `core` because dropping rules must be something the
  caller asked for, never something a typo achieves.
- **Would falsify it:** agents committing in a project repository and never
  passing the argument, so the hard `missing-issue` error stays the normal
  answer there. The next step would then be for `typo3_task_guide`, which does
  compute `outsideCore`, to hand the workflow to the commit guide by naming it
  in the follow-up tool call it suggests.
- **Assumed:** `[SECURITY]` stays refused for core work. The keyword exists in
  the core's history — the Security Team writes those commits — so its absence
  from the enum was a gap, but accepting it for a contributor would be a wrong
  answer with worse consequences than a missing one.

## 2026-07-29 — A missing release target becomes a placeholder, not `main`

`typo3_commit_message_guide` appended `Releases: main` when the caller named no
release, and warned `missing-releases` in the same answer. Two ways out: stop
warning, or stop filling in. It now fills in `RELEASE_TARGET` and keeps the
warning, the way a missing issue has always produced `Resolves: #ISSUE_NUMBER`.

- **Decided:** the draft states what it does not know, in the place where the
  answer belongs. A placeholder is visible in a `git commit` editor and in a
  diff; a plausible default is not, and `main` is the wrong answer for every
  backport.
- **Assumed:** the checks are read. A caller that copies the draft without
  reading them now commits `Releases: RELEASE_TARGET`, which is a worse commit
  message than `Releases: main` would have been — but a visibly broken one
  rather than a quietly wrong one, and Gerrit rejects it.
- **Would falsify it:** the placeholder showing up in a pushed commit. Then the
  guide would have to refuse the draft outright instead of marking it.

## 2026-07-29 — What is core-only is decided per line, by what it names

`typo3_task_guide` now drops core-only material outside the core. What counts
as core-only is not a flag on each entry but a check on its text: does it name
something that exists in the core repository and nowhere else —
`typo3/sysext/`, `Build/Scripts/`, Gerrit, a Change-Id, the core branch policy.

- **Decided:** a mechanical check over the rendered line, in `Scope`, applied
  to the checklist, the checkout discovery steps and the follow-up tools. The
  alternative — marking every checklist item, every intent item and every scope
  entry in the knowledge files — is a flag on a hundred strings that has to be
  set correctly each time one is added, and forgetting it fails silently.
- **Assumed:** naming a core artefact is a reliable proxy for being unusable
  outside the core, and the cost of the two error directions is asymmetric: a
  transferable line dropped because it mentioned a core path as an example is a
  smaller loss than an unrunnable command handed over as a step.
- **Would falsify it:** a checklist item that has to survive although it names
  a core path — advice about reading the core as a reference rather than
  changing it would be exactly that. It would then need the flag after all.

## 2026-07-29 — A core-only intent asks for evidence, not for silence

`outsideCore` was to be the gate for the patch submission intent. It is not
enough: the reported task — "Maintain and extend the third-party TYPO3
extension bk2k/bootstrap-package … review TCA …" — does not trip a single
outside-core marker, because "third-party TYPO3 extension" is not the phrase
the list carries. Gating on the flag alone would have left the note's own case
answered exactly as before.

- **Decided:** a core-only intent needs positive evidence — a `typo3/sysext/`
  path, or Gerrit, Forge, "TYPO3 core" named outright. Outside the core it is
  dropped, and where nothing says either way it is demoted to the conditional
  match the catalog already models, so the answer offers it rather than states
  it.
- **Assumed:** `coreOnly` is a property of the intent, not of the task, and
  patch submission is currently the only one. Deprecation, breaking change and
  changelog were considered and left alone: their subject is real work outside
  the core too, and it is their `checks` that are core-only, which R-SCO-2
  handles.
- **Would falsify it:** a core contributor whose task text names neither a
  sysext path nor Gerrit and who now gets the submission rules as conditional
  rather than as fact. That is the cost of not guessing, and the condition line
  is what keeps it cheap.

## 2026-07-29 — Outside the core the core test guide declines rather than adapts

`typo3_test_run_guide` recognises work outside the core and now returns no
suite at all, while `typo3_architecture_lookup` keeps its hints and drops only
their check commands.

- **Decided:** the difference between the two is what the payload is made of. A
  hint is a convention and travels; a suite is a command against a script that
  lives in the core repository and does not.
- **Assumed, and disproved on 2026-07-29:** nothing in `knowledge/` described
  how an extension runs its tests, so anything the guide offered instead would
  have been invented. `project-extension-tests` now carries that harness,
  verified against the matching `typo3/testing-framework` tags. The
  `typo3_test_run_guide` still declines because its answer shape is a core suite
  invocation; the `typo3-extension-testing` skill takes the other branch,
  verifies the checkout's harness, and routes setup or repair through the
  extension-test hint and versioned documentation before adding coverage.
- **Would falsify the remaining decision:** a project-suite source whose answer
  has the same targeted invocation shape as `typo3_test_run_guide`. Until then,
  project commands come from the checkout and the task skill, not from a core
  suite adapted by analogy.

## 2026-07-29 — A label query is words, and the console is asked with a regex

`language:domain:search --search=` matches one literal string, so the words of a
query had to be recomposed into something the console can answer at once.

- **Decided:** the words go over as `--regex=/(one|two)/i` — the union — and the
  intersection is taken here. One call per query rather than one per word,
  because a console call boots TYPO3, and the union is also what makes "save
  alone matches 65 labels" answerable without asking a second time.
- **Assumed:** `--regex` is available wherever the command itself is. It was
  added in the same commit as `--search`, `--json` and the command
  ([TASK] Add CLI command and service to search labels, on 14.0 and later), so
  an installation that answers the one answers the other.
- **Assumed:** matching is a plain case-insensitive substring on both sides, not
  a word boundary as elsewhere in this server. A trans-unit id is
  `labels.save_document` and an underscore is a word character, so anchoring
  would drop exactly the ids a caller searches by.
- **Assumed:** a console that exits 0 without a JSON payload found nothing.
  That is what this command does — it prints `[WARNING] No language resource
  files found.` and returns SUCCESS — and no other command this server calls
  answers `--json` with anything but JSON.
- **Would falsify it:** a command that exits 0 and prints nothing usable for a
  reason other than an empty result; the answer would then be a confident "none"
  where nothing was established. The exit code is the only signal being read.

## 2026-07-29 — The unanswered result keeps its shape and gains a reason

Two notes asked for the unavailable case to stop looking like an empty one. One
proposed dropping `matchCount`, `icons`, `found` altogether and returning an
error-shaped object instead, or renaming `answeredBy: "nothing"` to something
that cannot be read as "no source had it".

- **Decided:** the shape stays, an `unavailable` object carries the reason, and
  `found` is null rather than false when nothing was consulted. A field a schema
  requires has to be present on every path through the tool, and dropping keys
  in one case would make the declared output schema a shape a client cannot
  rely on — which is the same defect one level up.
- **Assumed:** a caller that reads `unavailable.reason` is better served than
  one that has to interpret an enum value, so renaming `nothing` buys little
  and breaks every client that already matches on it.
- **Would falsify it:** clients ignoring `unavailable` and still reading a miss
  as a registry answer. `isError: true` on the result is then the next lever —
  bluntest, and it would make the answer an error rather than an answer, which
  is why it was not taken first.

## 2026-07-29 — Three audiences, and the positioning that has not caught up

The server is to serve core contributors, extension authors and site developers
(R-AUD-1 to R-AUD-4). What it currently *contains* is core knowledge, and what
it says about itself matches that: `knowledge/server-scope.json` opens with "a
curated knowledge base for contributing to the TYPO3 core".

- **Decided:** the outward description stays core-first until there is
  non-core knowledge to describe. A promise is made when it can be kept; the
  requirement is the record that it is meant to be.
- **Assumed:** the boolean `outsideCore` cannot carry this. An audience has at
  least three values and an honest fourth — unknown — and the flag was written
  when "not core" was the only distinction that existed.
- **Assumed:** the audience is not readable from the checkout alone, because
  extension development happens inside site installations. Any detection that
  keys on the installation kind alone will be wrong for that case, which is a
  common one rather than an edge.
- **Would falsify it:** if a signal turns out to identify the audience reliably
  on its own — the presence of `typo3/sysext/` in the touched paths comes
  closest — the combining logic is unnecessary complexity.

## 2026-07-29 — Discovery honours the declared vendor-dir and bin-dir

Closes the notes about `.build/bin/typo3` being unreachable and about the
extension checkout not being recognised as an installation at all.

- **Assumed:** what the root `composer.json` declares is enough to find both the
  packages and the console. Composer's `config.vendor-dir` and `config.bin-dir`
  are the only two ways either moves in practice, and everything else — DDEV,
  the interpreter choice — was already right and simply never got a binary.
- **Assumed:** invoking the console through a path relative to the installation
  root works inside DDEV as it does on the host.
- **Evidence:** `bootstrap_package` on this machine — 21 packages found,
  console resolved as `ddev exec -- .build/bin/typo3` on PHP 8.5, and the 29
  `content-bootstrappackage-*` icons that were previously reported as
  non-existent.
- **Would falsify it:** an installation whose console is invoked from somewhere
  other than the root — a DDEV project whose container working directory is the
  docroot rather than the project root would need an absolute path or a `cd`.
  Also an absolute `bin-dir`, which is accepted by Composer and ignored here.

## 2026-07-29 — The root package counts as an installed package

- **Assumed:** in an extension development checkout, the extension being edited
  is the root package and is meant to be part of every answer about "this
  installation" — its icons and labels are as registered as any dependency's.
- **Assumed:** a root package alone is not an installation. The root is only
  added when Composer's metadata yielded packages, so an extension repository
  whose dependencies were never installed still reports no installation rather
  than one holding a single package and no console.
- **Would falsify it:** a monorepo whose root declares a TYPO3 package type but
  is not the thing being worked on, or a setup that installs the root into the
  vendor directory as well — the two entries then resolve to the same realpath
  under one key, which is intended, but has not been seen in the wild here.
