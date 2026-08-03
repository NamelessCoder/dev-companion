# What was decided, and on what evidence

A feedback is archived by the commit that closes it, and the commit message
says what changed and why. What a commit message cannot carry is the part that
may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

That is what this directory is for. One entry per decision worth revisiting. An
entry is not a changelog line — a change nobody would need to reconsider does
not belong here. When an assumption is later disproved, the entry stays and
gains a **Revoked on** line: the wrong assumption is the useful part, because
it names the place where the next one is likely to sit.

## Where an entry lives

One decision is one file, named after its id, in the group its id names. The
group is what the decision is about, and the prefix carries it, so a file's id
decides its path and two entries cannot quietly share a number.

The number is three digits wide, in the file name and in the id alike, because
that is what lists a group in the order it was written: unpadded, `dis-10` sorts
between `dis-1` and `dis-2` in every directory listing and in anything that
compares the ids as text. A requirement is numbered the same way, so one habit
covers both. `bin/cli decisions:check` fails on any other width.

| Group                                     | What it is about                                        |
| ----------------------------------------- | ------------------------------------------------------- |
| [audience/](audience/readme.md)           | Who the server answers for, and how it says so          |
| [discovery/](discovery/readme.md)         | Which installation is read, and how                     |
| [answers/](answers/readme.md)             | What a lookup returns, and what decides it              |
| [knowledge/](knowledge/readme.md)         | What the corpus holds and how it is written             |
| [versions/](versions/readme.md)           | What a statement holds on                               |
| [catalog/](catalog/readme.md)             | The curated indexes and where their contract comes from |
| [scope/](scope/readme.md)                 | Core conventions where they apply, and nowhere else     |
| [guides/](guides/readme.md)               | What a returned draft is worth                          |
| [evidence/](evidence/readme.md)           | How this server is measured                             |
| [task-skills/](task-skills/readme.md)     | What an installed workflow owes the task                |
| [feedback/](feedback/readme.md)           | What the backlog has to stay usable for                 |
| [documentation/](documentation/readme.md) | How what is written here is written                     |
| [code/](code/readme.md)                   | How the source is laid out                              |

Each group's `readme.md` says what that group is about, and the listing at the
foot of it is generated from the files below it by `bin/cli decisions:index`, as
is the listing at the foot of this file.

An id is never reused, and an entry is never deleted: a decision that turned out
wrong is the one most worth reading, and it is revoked in place.

How one is written — the sections, what a later session adds, and when to go
back to one: [documentation/feedback/writing-a-decision.md](../documentation/feedback/writing-a-decision.md).
`bin/cli decisions:check` holds every file to that shape.

## Every decision, newest first

What is not listed as revoked still holds. `confirmed` marks the ones somebody
went back to and found standing; the rest are open, which is the ordinary case
and not a defect.

