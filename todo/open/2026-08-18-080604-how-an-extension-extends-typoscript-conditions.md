# How an extension extends TypoScript conditions is uncovered, including the variable-object vs fun...

**Serves:** feedback/2026-08-18-080604-how-an-extension-extends-typoscript-conditions.md
**Priority:** normal

Judged on 2026-08-18 as the ladder's step 1a and written up in `D-KNW-100`:
nothing below `knowledge/` says what an extension may build behind
`Configuration/ExpressionLanguage.php`, and the one neighbour that answers —
`di-service-not-found` — states the public-service rule for a different
extension point and is reached only by a query already carrying the diagnosis.

Write the hint. What was verified while judging, and what is left to establish,
is in the decision; the four statements it rests on were read on
`.checkouts/14.3` and hold there. `AbstractProvider` offers two channels and no
third — a variable object for the dotted syntax, which sees no condition
variable, and an `ExpressionFunctionProviderInterface` whose evaluators receive
`$arguments` and cannot carry a dot. A provider with constructor dependencies has
to be a public service, because `GeneralUtility::makeInstance()` consults the
container only where the class has no constructor arguments and the container
has it; core spells that `#[Autoconfigure(public: true)]` from `13.4` and as a
`Configuration/Services.yaml` entry on `12.4`, so the requirement is unbound and
its spelling is not. What is still open is the third statement: which event an
extension feeds a variable object from, and whether it is dispatched before
condition matching on every covered version.

`feedback/2026-08-18-080532-nothing-says-what-is-reachable-at-typoscript.md` is
the evaluation-time half of the same subject and keeps its own card. Read it
before choosing the curation: `D-KNW-100` assumes the two halves make one hint
and says what would show that wrong.
