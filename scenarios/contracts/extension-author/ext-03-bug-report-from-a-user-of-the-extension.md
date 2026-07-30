# EXT-03 — A bug report from a user of the extension

**Environment:** `E-EXT` · **Contract:** `held` — `R-AUD-3`, `R-GUI-2`

> Somebody reported that our list plugin crashes when the storage page is empty.
> Reproduce it, fix it, and commit it — we tag a patch release afterwards.

**What the agent needs from this server**

- The conventions for the code being touched, without the core apparatus.
- A commit message that is right for this repository: the subject and body rules
  transfer, the Gerrit and Forge trailers do not.
- What the test for the fix should look like, and how this repository runs it.

**What has to come out of it**

- The commit message follows the TYPO3 subject and body conventions and carries
  no `Releases:`, no Forge issue trailer, and no `Change-Id`.
- The fix is accompanied by a test, and how to run it comes from the
  repository's own setup rather than from the core script.

**How it fails**

- A message with `Releases: main` in a repository that has no core releases
  (`R-AUD-3`).
- The commit rules only being available in their Gerrit-shaped form, so the
  agent either takes the trailers along or abandons the conventions entirely
  (`R-GUI-2`).
