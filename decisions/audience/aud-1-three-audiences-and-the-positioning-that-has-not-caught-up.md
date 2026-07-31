---
id: D-AUD-1
date: 2026-07-29
status: standing
---

# D-AUD-1 — Three audiences, and the positioning that has not caught up

**The outward description stays core-first until there is non-core knowledge to
describe, and the requirement is the record that it is meant to change.**

The server is to serve core contributors, extension authors and site developers
(R-AUD-1 to R-AUD-4). What it currently *contains* is core knowledge, and what
it says about itself matches that: `knowledge/server-scope.json` opens with "a
curated knowledge base for contributing to the TYPO3 core".

- **Decided:** the outward description stays core-first until there is
  non-core knowledge to describe. A promise is made when it can be kept; the
  requirement is the record that it is meant to be.
- **Assumed:** the boolean `outsideCore` cannot carry this. An audience has at
  least three values and an honest fourth — unknown — and the flag was written
  when "not core" was the only distinction that existed.
- **Assumed:** the audience is not readable from the checkout alone, because
  extension development happens inside site installations. Any detection that
  keys on the installation kind alone will be wrong for that case, which is a
  common one rather than an edge.
- **Wrong if:** a signal turns out to identify the audience reliably on its own
  — the presence of `typo3/sysext/` in the touched paths comes closest — which
  would make the combining logic unnecessary complexity.
