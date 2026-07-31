---
id: R-AUD-3
status: held
---

# R-AUD-3 — Commit conventions differ by audience

**Commit conventions differ by audience.**

The subject line and body rules transfer; `Releases:`, Forge issue numbers, and
the Gerrit `Change-Id` are core rules and belong to core work only. A site or
extension repository has its own workflow, and the guide must be usable there
without producing trailers that mean nothing in it.

**Held by:**
`CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded`,
`CommitMessageTest::outsideTheCoreTheSubjectAndBodyRulesStillHold`,
`CommitMessageTest::theSecurityKeywordIsTheRepositoryOwnOutsideTheCore`
