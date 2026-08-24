---
date: 2026-08-24T20:50:50+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup cannot filter changes by person, so "my reviews" is unanswerable

## Observation

Task: "we want to work on almost ready small open reviews and finish them", in a TYPO3 core checkout on main (15.0.0-dev).

I loaded typo3_gerrit_lookup's schema in my very first ToolSearch call, read it, and then never called it once in the entire session. Every Gerrit fact I used came from hand-built curl against review.typo3.org.

The reason is in the schema: the four ways in are issue, change, query and path. None of them takes a person. The session's opening question was "which open changes are mine" and its refinements were "which have I already voted on" (the user said twice: "patches auf denen ich bereits gevotet habe brauchen wir nicht mehr anschauen", "patches von mir, welche auf votes warten ebenfalls nicht"). Those are owner: and reviewedby: predicates. query matches commit message text, so a name reaches changes that mention the person, not changes that are theirs — the same limitation typo3_forge_lookup documents for its own query and solves with reportedBy/assignedTo/involving.

So I wrote the queries by hand:
- q=owner:<email>+status:open with o=CURRENT_REVISION
- the same again with o=LABELS,DETAILED_LABELS,MESSAGES
- and a third time, because votes came back with author name null until I added o=DETAILED_ACCOUNTS
- q=...-owner:<email> -reviewedby:<email> -is:wip label:Code-Review>=1 -label:Code-Review<=-1 -label:Verified<=-1

That last one is the query the whole session was built on and it has no expression in this tool.

Two further things I had to compute by hand from ALL_REVISIONS, and would need again: whether a local branch tip is a patch set of its change (the test that separates a recoverable branch delete from an unrecoverable one), and the refs/changes/<last two>/<number>/<patch set> refspec, which I derived arithmetically from the change number.

## Query

Intended calls that do not exist: typo3_gerrit_lookup(owner:"benjamin.kott@outlook.com", open:true) and typo3_gerrit_lookup(reviewedby:"benjamin.kott@outlook.com", open:false). Actual substitutes: GET https://review.typo3.org/changes/?q=owner:<email>+status:open&o=CURRENT_REVISION&o=LABELS&o=DETAILED_LABELS&o=DETAILED_ACCOUNTS&n=30 and ?q=project:Packages/TYPO3.CMS+status:open+branch:main+-owner:<email>+-reviewedby:<email>+-is:wip+label:Code-Review>=1+-label:Code-Review<=-1+-label:Verified<=-1

## Suggestion

Add person predicates to typo3_gerrit_lookup, mirroring what typo3_forge_lookup already does for the tracker:

- owner: changes this person owns
- reviewedby: changes this person has voted on
- involving: the union, since the two answer different questions and a reviewer wants both

Resolve the person the way typo3_forge_lookup resolves reportedBy — by name against the project's members, not only by exact email.

Combine them with the existing open flag and with a size or vote filter, because "almost ready" is the actual question: small, verified, at least one Code-Review+1, no negative vote. Every one of those is a label: predicate I had to spell by hand.

In the answer, carry per change: insertions/deletions, the current patch set number with its commit, the votes with voter names (i.e. DETAILED_ACCOUNTS behaviour by default — the anonymous default returning null names cost me a full repeat of the call), and the fetch refspec refs/changes/NN/NNNNN/P rather than leaving the caller to compute it.
