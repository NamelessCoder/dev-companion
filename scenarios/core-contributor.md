# Core contributor

Someone working in a TYPO3 core checkout, whose change ends up on Gerrit. This
is the audience the knowledge base was written for first, so most of these are
`covered` — and the ones that are not are the more interesting runs.

See [readme.md](readme.md) for how to run one and what the marks mean.

---

## CORE-01 — A bug in DataHandler

**Environment:** `E-CORE` · **Status today:** `covered`

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
  (`R-GUI-1`).
- Claims about which test covers the file, stated rather than looked up.

---

## CORE-02 — A new state on a backend list row

**Environment:** `E-CORE` · **Status today:** `covered` — `R-ANS-3` held

> Records that are scheduled for publication should be marked in the page module
> list — some badge next to the title, with an icon and a tooltip. Build it the
> way the backend does it elsewhere, I do not want a one-off style.

**What the agent needs from this server**

- The canonical markup of the component, its variants and its custom-property
  contract, instead of a class name invented from what the DOM looked like.
- An icon identifier that is actually registered — the shape, not the intent.
- Whether a label for that wording already exists before a new key is invented,
  and the domain the XLF file resolves to.
- The backend CSS rules: where the source lives, what may be written there, and
  what the build does.
- The suites that can fail on a Sass-and-Fluid change, which is not the PHP
  ones.

**What has to come out of it**

- Markup and class names come from the component catalog, and where the catalog
  has no such component the answer says so instead of inventing one.
- The icon identifier is one the installation has registered.
- An existing label is reused where one exists; a new key follows the naming of
  the file it goes into, and the domain reference is the computed one.
- The revision the catalog answers for is stated where the caller works on
  anything but that revision.

**How it fails**

- A plausible but unregistered icon identifier.
- A new label key for a wording that already exists three times.
- Markup from a newer core presented without a word about which revision it is
  from (`R-ANS-3`).

---

## CORE-03 — The patch came back from review

**Environment:** `E-CORE` · **Status today:** `covered`

> Review says my commit message is wrong and the subject is too long. Here it is:
>
> ```
> [BUGFIX] fixed the broken behaviour of the record list when copying pages with translations
>
> This fixes the issue.
> Resolves: 98765
> Change-Id: I8f1c2f9a4b7d6e5c0a3b2d1e9f8c7b6a5d4e3f2a
> ```
>
> Fix it, then amend and push again.

**What the agent needs from this server**

- What each rule is: subject length, keyword, the capitalisation after it, the
  body, which trailers belong there and in which order.
- That an existing `Change-Id` is kept, because this is an amend and not a new
  change.
- The local Gerrit steps for a second patch set.

**What has to come out of it**

- Every defect in the message is named, corrected, and the corrected message is
  ready to commit — the subject shortened, the body written to say what changed
  and why rather than "this fixes the issue", the trailers in the right order.
- The `Change-Id` survives.
- `git commit --amend` and the push refspec are the steps offered, not a new
  commit.

**How it fails**

- The `Change-Id` dropped or regenerated, which opens a second change on Gerrit.
- A message reported as fixed that still violates a rule the server knows.

---

## CORE-04 — Deprecating a public API

**Environment:** `E-CORE` · **Status today:** `covered` — `R-KNW-1` held

> `\TYPO3\CMS\Core\Utility\GeneralUtility::getUrl()` should go away in favour of
> the request factory. Deprecate it properly for the next major, including
> everything that goes with it, and make sure existing installations survive the
> upgrade.

**What the agent needs from this server**

- What a deprecation consists of in the core: the annotation, the runtime
  notice, the changelog entry, the extension scanner matcher.
- Whether the change is breaking, and what that does to the commit message.
- The upgrade path for installations — the wizard, where it lives, what it has
  to satisfy.

**What has to come out of it**

- The deprecation checklist is complete, and the parts that are branch-specific
  are given as a procedure to run in the checkout rather than a list from one
  revision.
- The commit message carries the deprecation shape and the right release line.
- Where the answer for `Classes/Updates/` is thin, the agent says so instead of
  inventing the conventions of an upgrade wizard.

**How it fails**

- Generic PHP hints for the wizard part, with nothing about upgrade wizards
  themselves (`R-KNW-1` — record what the task needed beyond that entry).
- A changelog file name or a version number quoted from the pinned revision as
  though it held on every branch.

---

## CORE-05 — A test fails and nobody knows on what

**Environment:** `E-CORE` · **Status today:** `covered`

> A functional test fails for me locally but passes in CI. It is in
> `typo3/sysext/core/Tests/Functional/DataHandling/`. Get it running the way CI
> runs it and find out what is different.

**What the agent needs from this server**

- The suite behind that directory and the targeted invocation.
- The options that make a local run match CI: the PHP version, the database
  vendor and version, the flags that select them.
- The other checks that exist next to the tests, so "runs green" means what CI
  means by it.

**What has to come out of it**

- Runnable commands, targeted at the failing test, with the option that pins the
  database vendor and the one that pins the PHP version.
- The difference between the unit, functional and acceptance suites is stated
  where it matters for the diagnosis.

**How it fails**

- A `vendor/bin/phpunit` invocation instead of the core's own script.
- Suites recommended by name with no command, or commands with invented flags.

---

## CORE-06 — The fix belongs on a release branch

**Environment:** `E-CORE`, checked out on a release branch · **Status today:**
`gap` — `R-AUD-4`

> This bug only exists on 13.4, on main the code was rewritten and the problem is
> gone. Prepare the patch for 13.4 and tell me what is different about pushing it
> there.

**What the agent needs from this server**

- That a patch which cannot go to `main` is pushed to the release branch
  directly, and what that changes about the release trailer and the refspec.
- Whether the conventions and any catalog answer it hands over hold on 13.4 at
  all.

**What has to come out of it**

- The release trailer names the branch the patch targets, and the push goes to
  that branch's refspec.
- Where an answer was taken from a pinned revision, the answer says so, and
  where the server cannot know whether something holds on 13.4, it says that
  rather than implying it does.

**How it fails**

- Anything stated for "TYPO3" that is really only true on the revision the
  catalogs were taken from, with no word about the branch (`R-AUD-4`).
- The normal main-first rule applied to a bug that does not exist on main.
