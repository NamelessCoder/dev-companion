# The Forge search miss names the enumeration as the other way in

**Serves:** feedback/2026-08-24-110926-four-wordings-of-a-duplicate-search-returned.md
**Priority:** normal

Judged on 2026-08-24 as the ladder's step 4, wording, and written up in the last
**Since then** of `D-ANS-038`: the empty answer of a `query` offers a rewording
and the person filters, and never `open` with `category`, which is what settled
the session on its ninth call. Rewrite that answer in `ForgeLookup::searched()`
so it names the enumeration as a call the caller can compose — the route rather
than the count the feedback asks for, which would be a second read of the
tracker on every miss — and widen the `category` parameter description, which
today names only the browse question "are there known bugs in the RTE" and not
the duplicate question a caller arrives with a written report for. Hold it
beside `ForgeTest::wordsThatMatchNothingAreEmpty` and declare `R-ANS-006` on it,
which is the requirement the hints, the labels and the changelog hold and this
miss does not. Read `feedback/2026-08-24-163235` first: it is unjudged and
reports the same three sentences from the other side, so the two may be one
rewrite.
