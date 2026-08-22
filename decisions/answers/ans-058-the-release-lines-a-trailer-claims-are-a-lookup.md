---
id: D-ANS-058
title: 'The release lines a trailer claims are a lookup'
date: 2026-08-05
status: confirmed
coveredBy:
  - ReleaseLinesTest::theLinesTakingAPatchNarrowAsTheirWindowsClose
  - CommitMessageTest::aBranchOutOfRegularSupportIsAnErrorNamingTheLinesThatTakeAPatch
  - CommitMessageTest::aBranchTheListDoesNotCarryIsAWarningSayingWhenItWasRead
  - CommitMessageTest::theMissingTrailerNamesTheLinesThatTakeAPatch
---

# D-ANS-058 — The release lines a trailer claims are a lookup

**Which TYPO3 branches are maintained today, and what each one is, becomes an
answer this server holds, and `typo3_commit_message_guide` validates the
`releases` it is handed against it.**

The `Releases:` trailer is a required part of every core commit message, and
nothing here says what may stand in it. The tool asks the caller for a fact this
server does not offer anywhere, and accepts whatever comes back.

## Evidence

- `feedback/2026-08-05-033924`: a session writing a core bugfix passed
  `releases: ["main", "14.3", "13.4"]` and got "No commit message readiness
  issues found". A long-dead branch would have been accepted the same way.
- `typo3_rule_lookup` answered the changelog half precisely — a casual bug fix
  owes no entry, a backport goes into the `<lts>.x` directory of the oldest
  branch it reaches — and "the oldest branch it reaches" is the fact it cannot
  supply.
- The session established it by counting trailers on 40 commits of `origin/main`
  — 19 `main, 14.3`, 11 `main`, 10 `main, 14.3, 13.4` — and then read the
  changed file on both other branches to confirm the defect was there.
- That is inference from a sample, and the checkout does not answer it either:
  `git branch -r` lists everything back to `TYPO3_3-6`. A session with less
  caution would take the branch list for the answer.
- It is the class of fact a knowledge server exists to hold. It goes stale on a
  release day, and every core change carries it.

## Decided

- **Taken on.** The judgement is step 1b, a missing shape: the answer is
  available in the world, and there is no way to get it here. What each line is
  — dev, sprint, LTS, ELTS, security-only, ended — is what makes the trailer a
  claim rather than a habit.
- The boundary is the maintained lines and what a change type owes each. It is
  not a release calendar, not a support-window product page, and not advice
  about which branch a patch should target, which is the author's reading of
  where the defect is.
- `typo3_commit_message_guide` turns the argument into a check. A branch that is
  not a maintained line is a finding, and a trailer that names nothing keeps the
  placeholder it already has.
- What the entry says about TYPO3 waits for the reading. Where the fact is read
  from — get.typo3.org, the core's own branch list, the release notes — is the
  first step of the todo, not a thing this judgement decides from recall.
- It stays a claim the author has to have verified. The check says the branch is
  maintained; it cannot say the defect is on it, which for the reported patch
  meant reading the changed file on `origin/14.3` and `origin/13.4`.

## Assumed

- The maintained lines are readable from a source that stays current. If the
  only honest source is a page somebody edits, this becomes a statement that
  ages exactly like the one it replaces.
- A wrong `Releases:` trailer costs something. It is caught in review today, so
  what the check buys is the round trip rather than the defect.

## Wrong if

- The lookup is written and sessions keep counting trailers in the checkout,
  which would say the answer arrives too late in the task.
- The list goes stale between releases and a check starts failing valid
  trailers, which is worse than the silence it replaced.
- What a change type owes each line turns out to be a judgement rather than a
  rule, in which case the check can only report the branch and not the set.

## Confirmed on 2026-08-05

The reading was done and the assumption held.
`https://get.typo3.org/api/v1/major/` answers one entry per major with
`maintained_until` and `elts_until`, which is what
`knowledge/release-lines.json` now carries. It knows nothing about the
development line — there is no entry for 15 — so `main` is stated in that file
rather than read, and it is the one line whose end date nothing supplies.

Two more properties of that source decide what a re-read finds, and neither is
visible without hitting it. Its `subtitle` is stale in the direction that
matters: major 14 still reads "The upcoming LTS release (for new projects)"
months after its `release_date` of 2026-04-21, while major 13 reads "The stable
LTS release", so a reader taking the prose for the state gets the current LTS
backwards — only the two dates are load-bearing. And `/api/v1/release/` carries
version strings that are not versions, `7-snapshot-20170404` among them, so
anything that sorts or parses that list filters first.

The checkout confirms the API instead of replacing it. The public branch of a
line stops receiving commits when regular support ends: `origin/12.4` last moved
on 2026-04-14 against a `maintained_until` of 2026-04-30, `origin/11.5` on
2024-10-16 against 2024-10-31, while `main`, `14.3` and `13.4` all carried a
commit on 2026-08-04. So the three lines the reported session inferred from 40
trailers were the right three, which is the outcome that makes the silence worth
closing rather than the inference worth distrusting.

What the two **Wrong if** about staleness bought is a design rather than a
promise: the windows are stored and the state is derived on the day the question
is asked, so a line moving into ELTS needs no re-read and cannot start failing a
valid trailer. Only a branch created after the file was read is missing from it,
and that case is a warning naming the source and the read date rather than an
error.

The third is settled and went the way it warned it might. Measured on the core
checkout, `origin/main..origin/14.3` carries no `[FEATURE]` and no `[!!!]` at
all, and `origin/main..origin/13.4` carries three `[FEATURE]` against 969
`[BUGFIX]` — all of them explicit backports. A feature on a release line is
therefore rare and permitted, which makes it the release managers' call and not
a set the check can hold. It is written into
`knowledge/documents/core/contribution/commit-messages.md` as prose, and the
check still reports the branch alone.
