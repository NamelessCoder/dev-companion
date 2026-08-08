---
id: D-ANS-061
date: 2026-08-07
status: open
---

# D-ANS-061 — An answer that names a document hands it over rather than pointing at it

**A `typo3://guides` document is reached by the tool the session already called,
or it is not reached at all.** Three core sessions finished long tasks holding a
`uri` field, an `alsoInHints` id and a resource list their client never
rendered, and read none of the three.

## Evidence

- `feedback/2026-08-07-065313` is a whole session that never called
  `typo3_server_scope`. Its client listed the tools as deferred schemas and
  showed no resource list, so no guide was ever seen. The server's own
  instructions predict exactly this, which is what makes it evidence about the
  instruction rather than about the session.
- `feedback/2026-08-07-130058` is the same session correcting itself in the
  debrief. `typo3_script_lookup` returns `typo3://guides/core/testing/scripts`
  inline, with its body, as an ordinary match — so the guide was reachable all
  along. What kept it out of sight is that `typo3_test_run_guide` answered every
  `runTests.sh` question first, and its description reads as a superset. The
  finding is overlap, not obscurity.
- `feedback/2026-08-07-132535` is a different session, a review rather than a
  patch. `typo3_rule_lookup` returned
  `uri: typo3://guides/core/contribution/commit-messages` on both of its calls;
  the session read the matched section twice and never fetched the document. It
  says why: nothing in the answer presents the `uri` as a next action.
- The same session names the page it wanted and never found — "what each change
  type owes" — and it is a section of the document it was holding the uri to.
  `feedback/2026-08-07-132446` is the failed query for that section, from the
  same review, and it carried `alsoInHints: documentation-changelog`, which the
  session saw twice and passed over.
- Re-run here on 2026-08-07: both of that feedback's queries still return
  `Release Targets` with the uri and the `alsoInHints` id attached, and never
  `Changelog Files`, which is a section of the same document.

## Decided

- A `uri` in an answer is not delivery. It is delivery to a client that renders
  MCP resources, and three sessions in a row had one that does not — which is
  the failure `bin/cli hints:coverage` was already measuring from the other
  side.
- The lever is the tool the session does call, not a tool it should have called.
  `typo3_server_scope` is correct and was never reached, twice, for the same
  stated reason: orientation felt complete. An answer reached that moment; a
  tool nobody invokes did not.
- So the work is on the answering side of the three tools that already name a
  document — `typo3_rule_lookup`, `typo3_test_run_guide`, `typo3_script_lookup`
  — rather than on the resource surface.
- This is step 2 of the ladder, delivery. Nothing is missing from the corpus:
  every document the three sessions wanted exists and two of them were named in
  answers the sessions read.

## Assumed

- The clients that render no resource list are the common case rather than these
  three. All three reports come from one model in one checkout, which is the
  weakest part of the evidence — but a client's resource support is a property
  of the client, and the failure is stated from the client side in all three.
- Naming a document in the answer is enough. Nothing here shows a session acting
  on such a name; what is shown is three sessions not acting on a bare uri.

## Wrong if

- A session reports reading a guide because an answer named it, and still
  reassembles the procedure by hand — which would say the handover has to carry
  the body rather than the name.
- A tool that inlines the whole document is reported as too long to read, which
  would say the section cut is right and only its label was wrong.
- A later debrief from a resource-rendering client reports the same miss, which
  would say the client was never the variable.

**Since then**, on 2026-08-09, the second **Assumed** did not hold on first
contact. `feedback/2026-08-08-224406` is a core patch session that held the
guide ids from `typo3_project_describe`, read the `documentId` parameter
description, got the line this entry produced at the foot of its answer, and ran
one search instead. Re-run with its own arguments, that answer is two of the
nine sections of `core/contribution/commit-messages` and says it cut a page
without saying how much of it it left.

Nothing decided here moves: the resource surface was never the lever, and this
session's client rendered no list either. What it adds is that the name is one
step short of the handover, and where the imperative to read a page whole is
written — two skills — it is still an address. That is
[`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it-and-by-what-the-answer-left-of-it.md).

## Since then

A third session reported `typo3_server_scope` unreached, on 2026-08-08, and it
cost something the two above did not. Its review of Gerrit change 95179 states
that a stdWrap property's documentation lives outside the core repository, as
the reason no documentation change was owed (`feedback/2026-08-08-224516`).
Re-run on 2026-08-09, `typo3_documentation_lookup` answers that query with the
page the patch makes wrong, so the claim shipped and it was false.

The **Decided** above is what that confirms, from the side it could not be
argued from before. This session asked for the same fix the other two did — put
the orientation tool into `skills/base.md` — and its own account names why that
is not where the moment is: the question arose while a review surface was being
disposed of, long after the order had moved on. So the lever is again the skill
that owns the moment rather than a tool nobody invokes, and the base is
unchanged.
[`D-SKL-030`](../task-skills/skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md)
is that judgement. What is untouched here is this entry's own **Assumed**:
nothing yet shows a session acting on a document named in an answer.
