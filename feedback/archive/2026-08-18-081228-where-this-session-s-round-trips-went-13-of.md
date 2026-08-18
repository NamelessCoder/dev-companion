---
date: 2026-08-18T08:12:28+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_changelog_lookup, typo3_reference_list
directory: /home/benji/projects/blog
---

# Where this session's round trips went: 13 of roughly 30 answered one uncovered question about the...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions on v14 without breaking v13. This note is cost data for prioritising the gaps the session exposed, since a maintainer reading five findings cannot tell which one was expensive.

Roughly 30 tool round trips, all Bash. Where they went:

13 round trips — what exists at TypoScript condition evaluation time, per major. Split: 6 establishing what a condition can see at all (DefaultProvider, TypoScriptConditionProvider, Typo3ConditionFunctionsProvider, Resolver, AbstractProvider, ProviderInterface, FrontendTypoScriptFactory, IncludeTreeConditionMatcherVisitor, prepareConditionMatcherVariables on both branches); 3 establishing that $GLOBALS['TYPO3_REQUEST'] is assigned before condition matching on 13.4 but only after the middleware stack on 14.x, which required reading both branches' RequestMiddlewares.php and both RequestHandlers; 3 finding a PSR-14 event that fires before condition matching on both majors and confirming the class is identical there; 1 spent ruling out a request-scoped DI service in v14, which returned nothing and was still necessary.

7 round trips — proving the verdict against the running frontend, including about 2 lost to a marker that did not discriminate. Filed separately.

2 — whether GeneralUtility::makeInstance does constructor injection, and on both majors.
2 — repository conventions: Services.yaml, the Listener directory, the existing test style.
2 — locating the changelog entries by listing a directory and guessing at filenames, where typo3_changelog_lookup exists and would have searched by words.
The remainder went on writing files, the declared check suite, and git.

So a single uncovered subject — what the frontend request has assembled at each point, per major — took about 43 percent of the session, and the fix itself was small: one modified class, two new ones, two test files. The imbalance is the finding. Everything expensive was archaeology to establish facts that do not change between projects and are the same for every extension that touches the frontend lifecycle.

The three sub-questions inside that 13 are one document's worth of material: what the condition matcher is handed and by whom; when each superglobal is populated relative to the middleware stack, per major; and which PSR-14 events fire before which stage. I answered all three by git-grepping two branches. Any extension author hitting the v14 removal will answer them the same way.

Two things this accounting also shows, in fairness. The cross-branch git reads were genuinely efficient — one `git show origin/13.4:<path>` against the same path on main settles a version question in a single round trip, which no lookup beats. And nothing here failed: the 30 round trips produced a correct fix that I verified end to end on the installed major. The cost was in establishing what should have been lookup-able, not in getting anything wrong.

## Query

Round-trip accounting for one session, counted from the transcript. Task (translated from German): "these typoscript conditions seem to be broken in v14 and no longer work, please validate that and fix it, they must still work in v13". ext:blog at /home/benji/projects/blog on typo3/cms-core 14.3.6, core checkout with origin/13.4, origin/14.1 and main at /home/benji/projects/typo3-cms. Zero calls to this server during the work; all counts are Bash round trips.

## Suggestion

Prioritise by this: the frontend request lifecycle is one hint-sized document that would have collapsed about 13 round trips in one session, and it is the same document for every caller who hits a removed global.

Shape it as a table rather than prose, keyed by stage of the frontend middleware stack, with a column per covered major, saying at each stage: which request attributes exist, which superglobals are populated, which PSR-14 events have fired, and what is compiled. The questions it must answer directly, because they are the ones that cost the round trips: at the moment TypoScript conditions are evaluated, what is available — and the answer differs between 13.4 and 14.x for both $GLOBALS['TSFE'] and $GLOBALS['TYPO3_REQUEST'], which is the whole trap.

typo3_reference_list would help beside it: typo3/sysext/frontend/Configuration/RequestMiddlewares.php and typo3/sysext/frontend/Classes/Event/ are the two directories that answer the ordering question, and neither is discoverable as a reference today.
