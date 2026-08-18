---
date: 2026-08-18T11:33:43+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3-extension-conformance, typo3-core-patch-review, typo3-extension-cleanup
directory: /home/benji/projects/bootstrap_package
---

# no skill covers reviewing an incoming pull request against an extension or sitepackage repository

## Observation

Task: the request arrived verbatim as "review mir bitte den patch https://github.com/benjaminkott/bootstrap_package/pull/1627" — a GitHub pull request, one file, one line changed, against a sitepackage, not the core.

That was the moment a skill would have had to open, and none did. Of what was listed: typo3-core-patch-review is scoped to review.typo3.org and says so in its description ("your own before you push it, or somebody else's patch set"), which a GitHub PR against a third-party extension is not. typo3-extension-conformance is framed as auditing a repository as a whole — "Review or audit a TYPO3 project, sitepackage or extension against its checkout and active installation, and report what is wrong in priority order" — which reads as a sweep of everything, not as a verdict on one incoming diff. typo3-extension-cleanup is about carrying findings through to committed changes. So none of the three descriptions matched, and I worked the review out myself. I would work it out the same way next session.

What the review actually had to establish, because a skill for it would have to cover the same ground: whether the fix is correct; whether it holds on every version the composer constraint claims (^13.4 || ^14.3 here, verified separately for both); whether an idiomatic core API exists that the patch should be using instead of the hand-rolled construct it changes; whether the commit message matches the repository's own convention rather than the core's; and whether CI is green and the branch mergeable. I did all five by hand, including simulating the TCA showitem parsing in `php -r` to prove the failure mode before asserting it.

Note also which files I had open at that moment, since a skill is chosen before any of them are read: only the PR URL. Everything else — the changed file, the core definition it depends on, the neighbouring Overrides file that already used the better API — was found during the review, not before it.

## Query

"review mir bitte den patch https://github.com/benjaminkott/bootstrap_package/pull/1627" — extension repository, single-file diff, GitHub rather than Gerrit. No skill activated.

## Suggestion

A skill scoped to "review an incoming pull request or patch against an extension, sitepackage or project package" — distinct from the whole-repository audit typo3-extension-conformance describes and from the core/Gerrit workflow typo3-core-patch-review owns. Its description has to say "pull request" and "GitHub" in so many words, because that is the vocabulary the request arrives in, and it should name the checks above so the skill is picked for a one-line diff and not only for a large one.
