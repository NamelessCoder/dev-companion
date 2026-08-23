---
id: R-KNW-040
title: 'An environment variable answer names what the core reads itself'
status: held
heldBy:
  - HintsTest::settingTheEncryptionKeyFromAnExtensionIsBoundToWhereItBreaks
  - HintsTest::whichEnvironmentVariablesTheCoreReadsItselfIsAnswered
---

# R-KNW-040 — An environment variable answer names what the core reads itself

**A question about configuration from the environment is answered with the
variables TYPO3 reads on its own, and with what a project has to wire up
itself.**

The three the bootstrap reads are named, as are the prefixed spellings a web
server forwards. `%env()%` is answered as a placeholder of the YAML loader, with
what it therefore reaches and what it does not. Everything else — the encryption
key and the database credentials included — is answered as the project's own
`getenv()`, and the install-time commands that do accept a fixed set of
variables are separated from what a running installation reads, because that is
where the belief that the core reads them comes from.

Where the version boundary of an assignment moves, the answer carries it: an
encryption key set from an extension is stated against the majors it still boots
on.

## From

A session that could not verify whether the core reads `TYPO3_ENCRYPTION_KEY` or
`TYPO3_DB_HOST` and answered from its own knowledge instead — correctly, but
unverifiably, because the corpus stated the project half of the boundary without
the core half (2026-07-31).
