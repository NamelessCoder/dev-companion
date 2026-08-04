# State how an extension's coding standards are set up, and route the task to it

**Serves:** feedback/2026-08-04-055420-task-add-a-code-style-fixer-php-cs-fixer-to-a.md, feedback/2026-08-04-055715-the-server-sees-the-four-calls-i-made-and.md
**Priority:** normal

Step 1a, on the evidence in
[`D-KNW-055`](../../decisions/knowledge/knw-055-the-first-check-a-standalone-extension-repository-gets-is-a-gap-this-server-owns.md):
the corpus holds the analyser half of the static-quality layer and nothing of
the fixer half, and it spells the whole layer with the analyser's words. Read
`typo3/coding-standards` before writing anything — it is in no checkout and in
no environment, so require it into `.environments/e-site-14.3` or a scratch
Composer project and read `templates/extension_php-cs-fixer.dist.php`,
`src/CsFixerConfig.php` and `src/Setup.php` there rather than the feedback's
account of them: what `typo3-coding-standards setup` takes for its type and its
rule set, what `CsFixerConfig::create()` excludes, and whether those excludes
are literal directory names — which is what decides whether the shipped
`->in(__DIR__)` template is wrong for a package whose `vendor-dir` sits under
`.build/`. Then write it as one hint beside `extension-static-analysis` in
`knowledge/hints/testing.json`, with the `appliesTo` needles a fixer task
actually carries (`php-cs-fixer`, `coding standards`, `.editorconfig`,
`code style`) rather than the analyser's; add the intent for the layer to
`knowledge/task-intents.json` with `typo3-extension-testing` as the skill that
owns it, so `typo3_task_guide` stops answering the task with `intents: []` and a
core patch checklist; and correct the `covers` line in
`knowledge/server-scope.json` that says "setting up static analysis for an
extension" for a layer with two halves.
`bin/cli hints:probe "coding standards php-cs-fixer setup for an extension"` is
what shows it landed, and the commit that ships it archives both feedback.
