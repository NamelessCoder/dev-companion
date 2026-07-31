---
id: D-SCO-4
date: 2026-07-29
status: standing
---

# D-SCO-4 — The frontend is recognised by name, and only the two UI sections go

**A frontend marker with no backend marker withholds the `Backend CSS` and
`Backend TypeScript` categories, and the answer says which and why.**

The backend CSS hints were answered for a Bootstrap 5 theme extension, where
every one of them is inverted: treat Bootstrap as legacy, prefer `--typo3-*`
properties, work in both backend color schemes. The paths gave nothing away —
`Resources/Public/Scss/bootstrap.scss` and `Build/Sources/Sass/_variables.scss`
are shaped exactly like core paths, and the second one is one.

- **Decided:** the task text decides. A frontend marker with no backend marker
  withholds the `Backend CSS` and `Backend TypeScript` categories, and the
  answer says which and why. `Scope::isOutsideCore` was the obvious lever and
  is the wrong one: an extension's backend module has backend CSS, and the core
  renders a frontend.
- **Assumed:** words are enough here although R-SCO-1 says they are not for
  `outsideCore`. The difference is the cost of being wrong: withholding leaves
  a caller with a pointer to docs.typo3.org, while the wrong direction hands
  over four confident instructions to rewrite a working theme.
- **Assumed:** naming the categories `Backend CSS` and `Backend TypeScript` is
  worth the churn in every rendered answer. A category label is read on every
  hit, a boundary note only when it fires.
- **Wrong if:** a core contributor working on the frontend rendering of
  `fluid_styled_content` loses the CSS hints they wanted. `styleguide` and
  `backend` are the escape, and they are named in the notice.
