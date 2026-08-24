---
date: 2026-08-24T11:08:51+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Signed-off-by is in no rule document, and querying for it returns an unrelated page at score 0

## Observation

Task: review a local core patch before pushing it to Gerrit. The commit carried Resolves, Related, Releases and Change-Id and no Signed-off-by, and I had to decide whether that blocks submission.

typo3_rule_lookup query "signed-off-by" returned exactly one match: "Proving a TypoScript Condition Verdict" (any/testing/proving-a-condition), score 0, matchedHeadings ["Which URL Is Requested"]. Nothing in that page is about a trailer. A single match at score 0, presented in the same shape as a real answer, reads as "this is what the server has on the subject" rather than as "nothing matched", and I only knew otherwise because I read the body.

I then read core/contribution/commit-messages whole by documentId. It carries the summary line, the body, Resolves/Related/Releases, Release Targets, breaking changes, changed signatures, deprecations and changelog files — and no mention of Signed-off-by or the DCO at all. So the page that should hold it does not.

I settled it from the checkout instead: the core repository's own AGENTS.md demands a sign-off ("Sign off every commit — git commit -s ... Developer Certificate of Origin"), CONTRIBUTING.md does not mention it, .git/hooks/commit-msg strips ^Signed-off-by: when computing the Change-Id (line 44), and 2 of the last 50 commits on origin/main carry the trailer. That is what let me rank the missing trailer as "worth changing, does not block submission" rather than as a blocker — the opposite of what AGENTS.md alone would have said.

Two things from the same document I did act on and which were right: "A BUGFIX owes none" settled the changelog question outright, including the sentence that demanding an entry of a BUGFIX is itself a review defect; and the Release Targets section is what backs dropping 13.4 from the trailer.

## Query

typo3_rule_lookup query "signed-off-by" targetVersion "15.0"; then typo3_rule_lookup documentId "core/contribution/commit-messages"

## Suggestion

A section in core/contribution/commit-messages on the sign-off, saying what the project actually holds: Gerrit does not require it (the hook only strips it when computing the Change-Id, and the great majority of merged core commits carry none), it is a house rule where a repository states one, and git commit -s / format.signOff is how it is added without disturbing an existing Change-Id. That is the finding a reviewer needs, and it is not derivable from the trailer list as it stands.

Separately: a match at score 0 with no matched heading is not an answer. Returning nothing, or saying "no section matched; the closest document was X", would keep a caller from reading an unrelated page to find out.
