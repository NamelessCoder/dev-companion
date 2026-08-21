---
name: typo3-extension-asset-build
description: 'The asset build of a TYPO3 extension, sitepackage or project package: npm and package.json dependency updates, Dependabot pull requests, a webpack, vite, Grunt or Sass pipeline, the built CSS and JavaScript committed under Resources/Public, the import map that loads it into the backend, and the core backend classes and icons that output borrows. It stops where a bundler or a JavaScript library asks for a migration of its own.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
metadata:
  typo3-dev-companion-status: draft
---

# TYPO3 Extension Asset Build

A package's own build produces the CSS and JavaScript its backend and frontend
load, and most of that task is npm's, the bundler's and the library's. What this
workflow orders is the TYPO3 half: what the build is run as, what its output
promises the backend, and which of that output is committed. Keep this skill as
routing and workflow; never retain a dependency version, a bundler
configuration, a build command or a core class name — every one of those is a
property of the repository in front of you and of the majors it declares.

## The order

1. Work through [references/base.md](references/base.md). It fixes what this
   package is, which majors it declares, and what it runs its build with.
2. Establish which of the output is committed, below.
3. Build the tree before you change it, below. A build whose output is not
   reproducible makes every later diff unreadable, and that is cheapest to learn
   before there is a change in it.
4. Change what the task asks for, and stop where the library's own migration
   begins.
5. Verify what the rebuilt output promises the backend, below.
6. Rebuild the committed artefacts in the commit that changes their source.

`typo3_project_describe` is discharged by the first step: it reports the
manifests this repository keeps — at the root, and one directory down where the
build sits there — the commands each of them declares with the manifest they
came from, whether a command reports or changes, and the Node that the manifest,
the pinned version, the CI workflow and the container each state, with the
disagreements between them named. Run the commands as it reported them. An
invocation rewritten from habit runs the build in the wrong directory or on a
Node that CI does not use, and both of those surface as a diff nobody can
explain.

## Which of the output is committed

This server does not read your working tree, so this is the repository's answer
and not a lookup: is the built output tracked in git, and does any check assert
that a build leaves the tree clean.

- **Committed output means source and output change together.** The package's
  consumers install what is in the repository, so a commit carrying new source
  and last month's artefacts ships the old behaviour to every one of them.
- **Uncommitted output means the deployment runs the build.** Then the artefacts
  are not yours to commit, and what has to hold instead is that the build runs
  where the deployment runs it.
- A check that asserts a clean tree after a build is the executable form of that
  decision, and where the repository has none, its absence is a finding rather
  than a licence. Establishing that check is `typo3-extension-testing`: invoke
  `typo3-extension-testing` for it, carrying the build command and the output
  paths already established here.

## Build the tree before you change it

Run the build on the untouched checkout, then look at the working tree. It is
one command and it separates two failures that are indistinguishable afterwards:
output that differs because of your change, and output that differs on every run
whatever the source says.

- Where the tree comes back dirty on an unchanged checkout, that is the finding,
  and it is about the toolchain rather than about the package. Say which file
  differs and how, before anything is changed on top of it.
- Where the build will not run at all, the Node the first step reported against
  what this machine has is the first thing to read, and reinstalling the
  dependency tree is the second.

## Where this workflow stops

The bundler's configuration format, a JavaScript library's own API change and a
defect in the runtime are that project's manual, not this server's. Read them
there, and say in the answer which manual answered — a migration reconstructed
from the installed sources of a dependency is a reading of one version of it,
and it is worth what it says about that version alone.

Two changes look like this task and are not:

- The package is being carried to another set of TYPO3 majors, or is broken by
  what one of them removed. That decides the whole reading below, so invoke
  `typo3-extension-upgrade` and carry across the build commands and the output
  paths already established.
- The request is an audit of the package rather than a change already agreed.
  Invoke `typo3-extension-health` and carry across what was established about
  the build.

## What the rebuilt output promises the backend

Built backend JavaScript does not reach the backend by being present. It is
declared, in an import map: `Configuration/JavaScriptModules.php` maps a bare
specifier onto a path in the package and lists the extensions its modules depend
on. A build that renames, splits, hashes or drops an output breaks that mapping
without anything failing in PHP, so after every rebuild each mapped path is
checked against the file the build actually wrote.

- `typo3_documentation_lookup`, at each major the package declares, for the
  backend JavaScript module contract and what an extension may assume is already
  loaded. Assuming a library is present because the backend once shipped it is a
  decision, and it is one this answer settles.
- **A CSS class or an icon identifier the output borrows from the core is
  verified against every declared major, one call each, and never read off its
  name.** `typo3_component_lookup` with the `targetVersion` of a major answers
  what the class provides there, and the query names the class itself rather
  than the component around it. Where the component was never verified on that
  major its entry comes back withheld and the class is answered beside it, under
  `coveredClasses`, as the name and the versions it holds on with nothing to
  paste. Where the class is not there either, the withholding names the core
  Sass file, and reading that file on that branch is the next step rather than
  the end of one. A class the package's own stylesheet only adds a rule to is
  one of these, and reads in the diff exactly like one the package owns.
- `typo3_icon_lookup` for a borrowed icon identifier. It answers from the
  installation, so it settles the major installed and says nothing about the
  others the package declares.
- `typo3_changelog_lookup`, restricted to each declared major, for a core asset
  the output stops relying on. Deleting a rule because the core no longer ships
  the icon font it names is the same unverified decision as attaching a class
  because the core does, and the build goes green either way.

Each of those answers is about one major. Where the package declares several,
the finding is the range the borrowed class or identifier holds on, and a
borrowed surface that is not verified on the lowest declared major is a defect
in that version rather than a detail.

## Closing the change

1. Rebuild, and commit the artefacts together with the source that produced
   them.
2. Report what was rebuilt, what the build printed, and which of the mapped
   paths and borrowed surfaces were verified on which majors — including the
   ones that came back withheld or unanswerable, by name.
3. Draft the message with `typo3_commit_message_guide` and `workflow="project"`.
   The change lands in the package's own repository.

This skill owns the TYPO3 half of a package's asset build: what the build is run
as, whether its output is committed and rebuilt with its source, what the import
map promises about the files that output consists of, and which majors a
borrowed core class or icon holds on. It does not own the migration a bundler or
a JavaScript library asks for on its own account, the range of TYPO3 majors the
package declares, the audit that decides what else is wrong with it, or the
harness that would prove the build — each of those is named above with the
workflow or the manual it belongs to.
