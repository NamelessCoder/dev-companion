# Measure whether a rank gap separates a thin answer from a collision

**Serves:** feedback/2026-08-03-164734-installation-the-extension-ships-a-fork-of-the.md
**Priority:** normal

Measure, over the live 14.3 index `Manual\Documentation` builds, whether the gap
between a query's top result and its second — rather than the share of the query
either of them covers — separates the six collisions of
`feedback/2026-08-03-164734` from the pages that answer a three-word question.
Take the eight queries `D-ANS-051` already priced, print top-hit coverage,
second-hit coverage, their difference and their ratio for each, and say in the
commit which of the two gap forms puts `login provider`, *IRRE / inline* and
*Functional tests* on one side and the six collisions on the other — or that
neither does. No change in `Documentation::lookup()` before that reading.

## Why the share was abandoned, and what the answer was

The absolute share does not separate them, and that is measured rather than
suspected. On 2026-08-04 over the live 14.3 index, a floor returning
`login screen layout` — where *LoginProvider* covers 0.34 — is at 0.34 or under,
while one emptying `Fluid template file naming convention v14` is above the 0.40
its top collision reaches. Over a corpus whose ordinary field is 2.66 words, the
page that answers a three-word question covers a third of it and the collisions
sit in the same band. Two further queries `DocumentationTest` holds go with them,
both ranking their page first today: *IRRE / inline* at 0.43 and *Functional
tests* at 0.19.

Put to the maintainer on 2026-08-04 with three answers priced — close it out, try
a different measure, or keep waiting — the answer was to try the rank gap. So
this is a reading first, and a card in the queue rather than a blocked one.

What makes the gap worth trying is that both failing cases are about shape rather
than level: a query that is answered has one page well clear of the rest, and a
query that is not has six pages within a few hundredths of each other. Nothing
has measured that, and the six collisions may turn out to sit as far apart as the
answers do.

## What is built if it separates, and what holds it

Unchanged from the card this replaces: the drop in `Documentation::lookup()`,
`empty` where nothing clears the measure, `Search\Subsets::largestReaching()`
over the same searchable fields and `TermSearch::carries()`, then the corpus
sentence `D-ANS-043` built, pointing at `typo3_changelog_lookup` — which returns
`Feature-108166` alone for `fluid file extension`. The offer does not carry what
a drop would take away today: `layout root paths login screen override` offers
`layout login`, which returns three of the six collisions and not
*LoginProvider*, so the offer is part of the change rather than beside it. What
must hold from then on is written beside `R-DOC-002`, and the commit that ships
it archives the feedback.

The half that is already built stays whichever way this goes. Every result
carries the share of the query it covers, and the text says where nothing covers
half — `D-ANS-051`, on the maintainer's choice of 2026-08-04 between the three
options that card priced. What is still open is the feedback's second sentence:
return the fact that nothing clears a threshold, rather than the best six
collisions.
