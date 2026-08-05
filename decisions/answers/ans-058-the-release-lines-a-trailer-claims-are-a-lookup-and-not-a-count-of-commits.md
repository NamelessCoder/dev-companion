---
id: D-ANS-058
date: 2026-08-05
status: open
---

# D-ANS-058 — The release lines a trailer claims are a lookup, and not a count of commits

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
