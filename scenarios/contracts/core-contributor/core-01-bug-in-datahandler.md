# CORE-01 — A bug in DataHandler

**Environment:** `E-CORE` · **Contract:** `held`
**Held by:** `CommitMessageTest::theDraftCarriesKeywordIssueAndReleases`,
`CommitMessageTest::aTrailerTheDraftCarriesIsNotAlsoReportedAsMissing`,
`HintsTest::aSuiteIsFoundByItsName`

> Copying a page with `l10n_mode=exclude` fields loses those field values on the
> copy, but only when the page has translations. I want to fix that in
> DataHandler. Have a look, tell me how you would approach it, and take me
> through it until it is ready to push.

**What the agent needs from this server**

- The conventions that apply to `typo3/sysext/core/Classes/DataHandling/` —
  persistence, hooks and events, what a patch there is judged by.
- Which test suite can actually fail on that path, and the targeted invocation
  rather than the whole functional run.
- The commit message, including the keyword, the issue trailer and the release
  line, wrapped correctly.
- The Gerrit side: what has to be in place before the first push, and what
  happens on a second patch set.

**What has to come out of it**

- The checklist it works off is a core checklist, and it says which parts the
  agent has to establish in the checkout itself rather than pretending to know
  them — the changed files, the covering test, the branch.
- The recommended test command is targeted at the DataHandler tests, not four
  full suites.
- The commit message is a valid TYPO3 core message and the checks reported on
  it describe the draft that was returned.
- Backporting is described as the merging core team member's job for a patch
  that targets `main`.

**How it fails**

- A generic "run the tests" without a runnable command.
- A commit message whose own check warns about a trailer the draft contains
  (`R-GUI-001`).
- Claims about which test covers the file, stated rather than looked up.
