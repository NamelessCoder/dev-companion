---
date: 2026-08-24T22:51:53+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# What worked: gerrit_lookup caught a stale patch set, the guides list surfaced a page I would not ...

## Observation

Task: review core change 93177, then amend it, then build a follow-up hardening patch. Filing what must not be broken later, as concretely as the failures.

1. typo3_gerrit_lookup(change="93177", messages="people") was the highest-value call of the session and it was the second one I made. It returned patch set 10 with its commit sha, which let me discover that the change had moved twice that same day; a review of what I would otherwise have fetched would have been of an older patch set. It returned the four unresolved comment threads with their authors, files and line numbers, and the vote history including Gerrit's "Outdated Votes" lines. Without it I would have re-reported every point Markus Klein, Stefan Bürk and Oli Bartsch had already raised and the author had already answered — the exact failure the review skill warns about, avoided by one call. The `messages="people"` argument dropping bot pings was worth it. The `issues` array joining the change to Forge #109233 and #110365 with subjects meant I never had to guess which issue the trailer meant.

2. typo3_project_describe's `guides` array is how I learned any/testing/browser-check exists. My client shows no resource list, so without that array at the end of the orientation call I would have improvised the whole Playwright setup. That page then told me to write the harness and its output below the checkout's own typo3temp/var/ because it is gitignored — I did, and the harness never appeared in git status while I was preparing a patch. That is precisely the failure it exists to prevent, prevented.

3. typo3_rule_lookup(query="changelog entry") returned the sentence that settled two separate decisions hours apart: "A casual bug fix carries none, because the commit message is what informs the reader." It stopped me demanding a changelog entry from 93177 (the review checklist calls that a review defect), and at the very end the user rejected an Important entry I had written for the hardening patch — I could check their objection against the rule rather than argue, and they were right: nothing in the change requires manual action.

4. typo3_test_run_guide's note that runTests.sh stops reading its own options at the first non-option word, so every option stands before `--` and the path after. I wrote roughly fifteen targeted invocations over the session and never hit `Test file "--filter" not found`.

5. The base order's instruction to hold the server's answer against the checkout. typo3_changelog_lookup(type="deprecation", version="15") returned five entries, none touching this code. That is a correct empty and it cost one call; I would run it again.

## Query

typo3_gerrit_lookup(change="93177", messages="people"); typo3_project_describe(); typo3_rule_lookup(query="changelog entry", targetVersion="15"); typo3_test_run_guide(paths=[backend controller and test paths]); typo3_changelog_lookup(type="deprecation", version="15", limit=100)

## Suggestion

Keep all five as they are. Two are worth protecting explicitly:

- typo3_gerrit_lookup returning the current patch set's commit sha is what lets a session compare against git rev-parse HEAD and notice it is reviewing the wrong revision. Do not drop that field to shorten the answer.
- The `guides` array at the end of typo3_project_describe is, for a client that renders no resource list, the only route to those documents. Any change that trims the orientation answer should keep it.
