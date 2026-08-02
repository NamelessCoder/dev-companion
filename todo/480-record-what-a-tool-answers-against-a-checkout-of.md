# Record what a tool answers, against a checkout of somebody's own

**Serves:** documentation/

`documentation/clients/tools.md` says what every tool takes and which fields it
answers with, rendered from the registry by `bin/cli tools:index`. The half it
leaves out is the one a client reads first: `outputSchema` names the fields, and
what a filled one looks like — a match with its score, an `unsupported` with its
cause — is visible only by calling the server. That half is not derivable from
the registry, and it cannot be a test either: no installation is discovered in a
test run, which `ToolContractTest` says where it drives `typo3_label_lookup` and
`typo3_icon_lookup` for the unanswered path, so a recorded answer needs
`.checkouts/`, which is gitignored and which no test may depend on.

So the step is a command in the shape of `catalog:check`: run against a checkout
of somebody's own, writing the answers out as data that is committed, over the
call table `ToolContractTest::toolCalls` already holds — one hit and one miss per
tool, which is the pair a client is most likely to need to see. What to settle
before writing it is what the recorded file says about itself, because it is
evidence from one machine on one day: which checkout and which revision answered,
and what a reader is owed when the code has moved since. Nothing may fail on it
being older than the registry, or the command becomes a second `tools:check` that
only a machine with checkouts can satisfy.
