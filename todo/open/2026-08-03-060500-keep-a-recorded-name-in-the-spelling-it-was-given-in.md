# Keep a recorded name in the spelling it was given in

**Serves:** feedback/2026-08-02-150625-task-file-feedback-about-typo3-core-work-naming.md
**Priority:** normal

`Channel::toolNames()` in `src/Feedback/Channel.php:644` strips every character
outside `[a-z0-9_]`, so a hyphen goes and an underscore stays: the skill
`typo3-extension-conformance` is stored as `typo3extensionconformance`, which is
a name the project does not have anywhere, and the tool's own description asks
for the hyphenated one. The corpus already carries 46 such names, and the second
effect is the one nobody can see: `typo3_documentation_lookup` appears 12 times
in the `tool:` front matter and `typo3documentationlookup` five, so one tool
sits under two identifiers and a `typo3_feedback_list` filtered on the right
name misses five of seventeen reports about it — the sideways reading
[`D-FBK-025`](../../decisions/feedback/fbk-025-a-judgement-reads-the-corpus-decides-the-shape-and-sets-the-priority.md)
makes the first duty of a judgement. Keep the given spelling when writing the
front matter and normalise both sides where they are compared, which the filter
already does; decide in the same commit what happens to the 46 stored names —
rewritten once, or matched across both forms forever — and hold it with a
`FeedbackTest` case that records `typo3-extension-conformance` and finds it back
under that spelling.
