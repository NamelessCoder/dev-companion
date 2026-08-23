---
id: D-ANS-063
title: An option list is what the caller did not know to ask for
date: 2026-08-07
status: open
---

# D-ANS-063 — An option list is what the caller did not know to ask for

**Three strength reports out of one day of core work name the same two kinds of
answer as load-bearing.** One is an option a session would not have known to ask
for; the other is a fact no checkout can supply.

Read as a boundary rather than as a confirmation
([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).

## Evidence

- `feedback/2026-08-07-065401`, from the triage and patch session.
  `typo3_forge_lookup` returned a note — "i work on that", posted a day earlier
  — which is the one fact that changed what the session recommended; it is in
  the comments and in nothing else the session called. `typo3_test_run_guide`
  returned `-d sqlite|mariadb|postgres` and the warning that a suite reporting
  success over no files is not a green; the session ran every check on three
  databases because the option was there, and added a row count because of the
  warning. `typo3_changelog_lookup` matched nothing and returned `termCounts`
  and `termSubsets`, so the retry was targeted. `typo3_project_describe` said
  the only declared scripts are Gerrit hooks, so the session never looked for a
  `composer test` that does not exist.
- `feedback/2026-08-07-065419`, same session, on `typo3_commit_message_guide`.
  Four calls, each after the change had moved. `summary-length-preferred` caught
  a 62-character subject for one round trip. `breaking-not-assessed` said the
  classification had been assumed rather than checked, which is why the session
  passed `isBreaking` and `isDeprecation` after reading the diff. The Releases
  validation is the one it ranks highest: it had drafted "main, 13.4" from its
  own reading and the guide held all three branches against what takes a patch
  today.
- `feedback/2026-08-07-132520`, a different session reviewing the resulting
  commit. `typo3_project_describe` gave `typo3Version 15.0.0-dev` — the only way
  it could know the branch it stood on was not v14 — and `extensions: []`
  explicitly, which closed a prescribed step instead of leaving it to be
  inferred. `typo3_test_run_guide` again: `-b docker` is why the first run did
  not fail on a host that has no podman, and `-d postgres` is why two findings
  are measured rather than argued. The session could show `SQLSTATE[22008]` on
  postgres where mariadb returned zero rows, and it says a guide returning only
  the suite name would have left that as "this would presumably throw".
- Both sessions name the Releases validation and the DBMS option list. Neither
  could have produced either from the checkout.

## Decided

- The boundary these three describe runs between a fact the checkout holds and a
  fact only the project holds. Which branches take a patch today, which
  container runtime is the fallback, which databases a suite can be pointed at,
  and what a subject line may weigh are all on the second side, and all four are
  named as having changed what the session did.
- The second kind is the option list. `-d postgres` and `-b docker` are not
  answers to what was asked; they are what the caller did not know to ask, and
  the review session says plainly that its strongest finding exists because
  switching database was one flag away. An answer trimmed to the question would
  have cost it.
- So neither is dropped for brevity. The suites keep their invocation notes, the
  guide keeps the runtime and database options, `typo3_project_describe` keeps
  answering `extensions: []` rather than answering nothing, and
  `typo3_commit_message_guide` keeps validating the releases list rather than
  only formatting the message.
- `D-ANS-059` read a comparable split and put the reported costs on the network
  side. This one does not repeat that: `typo3_forge_lookup` is a network reader
  and its comments are named here as load-bearing, which is the same turn
  `D-ANS-059` recorded at its foot.

## Assumed

- Three reports from one model in one checkout on one day. They are three tasks
  rather than three sessions of one task — a triage, a patch, a review — but the
  reader is the same and its habits are the variable nothing here controls.
- A strength names what would be missed if it went. None of these is a recorded
  run, so what is carried is where the boundary is, not that any decision holds.

## Wrong if

- A session reports the option lists as noise it had to read past, which would
  say the second kind is worth less than these three make it.
- The Releases validation is found to be answerable from a checkout after all,
  which would move it across the boundary.
- A debrief names a computed answer as the one that misled it, which is the
  first **Wrong if** of `D-ANS-059` and would be the same event here.

## Since then

The same day, `feedback/2026-08-07-132416` is the counter-case inside this
boundary: `typo3_gerrit_lookup` is a network reader whose answer misled a
review, and `D-ANS-062` is what came of it. The split holds — what the server
computes was not reported wrong in any of the three — and the network side has
now been reported on twice more.
