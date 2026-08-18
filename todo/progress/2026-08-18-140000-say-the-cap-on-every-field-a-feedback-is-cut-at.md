# Say the cap on every field a feedback is cut at, the subject included

**Serves:** feedback/2026-08-18-080803-the-4000-character-observation-limit-is-not-in.md, D-FBK-049
**Priority:** normal
**Branch:** todo/say-the-cap-on-every-field-a-feedback-is-cut-at
**Claimed:** 2026-08-18

Write the cap into the four descriptions `FeedbackRecord::inputSchema()`
declares — 4000 characters on `observation`, `query` and `suggestion`, 100 on
`subject`, each saying the text is cut rather than refused — and make
`Channel::record()` report the subject's shortening in `cut` the way `text()`
reports the other three. The title keeps its `...` and gains no marker. The
assertion this needs is beside
`FeedbackTest::aFieldCutForLengthSaysSoInTheFileAndInTheAnswer`, which holds
`R-FBK-015` on the three fields that go through `text()` and leaves the one that
does not.

`D-FBK-049` is the judgement and carries the measurement: two of 440 recorded
feedback were cut at 4000, and 115 had a subject shortened to 97 characters
without being told. The reporting feedback is one of the 115 and never saw it.
