# What was decided, and on what evidence

A feedback is archived by the commit that closes it, and the commit message
says what changed and why. What a commit message cannot carry is the part that
may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

That is what this directory is for. One entry per decision worth revisiting. An
entry is not a changelog line — a change nobody would need to reconsider does
not belong here. When an assumption is later disproved, the entry stays and
gains a **Corrected on** line: the wrong assumption is the useful part, because
it names the place where the next one is likely to sit.

## Where an entry lives

One decision is one file, named after its id, in the group its id names. The
group is what the decision is about, and the prefix carries it, so a file's id
decides its path and two entries cannot quietly share a number.

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
foot of it is generated from the files below it by `bin/cli decisions index`, as is
the listing at the foot of this file. `bin/cli decisions check` holds the files to
the shape described below, and `composer test` runs the same check through
`DecisionsTest`.

An id is never reused, and an entry is never deleted: a decision that turned out
wrong is the one most worth reading, and it is corrected in place.

## What an entry looks like

```markdown
---
id: D-DIS-4
date: 2026-07-29
status: standing
---

# D-DIS-4 — The version comes from the core package, not from the console

**The installed version is read from the core package's `Typo3Version` class
rather than asked of `bin/typo3 --version`.**

The catalogs are pinned to one revision and every answer was phrased as
timeless fact, while the server had the other number all along.

- **Evidence:** what was measured or read at the time, with its numbers.
- **Decided:** what was done, and what was rejected in doing it.
- **Assumed:** what the decision rests on that nobody has verified.
- **Wrong if:** what would show it to have been wrong, concretely enough that
  somebody could notice it happening.
```

- The **bold first sentence** is the decision. A reader who stops after it knows
  what was settled; everything under it is what settled it.
- The fields are a fixed set, in that order: **Evidence**, **Decided**,
  **Assumed**, **Wrong if**. Each may appear more than once and only **Wrong
  if** is required — an entry that cannot say what would falsify it is not a
  decision worth recording. `date` is the day it was decided.
- A later session adds one of three lines at the foot, and nothing else: **Tested
  on `<date>`** where a run confirmed it, **Corrected on `<date>`** where one
  disproved it, and **Since then** for what followed without a date of its own.
- `status` says which of those is there: `tested`, `corrected`, or `standing`
  for a decision nothing has come back about yet.

Most decisions are standing and stay that way, which is what makes the state
easy to stop seeing: a **Wrong if** written and never read is a promise, and
nothing here says when to keep it. `bin/cli backlog list` counts them and names
the oldest — not because age disproves anything, but because that is the entry
the repository has moved furthest away from since. Going back to one and adding
**Tested on** or **Corrected on** is a legitimate task with no feature behind it.

## Every decision, newest first

