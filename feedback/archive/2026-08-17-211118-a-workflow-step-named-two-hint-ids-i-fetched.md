---
date: 2026-08-17T21:11:18+00:00
category: idea
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup, typo3-development-installation
directory: /home/benji/projects/site-demo
---

# a workflow step named two hint ids, I fetched one, and the one I skipped held the answer to five ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. The user reviewed the finished repository and listed ten defects. This reports where those defects came from, which the server cannot see.

typo3-development-installation step 5 reads: "typo3_hint_lookup with id=project-configuration-files and id=project-build-and-scripts says which of them belong to the project and which are generated". I called the first. I did not call the second, and I did not notice that I had answered half a prescription. Nothing in the session flagged it — the first hint answered fully and well, so the step felt discharged.

I called project-build-and-scripts for the first time during the review, after the fact. Five of the reviewer's ten findings are in it, in plain words:

- "the Playwright configuration and its specs" belongs in Build/ — the reviewer's "you didn't install Playwright".
- "the static analysis configuration" belongs in Build/ — the reviewer's "no code quality tools, phpunit, php-cs-fixer, editorconfig".
- "a one-off script that seeds an installation" belongs in Build/, and "it goes into Build/ with the rest of the tooling, or it is not kept at all" — the reviewer asked twice why the seed script was left lying around. The hint states the choice explicitly; I had made the placement right by accident and never faced the keep-or-drop question.
- "How anything is run is answered by the scripts in composer.json and package.json, and nowhere else — there is no runTests.sh here" — the reviewer's "you made a composer script that calls a shell file". That shell file is exactly the shape this sentence rules out.
- "Name them after what they do rather than after the tool behind them (test:unit, test:functional, test:e2e...)" — I named mine app:install and app:seed.

So this is not missing knowledge. The knowledge was written, correct, specific, and named by id in the workflow I was executing. What failed is that a two-id step degrades silently to a one-id step, and the half that was fetched was substantial enough that nothing felt absent. The cost was five review findings and, more importantly, the absence of any test suite in a project whose own workflow asks for one.

Worth noting which half I dropped: project-configuration-files is about the installation, which was the thing in front of me at that moment; project-build-and-scripts is about what surrounds it, which did not exist yet. The step is placed at install time and half of it is about work that only starts later.

## Query

typo3-development-installation step 5 names typo3_hint_lookup with id=project-configuration-files and id=project-build-and-scripts. Call only the first, finish the build, then have a human review the repository.

## Suggestion

Two things would have caught it. Make the multi-id steps in the workflows enumerate what each id is for, so a caller can see which one is still owed — "project-configuration-files for what the installation is configured by, project-build-and-scripts for what surrounds it: the ignore rules, the scripts a colleague runs, and where the test and seeding tooling lives". And consider raising project-build-and-scripts a second time later in that workflow, at the proving step, since its content is about the tooling and scripts that only exist once the installation is up — read at install time it describes a directory the caller has not created yet, and by the time Build/ is being filled the step is far behind.
