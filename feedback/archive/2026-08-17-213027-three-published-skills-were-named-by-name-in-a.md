---
date: 2026-08-17T21:30:27+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5
tool: typo3-content-element-development, typo3-extension-testing, typo3-extension-documentation, typo3-extension-conformance, typo3_extension_describe
directory: /home/benji/projects/site-demo
---

# three published skills were named by name in a handoff I read and none activated, and a human the...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. I filed the two missing skills separately. This is the opposite finding and I think it matters more, because nothing has to be written to fix it.

typo3-content-element-development closes with: "Activate typo3-extension-testing for test infrastructure, typo3-extension-documentation for manuals, and typo3-extension-conformance for a broader extension audit — stop before editing that owner's files, and carry across the extension key, the target version and the behaviour already verified."

Three skills, named by name, in a sentence I read. None of the three activated. What happened instead:

- No tests were written at all. Not one unit test, not one functional test, no Playwright. typo3_extension_describe reported tests: [] and I read that field.
- I wrote three README files by hand. typo3_extension_describe reported manual: null and readme: null, twice, and I treated those as facts about the repository rather than as the finding they are.
- Nobody audited the result. Then the user did it by hand and listed ten defects — and seven of them sit squarely inside typo3-extension-conformance's own stated scope: TCA, content elements, site sets, TypoScript, Fluid, labels, icons. He performed, manually and after delivery, the review a published skill exists to perform before it.

Two things about the mechanism, because "I should have activated them" is not actionable.

First, the handoff is the last line of the document. It is read at the moment of leaving a workflow, which is the moment of lowest appetite for opening a new one — the same position as the neighbour sentence at the end of a hint and the guides array at the start of a session, both of which I also filed. But a skill handoff is worse than a hint reference, because activating a skill is not a lookup: it visibly commits the caller to more work. The threshold is highest exactly where the momentum is lowest.

Second, and this is the part the server can act on: extension_describe already reported all three absences as structured fields. manual: null. readme: null. tests: []. I called it twice, read both answers, and used them to confirm that my content elements were registered correctly — which they were. The fields that said what was missing sat in the same object and I read straight past them, because they read as description rather than as verdict. The tool description is explicit that what an extension does not ship "is answered too, and that is the half no file listing can give you" — so the intent is there and it did not reach me.

## Query

Follow typo3-content-element-development to completion on a six-element sitepackage and check which of the three skills its closing handoff names were subsequently activated. Then compare the user's review findings against typo3-extension-conformance's stated scope.

## Suggestion

Make the absences in typo3_extension_describe say what they are for. manual: null, readme: null and tests: [] are the three most actionable fields in that answer for a package about to be delivered, and each has a published skill that owns it — naming that skill beside the null would turn a description into a route, at the one moment a caller is definitely looking at the object. That is cheaper than any change to the skills themselves and it fires late, which is when it is needed. Beyond that: a handoff sentence in a skill's last line is doing the same job as a neighbour reference and failing the same way, so if the workflows ever get a terminal checklist, the handoffs belong in it as items rather than as prose — "tests: activate typo3-extension-testing", not "activate it for test infrastructure".
