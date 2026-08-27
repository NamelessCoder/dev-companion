---
date: 2026-08-25T10:53:24+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# task_guide returned a large brief that changed no decision next to test_run_guide, which did

Trimmed on 2026-08-27. The podman half is answered: `runTests.sh` probes for
podman and takes docker where there is none, on every covered branch, and the
three sentences in `knowledge/test-suite-hints.json` that called it a default now
say so. `D-GUI-022` is the judgement of the rest.

## Observation

Task: reviewing and extending a core patch that replaces documentation URLs — files touched were two git hooks, `tsref.xml`, a unit test, two PHP docblocks and five Fluid templates.

I called `typo3_task_guide(changeType="audit", targetVersion="15.0")` with four of those paths, as the base order prescribes. The answer was one of the largest of the session and I can point to nothing in it that changed what I did. It returned hints for `fluid-templates` (template resolution order, `.fluid.html` versus `.html`, root path sorting), `fluid-viewhelpers` (how to write one), `sitepackage-templates` (layout and partial collisions in a sitepackage) and `system-extension-boundaries`. None of those bear on a link swap. The audit checklist it returned is written for reviewing a change that removes or renames API — extension scanner matchers, `[!!!]` prefixes, Breaking changelog files — and this change removes nothing.

`typo3_test_run_guide` with the same paths was the opposite: short, and every line of it was used. It gave me `cgl -n` as the targeted form and told me why `cglGit` is the wrong one from a worktree, `checkIntegrityPhp`, `lintHtml` for the templates and `checkComposer` for later. I ran exactly those, plus `checkRst` and one filtered functional test, all green.

If one call had to settle "what do I run and what do I watch out for" for this change, `typo3_test_run_guide` with the paths was it. `typo3_task_guide` is the one I would not make again in this shape.

One smaller thing from the same pair: `typo3_test_run_guide` narrowed to domains `php` and `fluid` and withheld `xliff`, `docs`, `typoscript`, `typescript`, `css`. But the change also touches `typo3/sysext/backend/Resources/Private/tsref.xml`, and no suite in the answer covers it. I had to conclude by myself that nothing checks that file. Saying so explicitly — "these paths are covered by no suite" — would be a better answer than silence, because silence reads as coverage.

## Query

typo3_task_guide(task="Check out Gerrit change 95369 ... and compare it against local uncommitted changes doing the same thing", changeType="audit", paths=["Build/git-hooks/commit-msg","typo3/sysext/backend/Resources/Private/tsref.xml","typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php","typo3/sysext/install/Resources/Private/Templates/Upgrade/ExtensionScanner.fluid.html"], targetVersion="15.0") followed by typo3_test_run_guide(paths=[same five], targetVersion="15.0").

## Suggestion

Two things would have made the brief earn its size for this task.

First, let the paths suppress hints that do not bear on them. Four Fluid hint groups came back because a `.fluid.html` path was in the list, but the change to that file was one attribute on one anchor. A hint about template root path ordering is not wrong, it is simply not about this diff, and a brief that returns four such groups teaches a session to skim the next one.

Second, `changeType="audit"` produced a checklist about removals and renames. A change type for "a mechanical sweep across many files that changes no behaviour" — link replacement, a label rewording, a formatting pass — would have a genuinely different checklist: prove every replacement, check the file types no suite covers, keep the diff to the one thing, and say what was deliberately left out. That is the shape of work this session actually was, and it recurs.

For `typo3_test_run_guide`: name the paths in the input that no listed suite covers, rather than dropping them silently.
