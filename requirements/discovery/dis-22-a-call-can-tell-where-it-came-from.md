---
id: R-DIS-22
status: held
---

# R-DIS-22 — A call can tell where it came from

**A server started inside an installation answers about that installation, from
wherever inside it the session happens to be.**

This is the feature the rest of them depend on. Thirteen of the twenty tools
answer differently once an installation is found, and three are not offered at
all — so a server that works out nothing answers about TYPO3 in general where it
was asked about a checkout, and nothing in the answer says which of the two the
caller is holding.

How it is worked out is not fixed here: today the entrypoint hands in the
working directory and `Instance` walks up from it, which is what
[`R-DIS-1`](dis-1-discovery-belongs-to-the-stdio-entrypoint-alone.md) restricts
to that one caller. Should the protocol offer something better than a working
directory, the mechanism may change; this may not.

**From:** the line that finds it could be deleted from the entrypoint with all
495 tests staying green, because every test that covers discovery hands
`Instance` a directory itself and so covers what happens after somebody does.
Nothing covered that somebody does (2026-08-01).

**Held by:** `StdioServerTest::theServerWorksOutWhichInstallationItWasStartedIn`,
`StdioServerTest::itWalksUpToTheInstallationFromInsideIt`
