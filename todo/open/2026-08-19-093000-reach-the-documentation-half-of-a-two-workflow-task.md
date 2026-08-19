# Reach the documentation half of a two-workflow task

**Serves:** scenarios/contracts/, D-GUI-015
**Priority:** normal

`SKILL-07` is a task that crosses from one workflow to another — build the
backend module, then document the extension — and only the first half arrives.
"Once it works, document the public workflow for the extension's maintainers and
editors in the right place" reaches no documentation intent, measured on
2026-08-19 after the `audit` needles were curated.

What the step turns on is which intent should carry it. The one that names
`typo3-extension-documentation` today is `changelog`, whose needles are about an
entry a release carries, and a request to document a workflow for maintainers is
not that. So the question is whether the documentation workflow earns an intent
of its own, or whether `changelog` widens — and `D-SKL-013`'s third **Wrong if**
is what a new row has to answer to, because a name invented for a row nobody
asked for is a route into a workflow nobody asked for.

Measure before and after with the case's own prompt, and against the neighbour
each candidate needle could steal from, the way the four before it were.
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout` is where the
arrival is asserted once it holds.