- [`D-ANS-033`][D-ANS-033] — The review server is read anonymously, and the answer says what that leaves out · 2026-08-03
- [`D-ANS-034`][D-ANS-034] — A source outside this package answers JSON, or it did not answer · 2026-08-03
- [`D-ANS-035`][D-ANS-035] — The matcher entry is owed to what the changelog tag claims · 2026-08-03
- [`D-ANS-036`][D-ANS-036] — A query written in Fluid tags is searched in the book that documents them · 2026-08-03
- [`D-ANS-037`][D-ANS-037] — A compound rule query is owed the section its score prefers, and a miss that names the words · 2026-08-03
- [`D-ANS-038`][D-ANS-038] — The tracker is searched by words as well as read by number · 2026-08-03
- [`D-COD-004`][D-COD-004] — What leaves this process goes through one seam · 2026-08-03
- [`D-DOC-009`][D-DOC-009] — Prose names what counts rather than the count · 2026-08-03
- [`D-DOC-010`][D-DOC-010] — `targetVersion` opens with one sentence and diverges after it · 2026-08-03
- [`D-DOC-011`][D-DOC-011] — A schema is written as the shape it validates · 2026-08-03
- [`D-DOC-012`][D-DOC-012] — The second root is an installation this repository writes · 2026-08-03
- [`D-EVI-006`][D-EVI-006] — One installation per covered version, kept and started · 2026-08-03
- [`D-FBK-025`][D-FBK-025] — A judgement reads the corpus, decides the shape, and sets the priority · 2026-08-03
- [`D-FBK-026`][D-FBK-026] — The ladder needs an outcome that builds something · 2026-08-03
- [`D-FBK-027`][D-FBK-027] — The server builds what costs its caller round trips · 2026-08-03
- [`D-FBK-038`][D-FBK-038] — What decides a breaking removal is the caller, not the marker · 2026-08-03
- [`D-FBK-039`][D-FBK-039] — A mangled name is rewritten once, and the comparison carries the rest · 2026-08-03
- [`D-GUI-003`][D-GUI-003] — The wrapping conflict is resolved in the answer rather than in silence · 2026-08-03
- [`D-GUI-004`][D-GUI-004] — A review brief states the removal surface rather than matching it · 2026-08-03
- [`D-GUI-005`][D-GUI-005] — The product premise is one statement, on the brief every task passes through · 2026-08-03
- [`D-GUI-006`][D-GUI-006] — A task that changes nothing is a change type of its own · 2026-08-03
- [`D-GUI-007`][D-GUI-007] — The brief carries a selection of the hints and says whose they are · 2026-08-03
- [`D-KNW-029`][D-KNW-029] — A hint names the domains it is asked from, and the file names the subject · 2026-08-03
- [`D-KNW-030`][D-KNW-030] — A hint is one question, and the DataHandler family is six of them · 2026-08-03
- [`D-KNW-031`][D-KNW-031] — A suite is a property of the domain, not of the hint · 2026-08-03
- [`D-KNW-032`][D-KNW-032] — The corpus is filed by question, and two splits were taken back · 2026-08-03
- [`D-KNW-033`][D-KNW-033] — Every hint names the domains it is asked from, and none is `any` · 2026-08-03
- [`D-KNW-034`][D-KNW-034] — The file is the subject, and JavaScript is not a domain of its own · 2026-08-03
- [`D-KNW-035`][D-KNW-035] — The corpus and the tool that answers from it are called hints · 2026-08-03
- [`D-KNW-036`][D-KNW-036] — The standards check handed over is the one that cannot pass empty · 2026-08-03
- [`D-KNW-037`][D-KNW-037] — A content-element preview draws the element's own payload, and the corpus names the fields · 2026-08-03
- [`D-KNW-038`][D-KNW-038] — A hint is reached by the role of a file rather than by the extension it sits in · 2026-08-03
- [`D-KNW-039`][D-KNW-039] — The type a changelog entry owes is stated in prose and the skeleton stays a hint · 2026-08-03
- [`D-KNW-040`][D-KNW-040] — What asserts a rendered output is a gap this server owns · 2026-08-03
- [`D-KNW-041`][D-KNW-041] — The checkout a suite is started in supplies its own dependencies · 2026-08-03
- [`D-KNW-042`][D-KNW-042] — What the image pipeline does below the task layer is a gap this server owns · 2026-08-03
- [`D-KNW-043`][D-KNW-043] — A rule about what an API may be used for carries the strength of the claim and the source it was read from · 2026-08-03
- [`D-SKL-005`][D-SKL-005] — Core contribution earns two task skills, one for reviewing a patch and one for creating one · 2026-08-03
- [`D-SKL-006`][D-SKL-006] — The site-new cluster earns the route into the skill that owns the task · 2026-08-03
- [`D-SKL-007`][D-SKL-007] — Every disposition a review makes carries its evidence · 2026-08-03
- [`D-SKL-008`][D-SKL-008] — A review reads the review the patch is already in · 2026-08-03
- [`D-SKL-009`][D-SKL-009] — The rule that keeps not landing is written as an act with an object · 2026-08-03 · confirmed
- [`D-SKL-010`][D-SKL-010] — The assessment that precedes a core patch reads the issue and the review server · 2026-08-03
- [`D-ANS-005`][D-ANS-005] — A question that is not supported here is answered in a shape of its own · 2026-08-02
- [`D-ANS-006`][D-ANS-006] — An identifier is found however it is spelled · 2026-08-02
- [`D-ANS-007`][D-ANS-007] — Two shapes for "not answered", one word for why · 2026-08-02
- [`D-ANS-008`][D-ANS-008] — A number a reader cannot reproduce is read as wrong · 2026-08-02
- [`D-ANS-009`][D-ANS-009] — A shipped-file deprecation is found by the tool that lists the file · 2026-08-02
- [`D-ANS-010`][D-ANS-010] — "Does it still work" is a question for the manual, not the changelog · 2026-08-02
- [`D-ANS-011`][D-ANS-011] — A scope answer states what a manifest declares, and the comparison is the audit's · 2026-08-02
- [`D-ANS-012`][D-ANS-012] — An `oneOf` alternative is stated where the caller composes the call · 2026-08-02
- [`D-ANS-013`][D-ANS-013] — What runs a project is a placement, not a missing answer · 2026-08-02
- [`D-ANS-014`][D-ANS-014] — The extension answer enumerates registrations, not files — and a registration is one wherever it is declared · 2026-08-02
- [`D-ANS-015`][D-ANS-015] — A registration the extension answer misreads is inside its boundary, not evidence about where it runs · 2026-08-02
- [`D-ANS-016`][D-ANS-016] — A miss names the query that would have hit, not only the reach of each word · 2026-08-02
- [`D-ANS-017`][D-ANS-017] — A union-typed argument gets the wording a client can compose against · 2026-08-02
- [`D-ANS-018`][D-ANS-018] — A plugin is a kind of content element, not one whose template is missing · 2026-08-02
- [`D-ANS-019`][D-ANS-019] — A FlexForm, a site set and a form set are read from the file names and call shapes core itself reads them by · 2026-08-02
- [`D-ANS-020`][D-ANS-020] — A deprecation is answered by the version that removes it · 2026-08-02
- [`D-ANS-021`][D-ANS-021] — A manual query is told what short buys, because the index is a table of contents · 2026-08-02
- [`D-ANS-022`][D-ANS-022] — The matcher takes a hyphenated compound apart, measured over the corpus first · 2026-08-02
- [`D-ANS-024`][D-ANS-024] — A rule reaches only the task that already names its subject · 2026-08-02
- [`D-ANS-025`][D-ANS-025] — A query a hint carries whole is not diluted out of it · 2026-08-02
- [`D-ANS-026`][D-ANS-026] — The ViewHelper reference is indexed, and a manual carries the collection it is published in · 2026-08-02
- [`D-ANS-027`][D-ANS-027] — The Extbase fork is placed where a caller who has not chosen passes · 2026-08-02
- [`D-ANS-028`][D-ANS-028] — A two-letter query word is searched for, and the stopword list is what keeps the others out · 2026-08-02
- [`D-ANS-030`][D-ANS-030] — The changelog matcher runs over the title it prints · 2026-08-02
- [`D-ANS-031`][D-ANS-031] — The core answer names the tool that runs the suites · 2026-08-02
- [`D-ANS-032`][D-ANS-032] — The dilution reference of the manual ranking is the length of an ordinary title · 2026-08-02
- [`D-AUD-004`][D-AUD-004] — Every client is offered every tool, and the answer says who it obliges · 2026-08-02
- [`D-COD-003`][D-COD-003] — A directory is read through symfony/finder · 2026-08-02
- [`D-DIS-007`][D-DIS-007] — The DDEV console is named by the mount, not by the variable · 2026-08-02 · confirmed
- [`D-DIS-008`][D-DIS-008] — The columns TYPO3 derives are reachable where the database server is · 2026-08-02 · confirmed
- [`D-DIS-009`][D-DIS-009] — Installed is one step short of callable, and the install is what says so · 2026-08-02 · confirmed
- [`D-DOC-003`][D-DOC-003] — A decision says what came back, and a requirement says what it rests on · 2026-08-02
- [`D-DOC-004`][D-DOC-004] — A requirement is written in the same sections as a decision · 2026-08-02
- [`D-DOC-005`][D-DOC-005] — A number is three digits so a group lists in order · 2026-08-02
- [`D-DOC-006`][D-DOC-006] — A recording says what it is of, and nothing fails on its age · 2026-08-02
- [`D-DOC-007`][D-DOC-007] — One page per tool, and the answer on it whole · 2026-08-02
- [`D-DOC-008`][D-DOC-008] — The calls that reach outside stay in the shared table · 2026-08-02
- [`D-EVI-004`][D-EVI-004] — The environment is made here, and the repository under review is not · 2026-08-02
- [`D-EVI-005`][D-EVI-005] — A registration nothing can reach is cleared, and the database goes with it · 2026-08-02
- [`D-FBK-011`][D-FBK-011] — The suite holds what one branch can be right about · 2026-08-02
- [`D-FBK-012`][D-FBK-012] — The queue comes first, and the sighting hands over one · 2026-08-02
- [`D-FBK-013`][D-FBK-013] — An empty queue is a state, not a failure · 2026-08-02
- [`D-FBK-014`][D-FBK-014] — Every stage is a directory, and closing is none · 2026-08-02
- [`D-FBK-015`][D-FBK-015] — A priority is a class, and the stamp is the rest · 2026-08-02
- [`D-FBK-016`][D-FBK-016] — A feedback waits on the board rather than behind it · 2026-08-02
- [`D-FBK-017`][D-FBK-017] — A judgement turns a feedback into work, and the work closes it · 2026-08-02
- [`D-FBK-018`][D-FBK-018] — A strength is evidence about a boundary, not about a decision · 2026-08-02 · confirmed
- [`D-FBK-019`][D-FBK-019] — A secret pasted into a feedback is taken out on the way in · 2026-08-02
- [`D-FBK-020`][D-FBK-020] — A session is charged per call, so the calls are what is budgeted · 2026-08-02 · confirmed
- [`D-FBK-021`][D-FBK-021] — A summary feedback is judged against its series, not on its own · 2026-08-02
- [`D-FBK-022`][D-FBK-022] — A feedback brings its card in the commit that brings it in · 2026-08-02
- [`D-FBK-023`][D-FBK-023] — A correction is judged by what its withdrawal moves · 2026-08-02
- [`D-FBK-024`][D-FBK-024] — A feedback about the caller's conduct toward its user names no surface · 2026-08-02
- [`D-KNW-005`][D-KNW-005] — One `Scope` replaced the four vocabularies · 2026-08-02
- [`D-KNW-006`][D-KNW-006] — A word for a thing administered from the backend adds no domain to a backend-only task · 2026-08-02
- [`D-KNW-007`][D-KNW-007] — A hint says whose it is in both directions · 2026-08-02
- [`D-KNW-008`][D-KNW-008] — Tooling is a row the answer crosses, not a dimension the corpus stores · 2026-08-02
- [`D-KNW-009`][D-KNW-009] — A domain keyword is a phrasing, not a word · 2026-08-02
- [`D-KNW-010`][D-KNW-010] — What the core reads from the environment is a gap this server owns · 2026-08-02
- [`D-KNW-011`][D-KNW-011] — A rule that names a defect names its correction · 2026-08-02
- [`D-KNW-012`][D-KNW-012] — `extension.neon` is PHPStan's filename, and the hint keeps the one include it means · 2026-08-02
- [`D-KNW-013`][D-KNW-013] — This repository's own sentence is reworded rather than indexed · 2026-08-02
- [`D-KNW-016`][D-KNW-016] — What an `f:else` does to the branch beside it is a gap this server owns · 2026-08-02
- [`D-KNW-017`][D-KNW-017] — A verification question is routed to the layer that verifies it · 2026-08-02
- [`D-KNW-018`][D-KNW-018] — What a datamap does to a relation field is a gap this server owns · 2026-08-02 · confirmed
- [`D-KNW-019`][D-KNW-019] — The corpus states that a functional test sees only what it primed · 2026-08-02
- [`D-KNW-020`][D-KNW-020] — What a preview template is handed is stated on both majors, and a field resolves by its TCA type · 2026-08-02
- [`D-KNW-021`][D-KNW-021] — A Fluid preview template replaces the content half, and the corpus names what is drawn around it · 2026-08-02
- [`D-KNW-022`][D-KNW-022] — The corpus states how long a per-class test database lives · 2026-08-02
- [`D-KNW-023`][D-KNW-023] — Which page may hold a record is a gap this server owns · 2026-08-02
- [`D-KNW-024`][D-KNW-024] — The Fluid namespace prefix is what a template question is written in · 2026-08-02
- [`D-KNW-026`][D-KNW-026] — Where a one-off script may not be written is a gap this server owns · 2026-08-02
- [`D-KNW-027`][D-KNW-027] — Which caches a change invalidates is a gap this server owns · 2026-08-02
- [`D-KNW-028`][D-KNW-028] — How a file becomes a processed one is a gap this server owns · 2026-08-02
- [`D-SCO-009`][D-SCO-009] — The brief is one brief, and names the paths a step is not for · 2026-08-02
- [`D-SKL-002`][D-SKL-002] — A focused audit narrows what is assessed, not the list it closes on · 2026-08-02
- [`D-SKL-003`][D-SKL-003] — A sweep is bounded by the changelog's own axes, not by the extension's vocabulary · 2026-08-02
- [`D-SKL-004`][D-SKL-004] — What a task does when the lookups run out is written for a review · 2026-08-02
- [`D-COD-001`][D-COD-001] — One file declares one class · 2026-08-01
- [`D-COD-002`][D-COD-002] — The upkeep CLI is a Symfony Console application · 2026-08-01
- [`D-DIS-006`][D-DIS-006] — The installation stays worked out from the directory the server was started in · 2026-08-01
- [`D-DOC-001`][D-DOC-001] — A table is written so it reads unrendered · 2026-08-01
- [`D-DOC-002`][D-DOC-002] — The prose rule is measured, and only the lead fails on it · 2026-08-01
- [`D-FBK-006`][D-FBK-006] — A name is cut where the feedback starts to differ · 2026-08-01
- [`D-FBK-007`][D-FBK-007] — How a todo is worked travels with the todo · 2026-08-01
- [`D-FBK-008`][D-FBK-008] — One todo is one file, and the queue is in the names · 2026-08-01
- [`D-FBK-009`][D-FBK-009] — A todo nobody can start waits where it says why · 2026-08-01
- [`D-FBK-010`][D-FBK-010] — `main` carries the state and the branch carries the work · 2026-08-01
- [`D-SCO-007`][D-SCO-007] — The signals are combined per call, and a call is not a path · 2026-08-01
- [`D-SKL-001`][D-SKL-001] — The order a task starts in is one file, and the reading comes last in it · 2026-08-01 · confirmed
- [`D-ANS-004`][D-ANS-004] — The instruction budget is 2048 characters, on one client's evidence · 2026-07-31
- [`D-AUD-003`][D-AUD-003] — The instructions carry the entry point, because the tool descriptions never arrive · 2026-07-31 · confirmed
- [`D-DIS-005`][D-DIS-005] — A registry with no console command is read by booting the installation · 2026-07-31 · confirmed
- [`D-EVI-001`][D-EVI-001] — Forward evidence comes from a review, not from a prompt that knows the answer · 2026-07-31 · confirmed
- [`D-EVI-002`][D-EVI-002] — A skill crossing is read rather than run · 2026-07-31 · confirmed
- [`D-EVI-003`][D-EVI-003] — A review runs the checks that cannot change the code · 2026-07-31
- [`D-FBK-001`][D-FBK-001] — The backlog is read out rather than enforced · 2026-07-31 · confirmed
- [`D-FBK-002`][D-FBK-002] — The order of the work is declared, not inferred · 2026-07-31 · confirmed
- [`D-FBK-004`][D-FBK-004] — The model is asked, because nothing else here can say it · 2026-07-31
- [`D-VER-004`][D-VER-004] — A supported range is a property of the package, not of the checkout · 2026-07-31 · confirmed
- [`D-ANS-002`][D-ANS-002] — Three numbers now decide what a lookup answers, and they were measured, not reasoned · 2026-07-30 · confirmed
- [`D-ANS-003`][D-ANS-003] — Retrieval stays lexical and runtime inspection stays narrow · 2026-07-30 · confirmed
- [`D-CAT-003`][D-CAT-003] — The component index is curated; its contract comes from the installation · 2026-07-30
- [`D-KNW-004`][D-KNW-004] — Package knowledge needs a producer before it needs discovery · 2026-07-30
- [`D-VER-003`][D-VER-003] — The Fluid engine gets no version axis of its own, because the core pins it · 2026-07-30 · confirmed
- [`D-AUD-001`][D-AUD-001] — The outward description stays core-first until there is non-core knowledge · 2026-07-29 · confirmed
- [`D-CAT-001`][D-CAT-001] — A catalog entry is bound whole, and the binding is derived · 2026-07-29
- [`D-DIS-001`][D-DIS-001] — The root package counts as an installed package · 2026-07-29 · confirmed
- [`D-DIS-004`][D-DIS-004] — The version comes from the core package, not from the console · 2026-07-29 · confirmed
- [`D-GUI-001`][D-GUI-001] — A missing release target becomes a placeholder, not `main` · 2026-07-29
- [`D-GUI-002`][D-GUI-002] — The commit workflow is asked for, not inferred · 2026-07-29
- [`D-SCO-002`][D-SCO-002] — A core-only intent asks for evidence, not for silence · 2026-07-29 · confirmed
- [`D-SCO-003`][D-SCO-003] — What is core-only is decided per line, by what it names · 2026-07-29 · confirmed
- [`D-SCO-005`][D-SCO-005] — The installation is evidence about the task, and the weakest kind · 2026-07-29
- [`D-SCO-006`][D-SCO-006] — Why "project work is out of scope" kept coming back · 2026-07-29
- [`D-VER-001`][D-VER-001] — A version range is data on the statement, not a sentence in it · 2026-07-29 · confirmed
- [`D-VER-002`][D-VER-002] — The prose is not bound; it says which half it is · 2026-07-29 · confirmed

