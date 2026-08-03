---
id: D-ANS-049
date: 2026-08-04
status: open
---

# D-ANS-049 — An answer from outside is held where the caller cannot change it

**What the tracker and the review server answered is held in the process for a
while, and what they did not answer is fetched again every time.**

Every lookup went over somebody else's network to somebody else's machine, and
a session walking a list of issues asked forge.typo3.org the same thing once per
call.

## Evidence

- A tool call costs the caller no tokens while it runs, so the wait is cheap to
  the agent and the request is not cheap to the host. What a held answer is
  against is the rate limit, which arrives as an unanswered question rather than
  as a slow one.
- The two sources differ in who can change the answer. Nothing a caller does
  through this server reaches Forge. Gerrit is reached by the caller's own git,
  and "is there a change for this issue" is the question asked immediately after
  the push that changes it.

## Decided

- Held per process and nowhere on disk. As a Composer dependency this package
  lives in `vendor/`, where a written cache is lost on the next install, and a
  store on disk would make `readme.md`'s "request serving is read-only" false
  for a gain nothing has measured.
- Forge holds an answered read for 300 seconds, Gerrit a found change for 30.
  The short one is not about volatility but about authorship: an amend changes
  which patch set is current, and the caller is who amends.
- A miss is never held, on either. Forge holds only what parsed as the API, so a
  404 and a challenge page are both fetched again; Gerrit holds only `answered`,
  so "no change for this issue" is never served from a store.
- The store is keyed by the request URL and holds the parsed answer rather than
  the body, so the retry with the plain agent is inside what is held rather than
  in front of it.
- `Registry::call` does not drop it. The reading of an installation is dropped
  there because the caller writes to the installation between two calls
  (`D-DIS-011`); that is the property these two sources do not share, and it is
  the whole difference between the two decisions.

## Assumed

- Five minutes is short enough that a status or a comment arriving mid-session
  does not change what a task does. Nothing has measured how long a session
  works off one issue.

## Wrong if

- A caller reads an issue, watches somebody move it, and is told the old status
  long enough to act on it.
- Sessions turn out to hammer the hosts across processes rather than inside one,
  which a per-process store does nothing about and a store on disk would.

## Covered by

- `RecentTest::anAnsweredIssueIsReadFromTheTrackerOnce`
- `RecentTest::anIssueIsReadAgainOnceWhatWasHeldIsOld`
- `RecentTest::aBodyTheTrackerDidNotAnswerIsNotHeld`
- `RecentTest::aChangeThatExistsIsReadFromTheReviewServerOnce`
- `RecentTest::noChangeForAnIssueIsAskedEveryTime`
