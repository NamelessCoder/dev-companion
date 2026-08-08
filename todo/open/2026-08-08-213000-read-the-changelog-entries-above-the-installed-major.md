# Read the changelog entries above the installed major

**Serves:** R-DOC-001
**Priority:** normal

The installation ships every changelog down to 7.0 and nothing above its own
major, so a session upgrading to a version it has not installed cannot read the
entries it is upgrading to. That is the gap
`skills/typo3-extension-upgrade/SKILL.md` names in its own words.

What the host already answers, measured 2026-08-08 and recorded in `D-ANS-065`:
`Changelog-<major>` in the inventory of `cms-core` names 3800 entries over 55
version directories up to 15.0, each with its file name and its stated title —
the two fields `Changelog::entries()` searches on disk. And
`_sources/<page>.rst.txt` gives back the RST of a matched entry byte for byte,
`.. index::` included, so `Changelog::read()`, `identifiers()` and `removal()`
run on it unchanged.

So the step is to put the two behind `Changelog` where the installation stops,
and to say in the answer which side an entry came from — a bundled changelog is
what the installation ships and a fetched one is what the host publishes today,
and a caller upgrading needs to know which it is holding.

**Run:** `bin/cli todo:next`

## What is not established

- What the reads cost at a major the host has not published, and what the answer
  says then. `objects.inv` answers 200 for the four covered versions
  (`D-ANS-065`); nothing has been measured above them.
- Whether the entries a not-yet-released major carries are stable enough to be
  handed on without saying they are a moving target.
