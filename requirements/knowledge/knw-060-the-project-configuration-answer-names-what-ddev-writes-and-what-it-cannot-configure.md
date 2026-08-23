---
id: R-KNW-060
title: 'The project configuration answer names what DDEV writes and what it cannot configure'
status: held
restsOn: [D-KNW-049]
heldBy:
  - HintsTest::theDdevSettingsAnswerNamesEverySectionItGenerates
---

# R-KNW-060 — The project configuration answer names what DDEV writes and what it cannot configure

**The project-configuration answer names every section DDEV's settings
management generates into `config/system/additional.php`, and says that its
generator configures its own database container and nothing else.**

Naming the database section alone makes the two ways out of that file look
interchangeable. They are not: taking the file over means supplying the image
processing, the mail transport and the `SYS` block as well, and the trusted
hosts pattern in that block is what an installation stops answering without. An
installation whose connection comes from somewhere else — SQLite, a database
container that was omitted — is the case the generator has no variant for, so
the answer says that leaving the file generated is not among the ways out there,
and which of the two that are keeps the sections DDEV had right.

## From

`feedback/2026-08-03-162858` (2026-08-03), a session bringing a TYPO3 14.3.5
instance up under DDEV v1.25.1 for an extension, on SQLite with
`omit_containers: [db]`. It read the statement naming the database settings,
disabled settings management, wrote that half back, and got
`UnexpectedValueException` 1396795884 for the trusted hosts pattern it had not
been told about.
