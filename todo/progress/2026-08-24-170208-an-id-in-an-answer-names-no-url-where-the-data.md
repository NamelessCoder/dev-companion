# The three lines that name an id print the URL that reaches it

**Serves:** feedback/2026-08-24-170208-an-id-in-an-answer-names-no-url-where-the-data.md, D-ANS-103
**Priority:** normal
**Branch:** todo/an-id-in-an-answer-names-no-url-where-the-data
**Claimed:** 2026-08-24

Print the URL on the three lines that name a record by number and carry none:
`ForgeLookup::relationLine` and `GerritLookup::issues` from the `url` their
records already hold, and `GerritLookup::chain` from a `url` the chain entry
does not have yet — built in `Gerrit::chain()` out of the `project` and
`_change_number` that `/changes/<n>/revisions/current/related` already answers
with, the way `Gerrit` builds a change URL. `D-ANS-103` is the reading behind
it, including the measurement that says why the Forge review line composes a
shorter form than the Gerrit change line and why unifying the two would assert a
project neither side knows.
