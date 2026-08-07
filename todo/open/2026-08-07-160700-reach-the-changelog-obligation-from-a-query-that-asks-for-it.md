# Reach the changelog obligation from a query that asks for it

**Serves:** feedback/2026-08-07-132446-no-rule-section-says-what-each-change-type-owes.md, feedback/2026-08-07-065419-commit-message-guide-caught-a-too-long-subject.md
**Priority:** high

The obligation is written down twice and reached by nobody. `## Changelog Files`
is a section of `knowledge/documents/core/contribution/commit-messages.md` and
the `documentation-changelog` hint carries the whole matrix — which of the four
types a change owes, the filename, the sections, the index tags. Re-run here:
`typo3_rule_lookup` with "changelog entry branches that take a patch today
releases trailer breaking change" returns `Release Targets` alone, and with
"bugfix changelog entry obligation and review readiness" returns
`Release Targets` and `TYPO3 Contribution Sources`. Both carry
`alsoInHints: documentation-changelog`, which one session saw twice and passed
over — it says why: a query with "changelog" in it answered by a section about
release branches reads as this being what the server has. Fix the ranking so a
query naming the obligation reaches the section that states it, and decide
whether a rule answer inlines the hint body when the query names its subject
rather than printing the id in its least prominent field. Three sessions in one
day failed to get this answer by three different routes and all three settled it
from their own knowledge, so the third route is in scope too: have
`typo3_commit_message_guide`'s `breaking-not-assessed` name what to look for in
the diff — a removed or narrowed public or protected member — instead of routing
to a second call that nobody made. `D-ANS-061` is the delivery judgement behind
this.
