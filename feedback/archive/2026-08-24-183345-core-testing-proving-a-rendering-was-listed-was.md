---
date: 2026-08-24T18:33:45+00:00
category: idea
status: closed
closed: 2026-08-25
model: claude-opus-5
tool: typo3-core-patch-review, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# core/testing/proving-a-rendering was listed, was exactly what I needed, and I never opened it

## Observation

Task: review Gerrit change 91127 and settle whether a functional test can cover it, against a 15.0.0-dev core checkout.

My client (Claude Code) shows no MCP resource list. The only place I learned these documents exist is the `guides` array at the end of typo3_project_describe, which listed 17 ids. That mechanism worked — the description is right that it is the only place a client rendering no resource list finds them.

I read exactly one whole: typo3_rule_lookup(documentId "core/contribution/gerrit-workflow"). It carried its procedure end to end and it is the single most valuable answer of the session — see the separate note on what it saved me from. No complaints there.

The one I should have read and did not is core/testing/proving-a-rendering. The review skill names it, in these words: "A diff that changes what the frontend renders is a class of patch where reading is not evidence at all. TypoScript defaults, TypoScript declared in an ext_localconf.php, anything below lib.parseFunc ... What settles it is a throwaway functional test that renders one snippet and prints what came out, and building one is typo3_rule_lookup with documentId=core/testing/proving-a-rendering".

I read that paragraph and skipped the document, because my diff is PHP — three lines in PageContentErrorHandler and a visibility change — and none of the trigger words applied. But what the page is described as carrying ("which cObj renders the snippet, which operator form takes markup that spans lines, and how the output is got out of a test that would otherwise print nothing") is precisely what I then spent six round trips working out by hand:

- built a functional test, ran it, it passed with and without the patch
- added fwrite(STDERR, ...) of the response body to see what actually came out
- instrumented the fixture userFunc to print strlen($pageRenderer->getState()['bodyContent']) at throw time, which returned 0 and was the finding that reoriented the whole review
- read RequestHandler and PageRenderer source to find why
- rewrote the TypoScript fixture with distinguishable markers in body, headerData, footerData and meta

The "how the output is got out of a test that would otherwise print nothing" line is the one I needed on round trip one. Every one of those steps was about proving what a rendering contains, even though the diff that caused the rendering to change was PHP.

So the finding is about the gate rather than the page: the page is filed under what kind of *diff* is being reviewed, and it is needed by what kind of *evidence* is being gathered. Those are not the same set, and the second is much larger.

## Query

typo3_project_describe (read the `guides` array — 17 documents), then the typo3-core-patch-review skill body, section "Verification is the project's own, and it is narrowed by the diff", which names documentId "core/testing/proving-a-rendering" only for diffs that change what the frontend renders. The diff under review was typo3/sysext/core/Classes/Error/PageErrorHandler/PageContentErrorHandler.php plus a protected-to-public change in PageRenderer.php.

## Suggestion

Widen the trigger in the skill from "a diff that changes what the frontend renders" to "any finding that has to be settled by what a rendering contains" — a PHP change to the frontend request pipeline, an error handler, a middleware, a PageRenderer or AssetCollector caller all land there without matching the current wording.

Second, make typo3_test_run_guide route to it. That tool is already called with the changed paths and already knows a functional test is the applicable suite; a line saying "to see what the rendering actually produced rather than only whether the suite passed, read typo3_rule_lookup documentId core/testing/proving-a-rendering" costs nothing and arrives at the moment the test is about to be written rather than in a paragraph about TypoScript.

Third, if the page does not currently cover getting output out of a test whose subject is a PHP class rather than a TypoScript snippet — instrumenting a fixture, printing a service's state mid-request, writing to STDERR from inside a functional run — that is the half I needed and could not have got from the title.
