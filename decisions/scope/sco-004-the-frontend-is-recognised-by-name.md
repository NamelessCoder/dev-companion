---
id: D-SCO-004
title: 'The frontend is recognised by name'
date: 2026-07-29
status: revoked
---

# D-SCO-004 — The frontend is recognised by name

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

The withholding held and the sentence about the escape did not. Asked with a
`fluid_styled_content` CSS path and a frontend task, both sections go and the
corpus says the contributor loses nothing they wanted. What was wrong is that
`styleguide` worked and was named nowhere, while the escape that was named
addressed somebody styling a backend module. The notice names both now, each
with the case it is for: a mechanism reachable by a word is no escape while the
word appears in no answer this server writes.

## Since then

Both halves are held rather than argued.
`ScopeTest::aCoreContributorOnFrontendLosesTheBackendUiSections` is the loss and
`ScopeTest::theNoticeNamesTheWordsThatBringTheBackendUiSectionsBack` is the
escape — the second asserts the clause in the notice and then calls with it,
because a notice offering an escape and a notice apologising for one are the
same string to everything except a caller who tries it.

What carries the withholding is `R-SCO-004`, and no successor decision is owed.
It names the two categories as the backend interface's and says they are
withheld with the reason where a task names the frontend, which is this entry's
statement; the two tests above are among the five that hold it.
