# What was decided, and on what evidence

A feedback note is deleted by the commit that closes it, and the commit message
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

| Group | What it is about |
| --- | --- |
| [audience/](audience/readme.md) | Who the server answers for, and how it says so |
| [discovery/](discovery/readme.md) | Which installation is read, and how |
| [answers/](answers/readme.md) | What a lookup returns, and what decides it |
| [knowledge/](knowledge/readme.md) | What the corpus holds and how it is written |
| [versions/](versions/readme.md) | What a statement holds on |
| [catalog/](catalog/readme.md) | The curated indexes and where their contract comes from |
| [scope/](scope/readme.md) | Core conventions where they apply, and nowhere else |
| [guides/](guides/readme.md) | What a returned draft is worth |
| [evidence/](evidence/readme.md) | How this server is measured |
| [task-skills/](task-skills/readme.md) | What an installed workflow owes the task |
| [feedback/](feedback/readme.md) | What the backlog has to stay usable for |

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

| Decided | Id | What was decided | About | State |
| --- | --- | --- | --- | --- |
| 2026-08-01 | [`D-SKL-1`](task-skills/skl-1-the-order-a-task-starts-in-is-one-file.md) | The order a task starts in is one file, and the reading comes last in it | task-skills | tested |
| 2026-07-31 | [`D-AUD-3`](audience/aud-3-the-instructions-carry-the-entry-point.md) | The instructions carry the entry point, because the tool descriptions never arrive | audience | standing |
| 2026-07-31 | [`D-EVI-1`](evidence/evi-1-forward-evidence-comes-from-a-review.md) | Forward evidence comes from a review, not from a prompt that knows the answer | evidence | standing |
| 2026-07-31 | [`D-FBK-1`](feedback/fbk-1-the-backlog-is-read-out-rather-than-enforced.md) | The backlog is read out rather than enforced | feedback | standing |
| 2026-07-31 | [`D-FBK-2`](feedback/fbk-2-the-order-of-the-work-is-declared-not-inferred.md) | The order of the work is declared, not inferred | feedback | standing |
| 2026-07-31 | [`D-VER-4`](versions/ver-4-a-supported-range-is-a-property-of-the-package.md) | A supported range is a property of the package, not of the checkout | versions | standing |
| 2026-07-30 | [`D-ANS-2`](answers/ans-2-three-numbers-decide-what-a-lookup-answers.md) | Three numbers now decide what a lookup answers, and they were measured, not reasoned | answers | standing |
| 2026-07-30 | [`D-ANS-3`](answers/ans-3-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md) | Retrieval stays lexical and runtime inspection stays narrow | answers | standing |
| 2026-07-30 | [`D-CAT-3`](catalog/cat-3-the-component-index-is-curated-its-contract-comes-from-the-installation.md) | The component index is curated; its contract comes from the installation | catalog | standing |
| 2026-07-30 | [`D-KNW-3`](knowledge/knw-3-provenance-is-not-the-third-spelling-of-binding.md) | `provenance` is not the third spelling of `binding`, and stays | knowledge | standing |
| 2026-07-30 | [`D-KNW-4`](knowledge/knw-4-package-knowledge-needs-a-producer-before-it-needs-discovery.md) | Package knowledge needs a producer before it needs discovery | knowledge | standing |
| 2026-07-30 | [`D-VER-3`](versions/ver-3-the-fluid-engine-gets-no-version-axis-of-its-own.md) | The Fluid engine gets no version axis of its own, because the core pins it | versions | standing |
| 2026-07-29 | [`D-ANS-1`](answers/ans-1-the-unanswered-result-keeps-its-shape-and-gains-a-reason.md) | The unanswered result keeps its shape and gains a reason | answers | standing |
| 2026-07-29 | [`D-AUD-1`](audience/aud-1-three-audiences-and-the-positioning-that-has-not-caught-up.md) | Three audiences, and the positioning that has not caught up | audience | standing |
| 2026-07-29 | [`D-AUD-2`](audience/aud-2-two-profiles-because-a-third-would-have-been-the-same-set.md) | Two profiles, because a third one would have been the same set | audience | standing |
| 2026-07-29 | [`D-CAT-1`](catalog/cat-1-a-catalog-entry-is-bound-whole-and-the-binding-is-derived.md) | A catalog entry is bound whole, and the binding is derived | catalog | standing |
| 2026-07-29 | [`D-CAT-2`](catalog/cat-2-the-index-of-worked-examples-is-curated.md) | The index of worked examples is curated, and existence is all that is checked | catalog | standing |
| 2026-07-29 | [`D-DIS-1`](discovery/dis-1-the-root-package-counts-as-an-installed-package.md) | The root package counts as an installed package | discovery | standing |
| 2026-07-29 | [`D-DIS-2`](discovery/dis-2-discovery-honours-the-declared-vendor-dir-and-bin-dir.md) | Discovery honours the declared vendor-dir and bin-dir | discovery | standing |
| 2026-07-29 | [`D-DIS-3`](discovery/dis-3-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md) | A label query is words, and the console is asked with a regex | discovery | standing |
| 2026-07-29 | [`D-DIS-4`](discovery/dis-4-the-version-comes-from-the-core-package-not-from-the-console.md) | The version comes from the core package, not from the console | discovery | standing |
| 2026-07-29 | [`D-GUI-1`](guides/gui-1-a-missing-release-target-becomes-a-placeholder-not-main.md) | A missing release target becomes a placeholder, not `main` | guides | standing |
| 2026-07-29 | [`D-GUI-2`](guides/gui-2-the-commit-workflow-is-asked-for-not-inferred.md) | The commit workflow is asked for, not inferred | guides | standing |
| 2026-07-29 | [`D-KNW-1`](knowledge/knw-1-sitepackage-work-is-answered-from-the-general-category.md) | Sitepackage work is answered from the General category | knowledge | standing |
| 2026-07-29 | [`D-KNW-2`](knowledge/knw-2-a-hint-about-typo3-testing-framework-is-verified-against-tags.md) | A hint about typo3/testing-framework is verified against tags, not against the checkouts | knowledge | standing |
| 2026-07-29 | [`D-SCO-1`](scope/sco-1-outside-the-core-the-core-test-guide-declines-rather-than-adapts.md) | Outside the core the core test guide declines rather than adapts | scope | corrected |
| 2026-07-29 | [`D-SCO-2`](scope/sco-2-a-core-only-intent-asks-for-evidence-not-for-silence.md) | A core-only intent asks for evidence, not for silence | scope | standing |
| 2026-07-29 | [`D-SCO-3`](scope/sco-3-what-is-core-only-is-decided-per-line-by-what-it-names.md) | What is core-only is decided per line, by what it names | scope | standing |
| 2026-07-29 | [`D-SCO-4`](scope/sco-4-the-frontend-is-recognised-by-name-and-only-the-two-ui-sections-go.md) | The frontend is recognised by name, and only the two UI sections go | scope | standing |
| 2026-07-29 | [`D-SCO-5`](scope/sco-5-the-installation-is-evidence-about-the-task-and-the-weakest-kind.md) | The installation is evidence about the task, and the weakest kind | scope | standing |
| 2026-07-29 | [`D-SCO-6`](scope/sco-6-why-project-work-is-out-of-scope-kept-coming-back.md) | Why "project work is out of scope" kept coming back | scope | standing |
| 2026-07-29 | [`D-VER-1`](versions/ver-1-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md) | A version range is data on the statement, not a sentence in it | versions | standing |
| 2026-07-29 | [`D-VER-2`](versions/ver-2-the-prose-is-not-bound-it-says-which-half-it-is.md) | The prose is not bound; it says which half it is | versions | standing |
