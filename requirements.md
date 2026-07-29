# What this server has to do

A feedback note is a question, and it is deleted once it is answered. The
requirement the note established is not a question and does not go away with it:
it has to keep holding while everything around it changes. This file is where it
survives the note.

Every entry states one thing that must be true, names where the demand came
from, and says what holds it to that — a test where there is one, `not guarded`
where there is none. An entry marked **open** is a requirement that has been
accepted and not yet met; that is the backlog, and it is deliberately the same
list, because a requirement nobody has implemented yet and one that could
silently regress are the same kind of thing.

Rules for keeping it usable: an entry is added when a note is worked off, not
when it arrives — a note nobody has judged yet is a note, not a requirement. An
entry is never deleted because it was implemented; it is deleted only when the
requirement itself is withdrawn, and then the reason goes in
[decisions.md](decisions.md). Assumptions and evidence live there too; this file
holds only what must be true.

## Discovery — which installation is read, and how

- **R-DIS-1** The installation is never derived from `getcwd()` on its own; only
  `bin/typo3-cms-mcp` enables discovery, because an HTTP endpoint has no such
  relationship to its callers.
  *Held by:* `InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance`
- **R-DIS-2** The packages of a Composer installation are read from the vendor
  directory it declares, not from the default.
  *From:* the extension checkout with `config.vendor-dir=.build/vendor` that was
  reported as "no installation found" (2026-07-29).
  *Held by:* `InstanceTest::aProjectThatMovedItsVendorDirectoryIsFoundThereRatherThanMissed`
- **R-DIS-3** The console is looked for at the `config.bin-dir` the installation
  declares, before the Composer defaults.
  *From:* `.build/bin/typo3` existing, working, and never being probed
  (2026-07-29).
  *Held by:* `Typo3CliTest::aConsoleInTheDeclaredBinDirectoryIsFound`
- **R-DIS-4** The extension being worked on is part of the answers about its own
  installation, although Composer lists dependencies rather than the root.
  *From:* 29 registered icons of the root extension reported as non-existent
  (2026-07-29).
  *Held by:* `InstanceTest::theExtensionBeingWorkedOnIsAmongThePackagesAlthoughComposerListsOnlyDependencies`
- **R-DIS-5** A repository whose dependencies were never installed is not
  reported as an installation.
  *Held by:* `InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne`
- **R-DIS-6** Nothing on the caller's machine is started as a side effect of a
  lookup; a stopped DDEV project is reported with the command that would fix it.
  *Held by:* `Typo3CliTest::aDdevProjectThatIsNotRunningIsReportedRatherThanStarted`
- **R-DIS-7** **open** — The installation root and the console command can be
  set explicitly, and the answer says which of the two was used. Every
  layout-specific discovery failure is then a one-line fix for the user instead
  of five tools silently going quiet.
  *From:* a session where two links broke at once — a moved bin-dir and a host
  PHP below the required one — with no lever available (2026-07-29).
- **R-DIS-8** **open** — When discovery fails, the answer names where it started
  and why each candidate was rejected.
  *From:* the same session; "no installation found" was indistinguishable from
  "started in the wrong directory".

## Answers — what a caller may conclude from one

- **R-ANS-1** An empty answer that means "could not ask" is never shaped like one
  that means "does not exist". Every installation-backed tool carries
  `answeredBy`.
  *Held by:* `ToolContractTest`
- **R-ANS-2** **open** — The reason behind `answeredBy: "nothing"` is in the
  structured data, not only in the text, and `typo3_server_scope` carries the
  installation and console diagnostic as data too.
  *From:* a client that renders `structuredContent` and drops the text block; the
  agent twice concluded an extension registered no icons and no labels
  (2026-07-29).
- **R-ANS-3** **open** — What a component answer describes is qualified by the
  revision it was taken from, inside the entry rather than only in a trailing
  block.
  *From:* 15.0 markup handed to a caller supporting 13.4 and 14.3 (2026-07-29).

## Scope — core conventions where they apply, and nowhere else

- **R-SCO-1** **open** — Work outside the core is recognised from structural
  evidence — the kind of installation, the shape of the paths, an area that is
  no known system extension — rather than from wording, and where it stays
  uncertain the answer says so instead of defaulting to core.
  *From:* `outsideCore` flipping only after the caller spelled out "not TYPO3
  core" in prose (2026-07-29).
- **R-SCO-2** **open** — `outsideCore` changes the payload. Core-only commands,
  checklists and checkout discovery are dropped; conventions that transfer stay
  and are marked as such.
  *From:* an answer that reported `outsideCore: true` and then returned four
  `runTests.sh` suites for a repository that has no `Build/Scripts/`
  (2026-07-29).
- **R-SCO-3** **open** — Core-only intents such as patch submission are not
  selected for work that is not core work.
  *From:* third-party extension maintenance recognised as a Gerrit patch
  submission (2026-07-29).
- **R-SCO-4** **open** — The backend CSS conventions are answered as what they
  are — the core backend's — and do not match a frontend theme, nor a PHP file
  whose name merely contains "scss".
  *From:* four confidently inverted hints for a Bootstrap 5 frontend theme
  (2026-07-29).

## Guides — what a returned draft is worth

- **R-GUI-1** **open** — The checks a guide returns describe the draft it
  returns. A trailer the tool adds itself is never reported as missing.
  *From:* `Releases: main` being appended and `missing-releases` warned in the
  same answer (2026-07-29).
- **R-GUI-2** **open** — The TYPO3 commit message rules are available without the
  Gerrit trailers, because the conventions are used well outside the core and
  the trailers are not.
  *From:* the same note.

## Knowledge — what is covered

- **R-KNW-1** **open** — Upgrade wizards and frontend DataProcessors have
  architecture hints; both are core subsystems with none.
  *From:* an extension maintenance task that got generic TCA and Fluid hints and
  nothing for `Classes/Updates/` or `Classes/DataProcessing/` (2026-07-29).
