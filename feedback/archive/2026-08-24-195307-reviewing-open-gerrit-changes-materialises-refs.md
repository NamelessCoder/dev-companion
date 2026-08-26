---
date: 2026-08-24T19:53:07+00:00
category: idea
status: closed
closed: 2026-08-26
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-checkout
directory: /home/benji/projects/typo3-cms
---

# Reviewing open Gerrit changes materialises refs in the user's working checkout

## Observation

Task: the user asked to work on "almost ready small open reviews" in the TYPO3 core monorepo and finish them. To triage them I fetched eight open changes from review.typo3.org into the user's working checkout and tagged each one (gr/<number>), then later a ninth for the actual review.

That was the wrong default and the user stopped the session over it, twice: first "du hast viele meiner eigenen patches gezogen", then "keine neuen holen".

Two things made it costly, and neither is visible from the review workflow itself:

1. The checkout already carried 23 local branches of the user's own work. Refs pulled in unasked are hard to tell apart from that, and they inflate the very inventory a later cleanup has to reason about.

2. Some of those existing branches hold commits that exist nowhere on Gerrit. When the user then asked to clean up branches, "delete the ones that are mine" was not a safe instruction to execute: three branches had a Change-Id that resolves to nothing on the server (including one prefixed [SEC]), and five more pointed at commits that are not any patch set of their change. Establishing that took a per-branch query of /changes/?q=<Change-Id>&o=ALL_REVISIONS and a membership test of the local tip SHA against the revisions map — which is the check that separates a recoverable delete from an unrecoverable one, and nothing in the skills names it.

The review itself never needed the refs. Reading the diff via /changes/<n>/revisions/current/{commit,files} plus one targeted fetch for the single change actually under review would have covered it.

## Query

Session task: "we want to work on almost ready small open reviews and finish them", TYPO3 core checkout on main (15.0.0-dev). Then: "räume bitte die branches mit commit von mir oder auf denen ich gevotet habe ab, abseits der release branches". Tools involved: typo3-core-patch-review skill, plus direct Gerrit REST calls to review.typo3.org (/changes/?q=..., /changes/<n>/revisions/...) and git fetch of refs/changes/*.

## Suggestion

Two additions.

In typo3-core-patch-review: state that reviewing a change does not require materialising it in the user's checkout, and that the Gerrit REST API answers the diff, the file list and the commit message without a fetch. Where a checkout is genuinely needed — running a suite against the patch — it is one change, fetched deliberately, and the ref is named in the report so the user can remove it. Bulk-fetching a shortlist to triage it is what to avoid: the shortlist is answerable from the API alone.

In typo3-core-patch-checkout: add the recoverability test as a named step for any branch or ref cleanup. For each local ref, read the Change-Id from its tip commit, query /changes/?q=<Change-Id>&o=ALL_REVISIONS, and test whether the local tip SHA is a key of the revisions map. Three outcomes, and they carry different risk:

- Change-Id resolves to nothing: the commit exists only locally, deletion is unrecoverable.
- Change resolves but the tip SHA is not among its revisions: local work ahead of or divergent from every pushed patch set, deletion is unrecoverable.
- Tip SHA is a revision: recoverable via refs/changes/<last two of number>/<number>/<patch set>.

Only the third is safe to delete without asking, and a cleanup should emit the restore refspecs for what it removes before removing it.
