---
date: 2026-08-01T11:52:20+00:00
category: idea
status: open
model: GPT-5 mini
tool: typo3patchreview
directory: /home/benji/projects/typo3-cms
---

# Proposal: Add a dedicated MCP skill `typo3-patch-review` to support automated patch reviews.

## Observation

Proposal: Add a dedicated MCP skill `typo3-patch-review` to support automated patch reviews.

Rationale
- Repeated manual steps during reviews: parsing diffs, locating affected processors, running focused static checks, running bootstrap-aware PHPUnit, and performing lightweight runtime checks (thumbnail route) are common and error-prone when done ad-hoc.
- A single skill would standardize and automate these steps, reduce file reads and fragile grep searches, and produce reproducible review artifacts for maintainers.

Skill specification (suggested)
- Name: `typo3-patch-review`
- Purpose: Analyze a patch or branch and produce a structured review report with focused checks and suggestions.

Inputs
- `patch` (string, optional): raw unified diff. Mutually exclusive with `branch`.
- `branch` (string, optional): branch name to compare with default branch.
- `scope` (string|list, optional): restrict to subsystems (e.g., core, backend, online-media).
- `runTests` (bool, default false): whether to run focused PHPUnit suites.
- `runStaticChecks` (bool, default true): run phpstan/php-cs-fixer on changed files.
- `runtimeChecks` (bool, default false): invoke lightweight runtime checks (HTTP routes) if a test instance is available.
- `maxRuntimeSeconds` (int, optional): timeout for runtime checks.

Actions
1. Parse diff/branch; list changed files and map to subsystems.
2. Resolve config-registered mappings (e.g., `processingTaskTypes`, `SYS.fal.processors`) to find impacted processors/classes.
3. Run static analysis and formatting checks only on changed files; return results.
4. Run focused PHPUnit tests related to changed files (using repo bootstrap such as `Build/bin/phpunit`) — return filtered results and traces.
5. If `runtimeChecks` enabled and a backend URL is reachable, run selected checks (e.g., `GET /resource/request-thumbnail` for changed resource processing code), follow redirects, capture Content-Type and response body size.
6. Summarize risks (missing processors, changed public APIs, regression candidates), suggest fixes, and produce PR-ready comment text.
7. Optionally file a `typo3_feedback_record` note with findings if the skill detects knowledge-server gaps.

Outputs
- `report` (object): changedFiles, staticChecks, unitTests, runtimeChecks, risks, suggestions.
- `summary` (string): short human-readable summary suitable for PR comment.
- `artifacts` (list): logs, failing test traces, captured HTTP responses (if any), and generated diffs.

Safety and limits
- Runtime checks are opt-in and sandboxed by timeout.
- Use the configured repo bootstrap for tests; skill should not try to alter repo state.

Why this helps
- Consolidates repeated manual steps; avoids broad grep searches and trial-and-error test runs; speeds reviewers and improves reproducibility.

Requested changes to existing MCP endpoints
- Add `processingTaskTypes` and `SYS.fal.processors` to `typo3_project_scope` output (or provide a new MCP call to return these mappings). This reduces file scanning to find registered processors.

Model: GPT-5 mini
Category: idea
Tool/feature suggested: typo3-patch-review
Suggestion: Implement the above skill and add the config mappings to `typo3_project_scope` output.

## Query

Propose new MCP skill 'typo3-patch-review' to automate patch reviews

## Suggestion

Implement skill and include processingTaskTypes/SYS.fal.processors in project scope output.
