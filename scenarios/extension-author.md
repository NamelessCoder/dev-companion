# Extension author

Someone who maintains an extension: their own repository, released for several
TYPO3 versions, and usually developed inside a site installation rather than
next to one. This audience is where the boundary between "core convention" and
"convention that transfers" decides whether an answer is right or merely
plausible.

See [readme.md](readme.md) for how to run one and what the marks mean.

---

## EXT-01 — Making the extension run on the next major

**Environment:** `E-EXT` · **Status today:** `partial` — `R-AUD-1` and `R-SCO-2`
held; what an extension author gets is still the core's conventions, filtered

> Our extension supports TYPO3 12 and 13. The next major is out and I want to add
> support for it without dropping 13. Go through the extension, find what breaks,
> and fix it.

**What the agent needs from this server**

- How to find out what actually changed: the changelog directory, the extension
  scanner, the deprecation annotations — a procedure that works on any branch,
  not a list from one.
- Which conventions the new code has to follow where it is rewritten: DI, event
  listeners instead of hooks, the TCA and schema shape, the Fluid changes.
- What supporting two majors at once does to `composer.json`, `ext_emconf.php`
  and the code that has to branch.

**What has to come out of it**

- The conventions that transfer come over and are marked as conventions rather
  than as core rules; nothing that only exists in the core repository is handed
  to a repository that does not have it.
- Where the server has no upgrade knowledge, the answer says so and points at
  the documentation instead of producing a checklist that reads authoritative.
- The version-specific part is a procedure to run against the checkout.

**How it fails**

- `Build/Scripts/runTests.sh` offered to a repository that has no `Build/`
  (`R-SCO-2`).
- A core patch checklist — Gerrit, Forge, `Releases:` — handed to an extension
  release (`R-AUD-1`).
- A list of "what changed in the new major" invented from memory, presented as
  fact.

---

## EXT-02 — A new extension from nothing

**Environment:** `E-SITE`, in a project where the new extension will live
under `packages/` · **Status today:** `covered` — `R-SCO-1`, `R-SCO-2`

> I need a new extension `acme_events` in this project: a record type for events
> with dates and a location, editable in the backend, listed in the frontend by a
> plugin. Set it up from scratch.

**What the agent needs from this server**

- The file layout of an extension and what each required file is for.
- TCA conventions for a new table, and how the schema is declared.
- Services and dependency injection, so the plugin is not built with
  `GeneralUtility::makeInstance` everywhere.
- Where labels go, what the file is called, and what domain it resolves to.
- How an icon for the record type is registered, and which identifier to use.
- Fluid conventions for the plugin template, and the namespaces available
  without declaring them.

**What has to come out of it**

- The answer is right for an extension, not for a system extension: paths under
  the package, no core-only steps, no checkout discovery instructions for a
  checkout that is not there.
- The label domain for a file that does not exist yet is computed, not guessed.
- The registered icon identifier is verified against the installation.

**How it fails**

- The task recognised as core work because it is about TCA and Fluid, and
  answered with core paths (`R-SCO-1`).
- The extension's own not-yet-installed state confusing the installation-backed
  lookups into reporting "nothing registered".

---

## EXT-03 — A bug report from a user of the extension

**Environment:** `E-EXT` · **Status today:** `covered` — `R-AUD-3`, `R-GUI-2`

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

---

## EXT-04 — A backend module in the extension

**Environment:** `E-SITE` with the extension under `packages/` ·
**Status today:** `partial`

> Our editors need a backend module that lists all events with their state and
> lets them trigger a re-import. It should look like a normal TYPO3 backend
> module, not like something bolted on.

**What the agent needs from this server**

- How a backend module is registered and what the registration file contains.
- Which modules are already registered in this installation, so the new one gets
  a place in the tree rather than a collision.
- The backend UI components for the list, the buttons and the state markers, with
  their real markup.
- Registered icons for the module and its actions.
- Existing labels for the recurring wordings — save, delete, refresh — before new
  ones are invented.

**What has to come out of it**

- The module registration is the current shape and the paths are the extension's
  own.
- Component markup comes from the catalog, with its revision stated, and the
  agent verifies against the installation's TYPO3 version where they differ.
- The backend look comes from core classes, not from custom CSS reimplementing
  them.

**How it fails**

- Backend CSS source paths in the core handed over as the place to write the
  module's styles.
- Component markup from a newer core silently used on an older installation.

---

## EXT-05 — Tests for the extension

**Environment:** `E-EXT` · **Status today:** `boundary`, by an explicit decision
in [decisions.md](../decisions.md)

> Set up tests for this extension — unit and functional — and wire them into our
> GitHub Actions so every pull request runs them against all supported TYPO3
> versions.

**What the agent needs from this server**

- Testing conventions that transfer: what belongs in a unit test, what needs a
  functional one, how a test is named and where it sits.
- An honest statement that how an extension runs its tests is not knowledge this
  server carries, plus where it is documented.

**What has to come out of it**

- No `runTests.sh` command, because the script lives in the core repository.
- The conventions that do transfer come over and are marked as conventions.
- The pointer to the documentation is given instead of an invented setup.

**How it fails**

- Four core suites returned for a repository that cannot run any of them.
- A confident, invented testing setup — the failure the decline exists to
  prevent, one level further along.

---

## EXT-06 — Wording, labels and translations

**Environment:** `E-SITE` with the extension under `packages/` ·
**Status today:** `covered`

> The new module needs its texts. Use the wording TYPO3 already uses where there
> is one, and put the rest in our own language file so the translation server can
> pick it up. Some of the core labels are wrong for our context, I want those
> overridden.

**What the agent needs from this server**

- Which labels already exist for a given wording, across everything this
  installation has active, and what their domain reference is.
- The domain the extension's own XLF file will resolve to, before the file
  exists.
- The conventions for language files: naming, where they sit, how a key is
  formed.
- How an existing label is overridden for this installation.

**What has to come out of it**

- Existing labels are found by the words of the wording, in any order, and are
  reused with their real domain reference.
- New keys go into a file whose domain the answer states.
- Where nothing matches, the answer says how far each single word reached,
  rather than an empty result that reads like "no labels exist".

**How it fails**

- A multi-word query coming back empty and being read as "no such label"
  (`R-ANS-4`).
- A console that could not be reached reported as an installation with no labels
  (`R-ANS-1`, `R-ANS-5`).
