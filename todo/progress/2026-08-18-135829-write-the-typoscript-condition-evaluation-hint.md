# Write the TypoScript condition evaluation hint

**Serves:** feedback/2026-08-18-080532-nothing-says-what-is-reachable-at-typoscript.md
**Priority:** normal
**Branch:** todo/write-the-typoscript-condition-evaluation-hint
**Claimed:** 2026-08-18

Judged as `D-KNW-101`: step 1a, the knowledge is missing. That entry establishes
the variable set, the ordering on 13.4, 14.3 and `main`, and the event that is
reachable on both, so what is left is the reading on 12.4, the binding, and where
the statement goes.

- State what a condition is handed, per covered major: `page`, `site`,
  `siteLanguage`, `request` as a `RequestWrapper`, `context`, `tree` and the
  `frontend`, `backend` and `workspace` objects, that `pageId`, `localRootLine`
  and `fullRootLine` are unset again before the resolver sees them, and that
  `tsfe` was there until 14. `D-KNW-101` names both classes the list is assembled
  in.
- State that condition evaluation happens inside the middleware stack, which is
  what makes `$GLOBALS['TYPO3_REQUEST']` unusable there from 14 while it happens
  to work on 13. Say what the failure looks like, because that is what somebody
  searches with: the condition evaluates false, no error is raised and nothing is
  logged.
- Establish the 12.4 half before binding anything.
  `AfterPageAndLanguageIsResolvedEvent` is in `.checkouts/12.4` but
  `PageInformationFactory` is not, so where it is dispatched relative to
  condition matching is what decides whether the recommendation holds on the
  oldest covered LTS or binds `since: 13`.
- Recommend the event as the way an extension gets the current page record to a
  condition on more than one major, and say what makes it that: the class is
  byte-identical on 13.4, 14.3 and `main`, and it is dispatched before condition
  matching on all three.
- Place it. `typoscript` is a domain 21 hints already declare and none of them
  answers this, so the statement is a hint of its own; which file it goes in is
  open, and `knowledge/hints/` has no `typoscript.json` today.
- `appliesTo` decides whether it arrives.
  `bin/cli hints:probe "typoscript condition variables page request v14"` reaches
  nothing today, and reaching the new hint is what says the placement works.

The feedback is archived by the commit that lands the hint.