[D-ANS-033]: answers/ans-033-the-review-server-is-read-anonymously-and-the-answer-says-what-that-leaves-out.md
[D-ANS-034]: answers/ans-034-a-source-outside-this-package-answers-json-or-it-did-not-answer.md
[D-ANS-035]: answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md
[D-ANS-036]: answers/ans-036-a-query-written-in-fluid-tags-is-searched-in-the-book-that-documents-them.md
[D-ANS-037]: answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md
[D-ANS-038]: answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md
[D-COD-004]: code/cod-004-what-leaves-this-process-goes-through-one-seam.md
[D-DOC-009]: documentation/doc-009-prose-names-what-counts-rather-than-the-count.md
[D-DOC-010]: documentation/doc-010-targetversion-opens-with-one-sentence-and-diverges-after-it.md
[D-DOC-011]: documentation/doc-011-a-schema-is-written-as-the-shape-it-validates.md
[D-DOC-012]: documentation/doc-012-the-second-root-is-an-installation-this-repository-writes.md
[D-EVI-006]: evidence/evi-006-one-installation-per-covered-version-kept-and-started.md
[D-FBK-025]: feedback/fbk-025-a-judgement-reads-the-corpus-decides-the-shape-and-sets-the-priority.md
[D-FBK-026]: feedback/fbk-026-the-ladder-needs-an-outcome-that-builds-something.md
[D-FBK-027]: feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md
[D-FBK-038]: feedback/fbk-038-what-decides-a-breaking-removal-is-the-caller-not-the-marker.md
[D-FBK-039]: feedback/fbk-039-a-mangled-name-is-rewritten-once-and-the-comparison-carries-the-rest.md
[D-GUI-003]: guides/gui-003-the-wrapping-conflict-is-resolved-in-the-answer-rather-than-in-silence.md
[D-GUI-004]: guides/gui-004-a-review-brief-states-the-removal-surface-rather-than-matching-it.md
[D-GUI-005]: guides/gui-005-the-product-premise-is-one-statement-on-the-brief-every-task-passes-through.md
[D-GUI-006]: guides/gui-006-a-task-that-changes-nothing-is-a-change-type-of-its-own.md
[D-GUI-007]: guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md
[D-KNW-029]: knowledge/knw-029-a-hint-names-the-domains-it-is-asked-from-and-the-file-names-the-subject.md
[D-KNW-030]: knowledge/knw-030-a-hint-is-one-question-and-the-datahandler-family-is-six.md
[D-KNW-031]: knowledge/knw-031-a-suite-is-a-property-of-the-domain-not-of-the-hint.md
[D-KNW-032]: knowledge/knw-032-the-corpus-is-filed-by-question-and-two-splits-were-taken-back.md
[D-KNW-033]: knowledge/knw-033-every-hint-names-the-domains-it-is-asked-from-and-none-is-any.md
[D-KNW-034]: knowledge/knw-034-the-file-is-the-subject-and-javascript-is-not-a-domain.md
[D-KNW-035]: knowledge/knw-035-the-corpus-and-the-tool-that-answers-from-it-are-called-hints.md
[D-KNW-036]: knowledge/knw-036-the-standards-check-handed-over-is-the-one-that-cannot-pass-empty.md
[D-KNW-037]: knowledge/knw-037-a-content-element-preview-draws-the-elements-own-payload-and-the-corpus-names-the-fields.md
[D-KNW-038]: knowledge/knw-038-a-hint-is-reached-by-the-role-of-a-file-rather-than-by-the-extension-it-sits-in.md
[D-KNW-039]: knowledge/knw-039-the-type-a-changelog-entry-owes-is-stated-in-prose-and-the-skeleton-stays-a-hint.md
[D-KNW-040]: knowledge/knw-040-what-asserts-a-rendered-output-is-a-gap-this-server-owns.md
[D-KNW-041]: knowledge/knw-041-the-checkout-a-suite-is-started-in-supplies-its-own-dependencies.md
[D-KNW-042]: knowledge/knw-042-what-the-image-pipeline-does-below-the-task-layer-is-a-gap-this-server-owns.md
[D-KNW-043]: knowledge/knw-043-a-rule-about-what-an-api-may-be-used-for-carries-its-strength-and-its-source.md
[D-SKL-005]: task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md
[D-SKL-006]: task-skills/skl-006-the-site-new-cluster-earns-the-route-into-the-skill-that-owns-the-task.md
[D-SKL-007]: task-skills/skl-007-every-disposition-a-review-makes-carries-its-evidence.md
[D-SKL-008]: task-skills/skl-008-a-review-reads-the-review-the-patch-is-already-in.md
[D-SKL-009]: task-skills/skl-009-the-rule-that-keeps-not-landing-is-written-as-an-act-with-an-object.md
[D-SKL-010]: task-skills/skl-010-the-assessment-that-precedes-a-core-patch-reads-the-issue-and-the-review-server.md
[D-ANS-005]: answers/ans-005-an-unmet-precondition-is-answered-not-raised.md
[D-ANS-006]: answers/ans-006-an-identifier-is-found-however-it-is-spelled.md
[D-ANS-007]: answers/ans-007-two-shapes-for-not-answered-and-one-word-for-why.md
[D-ANS-008]: answers/ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md
[D-ANS-009]: answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-it.md
[D-ANS-010]: answers/ans-010-does-it-still-work-is-a-question-for-the-manual.md
[D-ANS-011]: answers/ans-011-a-scope-answer-states-what-a-manifest-declares.md
[D-ANS-012]: answers/ans-012-an-oneof-alternative-is-stated-where-the-call-is-composed.md
[D-ANS-013]: answers/ans-013-what-runs-a-project-is-a-placement-not-a-missing-answer.md
[D-ANS-014]: answers/ans-014-the-extension-answer-enumerates-registrations-not-files.md
[D-ANS-015]: answers/ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md
[D-ANS-016]: answers/ans-016-a-miss-names-the-query-that-would-have-hit.md
[D-ANS-017]: answers/ans-017-a-union-typed-argument-gets-wording-a-client-can-compose-against.md
[D-ANS-018]: answers/ans-018-a-plugin-is-a-kind-of-content-element-not-one-whose-template-is-missing.md
[D-ANS-019]: answers/ans-019-three-registration-kinds-read-from-what-core-reads-them-for.md
[D-ANS-020]: answers/ans-020-a-deprecation-is-answered-by-the-version-that-removes-it.md
[D-ANS-021]: answers/ans-021-a-manual-query-is-told-what-short-buys.md
[D-ANS-022]: answers/ans-022-the-matcher-takes-a-hyphenated-compound-apart.md
[D-ANS-024]: answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md
[D-ANS-025]: answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md
[D-ANS-026]: answers/ans-026-the-viewhelper-reference-is-indexed-and-a-manual-carries-the-collection-it-is-published-in.md
[D-ANS-027]: answers/ans-027-the-extbase-fork-is-placed-where-the-undecided-caller-passes.md
[D-ANS-028]: answers/ans-028-a-two-letter-query-word-is-searched-for-and-the-stopword-list-is-what-keeps-the-others-out.md
[D-ANS-030]: answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md
[D-ANS-031]: answers/ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md
[D-ANS-032]: answers/ans-032-the-dilution-reference-of-the-manual-ranking-is-the-length-of-an-ordinary-title.md
[D-AUD-004]: audience/aud-004-every-client-is-offered-every-tool-and-the-answer-obliges.md
[D-COD-003]: code/cod-003-a-directory-is-read-through-symfony-finder.md
[D-DIS-007]: discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md
[D-DIS-008]: discovery/dis-008-the-columns-typo3-derives-are-reachable-where-the-database-is.md
[D-DIS-009]: discovery/dis-009-installed-is-one-step-short-of-callable-and-the-install-is-what-says-so.md
[D-DOC-003]: documentation/doc-003-a-decision-says-what-came-back-and-what-rests-on-it.md
[D-DOC-004]: documentation/doc-004-a-requirement-is-written-in-the-same-sections-as-a-decision.md
[D-DOC-005]: documentation/doc-005-a-number-is-three-digits-so-a-group-lists-in-order.md
[D-DOC-006]: documentation/doc-006-a-recording-says-what-it-is-of.md
[D-DOC-007]: documentation/doc-007-one-page-per-tool-and-the-answer-whole.md
[D-DOC-008]: documentation/doc-008-the-calls-that-reach-outside-stay-in-the-shared-table.md
[D-EVI-004]: evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md
[D-EVI-005]: evidence/evi-005-a-registration-nothing-can-reach-is-cleared-and-the-database-goes-with-it.md
[D-FBK-011]: feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md
[D-FBK-012]: feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md
[D-FBK-013]: feedback/fbk-013-an-empty-queue-is-a-state-not-a-failure.md
[D-FBK-014]: feedback/fbk-014-every-stage-is-a-directory-and-closing-is-none.md
[D-FBK-015]: feedback/fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md
[D-FBK-016]: feedback/fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md
[D-FBK-017]: feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md
[D-FBK-018]: feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md
[D-FBK-019]: feedback/fbk-019-a-secret-pasted-into-a-feedback-is-taken-out-on-the-way-in.md
[D-FBK-020]: feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md
[D-FBK-021]: feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md
[D-FBK-022]: feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md
[D-FBK-023]: feedback/fbk-023-a-correction-is-judged-by-what-its-withdrawal-moves.md
[D-FBK-024]: feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md
[D-KNW-005]: knowledge/knw-005-one-scope-replaced-the-four-vocabularies.md
[D-KNW-006]: knowledge/knw-006-a-word-for-a-thing-administered-from-the-backend.md
[D-KNW-007]: knowledge/knw-007-a-hint-says-whose-it-is-in-both-directions.md
[D-KNW-008]: knowledge/knw-008-tooling-is-a-row-that-is-crossed-in-the-answer.md
[D-KNW-009]: knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md
[D-KNW-010]: knowledge/knw-010-what-the-core-reads-from-the-environment-is-a-gap-this-server-owns.md
[D-KNW-011]: knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md
[D-KNW-012]: knowledge/knw-012-an-extension-neon-is-phpstans-filename-and-not-a-typo3-one.md
[D-KNW-013]: knowledge/knw-013-this-repositorys-own-sentence-is-reworded-rather-than-indexed.md
[D-KNW-016]: knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-gap-this-server-owns.md
[D-KNW-017]: knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md
[D-KNW-018]: knowledge/knw-018-what-a-datamap-does-to-a-relation-field-is-a-gap-this-server-owns.md
[D-KNW-019]: knowledge/knw-019-the-corpus-states-that-a-test-sees-only-what-it-primed.md
[D-KNW-020]: knowledge/knw-020-what-a-preview-template-is-handed-is-stated-on-both-majors.md
[D-KNW-021]: knowledge/knw-021-a-fluid-preview-template-replaces-the-content-half-and-the-corpus-says-so.md
[D-KNW-022]: knowledge/knw-022-the-corpus-states-how-long-a-per-class-test-database-lives.md
[D-KNW-023]: knowledge/knw-023-which-page-may-hold-a-record-is-a-gap-this-server-owns.md
[D-KNW-024]: knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md
[D-KNW-026]: knowledge/knw-026-where-a-one-off-script-may-not-be-written-is-a-gap-this-server-owns.md
[D-KNW-027]: knowledge/knw-027-which-caches-a-change-invalidates-is-a-gap-this-server-owns.md
[D-KNW-028]: knowledge/knw-028-how-a-file-becomes-a-processed-one-is-a-gap-this-server-owns.md
[D-SCO-009]: scope/sco-009-the-brief-is-one-brief-and-names-the-paths-a-step.md
[D-SKL-002]: task-skills/skl-002-a-focused-audit-narrows-what-is-assessed.md
[D-SKL-003]: task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md
[D-SKL-004]: task-skills/skl-004-what-a-task-does-when-the-lookups-run-out-is-written-for-a-review.md
[D-COD-001]: code/cod-001-one-file-declares-one-class.md
[D-COD-002]: code/cod-002-the-upkeep-cli-is-a-symfony-console-application.md
[D-DIS-006]: discovery/dis-006-the-installation-stays-worked-out-from-the-start-directory.md
[D-DOC-001]: documentation/doc-001-a-table-is-written-so-it-reads-unrendered.md
[D-DOC-002]: documentation/doc-002-the-prose-rule-is-measured-and-only-the-lead-fails.md
[D-FBK-006]: feedback/fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md
[D-FBK-007]: feedback/fbk-007-how-a-todo-is-worked-travels-with-the-todo.md
[D-FBK-008]: feedback/fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md
[D-FBK-009]: feedback/fbk-009-a-todo-nobody-can-start-waits-where-it-says-why.md
[D-FBK-010]: feedback/fbk-010-main-carries-the-state-and-the-branch-carries-the-work.md
[D-SCO-007]: scope/sco-007-the-signals-are-combined-per-call-and-a-call-is-not-a-path.md
[D-SKL-001]: task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md
[D-ANS-004]: answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md
[D-AUD-003]: audience/aud-003-the-instructions-carry-the-entry-point.md
[D-DIS-005]: discovery/dis-005-a-registry-with-no-command-is-read-by-booting-the-installation.md
[D-EVI-001]: evidence/evi-001-forward-evidence-comes-from-a-review.md
[D-EVI-002]: evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md
[D-EVI-003]: evidence/evi-003-a-review-runs-the-checks-that-cannot-change-the-code.md
[D-FBK-001]: feedback/fbk-001-the-backlog-is-read-out-rather-than-enforced.md
[D-FBK-002]: feedback/fbk-002-the-order-of-the-work-is-declared-not-inferred.md
[D-FBK-004]: feedback/fbk-004-the-model-is-asked-because-nothing-else-can-say-it.md
[D-VER-004]: versions/ver-004-a-supported-range-is-a-property-of-the-package.md
[D-ANS-002]: answers/ans-002-three-numbers-decide-what-a-lookup-answers.md
[D-ANS-003]: answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md
[D-CAT-003]: catalog/cat-003-the-component-index-is-curated-its-contract-comes-from-the-installation.md
[D-KNW-004]: knowledge/knw-004-package-knowledge-needs-a-producer-before-it-needs-discovery.md
[D-VER-003]: versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md
[D-AUD-001]: audience/aud-001-the-outward-description-stays-core-first-until-there-is-more.md
[D-CAT-001]: catalog/cat-001-a-catalog-entry-is-bound-whole-and-the-binding-is-derived.md
[D-DIS-001]: discovery/dis-001-the-root-package-counts-as-an-installed-package.md
[D-DIS-004]: discovery/dis-004-the-version-comes-from-the-core-package-not-from-the-console.md
[D-GUI-001]: guides/gui-001-a-missing-release-target-becomes-a-placeholder-not-main.md
[D-GUI-002]: guides/gui-002-the-commit-workflow-is-asked-for-not-inferred.md
[D-SCO-002]: scope/sco-002-a-core-only-intent-asks-for-evidence-not-for-silence.md
[D-SCO-003]: scope/sco-003-what-is-core-only-is-decided-per-line-by-what-it-names.md
[D-SCO-005]: scope/sco-005-the-installation-is-evidence-about-the-task-and-the-weakest-kind.md
[D-SCO-006]: scope/sco-006-why-project-work-is-out-of-scope-kept-coming-back.md
[D-VER-001]: versions/ver-001-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md
[D-VER-002]: versions/ver-002-the-prose-is-not-bound-it-says-which-half-it-is.md

