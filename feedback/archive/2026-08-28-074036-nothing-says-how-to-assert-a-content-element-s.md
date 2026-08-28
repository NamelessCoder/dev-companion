---
date: 2026-08-28T07:40:36+00:00
category: missing-knowledge
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3-extension-testing, typo3_extension_describe, typo3_hint_lookup
directory: /home/benji/projects/bootstrap_package
---

# nothing says how to assert a content element's rendered output in a functional test outside the core

## Observation

Task: cover a Fluid rendering fix in bootstrap_package with a functional test that fails without the fix.

What the server gave me: typo3_extension_describe reported the package ships Functional, Packages and Unit test layers and named its fluidRoots. The typo3-extension-testing skill's phpunit.md said to use a functional test for "server-side frontend rendering". typo3_task_guide's content-element hints said "a functional test asserts the HTML the template produced". All three name the layer. None says how to stand one up for a content element of a package that is not the core, and that is the whole of the work.

What I worked out, in order, with what each step cost:

1. Wrote a site configuration through SiteWriter::write carrying 'dependencies' => ['bootstrap-package/content-elements'], plus a fixture setup.typoscript through setUpFrontendRootPage. The frontend answered with core's "Content Element with uid 1 and type table has no rendering definition!", meaning tt_content.table was not defined at render time. Two runs, roughly 8s each. I did not root-cause whether the site set was not resolved at all, or resolved in an order a sys_template-based setup could not copy from — I report the observation, not a claim about TYPO3.

2. Replaced the set dependency with two @import lines in the fixture setup.typoscript, naming the element's own TypoScript file and the lib.contentElement helper it copies from. That worked on the first run. The TypoScript constants the helper reads, {$plugin.bootstrap_package_contentelements.view.*}, stay unresolved and turned out to be harmless for the assertion.

3. Along the way I had to establish from the checkout that fluid_styled_content is not installed in this package at all, so the tt_content CASE comes from cms-frontend, and that `renderObj < tt_content.table` inside a CONTENT object is how to reach the element without it.

The test that came out is 104 lines and passes on both declared majors in CI. Reaching it cost three failed frontend runs and four TypoScript and Fluid files read. None of that reading was about the defect; all of it was about how to make the harness render anything at all.

Related: the bundled document core/testing/proving-a-rendering exists and is named in typo3_project_describe's guides. Its title is about a core rendering change. Whether it covers the extension case I cannot say, because I did not open it — that failure is filed separately. If it does cover it, the finding here is that its title and `when` line did not read as covering an extension.

## Query

typo3_extension_describe extension="bootstrap_package"

typo3_task_guide task="Review an incoming pull request that changes an f:if condition in a Fluid partial of the table content element", changeType="audit", paths=["Resources/Private/Partials/ContentElements/Table/Columns.html"]

Skill typo3-extension-testing, its references/phpunit.md read whole.

No lookup was made for "functional test frontend rendering content element extension" — I did not expect one to exist, which is itself part of the finding.

## Suggestion

A hint, reachable by a symptom query such as "functional test renders no content element" or "has no rendering definition in a functional test", stating for a package that is not the core:

- the fixture set is pages.csv, tt_content.csv and a setup.typoscript beside the test;
- the setup.typoscript imports the element's own TypoScript file and the helper it copies from, rather than relying on the site set;
- what a written site configuration's `dependencies` does and does not supply to a setUpFrontendRootPage-based setup — this is the single sentence that would have saved the whole detour, in either direction: "site set TypoScript does not reach a sys_template added this way, import the files", or "it does, and here is the invocation I got wrong";
- unresolved {$...} constants do not stop the assertion, so a test need not reconstruct the constants;
- `renderObj < tt_content.<ctype>` inside a CONTENT object reaches one element without a tt_content CASE, which matters where fluid_styled_content is not installed.

The last point deserves its own line anywhere content-element rendering is described: a sitepackage that defines `tt_content.<ctype> =< lib.contentElement` without requiring fluid_styled_content is a shape the corpus does not seem to anticipate, and it changed what my test had to do.
