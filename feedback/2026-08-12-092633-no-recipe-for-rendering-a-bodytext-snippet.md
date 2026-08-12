---
date: 2026-08-12T09:26:33+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_hint_lookup, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# no recipe for rendering a bodytext snippet through lib.parseFunc_RTE, so the probe that decided t...

## Observation

Task: review Gerrit change 95169, a three-line TypoScript edit to lib.parseFunc_RTE, and say what it does. Nothing in the diff says what it renders, and no core test covers figcaption through parseFunc — I grepped, and the 98 nearest functional tests (fluid_styled_content, frontend/Rendering, HtmlViewHelperTest) stayed green with the patch applied. So the only way to know the effect was to render it. Every substantive finding in my review came out of that probe and none of it out of reading.

I built the probe by hand from ParseFuncTest.php and paid for it. Six container rounds at roughly two minutes each; three of them produced nothing but my own harness mistakes:

1. I copied ParseFuncTest's FLUIDTEMPLATE-with-f:format.html shape and wrote the test markup as HTML entities. It came back entity-escaped inside a <p> — the RTE content never reached parseFunc at all.
2. Switching to a plain TEXT cObj, `page.10.value = <figure ...>` was unusable because a leading `<` in a TypoScript value is a reference copy, not text. The multi-line `value ( ... )` form was needed.
3. My first assertion did substr() on strpos() without an int cast and died on a TypeError before printing anything.

Only round four gave the baseline. The working shape, which I would write identically next session and would rather have been handed:

  page.10 = TEXT
  page.10.value (
  <figure class="table">...<figcaption>… <a href="t3://page?uid=1">link</a></figcaption></figure>
  )
  page.10.parseFunc =< lib.parseFunc_RTE

plus assertSame('PROBE', $extractedFragment) as a deliberate failure so PHPUnit prints the actual HTML — there is no other way to see rendered output from a functional test. Run with CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <file>; sqlite made each round about 12 seconds of PHPUnit rather than a MariaDB spin-up, which is the single thing that made six rounds affordable.

The payoff was worth it — it produced the `<p>` wrapping nobody had noticed, disproved a regression I had assumed (an empty `<figcaption></figcaption>` is unaffected; I would have written that `innerStdWrap_all.ifBlank = &nbsp;` fills it, and I would have been wrong), and let me test a non-breaking alternative and hand the author a verified counter-proposal instead of an objection.

## Query

Never asked. The question: "how do I render an RTE bodytext snippet through lib.parseFunc_RTE in a functional test and read the resulting HTML?" — arising while reviewing change 95169 against typo3/sysext/frontend/ext_localconf.php with typo3/sysext/frontend/Tests/Functional/Rendering/ParseFuncTest.php open.

## Suggestion

Carry the FE-rendering probe as a procedure, next to the scratch-probe permission that is already recorded (db693194). It needs four things the checkout does not volunteer: the TEXT-cObj-plus-parseFunc skeleton above; that a leading `<` in a TypoScript value is a reference and text must use the `value ( )` form; that reading rendered output means asserting a sentinel and reading the diff; and `-d sqlite` as the cheap DBMS for a probe that will be run several times over. The page I wanted would have been called "prove what a rendering change renders". More generally: a diff touching lib.parseFunc, TypoScript defaults or ext_localconf TypoScript is a class of patch where reading is not sufficient evidence, and the review skill could say so and point here.
