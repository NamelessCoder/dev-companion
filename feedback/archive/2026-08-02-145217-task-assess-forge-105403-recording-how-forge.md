---
date: 2026-08-02T14:52:17+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Task: assess Forge #105403. Recording how Forge has to be operated, because I established all of ...

## Observation

Task: assess Forge #105403. Recording how Forge has to be operated, because I established all of it by trial and a later session will otherwise repeat the same trial. Everything below I verified this session.

Access. Forge is Redmine at https://forge.typo3.org behind Anubis bot protection, and the protection inverts the usual repair. WebFetch returned HTTP 403. curl carrying a Chrome-like User-Agent returned HTTP 200 with an HTML challenge page titled "Making sure you're not a bot!" — a success status wrapping a non-answer, which is the trap, because a session that only checks the status code will parse garbage. curl with its own default User-Agent returned the real JSON. So: do not set a browser User-Agent, and check the body rather than the status.

Reading one issue: GET /issues/<id>.json?include=journals,relations,changesets

- Top level carries id, subject, project, tracker (Bug/Feature/Task), status with is_closed, priority, author, category, fixed_version, created_on, updated_on, closed_on.
- custom_fields carries the ones that matter for triage: "TYPO3 Version", "PHP Version", "Tags", "Complexity", "Is Regression", "Sprint Focus".
- journals[] with a notes key is the discussion, and it is where the decisive evidence usually sits — for #105403 both maintainer verdicts and the closure reason were only here. The Gerrit bot also posts into it as "Patch set N for branch <branch> ... available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/<number>", which is how you find the patch that belongs to an issue when changesets is empty.
- relations[] gives {issue_id, issue_to_id, relation_type} — following these found #100696, the ticket that introduced the behaviour causing #105403, which the issue text itself never names.
- changesets[] holds merged commits and was empty here, so an empty changesets does not mean unfixed.

Searching: GET /search.json?q=<urlencoded>&issues=1&limit=15 returns results[] with id and a title of the form "Bug #105403 (Closed): f:image and cache busting issue", so status and tracker are readable without a second request per hit. Four differently worded queries found four different sets; a single query is not sufficient.

Writing. Anything that changes an issue needs authentication and I did neither. Worth knowing that a closed issue must be reopened before a Gerrit change is pushed against it, which is a human step.

## Query

Operating forge.typo3.org during "evaluate Forge issue 105403, find similar issues, find patches that already fixed it". Verified calls: curl https://forge.typo3.org/issues/105403.json?include=journals,relations,changesets and https://forge.typo3.org/search.json?q=<urlencoded>&issues=1&limit=15

## Suggestion

Put this recipe where a session assessing an issue will find it — typo3_server_scope under what is deliberately not covered, or the issue-assessment procedure I proposed for typo3_task_guide. The two facts worth the most are the User-Agent inversion with its HTTP 200 challenge page, and that journals[] rather than the issue body carries the decision. If a thin lookup is ever added, returning subject, tracker, status, fixed_version, the TYPO3 Version custom field, relations and the journal notes in one call would replace everything I did by hand.