- [`D-COD-1`][D-COD-1] — One file declares one class · 2026-08-01 · standing
- [`D-DIS-6`][D-DIS-6] — The protocol offers nothing to replace the working directory · 2026-08-01 · standing
- [`D-DOC-1`][D-DOC-1] — A table is written so it reads unrendered · 2026-08-01 · standing
- [`D-DOC-2`][D-DOC-2] — The prose rule is measured, and only the lead fails on it · 2026-08-01 · standing
- [`D-FBK-5`][D-FBK-5] — The queue is worked before the pile is sighted · 2026-08-01 · standing
- [`D-FBK-6`][D-FBK-6] — A name is cut where the feedback starts to differ · 2026-08-01 · standing
- [`D-FBK-7`][D-FBK-7] — How a todo is worked travels with the todo · 2026-08-01 · standing
- [`D-SCO-7`][D-SCO-7] — The signals are combined per call, and a call is not a path · 2026-08-01 · standing
- [`D-SCO-8`][D-SCO-8] — The path decides, and the answer may say it cannot · 2026-08-01 · standing
- [`D-SKL-1`][D-SKL-1] — The order a task starts in is one file, and the reading comes last in it · 2026-08-01 · tested
- [`D-ANS-4`][D-ANS-4] — The instruction budget is 2048 characters, on one client's evidence · 2026-07-31 · standing
- [`D-AUD-3`][D-AUD-3] — The instructions carry the entry point, because the tool descriptions never arrive · 2026-07-31 · standing
- [`D-DIS-5`][D-DIS-5] — A registry with no console command is read by booting the installation · 2026-07-31 · standing
- [`D-EVI-1`][D-EVI-1] — Forward evidence comes from a review, not from a prompt that knows the answer · 2026-07-31 · standing
- [`D-EVI-2`][D-EVI-2] — A skill crossing is read rather than run · 2026-07-31 · standing
- [`D-EVI-3`][D-EVI-3] — A review runs the checks that cannot change the code · 2026-07-31 · standing
- [`D-FBK-1`][D-FBK-1] — The backlog is read out rather than enforced · 2026-07-31 · standing
- [`D-FBK-2`][D-FBK-2] — The order of the work is declared, not inferred · 2026-07-31 · standing
- [`D-FBK-3`][D-FBK-3] — A session is handed one todo, not the file · 2026-07-31 · corrected
- [`D-FBK-4`][D-FBK-4] — The model is asked, because nothing else here can say it · 2026-07-31 · standing
- [`D-VER-4`][D-VER-4] — A supported range is a property of the package, not of the checkout · 2026-07-31 · standing
- [`D-ANS-2`][D-ANS-2] — Three numbers now decide what a lookup answers, and they were measured, not reasoned · 2026-07-30 · tested
- [`D-ANS-3`][D-ANS-3] — Retrieval stays lexical and runtime inspection stays narrow · 2026-07-30 · standing
- [`D-CAT-3`][D-CAT-3] — The component index is curated; its contract comes from the installation · 2026-07-30 · standing
- [`D-KNW-3`][D-KNW-3] — `provenance` is not the third spelling of `binding`, and stays · 2026-07-30 · standing
- [`D-KNW-4`][D-KNW-4] — Package knowledge needs a producer before it needs discovery · 2026-07-30 · standing
- [`D-VER-3`][D-VER-3] — The Fluid engine gets no version axis of its own, because the core pins it · 2026-07-30 · tested
- [`D-ANS-1`][D-ANS-1] — The unanswered result keeps its shape and gains a reason · 2026-07-29 · standing
- [`D-AUD-1`][D-AUD-1] — Three audiences, and the positioning that has not caught up · 2026-07-29 · standing
- [`D-AUD-2`][D-AUD-2] — Two profiles, because a third one would have been the same set · 2026-07-29 · standing
- [`D-CAT-1`][D-CAT-1] — A catalog entry is bound whole, and the binding is derived · 2026-07-29 · standing
- [`D-CAT-2`][D-CAT-2] — The index of worked examples is curated, and existence is all that is checked · 2026-07-29 · standing
- [`D-DIS-1`][D-DIS-1] — The root package counts as an installed package · 2026-07-29 · standing
- [`D-DIS-2`][D-DIS-2] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29 · standing
- [`D-DIS-3`][D-DIS-3] — A label query is words, and the console is asked with a regex · 2026-07-29 · standing
- [`D-DIS-4`][D-DIS-4] — The version comes from the core package, not from the console · 2026-07-29 · standing
- [`D-GUI-1`][D-GUI-1] — A missing release target becomes a placeholder, not `main` · 2026-07-29 · standing
- [`D-GUI-2`][D-GUI-2] — The commit workflow is asked for, not inferred · 2026-07-29 · standing
- [`D-KNW-1`][D-KNW-1] — Sitepackage work is answered from the General category · 2026-07-29 · standing
- [`D-KNW-2`][D-KNW-2] — A hint about typo3/testing-framework is verified against tags, not against the checkouts · 2026-07-29 · corrected
- [`D-SCO-1`][D-SCO-1] — Outside the core the core test guide declines rather than adapts · 2026-07-29 · corrected
- [`D-SCO-2`][D-SCO-2] — A core-only intent asks for evidence, not for silence · 2026-07-29 · standing
- [`D-SCO-3`][D-SCO-3] — What is core-only is decided per line, by what it names · 2026-07-29 · standing
- [`D-SCO-4`][D-SCO-4] — The frontend is recognised by name, and only the two UI sections go · 2026-07-29 · standing
- [`D-SCO-5`][D-SCO-5] — The installation is evidence about the task, and the weakest kind · 2026-07-29 · standing
- [`D-SCO-6`][D-SCO-6] — Why "project work is out of scope" kept coming back · 2026-07-29 · standing
- [`D-VER-1`][D-VER-1] — A version range is data on the statement, not a sentence in it · 2026-07-29 · standing
- [`D-VER-2`][D-VER-2] — The prose is not bound; it says which half it is · 2026-07-29 · standing

