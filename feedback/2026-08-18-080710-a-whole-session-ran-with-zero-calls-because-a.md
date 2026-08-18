---
date: 2026-08-18T08:07:10+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_server_scope, typo3_changelog_lookup, typo3_documentation_lookup
directory: /home/benji/projects/blog
---

# A whole session ran with zero calls because a multi-branch core checkout outcompeted the server, ...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions, broken on TYPO3 v14, while keeping them working on v13.

I called this server zero times while doing the work. Every fact came from Bash: cat, grep, sed and git against the project and against a core checkout that had origin/13.4, origin/14.1 and main available side by side. The server sees no calls and therefore cannot see why, so here it is.

Three reasons, in order of weight.

1. The checkout won on the merits, and your own scope says so. doesNotCover names "PHP source as code: a method signature, whether a class or member is @internal or public API, an implementation to copy — instead: Read the class." Nearly this entire task was that: which classes still exist on 14.3, where a global is assigned, what the condition matcher passes, whether an event class is identical on two branches. `git show origin/13.4:<path>` against `<path>` on main answered every one of those in a single call, with both majors in one diff. No lookup competes with that, and it should not try.

2. Every tool arrived deferred, behind ToolSearch. So the first call of the session costs two round trips before any answer, at a moment when I already had a shell that answers in one. The already-open note "Every tool arrives deferred, so each first use costs a schema fetch the skill could have batched" covers the mechanism, so I am not re-filing it — but this session is the harder version of that evidence: the friction did not make the first call late, it made it never happen. The instruction "Start every task with typo3_project_describe" lost to a two-round-trip entry price against a cat of composer.json.

3. Nothing in the task named a subject the server advertises loudly. I was not writing backend markup, choosing an icon, or wording a label — the three cases the instructions single out as failing only at runtime if guessed. My subject was frontend TypoScript conditions, which reads as "read the source".

What that cost, concretely: I read the changelog entries Breaking-107831 and Breaking-107473 by `ls`-ing typo3/sysext/core/Documentation/Changelog/14.0/ and grepping filenames. typo3_changelog_lookup covers exactly that and would have been better — searched by words rather than by my guess at a filename, and it would have reached versions above the installed major. That is the one call I would make next time.

What did NOT cost me anything, and I want on record so it is not "fixed": typo3_project_describe would have told me the declared commands, and I got them right anyway by reading composer.json scripts and running exactly test:php:lint, phpstan, test:php:unit, test:php:functional and cgl. The console form the scope reports — ddev exec -- /var/www/html/.build/bin/typo3 — is what I independently arrived at as `ddev exec .build/bin/typo3 cache:flush`. Discovery had the installation right: root, kind composer-project, console reachable via ddev, PHP 8.2. All correct.

What I never put to it, and whether the assumption held: I assumed typo3_documentation_lookup would not carry middleware execution order or the population moment of a superglobal, so I never asked. I still think that holds, but it is an assumption, not a test. I assumed typo3_forge_lookup and typo3_gerrit_lookup were core-only and irrelevant to an extension repository — the scope confirms it, so that one held. I read typo3_schema_lookup's name and passed it over; doktype constants live in the extension's own Classes/Constants.php, which was right.

On resources: my client never showed me a typo3://guides list. I did not learn those documents exist until I read typo3_server_scope for this debrief, after the work was finished — the same finding as the already-open "The guides list is only visible inside typo3_project_describe". None of the listed guides covers this task, so nothing was lost here, but the session confirms the visibility problem from a second angle: a session that calls nothing at all never sees the lis [cut: 650 characters past the 4000-character limit]

## Query

Whole-session report. Task text (translated from German): "these typoscript conditions seem to be broken in v14 and no longer work, please validate that and fix it, they must still work in v13". Working directory /home/benji/projects/blog (ext:blog, typo3/cms-core v14.3.6, DDEV up). Second working directory available: a TYPO3 core checkout at /home/benji/projects/typo3-cms with origin/13.4, origin/14.0, origin/14.1 and main. Tool calls to this server during the work: none. typo3_server_scope and typo3_feedback_list were called only afterwards, for this debrief.

## Suggestion

Two things follow.

First, treat "a core checkout with several branches is present" as a first-class situation rather than competition. The server knows the installed version; it does not know I had 13.4 and 14.1 checked out beside it. Where a question is cross-major, the best answer this server can give may be the git invocation that settles it — `git show origin/13.4:<path>` against the same path on the target branch — named in the answer instead of prose. That turns the checkout from a reason not to call into a reason to call.

Second, the entry price. Since the deferred-schema mechanism is already reported, the point specific to this session is what a caller sees before paying it: nothing in the task text said "TYPO3 knowledge server". If the routing table's "Starting work on a TYPO3 major you have not built on recently ... before asking what a version changed → typo3_changelog_lookup" were reachable without a schema fetch, that one line would have caught this session — it describes it precisely.

Concretely for the covered topics: typo3_changelog_lookup is the tool this task should have used and did not, and the reason is discoverability rather than capability.
