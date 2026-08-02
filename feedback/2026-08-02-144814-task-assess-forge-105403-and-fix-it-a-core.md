---
date: 2026-08-02T14:48:14+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: assess Forge #105403 and fix it.

## Observation

Task: assess Forge #105403 and fix it.

A core maintainer had answered the issue with "As mentioned quite a few times and in the docs, you *must not* use f:image for anything but FAL resources", linking the official ViewHelper reference. I repeated that as correct in my first assessment. The user asked me what I made of the statement, which was the prompt to actually check it, and the checkout contradicts it:

- ImageViewHelper's own class docblock carries the example <f:image src="EXT:myext/Resources/Public/typo3_logo.png" width="100c" />.
- typo3/sysext/fluid/Tests/Functional/ViewHelpers/SvgImageViewHelperTest.php renders <f:image src="EXT:svg_image_test/Resources/Public/Images/..."> with scaling, cropping and fileExtension="png" across five data providers and asserts it works.
- ResourceFactory::retrieveFileOrFolderObject has a dedicated EXT: branch resolving package resources through the fallback storage.

What the docblock actually says is weaker than the rule as quoted: image operations on non-FAL files "may be changed in future TYPO3 versions" and each creates a "fake" FAL record "which may lead to problems". That is discouragement plus a stability warning, not a prohibition. Somewhere between the docblock and the documentation reference, "fragile and may change" became "must not", and the issue was closed on the stronger reading.

This matters beyond one comment. The server's own instructions already tell a session to read the checkout for what changed, which branch it is on and whether a path still exists. They do not extend that to behavioural rules, and a behavioural rule quoted by a maintainer in a tracker is exactly the kind of claim a session will accept without checking — I did.

## Query

Assessing the Forge #105403 comment "you must not use f:image for anything but FAL resources" against typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php and typo3/sysext/fluid/Tests/Functional/ViewHelpers/SvgImageViewHelperTest.php on 15.0.0-dev

## Suggestion

Where the catalogue carries a documented rule about what an API may or may not be used for, carry the strength of the claim with it and its source: whether it is enforced in code, warned about in a docblock, or only stated in prose documentation. For f:image specifically the accurate statement is that EXT: paths are supported and tested, that they are resolved through the storage-0 fallback which the source marks for removal, and that this is why the documentation discourages them — which is a far more useful answer than either "must not" or "works fine". More generally, add to the guidance that a rule quoted from a tracker comment or from prose docs is a claim to verify against the checkout, in the same way a path or an identifier is.
