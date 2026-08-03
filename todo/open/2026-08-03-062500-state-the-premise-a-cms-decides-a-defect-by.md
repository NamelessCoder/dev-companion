# State the premise a CMS decides a defect by

**Serves:** feedback/2026-08-02-145043-task-assess-forge-105403-and-fix-it-the-single.md
**Priority:** normal

A session records why its first assessment of a core bug was wrong: it read the
report as an API question — is the value passed to `f:image` of the type the
argument accepts — where the product asks what happens to the editor and the
visitor once the image is replaced. Answered the first way the reporter is
misusing the API; answered the second way the exception is a symptom and the
missing cache buster is the defect. The same blind spot produced three further
turns the user corrected: an argument to switch the new behaviour off, "use
`f:image` for FAL only", and a redesign of the FAL fallback while the reported
defect stood. Read it together with `2026-08-01-003937` on cache clearing and
the preview cards, which are the same premise met from three sides, then write
**one** statement where a building or fixing task passes — content changes, and
what is delivered has to be the current version, from which cache busting, cache
invalidation and rendering a preview to verify it all follow — rather than three
statements about their mechanisms. The boundary it may not cross is
[`D-FBK-024`](../../decisions/feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md):
this is a statement about TYPO3, not a rule about how a session conducts itself,
and it is worth the words only as the first.
