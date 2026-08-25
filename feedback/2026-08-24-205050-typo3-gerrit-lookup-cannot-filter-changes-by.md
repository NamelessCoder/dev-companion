---
date: 2026-08-24T20:50:50+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup cannot ask for the changes a person has not touched

## Observation

Trimmed on 2026-08-25 to the one part of this report that is still open. The
person filters it asked for were built as `backlog` with `owner`, `reviewedBy`
and `involving` (`D-ANS-107`), and so were the size and vote filters, the
insertions and deletions per row, and the `refs/changes/NN/NNNNN/P` refspec.

What is left is the negation. The session's task was "we want to work on almost
ready small open reviews and finish them", in a TYPO3 core checkout on main, and
the user said twice that patches they had already voted on and patches of their
own waiting on votes were not what to look at — "patches auf denen ich bereits
gevotet habe brauchen wir nicht mehr anschauen", "patches von mir, welche auf
votes warten ebenfalls nicht". Those are `-owner:` and `-reviewedby:` predicates
and the tool has no expression for either. `involving` is their union taken
positively, which is the opposite set.

That last query is the one the whole session was built on:

    q=project:Packages/TYPO3.CMS+status:open+branch:main+-owner:<email>
      +-reviewedby:<email>+-is:wip+label:Code-Review>=1+-label:Code-Review<=-1
      +-label:Verified<=-1

Every operator in it except the two negated person ones is now an argument.

The other thing the session had to compute by hand and would need again: whether
a local branch tip is a patch set of its change, which is the test that
separates a recoverable branch delete from an unrecoverable one. That reads a
checkout rather than the review server, so it is a different question to this
one.

## Query

The intended call that does not exist: the changes waiting for a reviewer who is
not this person. The actual substitute is the query above, written by hand.

## Suggestion

A person filter that excludes rather than selects, beside `owner`, `reviewedBy`
and `involving`. What it answers is "changes I could review" — everybody else's
open work that nobody has turned down and that I have not already judged — which
is the question a reviewer with time opens with, and no positive filter reaches
it.

The name is the hard part rather than the query: `AGENTS.md` does not take a
negation where an affirmative is meant, and `-owner: -reviewedby:` is a negation
in Gerrit's own spelling.
