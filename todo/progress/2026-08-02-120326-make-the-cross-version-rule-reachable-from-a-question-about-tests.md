# Make the cross-version rule reachable from a question about tests

**Serves:** feedback/2026-07-31-193642-19-suggested-consider-typo3-cms-compatibility.md
**Priority:** normal
**Branch:** todo/make-the-cross-version-rule-reachable-from-a-question-about-tests
**Claimed:** 2026-08-02

Step 2, delivery: the answer is written and no question about tests reaches it.
`extension-repository-layout` holds it — a matrix that resolves per supported
version, the lowest and the highest of each supported major — and both hops to
it fail, which `D-KNW-009`'s **Since then** measures and this todo owes the
entry its first **Wrong if** asks for. The first hop is the domain:
`typo3-extension-conformance`'s checklist calls its surface "Quality: tests,
…", and the bare `tests` is the word `Domains::KEYWORDS[PHP]` deliberately does
not carry, so the query resolves to `docs` alone. The second is inside PHP:
testing vocabulary lands on `project-extension-tests`, whose statements say
nothing about a supported range, while `extension-repository-layout` has no
testing phrasing in its `appliesTo` and scores on body text or not at all.
Repeat the measurement `D-KNW-009` names over the 40 scenario prompts and 65
hint titles before choosing, because term weights are taken over the candidates
(`R-ANS-007`) and widening either vocabulary reweighs the other's hits — the
four candidates are a phrasing added to `Domains::KEYWORDS[PHP]`, testing
vocabulary added to `extension-repository-layout`'s `appliesTo`, a statement
about the supported range added to `project-extension-tests`, and rewording the
checklist's own surface line, and the measurement is what says which of them
buys reach without buying a wrong hit. It is done when `bin/cli hints:probe`
reaches the matrix rule both from the checklist's quality-surface wording and
from "does the test suite cover every supported TYPO3 version", when
`typo3_architecture_lookup` returns it for an extension's quality surface rather
than the `scope: project` layout hint it returns today, and when the assertion
sits beside `HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat`.
