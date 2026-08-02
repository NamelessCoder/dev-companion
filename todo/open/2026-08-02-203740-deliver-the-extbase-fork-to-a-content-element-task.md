# Deliver the Extbase-or-not fork to a content-element task

**Serves:** feedback/2026-08-01-003925-extbase-was-never-considered-as-an.md, R-ANS-016
**Priority:** low

Ladder step 2, delivery: the fork is written on the `extbase` and the
`frontend-records` hint, and every path to it opens on a word the caller has not
thought of — `D-ANS-027` has the probes. Establish first what the fork actually
asks on the covered versions, in `.checkouts/`: a plugin is a `CType` like any
other since v14, so "a content element or an Extbase plugin" is not the two
categories the existing sentence was written against, and the wording that
lands has to be right about that before it is placed. Then place it in one of
the three, and say which and why: the `content-elements` hint, the
`content-element` checklist in `knowledge/task-intents.json`, or the
architecture section of `skills/typo3-content-element-development` — the last is
installed into other projects, so it is shown before it is published. The
`typo3_extension_scope` line of that intent is the second half: it asks for the
call "for the content elements this extension already registers and the
templates they render through", and what this feedback needed from it is that a
`kind` of `plugin` is the architecture the extension already has.
