---
date: 2026-08-02T14:50:03+00:00
category: tool-gap
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Task: fix Forge #105403 by changing the shape of rendered image and resource URIs across TYPO3 core.

## Observation

Task: fix Forge #105403 by changing the shape of rendered image and resource URIs across TYPO3 core.

This was by far the largest cost of the session. The change altered rendered output, so every test asserting a URI had to be found and updated: about 23 files, ~141 expectations. I found them one failing suite at a time, over roughly fifteen full functional runs of several minutes each, widening the path set after every round. The sequence went ImageViewHelperTest, then Uri/ImageViewHelperTest, MediaViewHelperTest, SvgImageViewHelperTest, ImageConvertGM/IMViewHelperTest, FlexFormProcessorTest, AssetRendererTest, DefaultResourcePublisherTest, four icon provider and icon ViewHelper tests, PageRendererTest, Uri/ResourceViewHelperTest, ContentObjectRendererTest, RichTextElementTest, XmlSitemapXslTest, AbsoluteUriPrefixRenderingTest, UriPrefixRenderingTest, ScriptAndLinkTagRenderingTest, ShortcutButtonTest with three Fixtures/*.php, FluidEmailTest, and finally the unit test CacheBustingUriTest.

No single grep finds them, because the same value is asserted in at least eight unrelated shapes: a bare 40-character hash in a literal, a PCRE with a capture group whose match is then used as a file path so the buster must sit outside the group, sprintf format strings like '@^<img src="(%s/%s)" width="%d"...', a 'contentMatchRegExp' key whose value is a loose regex with .*, string concatenation as "?' . $iconMtime" and "&' . $iconMtime", '{$fileMtimeActions}' placeholders inside Fixtures/*.php template files, '\?\d+' in the prefix-rendering tests, and quoted-printable-encoded text in an email body. I wrote five different rewrite passes and still missed cases each round.

Late in the process I ran a grep for likely-affected expectations across the sysexts I had not yet touched and it returned nothing, which is the only cheap confirmation I got all session — and I only thought to run it after most of the discovery was already done the expensive way.

## Query

Finding every core test that asserts a rendered resource URI, after changing the URI shape in typo3/sysext/core/Classes/SystemResource/Http/CacheBustingUri.php — repeated runs of CI=true ./Build/Scripts/runTests.sh -s functional over widening path sets, plus grep over typo3/sysext/*/Tests/

## Suggestion

Support the "I am changing rendered output, what asserts it" question, which is a recurring core-work shape and currently costs a full functional run per discovery round. Even without a new tool, typo3_test_run_guide could return, for a change to a class whose output is asserted, the advice to run the whole functional suite once up front rather than widening path sets iteratively, and could name the non-obvious places core hides rendered expectations: Tests/Functional/*/Fixtures/*.php template files, 'contentMatchRegExp' data-provider keys, sprintf-built PCRE expectations, and mail bodies that are quoted-printable encoded. A grep recipe covering those shapes would be worth more than a tool, and would have cut this session substantially.
