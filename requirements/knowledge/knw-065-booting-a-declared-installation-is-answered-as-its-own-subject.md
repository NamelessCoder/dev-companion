---
id: R-KNW-065
status: held
restsOn: [D-KNW-054]
---

# R-KNW-065 — Booting a declared installation is answered as its own subject

**A task that boots the installation a repository already declares is answered
with what a clone still owes it.**

That is five things: the schema an imported database is behind on, the caches
the dump brought with it, the backend user nobody has the password for, the host
the site configuration names, and where the files were expected. Every one of
them fails quietly. The schema is behind and nothing says so, the imported cache
serves another installation's pages, the create-user step waits on stdin inside
a hook, the site answers page-not-found at its own root, and the file records
have no bytes behind them. A brief that answers this task with the setup command
answers the other half of the subject — the installation that has to be created
— and a session booting a clone reads it as the whole of it.

## From

A session booting a TYPO3 Composer project from a fresh clone, whose brief came
back with `datahandler-basics`, `fal-basics` and `public-assets`
(`feedback/2026-08-03-154508`). The change type half of that report landed as
`operations` and the `installation-operations` intent on 2026-08-03 and did not
move the hints; the four knowledge cards `D-SKL-012` put first landed the
install rather than the boot, and the same query still reached the same four PHP
hints on 2026-08-03.

## Held by

- `HintsTest::bootingADeclaredInstallationIsAnsweredBeforeThePhpFallback`
