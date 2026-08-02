# A hint that grows past 544 words stops answering a one-term query

**Serves:** decisions/
**Priority:** normal
**Branch:** todo/a-hint-that-grows-past-544-words-stops-answering-a-one-term-query
**Claimed:** 2026-08-02

This is
[`D-ANS-006`](../../decisions/answers/ans-006-an-identifier-is-found-however-it-is-spelled.md)
meeting the dilution weight, and it is the second one that gives way. Growing a
hint's body silently removes it from queries it used to answer, and
nothing says so until a test happens to assert one of them. `content-elements`
went from 452 to 785 words on 2026-08-02 and `list_type` and `mod.web_layout`
stopped reaching it — not ranked lower, dropped: `ArchitectureHints::find()`
skips a hint on `keywords === 0 && coverage < MIN_COVERAGE`, and coverage of a
single matched term is `1 / (log(words / UNDILUTED_WORDS) + 1)`. At 0.5 that
solves to `200 * e`, so **544 words is the exact ceiling** past which no
one-term text query admits a hint at all. The two identifiers were curated into
`appliesTo` to restore them, which is how `tt_content` already survives, and
that is a repair rather than an answer.

Measure it first: `bin/cli hints:coverage` reports 30 of 66 hints over the
200-word reference and a longest of 1147, so the hints past 544 are already a
set, and each of them is unreachable this way today. What that costs is a
question about queries nobody has run — the sweep is the 195 bare `appliesTo`
patterns and the 41 scenario prompts asked as one term each, against the hint
they belong to, which is the shape `D-ANS-022` used.

Then choose with the numbers in hand. Curating every identifier into
`appliesTo` scales with the corpus and puts the burden on whoever writes a
statement; splitting a hint that outgrew the gate keeps the text route working
but changes what routing returns; making coverage read the strongest field's
dilution rather than the matched one would change every ranking and needs the
sweep before it is even proposed. `bin/cli hints:coverage` reports against
`MAX_MEAN_BODY_WORDS` and says the fix is to re-run the sweep and pick the
constant again, which is the same instruction and a different constant.
