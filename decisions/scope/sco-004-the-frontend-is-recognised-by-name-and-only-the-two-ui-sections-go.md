---
id: D-SCO-004
date: 2026-07-29
status: revoked
---

# D-SCO-004 — The frontend is recognised by name, and only the two UI sections go

**A frontend marker with no backend marker withholds the `Backend CSS` and
`Backend TypeScript` categories, and the answer says which and why.**

The backend CSS hints were answered for a Bootstrap 5 theme extension, where
every one of them is inverted: treat Bootstrap as legacy, prefer `--typo3-*`
properties, work in both backend color schemes. The paths gave nothing away —
`Resources/Public/Scss/bootstrap.scss` and `Build/Sources/Sass/_variables.scss`
are shaped exactly like core paths, and the second one is one.

## Decided

- The task text decides. A frontend marker with no backend marker withholds the
  `Backend CSS` and `Backend TypeScript` categories, and the answer says which
  and why. Scope::isOutsideCore was the obvious lever and is the wrong one: an
  extension's backend module has backend CSS, and the core renders a frontend.

## Assumed

- Words are enough here although R-SCO-001 says they are not for `outsideCore`.
  The difference is the cost of being wrong: withholding leaves a caller with a
  pointer to docs.typo3.org, while the wrong direction hands over four confident
  instructions to rewrite a working theme.
- Naming the categories `Backend CSS` and `Backend TypeScript` is worth the
  churn in every rendered answer. A category label is read on every hit, a
  boundary feedback only when it fires.

## Wrong if

- A core contributor working on the frontend rendering of `fluid_styled_content`
  loses the CSS hints they wanted. `styleguide` and `backend` are the escape,
  and they are named in the notice.

## Revoked on `2026-08-02`

The withholding held and the sentence about the escape did not. That contributor
was asked, with a `typo3/sysext/fluid_styled_content/Resources/Public/Css/` path
and a task naming the frontend rendering and both asset domains. Both sections
go — `withheldCategories` comes back as `Backend TypeScript` and `Backend CSS`,
and no hint in either category is in the answer. Being inside the core moves
nothing, which is this decision working rather than failing, and reading the
corpus back settles the loss the entry was worried about: `css-class-naming` is
`.toolbar-item`, `.module-docheader` and `t3js-*`, `css-source-build-boundaries`
is `Build/Sources/Sass/` and `lintScss`, `css-styleguide-demos` is a demo below
`typo3/sysext/styleguide/`. None of those is advice about what
`fluid_styled_content` renders, so the contributor loses nothing they wanted.

What was wrong is the last line above. `styleguide` did work — it is a
`BACKEND_MARKER`, so `Domains::namesTheFrontend` returns false on it and both
sections come back — but it was named nowhere in the notice, and the other one
was named as *"Name the backend in the task if you are styling a backend
module"*. The contributor above styles no backend module, so the one escape they
were shown read as addressed to somebody else and the one that would have fitted
was invisible. The notice now names both, each with the case it is for. The
assumption that failed is not the withholding but that a mechanism reachable by
a word is an escape somebody can find: the word was in the marker list and in no
answer this server writes.

## Since then

Both halves are held rather than argued.
`ScopeTest::aCoreContributorOnFrontendRenderingLosesTheTwoBackendUiSections` is
the loss and
`ScopeTest::theNoticeNamesTheWordsThatBringTheBackendUiSectionsBack` is the
escape — the second asserts the clause in the notice and then calls with it,
because a notice offering an escape and a notice apologising for one are the
same string to everything except a caller who tries it.
