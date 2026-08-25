# Route the rendering probe by the evidence a review needs, not by the diff

**Serves:** feedback/2026-08-24-183345-core-testing-proving-a-rendering-was-listed-was.md, D-KNW-122
**Priority:** normal
**Branch:** todo/core-testing-proving-a-rendering-was-listed-was
**Claimed:** 2026-08-25

Judged on 2026-08-25 as the ladder's step 3, routing, and the reading is in
[`D-KNW-122`](../../decisions/knowledge/knw-122-a-procedure-document-is-routed-by-the-evidence-a-task-needs.md).
`core/testing/proving-a-rendering` is gated on TypoScript in four of the five
places that name it, and the fifth — the throwaway-test rules of
`typo3-core-issue-triage` — is the form the other four take: the symptom is
rendered output, and the value is the unknown rather than the expectation.
Rewrite the four together, because a page gated twice routes twice: the
`whenToUse` on the document, the `covers` topic and the `routing` entry in
`knowledge/server-scope.json`, and the scratch-probe paragraph of
`typo3-core-patch-review`. Then have `typo3_test_run_guide` name the page where
its answer carries a functional suite, in the shape `SCRIPTS_GUIDE` and
`BROWSER_CHECK_GUIDE` already have in that file. Last, and against
`.checkouts/`, the half the page does not carry: how a rendering is made to say
which part of it changed — markers per region of the response, and printing a
service's state from inside the request.

What the reading established, so it is not established again. The `guides` array
of `typo3_project_describe` lists `id` and `title` alone, so the card
`D-KNW-057` composes never reached this session and the gate it read was the
skill's. Half of what the feedback asks for is already on the page: `echo` from
the test body is its **Reading What It Rendered** section and does not care what
the subject is. What the `guides` array carries belongs to `D-GUI-012` and to
the two feedback about that list, not here.