### Revoked, and kept as the record

- [`D-FBK-037`][D-FBK-037] — API stability is worth a lookup and git state is not · 2026-08-03 → D-FBK-038
- [`D-ANS-023`][D-ANS-023] — A ViewHelper question is answered by widening the manual index · 2026-08-02 → D-ANS-026
- [`D-ANS-029`][D-ANS-029] — The scanner matcher is stated on the route a removal takes · 2026-08-02 → D-ANS-035
- [`D-KNW-014`][D-KNW-014] — The record variable a v14 preview template is handed is a gap this server owns · 2026-08-02 → D-KNW-020
- [`D-KNW-015`][D-KNW-015] — The corpus states what a Fluid preview template replaces · 2026-08-02 → D-KNW-021
- [`D-KNW-025`][D-KNW-025] — What a backend preview owes the editor is a gap this server owns · 2026-08-02 → D-KNW-037
- [`D-FBK-005`][D-FBK-005] — The queue is worked before the pile is sighted · 2026-08-01 → D-FBK-012
- [`D-SCO-008`][D-SCO-008] — The path decides, and the answer may say it cannot · 2026-08-01 → D-KNW-005
- [`D-FBK-003`][D-FBK-003] — A session is handed one todo, not the file · 2026-07-31 → D-FBK-002
- [`D-KNW-003`][D-KNW-003] — `provenance` is not the third spelling of `binding`, and stays · 2026-07-30 → D-KNW-005
- [`D-ANS-001`][D-ANS-001] — The unanswered result keeps its shape and gains a reason · 2026-07-29 → D-ANS-005
- [`D-AUD-002`][D-AUD-002] — Two profiles, because a third one would have been the same set · 2026-07-29 → D-AUD-004
- [`D-CAT-002`][D-CAT-002] — The index of worked examples is curated, and existence is all that is checked · 2026-07-29
- [`D-DIS-002`][D-DIS-002] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29
- [`D-DIS-003`][D-DIS-003] — A label query is words, and the console is asked with a regex · 2026-07-29
- [`D-KNW-001`][D-KNW-001] — Sitepackage work is answered from the General category · 2026-07-29
- [`D-KNW-002`][D-KNW-002] — A hint about typo3/testing-framework is verified against tags, not against the checkouts · 2026-07-29
- [`D-SCO-001`][D-SCO-001] — Outside the core the core test guide declines rather than adapts · 2026-07-29
- [`D-SCO-004`][D-SCO-004] — The frontend is recognised by name, and only the two UI sections go · 2026-07-29

