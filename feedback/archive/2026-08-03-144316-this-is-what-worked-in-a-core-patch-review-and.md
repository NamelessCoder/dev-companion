---
date: 2026-08-03T14:43:16+00:00
category: idea
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup, typo3_changelog_lookup, typo3_rule_lookup, typo3_test_run_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# The Gerrit answer names the change and not the patch set the reviewer is holding

## Observation

Trimmed on 2026-08-03 to the half of item 2 that no answer carries. Four of the
five behaviours this report asks be kept reproduce in its own words, re-run
through this package from `/home/benji/projects/typo3-cms`: `typo3_forge_lookup`
on 110359 renders the empty description as an empty `## Reported` heading beside
the one automated note; `typo3_gerrit_lookup` answers in both directions and
names the query each ran; `typo3_changelog_lookup` with the bare query
`ResourceFactory` and no filters returns all four entries, the `Feature-72904`
precedent and the `Important-107735` counter-direction among them; and
`typo3_test_run_guide` with the seven changed paths returns all nine suites
rather than the narrowed ones, with the `cglGit` worktree caveat.

The fifth is refused, in `D-ANS-035`. The matcher-kind enumeration was read as
closed over visibilities and produced a finding saying that a matcher cannot
exist for a removed protected method. It can: the method matchers are a weak
match on the name where it is used and never resolve the class, and
`MethodCallMatcher.php` carries `RendererRegistry->getRendererInstances`, which
`Breaking-110277` turned from public to protected. The section says so now.

What is left is one claim inside item 2. The Change-Id lookup is credited with
establishing "that I was reading the same patch set that exists on the server",
and no part of the answer says that. Re-run on 2026-08-03 with
`change:Id53f1068d3866128320487df9ec59530d94a4aa1`, the text is the subject, the
status `NEW`, the branch, the change number, the URL and `Last moved`; the data
adds `project` and nothing else. There is no revision, no patch-set number and
no commit hash to hold a local `HEAD` against. The patch-set number the session
reports, 1, came from the automated Gerrit note that `typo3_forge_lookup`
returned — a different call, and one that only exists because Gerrit happens to
comment on the issue.

The question is the one a patch review opens on: whether the diff in the
checkout is the revision that is up for review. A stale local commit makes every
finding after it a finding about something nobody is reviewing.

## Query

`typo3_gerrit_lookup(change "Id53f1068d3866128320487df9ec59530d94a4aa1")` and
`typo3_gerrit_lookup(issue "110359")` during a core patch review of commit
9f6c6eb9093 (#110359), against change 95070 on `main`. Both answered; neither
answer carries a patch set or a revision.

## Suggestion

Keep both directions separate — they answered different questions in one review
and neither restated the other. Have the answer carry which patch set is current
and the revision it is, so a reviewer can hold it against the commit it has
checked out. The "same patch set" claim would then be one the answer supports
rather than one the reader supplies.
