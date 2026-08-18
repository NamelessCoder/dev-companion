---
date: 2026-08-18T08:11:59+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3-extension-testing, typo3-extension-upgrade, typo3_task_guide
directory: /home/benji/projects/blog
---

# Skills are matched once against the opening framing, so the sub-steps of a bugfix never reached t...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions on TYPO3 v14 without breaking v13.

Filed separately: typo3-extension-upgrade's description fits this task and did not fire. This note is about the structural reason it had only one chance to, which is a different problem and affects the other skills equally.

Skill selection happened once, silently, against the opening request — a German one-liner reporting a symptom with one file selected in the editor. Everything after that was sub-steps, and no sub-step ever re-opened the question of whether a skill owned it. Three did.

- The removal of $GLOBALS['TSFE'] in v14 is typo3-extension-upgrade's "replacing what a major deprecated or removed". Reachable only from the opening line, which did not say so.
- Adding Tests/Unit/ExpressionLanguage/ and Tests/Unit/Listener/ and running phplint, PHPStan, php-cs-fixer, unit and functional is typo3-extension-testing, whose description covers "PHPUnit unit and functional tests ... PHPStan, php-cs-fixer ... and a failing check" almost exactly. By the time I was writing a data provider I was squarely inside that skill's subject, and nothing reconsidered.
- Proving the fix against the running installation had no owner at all (filed separately).

I did all three unaided, from the repository: test style copied from the existing Tests/Unit/ConstantsTest.php, check commands read out of composer.json scripts. The results were fine. The point is not that the outcome suffered; it is that a skill system which only ever fires on the first sentence is being asked to predict a whole session from its least informative moment.

The asymmetry is worth naming: the opening request is where the least is known about what the work will contain, and it is the only moment skills are consulted. A bugfix that turns out to be an API-removal migration, or a one-line change that turns out to need a new test file, is the normal case rather than an edge one.

I can point at where a second look would have paid, precisely. When I created Tests/Unit/ExpressionLanguage/BlogVariableProviderTest.php I first had to go read an existing test to learn the conventions of the repository — the PHPUnit attribute style, the UnitTestCase base class, the header block. A skill that owns extension testing would have carried that, and the moment I opened the first Tests/ file is the moment it should have been offered.

Not asked, but part of the same shape: the git step at the end (branch off master, TYPO3-style [BUGFIX] subject) also went unaided. typo3_commit_message_guide exists and covers non-core repositories with workflow="project". I wrote the message from the repository's own log instead. It came out in the house style, but by imitation rather than by rule.

## Query

One session, one request: fix TypoScript conditions broken on v14, keep them working on v13. Sub-steps performed: reproduce against a running frontend, read core across two branches, design, implement, add unit tests, run the declared check suite (phplint, phpstan, php-cs-fixer, unit, functional), commit on a branch off master. Skills activated: none.

## Suggestion

Treat skill selection as re-evaluable rather than one-shot. The practical hooks are the moments an agent visibly enters a new subject: the first file created under Tests/, the first invocation of a declared check command, the first git branch or commit, the first edit to Documentation/. Any of those is a stronger signal about which workflow is in play than the opening sentence was.

Where that cannot be enforced from the server side, the cheaper version is in the descriptions: state the mid-task entry points explicitly, so a skill is reachable from what an agent is doing rather than only from what the user first asked. typo3-extension-testing would be reachable from "about to add a test file to an extension that already has a suite" or "about to run the checks a repository declares" — neither of which reads like the opening of a bug report, which is exactly why it should be written down.

typo3_task_guide is the natural owner of this: it is described as giving the workflow a task belongs to, and it could as usefully answer "which workflow does this step belong to" when called mid-session with the paths just touched.
