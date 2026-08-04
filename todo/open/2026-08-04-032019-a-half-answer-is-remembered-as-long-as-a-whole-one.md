# One resolution per console state, not one per accessor

**Serves:** R-DIS-009, R-DIS-010, META-02
**Priority:** low

`typo3_server_scope` reads the console state into locals now and writes both
halves of its answer from them, which is the step this card named and the
maintainer chose over putting the memo back. The reason first written here — the
guarantee stays inside `Typo3Cli`, so the upkeep commands that call it directly
keep it too — does not hold: the only two callers outside a tool,
`ToolAnswers::pointAt()` and `ToolRecord`, call `Typo3Cli::forget()` themselves,
so neither would have kept a stale resolution either way. What actually rules
the memo out is one state over: dropping it in `Registry::call`'s `finally`
drops every resolution, and the uncaveated one is meant to be discovered once
per process — 0.492s cold against 0.002s warm, paid again by every tool call.
Dropping only the caveated one is the same concept a fourth accessor adds, for
the same 0.25s. Measured against a stopped DDEV project whose pin host PHP
satisfies — the caveated resolution, `.environments/e-site-13.4`'s shape — on
2026-08-04: six `ddev describe -j` and 2.648s per answer before, two and 0.869s
after, at 0.25s a describe. Running, or once the resolution is remembered, it is
one describe and 0.002s either way, and
`Typo3CliTest::theScopeAnswerDescribesAStoppedProjectOncePerHalfRatherThanPerSentence`
fails at six.

Two rather than the one this card predicted, and the reason is the class rather
than the caller: `reason()` and `caveat()` resolve on their own, so what limits
a console cannot be read from outside `Typo3Cli` without a second resolution.
Getting to one means an accessor that hands back the invocation, the reason and
the caveat from a single resolve — which is a change to `Typo3Cli`, and the
question below.

**Open question, as it goes to whoever queued this:** is a fourth accessor on
`Typo3Cli` — say `state(): array{invocation, reason, caveat}` from one
resolution — worth the last 0.25s of a stopped project's scope answer? The
options are (a) add it and have `ServerScope` read the state in one call,
leaving `resolve()`, `reason()` and `caveat()` for the callers that want one
thing; (b) leave it at two, and the class keeps three accessors that each mean
one thing; (c) memoize the caveated resolution again and drop it in
`Registry::call`'s `finally`, which the maintainer already rejected and which
the paragraph above prices. Recommended: (b). The saving is 0.25s on one tool in
one state, and a fourth way to ask the same question is a concept added to a
class whose three accessors are each one sentence.

One thing this reading found next door, workable without an answer:
`Unsupported::because()` reads `Typo3Cli::caveat()` twice in two lines, so every
unsupported answer resolves once more than it needs. Measured the same day
against the same stopped fixture, `typo3_label_lookup`,
`typo3_fluid_namespace_list` and `typo3_configuration_lookup` each cost three
describes and 1.35s — one for the run, two for those lines. Reading it into a
local is one line and takes each of them to two.

`low` for both halves. What is left is 0.25s per answer in a state the caller is
told to leave by starting its project, and the six-fold cost this card was
opened for is gone.
