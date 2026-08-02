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

- [`D-ANS-005`][D-ANS-005] — A question that is not supported here is answered in a shape of its own · 2026-08-02
- [`D-ANS-006`][D-ANS-006] — An identifier is found however it is spelled · 2026-08-02
- [`D-ANS-007`][D-ANS-007] — Two shapes for "not answered", one word for why · 2026-08-02
- [`D-AUD-004`][D-AUD-004] — The tool list is not where the audience is said · 2026-08-02
- [`D-COD-003`][D-COD-003] — A directory is read through symfony/finder · 2026-08-02
- [`D-DIS-007`][D-DIS-007] — The DDEV console is named by the mount, not by the variable · 2026-08-02
- [`D-DIS-008`][D-DIS-008] — The columns TYPO3 derives are reachable where the database server is · 2026-08-02
- [`D-DOC-003`][D-DOC-003] — A decision says what came back, and a requirement says what it rests on · 2026-08-02
- [`D-DOC-004`][D-DOC-004] — A requirement is written in the same sections as a decision · 2026-08-02
- [`D-DOC-005`][D-DOC-005] — A number is three digits so a group lists in order · 2026-08-02
- [`D-DOC-006`][D-DOC-006] — A recording says what it is of, and nothing fails on its age · 2026-08-02
- [`D-FBK-011`][D-FBK-011] — The suite holds what one branch can be right about · 2026-08-02
- [`D-KNW-005`][D-KNW-005] — One `Scope` replaced the four vocabularies · 2026-08-02
- [`D-KNW-006`][D-KNW-006] — A word for a thing administered from the backend adds no domain to a backend-only task · 2026-08-02
- [`D-KNW-007`][D-KNW-007] — A hint says whose it is in both directions · 2026-08-02
- [`D-KNW-008`][D-KNW-008] — Tooling is a row the answer crosses, not a dimension the corpus stores · 2026-08-02
- [`D-SCO-009`][D-SCO-009] — The brief is one brief, and names the paths a step is not for · 2026-08-02
- [`D-COD-001`][D-COD-001] — One file declares one class · 2026-08-01
- [`D-COD-002`][D-COD-002] — The upkeep CLI is a Symfony Console application · 2026-08-01
- [`D-DIS-006`][D-DIS-006] — The protocol offers nothing to replace the working directory · 2026-08-01
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
- [`D-AUD-001`][D-AUD-001] — Three audiences, and the positioning that has not caught up · 2026-07-29 · confirmed
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

[D-ANS-005]: answers/ans-005-an-unmet-precondition-is-answered-not-raised.md
[D-ANS-006]: answers/ans-006-an-identifier-is-found-however-it-is-spelled.md
[D-ANS-007]: answers/ans-007-two-shapes-for-not-answered-and-one-word-for-why.md
[D-AUD-004]: audience/aud-004-the-tool-list-is-not-where-the-audience-is-said.md
[D-COD-003]: code/cod-003-a-directory-is-read-through-symfony-finder.md
[D-DIS-007]: discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md
[D-DIS-008]: discovery/dis-008-the-columns-typo3-derives-are-reachable-where-the-database-is.md
[D-DOC-003]: documentation/doc-003-a-decision-says-what-came-back-and-what-rests-on-it.md
[D-DOC-004]: documentation/doc-004-a-requirement-is-written-in-the-same-sections-as-a-decision.md
[D-DOC-005]: documentation/doc-005-a-number-is-three-digits-so-a-group-lists-in-order.md
[D-DOC-006]: documentation/doc-006-a-recording-says-what-it-is-of.md
[D-FBK-011]: feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md
[D-KNW-005]: knowledge/knw-005-one-scope-replaced-the-four-vocabularies.md
[D-KNW-006]: knowledge/knw-006-a-word-for-a-thing-administered-from-the-backend.md
[D-KNW-007]: knowledge/knw-007-a-hint-says-whose-it-is-in-both-directions.md
[D-KNW-008]: knowledge/knw-008-tooling-is-a-row-that-is-crossed-in-the-answer.md
[D-SCO-009]: scope/sco-009-the-brief-is-one-brief-and-names-the-paths-a-step.md
[D-COD-001]: code/cod-001-one-file-declares-one-class.md
[D-COD-002]: code/cod-002-the-upkeep-cli-is-a-symfony-console-application.md
[D-DIS-006]: discovery/dis-006-the-protocol-offers-nothing-to-replace-the-working-directory.md
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
[D-AUD-001]: audience/aud-001-three-audiences-and-the-positioning-that-has-not-caught-up.md
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

- [`D-FBK-005`][D-FBK-005] — The queue is worked before the pile is sighted · 2026-08-01
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