[D-COD-1]: code/cod-1-one-file-declares-one-class.md
[D-DIS-6]: discovery/dis-6-the-protocol-offers-nothing-to-replace-the-working-directory.md
[D-DOC-1]: documentation/doc-1-a-table-is-written-so-it-reads-unrendered.md
[D-DOC-2]: documentation/doc-2-the-prose-rule-is-measured-and-only-the-lead-fails.md
[D-FBK-5]: feedback/fbk-5-the-queue-is-worked-before-the-pile-is-sighted.md
[D-FBK-6]: feedback/fbk-6-a-name-is-cut-where-the-feedback-starts-to-differ.md
[D-FBK-7]: feedback/fbk-7-how-a-todo-is-worked-travels-with-the-todo.md
[D-SCO-7]: scope/sco-7-the-signals-are-combined-per-call-and-a-call-is-not-a-path.md
[D-SCO-8]: scope/sco-8-the-path-decides-and-the-answer-may-say-it-cannot.md
[D-SKL-1]: task-skills/skl-1-the-order-a-task-starts-in-is-one-file.md
[D-ANS-4]: answers/ans-4-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md
[D-AUD-3]: audience/aud-3-the-instructions-carry-the-entry-point.md
[D-DIS-5]: discovery/dis-5-a-registry-with-no-command-is-read-by-booting-the-installation.md
[D-EVI-1]: evidence/evi-1-forward-evidence-comes-from-a-review.md
[D-EVI-2]: evidence/evi-2-a-skill-crossing-is-read-rather-than-run.md
[D-EVI-3]: evidence/evi-3-a-review-runs-the-checks-that-cannot-change-the-code.md
[D-FBK-1]: feedback/fbk-1-the-backlog-is-read-out-rather-than-enforced.md
[D-FBK-2]: feedback/fbk-2-the-order-of-the-work-is-declared-not-inferred.md
[D-FBK-3]: feedback/fbk-3-a-session-is-handed-one-todo-not-the-file.md
[D-FBK-4]: feedback/fbk-4-the-model-is-asked-because-nothing-else-can-say-it.md
[D-VER-4]: versions/ver-4-a-supported-range-is-a-property-of-the-package.md
[D-ANS-2]: answers/ans-2-three-numbers-decide-what-a-lookup-answers.md
[D-ANS-3]: answers/ans-3-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md
[D-CAT-3]: catalog/cat-3-the-component-index-is-curated-its-contract-comes-from-the-installation.md
[D-KNW-3]: knowledge/knw-3-provenance-is-not-the-third-spelling-of-binding.md
[D-KNW-4]: knowledge/knw-4-package-knowledge-needs-a-producer-before-it-needs-discovery.md
[D-VER-3]: versions/ver-3-the-fluid-engine-gets-no-version-axis-of-its-own.md
[D-ANS-1]: answers/ans-1-the-unanswered-result-keeps-its-shape-and-gains-a-reason.md
[D-AUD-1]: audience/aud-1-three-audiences-and-the-positioning-that-has-not-caught-up.md
[D-AUD-2]: audience/aud-2-two-profiles-because-a-third-would-have-been-the-same-set.md
[D-CAT-1]: catalog/cat-1-a-catalog-entry-is-bound-whole-and-the-binding-is-derived.md
[D-CAT-2]: catalog/cat-2-the-index-of-worked-examples-is-curated.md
[D-DIS-1]: discovery/dis-1-the-root-package-counts-as-an-installed-package.md
[D-DIS-2]: discovery/dis-2-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-3]: discovery/dis-3-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
[D-DIS-4]: discovery/dis-4-the-version-comes-from-the-core-package-not-from-the-console.md
[D-GUI-1]: guides/gui-1-a-missing-release-target-becomes-a-placeholder-not-main.md
[D-GUI-2]: guides/gui-2-the-commit-workflow-is-asked-for-not-inferred.md
[D-KNW-1]: knowledge/knw-1-sitepackage-work-is-answered-from-the-general-category.md
[D-KNW-2]: knowledge/knw-2-a-hint-about-typo3-testing-framework-is-verified-against-tags.md
[D-SCO-1]: scope/sco-1-outside-the-core-the-core-test-guide-declines-rather-than-adapts.md
[D-SCO-2]: scope/sco-2-a-core-only-intent-asks-for-evidence-not-for-silence.md
[D-SCO-3]: scope/sco-3-what-is-core-only-is-decided-per-line-by-what-it-names.md
[D-SCO-4]: scope/sco-4-the-frontend-is-recognised-by-name-and-only-the-two-ui-sections-go.md
[D-SCO-5]: scope/sco-5-the-installation-is-evidence-about-the-task-and-the-weakest-kind.md
[D-SCO-6]: scope/sco-6-why-project-work-is-out-of-scope-kept-coming-back.md
[D-VER-1]: versions/ver-1-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md
[D-VER-2]: versions/ver-2-the-prose-is-not-bound-it-says-which-half-it-is.md
