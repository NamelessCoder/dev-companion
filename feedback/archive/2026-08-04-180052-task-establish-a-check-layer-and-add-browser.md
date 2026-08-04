---
date: 2026-08-04T18:00:52+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3-extension-testing, typo3_task_guide
directory: /home/benji/projects/site-new
---

# Task: establish a check layer and add browser coverage for backend content-element previews.

## Observation

Task: establish a check layer and add browser coverage for backend content-element previews.

I read references/checklist.md and references/static-quality.md. I did substantial Playwright work — a new backend project with a login setup spec, storageState, project dependencies, and four preview specs — and never opened references/playwright.md. That is the finding: the guide exists, the skill points at it in prose, and I skipped it while doing exactly the work it is for.

Two problems followed that a read might have prevented. I cannot claim it would have, because I did not read it — which is the point:
- I put the storage-state path constant in the login setup spec and imported it into playwright.config.ts. Playwright rejects that with "Playwright Test did not expect test() to be called here", naming the config as the likely cause. Cost one run and a refactor into a separate helper/paths.ts.
- I did not know a backend module renders inside an iframe, so four specs' locators found nothing and I debugged them with a throwaway probe spec.

Why it slipped: the skill names three guides in one sentence and leaves the choice to the reader mid-task. By the time I was writing browser specs I was several layers into a nested activation — cleanup → conformance → testing — and was working from the checklist I had already read. static-quality.md got read because the check-layer half of the task named it first and I was in it continuously.

This is not a complaint about the guide's content, which I cannot judge. It is about a pointer that competes with two siblings at the moment of choosing.

## Query

typo3-extension-testing: "After selecting a layer, read only its implementation guide: references/phpunit.md ... references/playwright.md ... references/static-quality.md" — during a task that added a backend Playwright project

## Suggestion

Have typo3_task_guide name the implementation guide alongside the skill when the task's paths or text imply a layer — a Playwright config or a Tests/E2E path in `paths` is a strong signal. A named file arriving in a tool answer is harder to walk past than one of three options in a sentence. Failing that, the skill could put the three guides in a table keyed by the layer chosen in the preceding step, so the selection and the pointer are the same act.