[D-FBK-037]: feedback/fbk-037-api-stability-is-worth-a-lookup-and-git-state-is-not.md
[D-ANS-023]: answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-index.md
[D-ANS-029]: answers/ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md
[D-KNW-014]: knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md
[D-KNW-015]: knowledge/knw-015-the-corpus-states-what-a-fluid-preview-template-replaces.md
[D-KNW-025]: knowledge/knw-025-what-a-backend-preview-owes-the-editor-is-a-gap-this-server-owns.md
[D-FBK-005]: feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md
[D-SCO-008]: scope/sco-008-the-path-decides-and-the-answer-may-say-it-cannot.md
[D-FBK-003]: feedback/fbk-003-a-session-is-handed-one-todo-not-the-file.md
[D-KNW-003]: knowledge/knw-003-provenance-is-not-the-third-spelling-of-binding.md
[D-ANS-001]: answers/ans-001-the-unanswered-result-keeps-its-shape-and-gains-a-reason.md
[D-AUD-002]: audience/aud-002-two-profiles-because-a-third-would-have-been-the-same-set.md
[D-CAT-002]: catalog/cat-002-the-index-of-worked-examples-is-curated.md
[D-DIS-002]: discovery/dis-002-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-003]: discovery/dis-003-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
[D-KNW-001]: knowledge/knw-001-sitepackage-work-is-answered-from-the-general-category.md
[D-KNW-002]: knowledge/knw-002-a-hint-about-typo3-testing-framework-is-verified-against-tags.md
[D-SCO-001]: scope/sco-001-outside-the-core-the-core-test-guide-declines-rather-than-adapts.md
[D-SCO-004]: scope/sco-004-the-frontend-is-recognised-by-name-and-only-the-two-ui-sections-go.md
