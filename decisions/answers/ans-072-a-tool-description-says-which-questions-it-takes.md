---
id: D-ANS-072
title: 'A tool description says which questions it takes'
date: 2026-08-10
status: open
coveredBy:
  - ScopeTest::theTwoLookupsThatBothReadAsAConventionNameEachOther
---

# D-ANS-072 — A tool description says which questions it takes

**`typo3_script_lookup` names what it holds and hands the run-shape questions to
`typo3_test_run_guide`; the session that had both in its list called neither for
six shell round trips.**

It read "find notes for TYPO3 core scripts" as "find the right suite", which the
other tool had already answered, and grepped a 1400-line script instead.

## Evidence

- `feedback/2026-08-10-101802`. The questions were how the browser suite
  provisions its instance, whether arguments reach Playwright, which image and
  network flags it uses, and whether an instance can be reused. None of them
  sounds like a script lookup, and the session says so itself: it cannot report
  whether the tool would have answered, because it never made the call.
- Three of the four are answered now, and not by this tool.
  [`D-KNW-068`](../knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md)
  put the prepare and browser suites into `test-suite-hints.json` with what each
  prints, that the containerised run passes nothing through, and what
  `PLAYWRIGHT_USE_EXISTING_INSTANCE` does.
- The fourth is not, and this tool would not have carried it either: the
  document it answers from has no section on the browser suites at all — asked
  the session's own four questions, it matches one, on the word "arguments".

## Decided

- The description says what the document holds — the invocation and what it
  needs first, the passthrough and the options, the commands per subject, the
  pre-commit hook — and names `typo3_test_run_guide` for the question this
  session actually had. Two adjacent tools whose names both read as "about the
  scripts" is what the routing has to survive, and only the descriptions can do
  it.
- No section on the browser suites in the document. A prose document may only
  name a suite every covered major carries, which `KnowledgeTest` holds, and the
  browser suites are not on every one — that is exactly why the suite list is
  where they live, with a version range each.
- The container image and the network parameters stay unanswered here. A session
  building its own harness needs the route rather than runTests.sh's internals,
  and that is
  [`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-site-on-the-router.md).

## Assumed

- That the name is not the whole of the problem. It was read as one kind of
  question and its description offered nothing to correct that, so the
  description is where the cheap fix is; whether a tool answering from one
  document earns a name beside `typo3_rule_lookup` at all is a larger question
  this does not settle.

## Wrong if

- Another session reports skipping it for the same reason. Then the word
  "script" is what misroutes, and renaming or folding the tool is the change —
  which costs a name clients already know.

## Since then

The description says both halves today, read on 2026-08-23. It opens on what the
tool holds — how `Build/Scripts/runTests.sh` is started and what it needs first,
what an argument after `--` reaches, the commands per subject, the pre-commit
hook — and hands the rest over by name: "Which suite a change actually needs,
and what one of them does when it runs — what it provisions, what it passes
through, which environment variables change it — is `typo3_test_run_guide`."

No session reports skipping it for the reason this entry names. The one open
feedback that names the tool since, `2026-08-19-090401`, reports a client that
delivered every tool as a bare name with no schema and a session that called
none of them — a cause on the client's side, which `D-AUD-011` owns, rather than
the word "script" misrouting a session that read the description.

The pattern recurred one pair over, on 2026-08-24. `feedback/2026-08-24-133651`
reports a session that could not tell whether "what is this codebase's idiom for
X" goes to `typo3_hint_lookup` or to `typo3_rule_lookup`, and read both
descriptions after the fact to find out. Neither names the other: the first
opens "Return hints for TYPO3 core paths or task topics, grouped by section",
the second "Search the TYPO3 rules and procedures this server carries, by
topic", and both plausibly take the question. So this entry's **Decided** — two
adjacent tools that only their descriptions can tell apart — holds on the pair
that carries most of the corpus, where it was written for `typo3_script_lookup`.

What it is not is this entry's **Wrong if**. Nothing in that report says the
word "hint" or the word "rule" misrouted it; the session called neither tool and
grepped its checkout instead, which is what `feedback/2026-08-24-133515` reports
for the whole of the same session. The lever is a clause in each description
naming what the other takes, and it touches `src/`, so it is queued on the card
serving that feedback rather than made here. The routing block of
`knowledge/server-scope.json` already splits the two for a core patch — hints by
the paths being changed, rules for the Gerrit workflow — and a session that
calls nothing never reaches it.

That clause landed on 2026-08-24. `typo3_hint_lookup` now says what a hint is —
a convention at the code itself — and hands the procedures to
`typo3_rule_lookup` by name; `typo3_rule_lookup` hands back what the code has to
look like. `ScopeTest` holds both halves, so a description that drops the other
name fails rather than misroutes.
