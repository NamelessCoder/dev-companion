# Say what --create-site leaves rendering the page

**Serves:** feedback/2026-08-24-140130-typo3-setup-create-site-also-writes-a-site.md
**Priority:** normal
**Branch:** todo/typo3-setup-create-site-also-writes-a-site
**Claimed:** 2026-08-24

Judged on 2026-08-24 as `D-KNW-116`: step 1a, queued. The session read
`installation-setup`, which is the hint that owns the subject, so what is
missing is the statement rather than its placement.

Read what the command leaves rendering the page on all four checkouts, because
the file differs across them. On `.checkouts/14.3` and `main`,
`SetupService::createSite()` calls `writeSiteSetupTypoScript()`, which writes
`config/sites/<identifier>/setup.typoscript` with a welcome `page = PAGE`; on
`12.4` and `13.4` the same method inserts a `sys_template` row with `root = 1`,
`clear = 3` and the same object in its `config`. Establish what each does to a
site that carries sets — `SysTemplateTreeBuilder::createSiteTemplateInclude()`
reads the site's own file after every set, and the clearing row is what
`extension-test-site` already warns about for the functional suite.

Then write it onto `installation-setup` beside the `--create-site` line, bound
to each major rather than stated flat, and say that the failure is HTTP 200 with
an empty log. `site-sets` gains the neighbour line, because that is the hint a
caller reaches from the symptom.

`feedback/2026-08-24-140317` is the same file from the other side and is
unjudged; leave its card alone and read it before choosing the wording, so the
two statements do not answer each other's question.
