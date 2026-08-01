---
date: 2026-07-29T10:06:40+00:00
category: idea
status: closed
closed: 2026-07-29
commit: d240ea2
subject: "[FEATURE] Offer a project only the half of the server it can use"
tool: typo3_server_scope
---

# A project could be offered the half of this server that fits it

## Observation

Half addressed: `typo3_server_scope` now states per covered topic what its
answers are worth outside the core — `core-only` for the contribution process
and the scripts of that repository, `transferable` for the conventions,
`installation` for what is read from the installation itself. A caller no longer
has to work that out per tool.

What remains is the packaging question this note actually opened with. The five
installation-backed tools plus the transferable architecture domains are a
usable site-developer tool; the Gerrit, changelog and runTests.sh material is
not, and it is still presented in the same list. Marking it is not the same as
being able to leave it out.

## Query

overall evaluation from the perspective of a site developer maintaining a TYPO3 v14 project

## Suggestion

Consider a profile — an environment variable, or a client-side selection — that
presents the installation-backed tools and the transferable domains without the
core contribution surface. That is a product decision rather than a defect: it
changes which tools a client sees, and the tool list is a contract.
