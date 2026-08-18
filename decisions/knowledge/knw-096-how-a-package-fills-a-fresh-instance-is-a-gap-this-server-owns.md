---
id: D-KNW-096
date: 2026-08-18
status: open
---

# D-KNW-096 — How a package fills a fresh instance is a gap this server owns

**How a package fills a fresh instance is a question this server answers, and a
package that declares no way at all is one of the answers.**

The corpus models one mechanism — the `Initialisation/` data file and the import
the package setup fires — and states it as the shape of the answer rather than
as one of several. An extension that fills an instance some other way leaves the
step that asks with nothing to say.

## Evidence

- **The re-run reaches no intent.** `typo3_task_guide` was called through
  `bin/typo3-dev-companion` from this worktree on 2026-08-18 with the feedback's
  own question, "how does this extension get content into a fresh instance after
  the installation booted". The answer is `Change type: unknown` with no
  `Recognized as:` line at all, and the hints it carries are about
  `ext_tables.php` at boot. The same guide asked with boot wording answers
  `Recognized as: Bringing an installation up and running it` and names
  `typo3-development-installation`, so the routing works and the question does
  not reach it.
- **The probe reaches the producing side alone.** `bin/cli hints:probe` for "how
  does an extension seed content into a fresh instance" and for "extension setup
  wizard backend module seeds blog root page" reaches `datahandler-seeding` and,
  on the second, `backend-modules`, `browser-tests` and `content-elements`.
  `datahandler-seeding` is writing records with a script, which is what a
  distribution is built from rather than how a package delivers one.
- **Nothing here names another mechanism.** A search of `knowledge/` and
  `skills/` for `wizard` matches `content-elements.json`, `upgrade.json`,
  `task-intents.json` and `server-scope.json`, and none of those is about
  filling an instance.
- **Both sides of the corpus model the one mechanism.**
  `sitepackage-initial-content` is `Initialisation/data.t3d` or
  `Initialisation/data.xml`, the site configuration beside it, and the package
  initialization event. Step 4 of `typo3-development-installation` routes to
  that hint and the two beside it. The seeding item of the
  `installation-operations` checklist is `typo3 extension:setup` and the
  inertness of `--distribution`. `D-SKL-050` sets the boundary between producing
  and consuming, and an ImpExp artifact is what both of its sides carry.
- **Two of the five calls the feedback counted are one call today.**
  `typo3_extension_describe` lists `Initialisation/data.t3d` and
  `Initialisation/data.xml` among the registration files —
  `Extension::ROOT_FILES` — and the `console.command` tag among the service
  tags, beside the backend modules, the site sets and where the manual is. Those
  are the two greps that returned nothing, and the reporting session says it
  should have made the call.
- **What that answer cannot carry is which module is the wizard.**
  `backendModules` are the identifiers in `Configuration/Backend/Modules.php`,
  and nothing that file declares says a module fills an instance.
- **The corpus is one session on this subject.** `bin/cli feedback:list` on
  2026-08-18 reports 27 open in two checkouts, 24 of them from
  `/home/benji/projects/blog` between 07:14 and 08:12, and this is the only one
  of the 27 that asks how a package fills an instance.

## Decided

- **Step 1a, and taken on.** What is missing is the knowledge that the question
  has more than one answer: which ways a package may declare that it fills a
  fresh instance, and what holds when it declares none.
- **Where the boundary runs.** Inside: the ways a package declares it in files —
  the `Initialisation/` data file, a console command, a site set a site has to
  depend on — and the statement that a package declaring none of them fills the
  instance by a procedure only its own manual writes down. Outside: which
  backend module is the wizard, which is a guess dressed as a reading.
- **The absence is the answer, not a miss.** A caller told that nothing
  declarable is there has been told where to read next, and
  `typo3_extension_describe` already reports where the manual is. That is what
  the reporting session established by hand out of `.rst` files.
- **One card for both halves of the feedback.** The closing item it asks for —
  establish how this package expects a fresh instance to be filled before
  reporting the boot done — is that knowledge delivered at the step that needs
  it, and its wording depends on what the reading finds.
- **Priority `normal`, set by the counted cost rather than by arrival.** One
  session on one extension is not what raises it; five calls with two of them
  answering nothing is the measure `D-FBK-027` names, and the gap is the
  corpus's model rather than this extension's peculiarity.
- **The feedback is taken on whole rather than trimmed.** Neither half is
  answered by what is here today.

## Assumed

- **That a package filling an instance by a documented backend procedure is
  worth a statement.** One extension has been seen doing it, and nothing here
  counts how many others do.
- **That the pointer at the manual is the lever.** The reporting session needed
  three facts and only the pointer is derivable; whether a caller sent to an
  extension's own `Documentation/` gets to them in one read was not measured.
- **That core declares no further way.** The three above are what the corpus and
  the extension answer already name, and the reading is what settles the list.

## Wrong if

- The reading finds core lets a package declare more ways than those three. Then
  the answer is a list rather than a list with an absence at the end of it, and
  this entry understated what is missing.
- The reading finds that outside this one extension nothing fills an instance by
  a documented backend procedure. Then the statement is about one package, and a
  hint carrying it is one nobody reaches.
- A boot session that is told to call `typo3_extension_describe` at the seeding
  step establishes the same three facts without any new knowledge entry. Then
  this was step 2, the answer had arrived all along, and one line on step 4 was
  the whole of it.
- A caller given the absence answer greps the extension's manual tree by hand
  anyway. Then what was missing is the structure of that manual rather than the
  mechanism, and no statement about mechanisms closes it.

## Since then

The reading against `.checkouts/` found more ways than the three this entry
named, so its first **Wrong if** holds in the narrow sense: the statement stands
and the list was understated. `fresh-instance-seeding` is what came out of it.
Core reads four files out of a package at `typo3 extension:setup` rather than
one — the `Initialisation/` data file, `Initialisation/Site/`,
`Initialisation/Files/` and `ext_tables_static+adt.sql` — and the last of those
is keyed by its own hash in the registry on every covered major, so it is the
one shipped file that arrives again after an edit. From 13 a package may also
listen on the package initialization event and fill the instance with no file
convention at all. The console command and the site set are the two the caller
sees and core never acts on, and the site set turned out to fill nothing by
itself: it reaches an installation only where a site's `config.yaml` names it
under `dependencies`.

Two things the reading turned up were left alone, because neither is what the
card asked for. `typo3_extension_describe` reports the two data files and not
`ext_tables_static+adt.sql`, `Initialisation/Site/` or `Initialisation/Files/`,
so three of the four mechanisms are checked against the package's own files
rather than out of that answer. And `typo3_task_guide` still reaches no intent
for the standalone question — the knowledge is delivered at step 4 of
`typo3-development-installation` and in the `installation-operations` checklist,
which is the caller who is already in the boot workflow.
