---
date: 2026-08-04T05:56:58+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_scope, typo3_extension_scope
directory: /home/benji/projects/ext-guidedtour
---

# Task: add a code style fixer to a standalone TYPO3 14.3 extension repository. This is the half th...

## Observation

Task: add a code style fixer to a standalone TYPO3 14.3 extension repository. This is the half that worked and must not be broken.

Two fields of typo3_project_scope decided the shape of every command I ran afterwards:

1. `environment: {via: "ddev", php: "8.4", entered: false, hooks: [...]}`. My own shell reported PHP 8.3.23; the container runs 8.4.18. Because the answer named both the environment and its interpreter as a different one from the caller's, I ran every single command through it — `ddev composer require --dev`, `ddev composer ci:php:cs`, `ddev exec .build/bin/php-cs-fixer`. Without that field the natural move is `composer require` in the host shell, which resolves dev dependencies against 8.3 and writes a lock file the container would then have to disagree with. The field is doing real work precisely because it contradicts what the shell reports, and the `entered: false` flag says the contradiction is live.

2. `commands: []`. This is a negative answer and it was worth as much as a positive one: it told me the repository declares no composer or npm scripts at all, so the two commands I was about to write were the first ones rather than a second answer beside an existing wrapper. I named them ci:php:cs / fix:php:cs and later ci:editorconfig / ci without having to check whether something equivalent already existed under another name.

typo3_extension_scope added the same kind of negative evidence: `artifacts.tests: []` and `artifacts.manual: null`, with `readme: "README.md"` present. A file listing shows what is there; this showed that there is no test layer and no Documentation/ to extend, which is what turned "add a fixer" into "establish the first check" rather than "extend the existing harness". Its `classes.directories` listing (EventListener 2 files, Utility 1) also cross-checked the fixer's own count: php-cs-fixer reported 7 files, which is exactly those 3 plus Configuration/JavaScriptModules.php, ext_localconf.php, ext_emconf.php and the config file itself. I could confirm the finder's scope without opening a single one of them.

Both calls cost one round trip each and neither had to be repeated with different arguments.

## Query

typo3_project_scope() and typo3_extension_scope(extension="guidedtour") as the first two calls of the session

## Suggestion

Keep the environment block, and keep it reporting the container's interpreter separately from the caller's — that is the field that prevented a whole class of wrong-interpreter work. Keep `commands: []` and `artifacts.tests: []` as explicit empty answers rather than omitted keys: the absence is the finding. If the answer ever grows, do not drop `entered`, which is what says whether the contradiction between the two PHP versions is currently live.
