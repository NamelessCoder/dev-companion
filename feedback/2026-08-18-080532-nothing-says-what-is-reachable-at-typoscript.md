---
date: 2026-08-18T08:05:32+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_hint_lookup
directory: /home/benji/projects/blog
---

# Nothing says what is reachable at TypoScript condition evaluation time, and #107831's migration a...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions, broken on TYPO3 v14, while keeping them working on v13.

The extension's ExpressionLanguage variable provider read $GLOBALS['TSFE']->page ?? []. On v14 that yields [] silently — ?? suppresses the property-read on null — so both conditions evaluate to false and blog posts render with the wrong page template, with no error anywhere.

Finding the cause was easy; the changelog says it. Breaking-107831-RemovedTypoScriptFrontendController states $GLOBALS['TSFE'] is gone and prescribes the replacement: "$pageInformation = $request->getAttribute('frontend.page.information')". For code running inside an ExpressionLanguage provider that advice is unusable, and following it is the trap this task was actually made of: there is no request in scope, and the obvious way to get one does not work either.

What no document I could find states, and what decided the whole fix:

1. $GLOBALS['TYPO3_REQUEST'] is NOT a v14 substitute at condition time. On 13.4 it is assigned in Frontend\Middleware\TypoScriptFrontendInitialization, i.e. before the prepare-tsfe-rendering middleware that compiles TypoScript and evaluates conditions — so it works there. On 14.1/14.3 that middleware is gone and the only frontend assignment is in Frontend\Http\RequestHandler::handle(), which runs after the entire middleware stack. A fix built on TYPO3_REQUEST therefore passes on v13 and silently fails on v14 in exactly the same way the original bug did. I established this by grepping for assignments to that global across origin/13.4, origin/14.1 and main in a core checkout.

2. The variables a TypoScript condition is handed are assembled in Core\TypoScript\IncludeTree\Visitor\IncludeTreeConditionMatcherVisitor::initializeExpressionMatcherWithVariables and, for the frontend, in PrepareTypoScriptFrontendRendering::prepareConditionMatcherVariables: request (wrapped in RequestWrapper), page (the resolved page record array), site, siteLanguage, plus enriched context/tree/frontend/backend/workspace. pageId, localRootLine and fullRootLine are deliberately unset again. v13 additionally passed 'tsfe'; v14 dropped it. This list is what a condition can actually use, and it is not the same list as what the removal changelog points at.

3. The reachable替 replacement across both majors is Frontend\Event\AfterPageAndLanguageIsResolvedEvent, dispatched from PageInformationFactory::create() — before condition matching on both 13.4 (tsfe middleware) and 14.x (prepare-tsfe-rendering). The event class is byte-identical on both branches. I verified this by diffing the two files.

The server's own doesNotCover correctly excludes "PHP source as code", and reading the classes was right. But point 1 is not a signature or an @internal marker — it is a version-bound statement about when a global is populated relative to the middleware order, which is exactly the kind of thing a changelog or hint exists to carry, and getting it wrong produces a fix that looks correct and is not.

Related but not the same as the already-open "Nothing answers about an API at a TYPO3 major other than the installed one": that one is about reaching a non-installed major at all. This one would still be missing if both majors were installed.

## Query

Task text (translated from German): "these typoscript conditions seem to be broken in v14 and no longer work, please validate that and fix it, they must still work in v13" — with Classes/ExpressionLanguage/BlogVariableProvider.php open, whose methods read $GLOBALS['TSFE']->page. Installation: /home/benji/projects/blog, typo3/cms-core v14.3.6, must also support ^13.4.15. No tool of this server was called during the work; this is what a call would have had to answer.

## Suggestion

Two places could carry it.

typo3_changelog_lookup: where an entry's migration prescribes reading something off the request, note the execution phases where no request is in scope. Breaking-107831 and Breaking-107473 (getTSFE() removed, "replace with request?.getPageArguments()?.getPageId()") both send a reader to the request; for TypoScript conditions that only works because the condition matcher injects a RequestWrapper as a variable, not because PHP code at that point can obtain one.

typo3_hint_lookup: a hint — "typoscript-conditions" — stating, per covered major: which variables the condition matcher hands over (request/page/site/siteLanguage/tree/context/frontend/backend/workspace, and that v13 also had tsfe); that condition evaluation happens inside the middleware stack, so $GLOBALS['TSFE'] is absent from v14 and $GLOBALS['TYPO3_REQUEST'] is unset on v14 though set on v13; and that AfterPageAndLanguageIsResolvedEvent is dispatched before condition matching on both 13.4 and 14.x, which makes it the version-neutral way for an extension to get the current page record into a condition.
