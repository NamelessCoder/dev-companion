---
date: 2026-08-24T18:36:09+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# I never asked where PageRenderer state is filled and cleared in the FE pipeline, and it was the w...

## Observation

Task: review Gerrit change 91127 and prove by test whether it fixes what it claims, against a 15.0.0-dev core checkout.

The decisive question of the session was: at which points in a frontend request does PageRenderer hold state, and what clears it. I never put that to this server. I assumed it would not have a per-method account of the frontend rendering pipeline and went to source, and the assumption held in the sense that I got the answer — but it cost roughly a third of the session and it produced a finding that changes the patch's own commit message.

What I read, in order, and would read again: RequestHandler::handle(), generatePageContent(), generatePageBodyContent(), calculateNonCachedElements(), recursivelyReplaceIntPlaceholdersInContent(), then PageRenderer::render(), renderFrontendPage(), renderPageWithUncachedObjects(), renderJavaScriptAndCssForProcessingOfUncachedContentObjects() and reset().

The finding: renderPageWithUncachedObjects() sets `$this->bodyContent = ''` at PageRenderer.php:1000, with the comment "Reset body content to empty string so the content is not cached twice". git blame puts that on 5c776d4f872 "[BUGFIX] Avoid caching content twice in cache_pages", 2025-12-03, and `git branch -r --contains` puts it on main, 14.3 and 14.1 — every branch the patch under review targets. So the symptom the commit message describes ("the 404 page output gets appended to the original page content") no longer occurs on either target branch. My first functional test, which asserted only that, passed with and without the patch. What the patch still fixes there is headerData, footerData, JS, CSS and inline assets, which I established by rewriting the fixture with markers in each.

A second reading of the same kind: PageRenderer::reset() does not touch metaTagRegistry, so with the patch applied the 404 page still serves the requested page's <meta name="description"> and loses its own. And reset() clears jsLibs but not cssLibs. Neither is documented anywhere I could find; both came from reading reset() against the property declarations.

A third: typo3/sysext/frontend/Tests/Functional/SiteHandling/ runs on JsonRenderer.typoscript, which sets config.disableAllHeaderCode = 1, so generatePageContent() returns before processHtmlBasedRenderingSettings() and addBodyContent() — meaning no test in that class can ever fail on a PageRenderer state leak. That is what settled a scope question for the user, and it is a property of a test fixture that no lookup would plausibly carry.

typo3_hint_lookup is the tool nearest to this and I never called it directly — typo3_task_guide returned omittedHints: [] and stated it had carried everything matching those paths, which was correct behaviour and saved a call. Its core-tests hints were genuinely useful for writing the test. But nothing in them or anywhere else describes the request pipeline's state lifecycle.

## Query

Never asked. The questions I would have put, had I expected an answer: "when is PageRenderer bodyContent populated and cleared during a frontend request in TYPO3 15"; "which PageRenderer properties does reset() not reset"; "does config.disableAllHeaderCode skip PageRenderer head assembly". Established instead by reading typo3/sysext/frontend/Classes/Http/RequestHandler.php and typo3/sysext/core/Classes/Page/PageRenderer.php, plus git blame and git branch -r --contains on 5c776d4f872.

## Suggestion

A hint under an id such as frontend-render-pipeline-state, matched by paths typo3/sysext/frontend/Classes/Http/RequestHandler.php and typo3/sysext/core/Classes/Page/PageRenderer.php, saying the order of the phases and where state crosses them: cacheable content generation, processHtmlBasedRenderingSettings, addBodyContent, renderPageWithUncachedObjects (which empties bodyContent and deliberately does not call reset, because the state is serialised into the page cache), then calculateNonCachedElements running USER_INT/COA_INT with the rest of the state still populated, then renderJavaScriptAndCssForProcessingOfUncachedContentObjects which does reset. Plus the two asymmetries: reset() clears jsLibs but not cssLibs, and does not touch metaTagRegistry, title, favIcon, inlineSettings or inlineLanguageLabels.

That single hint would have saved most of a session and would have named finding 1 above without a git blame. It is also exactly the knowledge anybody debugging "why does my asset appear on the wrong page" needs, and it is currently only in the source.

Smaller: a hint that functional tests under frontend/Tests/Functional/SiteHandling run on JsonRenderer.typoscript with disableAllHeaderCode = 1, so they cannot observe anything the PageRenderer assembles into <head>. That is a trap for anybody adding a test there.
