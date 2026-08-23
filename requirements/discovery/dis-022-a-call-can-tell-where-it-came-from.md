---
id: R-DIS-022
title: 'A call can tell where it came from'
status: held
restsOn: [D-DIS-006]
heldBy:
  - StdioServerTest::itWalksUpToTheInstallationFromInsideIt
  - StdioServerTest::theServerWorksOutWhichInstallationItWasStartedIn
---

# R-DIS-022 — A call can tell where it came from

**A server started inside an installation answers about that installation, from
wherever inside it the session happens to be.**

This is the feature the rest of them depend on. Thirteen of the twenty tools
answer differently once an installation is found, and three are not offered at
all — so a server that works out nothing answers about TYPO3 in general where it
was asked about a checkout, and nothing in the answer says which of the two the
caller is holding.

How it is worked out is not fixed here: today the entrypoint hands in the
working directory and `Instance` walks up from it, which is what
[`R-DIS-001`](dis-001-discovery-belongs-to-the-stdio-entrypoint-alone.md)
restricts to that one caller, and `TYPO3_DEV_COMPANION_ROOT` is how it is stated
outright instead. Nothing in the protocol replaces either — `roots` was the
candidate and is deprecated, which
[`D-DIS-006`](../../decisions/discovery/dis-006-the-installation-stays-worked-out-from-the-directory-the-server-was-started-in.md)
records. Should one arrive, the mechanism may change; this may not.

## From

The line that finds it could be deleted from the entrypoint with all 495 tests
staying green, because every test that covers discovery hands `Instance` a
directory itself and so covers what happens after somebody does. Nothing covered
that somebody does (2026-08-01).
