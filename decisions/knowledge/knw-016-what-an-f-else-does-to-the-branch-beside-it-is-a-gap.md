---
id: D-KNW-016
title: 'What an `f:else` does to the branch beside it is a gap'
date: 2026-08-02
status: open
---

# D-KNW-016 — What an `f:else` does to the branch beside it is a gap

**An `f:else` turns the inline body of its `f:if` into no branch at all, and
neither this server nor the manuals it serves says so.**

The failure is silent in both directions. The condition is true, the data is in
the template context, the else branch is visibly the wrong one to have rendered
— and the then branch produces nothing, with no error and no log line. A session
that has read the reference page still writes the markup that fails, because the
page says the opposite in the sentence it reads first.

## Evidence

- The miss reproduces. `bin/cli hints:probe` with the feedback's own query —
  *f:if with f:else but no explicit f:then swallows the inline then-branch /
  f:link.typolink output* — matches nothing, and returns 40 candidate hints as
  the index. `f:if f:then f:else branch renders empty` matches nothing either.
- Routing is not what failed. `fluid template conditional link` reaches
  `fluid-templates` at `appliesTo(14) + text(99)`, so a statement on that entry
  would arrive at a session writing a conditional into a template. There is
  nothing here to route and nothing to move.
- The corpus names `f:then` once, on `fluid-templates`, in the `until: 12`
  statement about an array-typed argument. That statement is about what an
  inline `f:if()` evaluates to as a value; which child renders is a different
  subject. Nothing below `skills/` names `f:if`, `f:then` or `f:else` at all.
- The manual still answers with the wrong book. Called over stdio with
  `f:if f:then f:else condition ViewHelper` at `targetVersion: "14"`,
  `typo3_documentation_lookup` returns Developing a custom ViewHelper, the Fluid
  Translate ViewHelper, JavaScript form helpers and three TypoScript
  *Conditions* pages. That is
  [`D-ANS-023`](../answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md)
  reproduced.
- Indexing the ViewHelper reference would not close this, which is what keeps
  the two levers apart. The If page is at
  `https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html`
  — the `typo3fluid/fluid/latest/If.html` path `D-ANS-023` names answers 404 —
  and its Basic usage reads "Everything inside the <f:if> tag is being displayed
  if the condition evaluates to true". The `f:then` / `f:else` form stands
  beside it as a second example. Nothing on the page says the first stops
  holding once an `f:else` is there.
- The claim about the engine holds, and `D-ANS-023` already read it in
  `typo3fluid/fluid` 5.3.1: uncached,
  `AbstractConditionViewHelper::renderThenChild()` returns `null` where it finds
  an `ElseViewHelper` and no `ThenViewHelper`; compiled, `convert()` wraps the
  body as `__then` only where no `f:then`, `f:else` or `f:else if` child was
  seen. It is not read again here.
- What that reading does not settle is the range. It was taken on one engine,
  and
  [`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)
  says each covered branch pins its own — 12.4 on `^2.15.0`, 13.4 on `^4.6.1`,
  14.3 on `^5.3.1`. A statement written from it is measured on 14 and guessed on
  the other two.

## Decided

- Step 1a of the ladder, and queued. The answer is in neither `knowledge/` nor
  `skills/`, the manuals this server serves do not carry it, and there is
  nothing to reword or place.
- Not closed on the spot, and the range is why. What lands is a statement about
  engine behaviour across three majors with one of them measured. `D-VER-003`
  says how the other two are settled — one throwaway directory per major with
  the engine required into it, and the behaviour rendered through a probe
  ViewHelper — and that is the todo's first step rather than this run's.
- The statement belongs on `fluid-templates`. That is the entry the query
  already reaches, and a session hitting this is writing a template rather than
  a ViewHelper class, which is what `fluid-viewhelpers` is for.
- The feedback's suggestion is taken as far as its author could see and no
  further. Its working markup is the right canonical example. What it cannot
  say, never having asked, is which majors the trap exists on — and a statement
  bound to the wrong range is the failure the ladder's step 1a warns about.
- This is not the lever `D-ANS-023` already has. That entry indexes a book; this
  one writes a sentence the book does not contain. Neither closes the other, and
  the reading above is what says so rather than an assumption that two Fluid
  feedback want one fix.

## Assumed

- That one statement covers `f:else if` as well. The compiled path names all
  three children, so a body beside an `f:else if` is taken to be swallowed the
  same way. Nothing here measured it, and the todo can settle it in the same
  directory it settles the range in.
- That the statement is read before the template is written rather than after
  the link comes back empty. Nothing about the failure points at the condition,
  so a session debugging it has no phrase to search with — which is the argument
  for the statement naming the symptom and not only the rule.

## Wrong if

- The behaviour differs on Fluid 2.15 or 4.6. It is then two statements with a
  boundary between them, and a single unbound one — the shape this queues — is
  wrong on whichever branch it is not about.
- The engine starts reporting it: a parse error, a warning, or a log line on an
  `f:if` that has both a body and an `f:else`. The statement then describes a
  version nobody is on, and it needs an `until` it was not written with.
- The reference page gains the note upstream. The corpus is then carrying a
  restatement, and what this server owns is a pointer rather than a statement —
  the same test `D-KNW-015` sets itself.

## Since then

The range was measured and there is no boundary. One throwaway directory per
major with the engine required into it — `typo3fluid/fluid` 2.15.0, 4.6.1 and
5.3.1, the three constraints `D-VER-003` reads off the covered checkouts — and
ten markup cases rendered through a `TemplateView`, each once uncached and once
off a `SimpleFileCache` that a prior render had filled. All three engines answer
identically on all ten, so the statement is unbound: no `since` and no `until`.

What the measurement says is stronger than what this entry assumed, and the
probe ViewHelper is what says it. Beside an `f:else` the body is not rendered
and swallowed — it is never evaluated. A ViewHelper standing there records no
call, on all three engines and on both the uncached and the compiled path, while
the same ViewHelper inside an `f:then` and inside an `f:if` that has no else
records one. Plain text in that position disappears the same way, so the
feedback's reading — that ViewHelper output in particular is what gets lost — is
narrower than the behaviour. The **Assumed** about `f:else if` holds: a body
beside `<f:else if="1">` renders nothing exactly as one beside `<f:else>` does,
and the false condition still reaches the right branch in both forms.

The **Evidence** bullet saying routing is not what failed is wrong, and its own
first bullet is what shows it. `fluid template conditional link` reaches
`fluid-templates` because it carries the word Fluid; the feedback's own query
carries no path, no extension and no such word, so `Domains::detect()` returned
the PHP fallback and the whole Fluid category was gone before anything was
scored. Writing the statement did not change that — the probe still matched
nothing with the sentence in the corpus.
[`D-KNW-024`](knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
is the second lever this entry said there was no need for, and with it the
feedback's own words reach the entry they were written onto.
