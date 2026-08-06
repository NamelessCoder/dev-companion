---
date: 2026-08-05T22:12:15+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# This is a report on something that worked and must not be broken.

## Observation

This is a report on something that worked and must not be broken.

Task: triage Forge #81619, "stdWrap_override does not override the current value if new value is 0", against a 15.0.0-dev core checkout.

The code path confirmed the report immediately — ContentObjectRenderer::stdWrap_override still does `if (trim($conf['override'] ?? false))`, and `(bool)trim('0')` is false — so I was one step away from writing this up as a live defect and patching it. Two things stopped me, and the decisive one came from this tool.

The search returned the TypoScript reference page for stdWrap, and the page read returned it whole, including the property definitions. The entry for `override` says: "If override returns something other than "" or zero (trimmed), the content is loaded with this." That is the intent stated in the project's own manual, in words that cover exactly the case under dispute, and it turned the verdict from "still happens, fix it" into "not a defect, and changing it is a feature with breaking character". Reading the neighbouring entries in the same page reinforced it — ifEmpty says "Zeros are treated as empty values", ifBlank says "Zeros are not treated as blank values", and `required` says "Zero is not regarded as empty" — so the zero handling is a deliberate and consistently documented axis across the whole property set, not an accident in one method.

Two design decisions made this work and are worth naming. The page mode returning the full text rather than an excerpt is what carried it: a snippet around the word "override" would have given me the heading and not the sentence. And the search answering with canonical URLs that feed straight back as the page argument meant two calls with no guessing in between.

The second thing that stopped me was a test in the checkout pinning the same behaviour, which I found myself. But a test pins what the code does; only the manual said what the project means, and that is the difference between "not a defect" and "works as designed, no source" — which the triage skill's own checklist calls an opinion in a maintainer's voice.

## Query

typo3_documentation_lookup with queries ["stdWrap override", "stdWrap ifEmpty override"] and targetVersion "14", then the same tool with page "https://docs.typo3.org/m/typo3/reference-typoscript/14.3/en-us/Functions/Stdwrap.html".

## Suggestion

Keep the page mode returning full text. The single highest-value call of this session was a page read, and an excerpt would not have carried it. Keep the search-returns-canonical-URLs-that-feed-back-as-page contract too: it made the two-step cost nothing.

If anything is worth adding, it is reach rather than shape. This landed because "stdWrap override" happens to spell a page title; the tool's own description already warns that matching is against page titles and section paths rather than page text, and that a PHP identifier therefore has no page to be titled after. In a triage that is precisely the risk — the reporter writes the method name, stdWrap_override, and the manual titles the property, override. I got there because I stripped the method name down to the property myself. A note in the answer when a query looks like an identifier, suggesting the bare property or ViewHelper name instead, would make that step less dependent on the caller noticing.
