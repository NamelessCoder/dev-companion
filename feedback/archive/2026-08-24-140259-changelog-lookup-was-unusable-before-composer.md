---
date: 2026-08-24T14:02:59+00:00
category: missing-knowledge
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/ext-usercentrics
---

# changelog_lookup was unusable before composer install and I never learned it became usable after

## Observation

Task: establish what TYPO3 v14 removed that a v11/v12 extension calls, before any dependency was installed.

My first call to this server was typo3_changelog_lookup with query "registerTagAttribute registerUniversalTagAttributes". It came back:

{"unsupported":{"cause":"no-installation","reason":"no TYPO3 installation was found whose core package ships the changelog","searched":["/home/benji/projects/ext-usercentrics","/home/benji/projects","/home/benji","/home","/"]}}

Correct behaviour — nothing was installed yet. The consequence is what I want to report: I fell back to a TYPO3 core git checkout I happened to have at /home/benji/projects/typo3-cms and answered the question with git grep against tags v13.4.34 and v14.3.6. That worked, and it is how I established every API question for the rest of the session: BeforeJavaScriptsRenderingEvent identical across majors, SettingsTypeInterface identical, SiteSettings::create() changing signature in 13.4.15 (found with git log -S), Fluid 4 vs 5 TagBuilder::addAttribute signatures, AbstractTagBasedViewHelper::initialize behaviour.

Roughly two hours later the repository had TYPO3 14.3.6 installed and typo3_project_describe confirmed it. I never called typo3_changelog_lookup again. I assumed the earlier refusal still held. That assumption was never tested and was probably wrong: the entries I wanted — Deprecation-104223 Fluid standalone methods, Breaking-108148 Fluid 5.0, Breaking-108304, Deprecation-108345 — were all in the installed package by then, and I read three of them by cat-ing files out of the core checkout instead.

So the tool was available for most of the session and I did not use it once, because the first answer taught me it could not help and nothing later corrected that.

## Query

typo3_changelog_lookup query="registerTagAttribute registerUniversalTagAttributes" limit=10, called before composer install had run in /home/benji/projects/ext-usercentrics. Not called again after typo3/cms-core 14.3.6 was installed in the same repository.

## Suggestion

The no-installation answer should say what makes it answerable, in one line: "install the dependencies and ask again, or point the server at a checkout". It currently reports the paths it searched, which reads as a dead end rather than as a precondition that the session is about to satisfy — this task installed TYPO3 an hour later and nothing told me the tool had come alive. If the server can see that the repository declares typo3/cms-core but has no vendor directory yet, saying so would turn the refusal into an instruction.
