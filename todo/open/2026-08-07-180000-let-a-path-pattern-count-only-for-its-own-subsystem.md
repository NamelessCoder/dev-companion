# Let a path pattern count only for its own subsystem

**Serves:** R-ANS-026
**Priority:** normal

Two short `appliesTo` patterns claimed a path belonging to another subsystem on
2026-08-07, and both were pruned from the corpus rather than fixed here — which
is symptom work, and the third one will land the same way.
`Hints::scoreKeywords()` asks a bare word of the paths with
`TermSearch::carries()`, a prefix match with no word boundary, and a punctuated
pattern with `str_contains()`; neither knows which extension the path is in.
Make a pattern that matches only inside a path count only where the hint does
not contradict that path's subsystem, so `storage` under `typo3/sysext/extbase/`
stops being FAL's and `/Persistence/` stops being DataHandler's. Two cases have
to keep passing and both are measured: `thumbnail` must still reach
`fal-processing` through `ThumbnailViewHelper.php`, which is the whole of what
`D-ANS-050` decided and runs past its own end on purpose, and the DataHandler
path must still reach `datahandler-basics`. The other thing to settle is the
tier order — both false positives carried `score: 0`, so their own words
answered nothing, while `keywords` sorts above `score` unconditionally. Whether
a zero-scoring keyword match may outrank a hint that answers is the question
`D-ANS-060` left open, and `bin/cli hints:coverage` is the sweep either change
is measured against.
