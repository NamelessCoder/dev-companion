---
id: D-ANS-061
title: 'An answer that names a document hands it over'
date: 2026-08-07
status: open
---

# D-ANS-061 — An answer that names a document hands it over

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

## Since then

On 2026-08-09, the second **Assumed** did not hold on first contact.
`feedback/2026-08-08-224406` is a core patch session that held the guide ids
from `typo3_project_describe`, read the `documentId` parameter description, got
the line this entry produced at the foot of its answer, and ran one search
instead. Re-run with its own arguments, that answer is two of the nine sections
of `core/contribution/commit-messages` and says it cut a page without saying how
much of it it left.

Nothing decided here moves: the resource surface was never the lever, and this
session's client rendered no list either. What it adds is that the name is one
step short of the handover, and where the imperative to read a page whole is
written — two skills — it is still an address. That is
[`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md).

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

### 2026-08-18 — a fourth session, and the name was in front of it all along

`feedback/2026-08-17-205945` is a build rather than a review: a v14 demo site on
14.3.6, a sitepackage and a distribution extension. It held the `guides` ids
from `typo3_project_describe`, reached the point where it wanted to verify six
backend previews in a browser, and called `typo3_rule_lookup` at no point in the
session. `any/testing/browser-check` and `project/testing/playwright` both name
that moment in their own `whenToUse`.

That is this entry's second **Assumed** on the weakest ground it has stood on.
The three sessions above held a bare `uri`; the one of 2026-08-09 held the ids
and made the call with a search instead. This one held the ids, made no call at
all, and says it stopped rather than that it could not.

What the **Decided** says is unmoved and is what the case turns on: the lever is
the moment, and here the moment is a build workflow's verification step rather
than an answer.
[`D-SKL-045`](../task-skills/skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md)
is that judgement, and it is `D-SKL-030`'s one workflow over.

### 2026-08-27 — the tool whose subject is one page names no page

`feedback/2026-08-25-114819` is a core session under a client that defers the
MCP resource tools themselves. Enumerating the shelf cost it a `ToolSearch` for
a schema, then a call to list and a third to read, against a checkout it could
grep in one.

That is a fifth client-side account of this entry's first **Assumed**, and it
moves nothing: the resource surface was never the lever. Its report of
`typo3_server_scope` unreached — "certainty is what suppresses an orientation
call" — is the third such account and is already the **Decided**.

What is new is where the session looked instead. It called
`typo3_commit_message_guide` twice and then assembled the page it wanted out of
`AGENTS.md`, the checkout's `commit-msg` hook and three `git log` statistics
runs. That page is `core/contribution/commit-messages`, and what it says it
wanted — the subject preference, the hook's length boundary, the trailer set,
which release lines a bugfix reaches — are headings of it.

The **Decided** above put the work on "the three tools that already name a
document". `typo3_commit_message_guide` is a fourth, its whole subject is that
one page, and its answer names no page at all. So the boundary is one step wider
than it was drawn: a tool that answers on a document's subject names it, whether
or not it returns a section cut from it.

Four sessions wanted that page and none read it. `feedback/2026-08-07-132535`
held its uri from two `typo3_rule_lookup` calls, `feedback/2026-08-08-224406`
was handed a cut of it and searched again, this one was inside the guide's own
answer and got no pointer, and `feedback/2026-08-26-223348` names the guide as
the natural place for the `Releases:` question it guessed from `git branch -r`
instead.

[`D-GUI-020`](../guides/gui-020-the-commit-guide-states-the-longest-line-the-hook-accepts.md)
moved one of that page's facts into the answer the day before, and is the
alternative this is weighed against. Inlining reaches the caller standing in the
tool and does not scale past a fact or two, which is the second **Wrong if**
above. Naming the page beside the inlined facts is its complement rather than
its competitor.

Step 2 of the ladder, delivery, and `todo/open/T-260825-2131.md` carried it. The
change is in `src/`, so `D-FBK-052` queued it rather than closing it here.

Built the same day: the core answer ends on the page as the `typo3_rule_lookup`
call, naming what it holds beside the checks above it, and the project answer
does not. `R-ANS-028` is what it is held by, widened there from a cut section to
a tool's subject.
