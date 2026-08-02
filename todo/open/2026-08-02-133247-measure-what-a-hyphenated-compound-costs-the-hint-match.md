# Measure what a hyphenated compound costs the hint match

**Serves:** R-ANS-007
**Priority:** normal

[`D-ANS-022`](../../decisions/answers/ans-022-a-hyphenated-compound-reaches-neither-the-phrase-nor-the-word.md)
has the diagnosis and the one-character measurement: `content-element` reaches
nothing that `content element` reaches, because `scoreKeywords()` searches the
`appliesTo` phrase in the query verbatim and `TermSearch::terms()` stems the
whole compound to the six-character prefix of its first word. Measure the
change before making it, the way `D-KNW-009` measured its keyword: run the 40
scenario prompts and the hint titles through `bin/cli hints:probe` as they are,
then again with a rule that also compares a hyphenated term as separate words,
and count what moved rather than only what the failing query does — then either
change `TermSearch` and `scoreKeywords()` together, or leave the matcher alone
and put the hyphenated spellings into the `appliesTo` of the hints that name a
compound, whichever the measurement says. `tt_content`, `list_type` and
`mod.web_layout` are the queries that may not get worse.
