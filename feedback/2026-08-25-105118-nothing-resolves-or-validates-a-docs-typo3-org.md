---
date: 2026-08-25T10:51:18+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_documentation_lookup, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Nothing resolves or validates a docs.typo3.org permalink identifier

## Observation

Task: extend Gerrit change 95369 ("[TASK] Use permalinks for documentation URLs") in a core checkout on main (15.0.0-dev), then "find every other link that should become a permalink" and finally "fix all broken links".

Roughly 80% of this session was one question the server has no tool for: given an old docs.typo3.org URL in core code, what is the permalink identifier that replaces it, and does a given identifier resolve at all. I made ~60 curl round trips against docs.typo3.org to answer it, plus two bulk sweeps (241 permalinks already in the tree, 302 docs.typo3.org URLs, 260 typo3.org/typo3.com URLs).

The method I worked out and would work out again:
1. `curl -s -o /dev/null -w '%{http_code} -> %{redirect_url}' https://docs.typo3.org/permalink/<id>` — 307 means registered, 404 means guessed wrong.
2. Where I did not know the id: fetch the current target page and grep `id="..."` for candidate anchors.
3. The one that actually scaled: download the Sphinx inventory of a manual, e.g. https://docs.typo3.org/m/typo3/reference-tca/main/en-us/objects.inv, split on the first four newlines and zlib-decompress the rest. That yields every label the manual publishes (2919 for the TCA reference) with its target page, which turns guessing into lookup.

Concrete results this produced that no lookup gave me:
- `typo3/sysext/backend/Classes/Form/Container/FlexFormElementContainer.php:79` linked to reference-tca `Columns/Properties/OnChange.html`, a 404; the inventory named `confval-columns-onchange` on `Columns/Index.html`.
- Old anchors survive as aliases beside the current ones: `t3tsref:setup-config-contentObjectExceptionHandler` and `t3tsref:confval-config-contentobjectexceptionhandler` both resolve to the same page. Which one a patch should use is a real review question and nothing states the rule.
- The TCA option `search.case` is documented on 13.4 (`confval-input-search-case` and friends, per feed type) and has no anchor at all on main. A comment in `LikeTest.php` still links the 11.5 page.
- Guessing the shortcode fails often enough to matter: `typo3-cms-felogin:start` and `typo3-cms-lowlevel:start` are 404 while 16 other system extensions answer `:start`; the working ids are `typo3-cms-felogin:typo3-frontend-login` and `typo3-cms-lowlevel:typo3-low-level`.
- Both `typo3/cms-form:...` and `typo3-cms-form:...` resolve; the repo uses both spellings and nothing says they are equivalent.

I did not put this to `typo3_documentation_lookup`. The reason is its own description, quoted in the skill base text: it matches page titles and section paths, never the text of a page. I read that as "cannot map an old URL to an identifier" and went to curl. I never tested the assumption, so it may be partly wrong for the "where did this topic move to" half.

## Query

Whole-session task: "wir sollten das abgleichen und ggf durch unsere erweitern" for review.typo3.org change 95369, then "bitte schaue ob du noch weitere links findest welche durch permalinks ersetzt werden sollten", then "fix bitte alle kaputten links". Core checkout at 15.0.0-dev. No typo3_* tool was called for any of it.

## Suggestion

A `typo3_permalink_lookup` that takes either an identifier or a docs.typo3.org URL and answers: does the identifier resolve; what page and anchor does it land on; for a URL, what identifier replaces it; and which manual shortcode a system extension or a manual uses. Feeding it from the published objects.inv per manual and per version would cover all of it, including the alias question — the inventory distinguishes `std:confval` entries from plain `std:label` aliases, which is exactly the "which spelling should a patch use" answer I had to invent a rule for.

Failing a tool, a document under a name like `core/documentation/permalinks` would have carried most of the session: the permalink URL form, the `@<branch>` suffix, the shortcode naming for core manuals (`t3coreapi`, `t3tsref`, `t3tca`, `t3start`, `t3contribute`, `t3viewhelper`) and for system extension manuals (`typo3/cms-<key>` and `typo3-cms-<key>`, both valid), that `confval-*` ids are the current generated form and older ids stay as aliases, and the objects.inv trick for finding an id without guessing.
