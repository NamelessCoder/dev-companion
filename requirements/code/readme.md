# Code — what must hold of the source itself

Not what an answer has to be right about, but what `src/` has to stay: how a
file maps to a class, what has to remain reachable, and what a check has to go
through to count as one. Every other group here is about a caller; this one is
about the tree the callers reach through, and it exists because a repository can
be wrong in a way no answer is wrong yet.

Its counterpart is [`decisions/code/`](../../decisions/code/readme.md), which
holds what a change to that shape rested on.

See [the requirements readme](../readme.md) for how an entry is written and
when it is added.

- [`R-COD-001`][R-COD-001] — Every entrypoint is driven by a test that goes through it · held
- [`R-COD-002`][R-COD-002] — What the server ships is held to the prose rule · not guarded

[R-COD-001]: cod-001-every-entrypoint-is-driven-by-a-test-that-goes-through-it.md
[R-COD-002]: cod-002-what-the-server-ships-is-held-to-the-prose-rule.md
