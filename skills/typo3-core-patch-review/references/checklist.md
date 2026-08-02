# Core patch review checklist

The surfaces below are written down whole before the diff is read a second
time, and each one is answered in the report — assessed, unassessed, or not
applicable to this diff. A surface this patch does not touch costs one line; a
surface nobody looked at reads as clean unless it is named.

## Review surfaces

- **Public API.** What the diff removes, renames, or changes the signature of,
  and what the contribution rules require for each of those. This is the surface
  a reading of the new code does not show, so it is enumerated from the diff's
  deletions rather than from its additions.
- **Behaviour.** What the patch changes for code that calls it, including the
  paths it does not touch: a guard that was unreachable before and is live
  after, a value that used to be written and now is not, a shape of output other
  code asserts on.
- **Compatibility.** Whether the change fits the branch it targets, and whether
  what it does is available on that branch at all.
- **Tests.** What exercises the changed behaviour, and whether the layer is the
  one that can fail on it. A change with no coverage is a finding; coverage that
  cannot fail on this change is a worse one, because it reads as coverage.
- **Documentation and changelog.** What the diff obliges — the entry, its
  directory, its file name, its cross-references — and equally, whether an entry
  is owed at all. Demanding one where the rules do not is a review defect.
- **Commit shape.** Subject, body, issue reference, target branch line, and the
  markers the change type requires.
- **Review readiness.** Whether the patch can be understood from the issue and
  the message alone, and whether a reviewer can reproduce what it claims to fix.
- **Security.** Where the diff touches authorization, user input, output
  escaping, file paths, or a boundary between what a role may and may not do. A
  finding here is a value and a sink, and both are established or it is not a
  finding.
- **The working tree around the patch.** What is modified or untracked beside
  the commit, because it ships with the patch if anybody stages it carelessly
  and it is not part of the change.

## What a finding owes

Every finding carries four things, and one of them is the one reviews skip:

1. the changed path, at its line;
2. what the patch does there;
3. the rule or the behaviour it collides with, from the lookup that owns it;
4. **the consequence** — what breaks, for whom, and when it would be noticed.

A finding without the fourth is a preference. A finding whose rule came from
recall rather than from a lookup is a preference with a citation.

Distinguish what was verified from what was reasoned. A behaviour traced into
the installed code, a command that was run with its output, and a reading of the
diff are three different weights, and a report that does not separate them
hands the reader a uniform confidence the review did not have.

## Severity

- **Blocks submission** — the patch cannot go up as it is: the message will be
  rejected, the change breaks something the rules forbid breaking, or the diff
  does not do what it says.
- **Sent back in review** — a reviewer would ask for it: missing coverage, a
  missing changelog entry, an unhandled case, a public API obligation not met.
- **Worth changing** — real and not blocking. Say so, and do not spend the
  reader's first paragraphs on it.
- **Correct and checked** — kept short and kept in, because it is the only thing
  that separates a surface that was read from one that was skipped.

Rank by what stops the patch first and by consequence second. A cosmetic finding
above a behavioural one costs the review its credibility for the rest of the
list.
