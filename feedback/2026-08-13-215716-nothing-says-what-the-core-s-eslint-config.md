---
date: 2026-08-13T21:57:16+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# nothing says what the core's ESLint config actually enforces, so lintTypescript is the only way t...

## Observation

Audit of an agent's own accumulated project notes against what this server answers. This one survived, and it is the one I am least sure belongs to the server — filing it so somebody who knows can decide.

The note distils Build/eslint.config.mjs, with one learned item at the top: @typescript-eslint/member-ordering is set to "error", and the ordering it enforces puts the constructor before all static methods. That is described in the note as "the most common lint failure when adding static methods", which is a statement about where sessions actually lose time rather than about the config.

The rest is the config read out: 2-space indent and semicolons via @stylistic, single quotes, curly always, object-curly-spacing "always", eol-last, no-multi-spaces, space-infix-ops; prefer-readonly, prefer-string-starts-ends-with, naming-convention PascalCase for classes and types, no-unused-vars as error; no-explicit-any and no-inferrable-types explicitly OFF; default-case, guard-for-in, no-var, no-eval, no-empty-function with constructors exempt; and the lit/wc rules — prefer-nothing, no-constructor-params, require-listener-teardown.

typo3_hint_lookup for it returned backend-typescript and backend-ui. Both are about the right subject and neither names a single rule: backend-typescript says where the sources live, that the generated JavaScript is committed, and to "Prefer existing TYPO3 backend imports, event patterns, and custom element conventions used nearby"; backend-ui is about custom element design. "Conventions used nearby" is the closest the server comes, and it is advice to imitate rather than a rule to check against.

Two things argue this is worth carrying and one argues against.

For: the two rules that are OFF are not guessable and change how you write. no-explicit-any off and no-inferrable-types off both permit code that a reader coming from another TypeScript project would "fix" into a diff nobody asked for. And member-ordering being on at all is unusual enough that the failure is surprising when it lands.

Also for: I hit the neighbouring case in this same session. Reviewing a patch that converts a Playwright fixture to an all-static class, I wanted to know whether the ordering held, and whether missing spaces after commas and two trailing blank lines were lint failures or only untidy. Answering it meant reading the config file, where comma-spacing and no-multiple-empty-lines turn out not to be configured — so those went into the review as cosmetics rather than as blockers, which is a different finding.

Against: it is one file in the checkout, and CI=true ./Build/Scripts/runTests.sh -s lintTypescript answers authoritatively in about a minute, which I also ran and which passed. The server's own principle is that what the checkout holds is mine to read.

## Query

typo3_hint_lookup(task: "backend TypeScript ESLint code style conventions building JavaScript modules") — returned backend-typescript and backend-ui, neither naming a rule

## Suggestion

If it is carried at all, carry the decisions rather than the rule list — the list goes stale on the next config commit and the checkout is always right. What does not go stale is: member-ordering is enforced and it is what fails; no-explicit-any and no-inferrable-types are deliberately off, so neither is a finding; and the way to settle any question about it is -s lintTypescript rather than reading the config.

The smaller fix that would have been enough for me: one line in backend-typescript naming Build/eslint.config.mjs as where the style is decided and -s lintTypescript as what checks it. "Conventions used nearby" currently sends a reader to imitate neighbouring files, which is how a rule that is OFF gets treated as a rule that is on.
