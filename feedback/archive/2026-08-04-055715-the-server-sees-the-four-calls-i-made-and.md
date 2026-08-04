---
date: 2026-08-04T05:57:15+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_documentation_lookup, typo3_server_scope, typo3_changelog_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: add a code style fixer to a TYPO3 14.3 extension. The server sees the four calls I made and...

## Observation

Task: add a code style fixer to a TYPO3 14.3 extension. The server sees the four calls I made and nothing about the ones I decided against, so here they are.

- **typo3_task_guide** — I loaded its schema with ToolSearch in the same batch as the scope tools and then never called it, although the skill's own references/base.md prescribes it as step 3 of a fixed order. The reason is concrete: by the time I would have called it I had already read references/static-quality.md, which carries the workflow for this exact layer — resolve dependencies from the package's range, one command per check, keep check and fix apart, prove it. I assumed task_guide would restate that. The assumption is untested. If it is right, the skill and the tool overlap and the order in base.md costs a round trip for nothing on a task the skill already covers; if it is wrong, the tool needs to say what it adds over the skill that routed to it.
- **typo3_documentation_lookup** — schema loaded, never called. I assumed docs.typo3.org has nothing about setting up php-cs-fixer or .editorconfig in an extension, because that is repository tooling rather than TYPO3 API. Untested. It held in the sense that I finished the task without it, which is not the same as it having been the right call.
- **typo3_changelog_lookup** — base.md step 5 prescribes a deprecation sweep before reading the checkout. I skipped it deliberately: the change was tooling and whitespace, touching no TYPO3 API. I would skip it again. Where a task establishes a check rather than changing code, the prescribed order asks for a sweep whose result cannot change anything.
- **typo3_server_scope** — never called and never even loaded, although the MCP server instructions name it as the thing that says what is covered, at which depth, by which tool. I went straight to the tools the skill named. Had I called it first I might have learned in one round trip that coding standards are not covered, instead of spending a hint_lookup to find that out.

How I reached the tools I did use: the deferred-tool list in the system prompt gave me exact names, and I fetched schemas with ToolSearch `select:` on those names — never by keyword search, never by guessing. The skill's base.md told me which of them to call and in what order. So the naming worked, with one exception worth recording: I searched the hint corpus with the words "coding standards", "php-cs-fixer" and "editorconfig", and the corpus spells this subject "static analysis" and covers only PHPStan under it. The vocabulary gap and the coverage gap were the same gap.

## Query

Tools whose schemas I loaded or read and never called during: "add a code style fixer like php-cs-fixer to the extension" (TYPO3 14.3 extension repository)

## Suggestion

Say in typo3_task_guide's description what it adds when a skill has already routed the task — if it is the same workflow the skill carries, base.md should not spend a step on it for tasks the skill covers end to end. Consider having the skills point at typo3_server_scope as step zero for any task whose subject might not be covered at all: a caller who learns "coding standards: not covered" in one call stops looking, whereas a caller who gets three near-miss hints keeps rephrasing.
