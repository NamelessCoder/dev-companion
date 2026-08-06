---
id: R-DIS-021
status: held
---

# R-DIS-021 — The client entry is rewritten when the project outgrows it

**Install and update both write the client entry, and both refuse only an entry
that starts something other than this server.**

What belongs in the entry is a property of the project, not of the run: a
project that required this package after it was first installed, or that gained
a DDEV configuration since, needs a different entry than the one that is there.
Checking it instead of writing it left that project with a message and no
command that would fix it, because `install` refuses an entry it did not just
write. The line is drawn at the server being started, so an entry belonging to
somebody else is still never replaced.

## From

`update` in `E-SITE` reporting
`.mcp.json has a different or missing typo3-dev-companion entry` after the
project required the server, with `install` refusing the same entry
(2026-07-31).

## Held by

- `InstallerTest::updateRewritesTheEntryAProjectHasOutgrown`
- `InstallerTest::updateRefusesToReplaceAnotherCommand`
- `InstallerTest::codexUpdateRewritesTheSectionAndKeepsTheRestOfTheFile`
