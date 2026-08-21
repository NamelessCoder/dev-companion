---
date: 2026-08-19T09:45:28+00:00
category: missing-knowledge
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_extension_describe
directory: /home/benji/projects/blog
---

# A release audit turns on what is already published in the TER, and nothing here can see it

## Observation

Task: full audit of the blog extension before its official v14 release — the user's framing was "wir wollen sie für v14 jetzt offiziell releasen da muss alles passen".

The hint typo3_hint_lookup id=extension-ter-release arrived automatically in the typo3_task_guide brief and is good: it states that Tailor refuses to package unless ext_emconf.php's version equals the version being released, that the artefact contents are decided by Tailor's own ExcludeFromPackaging.php and never by .gitattributes, and that tailor-version-artefact and .env belong in .gitignore. All three became findings.

What it cannot say — and nothing else here can either — is what is already in the TER. That turned out to decide a release-blocking finding.

The repository looks like an unreleased 14.0.0: ext_emconf.php says 'version' => '14.0.0' and composer.json carries branch-alias 14.0.x-dev. I reported that the version must be bumped. The maintainer answered "14.0.0 auch für das erste ter release" — reading it as not yet published. Neither of us could settle that from the checkout, because the checkout says the same thing in both worlds.

I settled it outside the server, two ways:
- gh run list --workflow=publish.yml -> the publish job ran successfully on 2025-08-25 for tag 14.0.0.
- curl https://extensions.typo3.org/api/v1/extension/blog/versions -> 31 versions, newest 14.0.0.

So 14.0.0 is published, the TER will not take it again, and a tag 14.0.0 would fail the release. There is a second layer: the published 14.0.0 declares 'typo3' => '13.4.15-13.4.99' in ext_emconf, so it is installable only on TYPO3 13.4 — the official v14 release the task was about has never existed, while the repository's README compatibility table already claims v14 for 14.0.x. That contradiction is invisible from the checkout alone and is exactly the kind of thing a pre-release audit should catch.

I am aware this may read as outside the server's boundary — it is a network fact about a registry, not TYPO3 knowledge. Filing it anyway, because the guidance for this debrief says a wish dropped as out of scope is the one the maintainers never hear, and because the server already carries a hint about publishing to the TER. A hint about how publishing works, with no way to see what has been published, is the half that leaves the audit guessing.

## Query

typo3_hint_lookup id=extension-ter-release (arrived via typo3_task_guide, changeType=audit, targetVersion=14, task="Full audit of the blog extension before an official TYPO3 v14 release"). The unanswered question: "which versions of this extension are already published in the TER, and what does each declare as its supported TYPO3 range?"

## Suggestion

Two options, in order of how much I would want them.

First, a field on typo3_extension_describe for a package that declares a TER extension key: the versions published there and what each declares, read from the TER's public REST API (extensions.typo3.org/api/v1/extension/<key>/versions). Then the answer to "is this version free" comes with the answer to "what does this extension register", which is where the question actually arises. The API is public and needs no token; typo3_changelog_lookup already reaches docs.typo3.org, so a network source is not new here.

Second, and cheaper: extend the extension-ter-release hint with the consequence rather than only the rule. It says Tailor refuses a version mismatch. It should also say that the TER refuses a version that already exists, that the repository's ext_emconf version after a release still names the released version and therefore looks unreleased, and that the publish workflow's run history is what settles which tags actually reached the TER. That last sentence alone would have routed me correctly in one step instead of two guesses and a contradiction with the maintainer.
