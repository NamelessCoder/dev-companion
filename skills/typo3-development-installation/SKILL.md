---
name: typo3-development-installation
description: 'Bring the local development installation of a TYPO3 extension, sitepackage or project package into existence, or boot and repair the one the repository declares: the container, DDEV where it declares one, the unattended install, seeded demo content, and a site that will not come up.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Development Installation

Produce an installation the developer of this package can run, in an order where
each step decides what the next one can be. Keep this skill as routing and
workflow; never retain layout keys, environment defaults, command options or
package names — each of those belongs to a tool that releases on its own cycle
and none of them can be re-asked from here.

## Where this starts

Work through [references/base.md](references/base.md) first. One of its answers
is this workflow's entry condition rather than a failure: an installation lookup
that reports there is no installation to describe is the task, not the
disconnected server the base tells you to stop for. Stop for an error, continue
for that answer, and say which of the two came back.

Then, before anything is created:

- `typo3_server_scope` for whether an installation and a console can be reached
  at all. That is what separates a repository with nothing installed from one
  whose installation is merely not running. Skip it only where the base's
  installation lookup already described a booted installation, because that
  answer contains this one; everywhere else it is run, since a prescription that
  gets skipped teaches the next reader to skip the steps that matter.
- The calls in the base that read the installation are asked again once it
  exists. Asked before, they are unanswerable; asked after, they are what says
  the work succeeded.

The repository decides which of two shapes this task has, and only the first
step differs. A repository that already declares an installation — an
environment configuration, a document root, a site configuration, a lock file —
is booted from what it declares. One that declares none has an installation
created for it.

## Boot what the repository already declares

- Read the environment configuration whole before running anything. The scope
  answer names the interpreter and the commands the manifests declare; the
  lifecycle the environment runs by itself — the tasks bound to each stage, the
  configured data sources — lives in that file, and where the answer does not
  carry them, the file is the only place they are readable. Starting the
  environment runs them whether or not they were read.
- Read the repository's own instructions beside it. A project that ships a boot
  procedure has usually written down which data it is meant to be filled with,
  and that is not derivable from the code.
- Run the declared steps in the declared order, and change nothing that already
  works. Booting is not repairing, and a rewritten configuration that boots the
  same way is a change nobody asked for.
- Where a step fails, the finding is which declared step failed and on what —
  not a second procedure written beside the one the repository has.

## Create one where none is declared

1. **Make the package's own manifest the Composer root package.** It has to be
   able to install TYPO3 beneath itself, into a directory that is ignored. What
   that takes is the `extra` block the TYPO3 Composer installer reads plus the
   plugins the install has to be allowed to run: ask
   `typo3_documentation_lookup` at the version being installed, and read the
   installed installer package where the documentation is thinner than the
   question. Three properties of that step survive any version: a layout key
   that is no longer honoured is a warning rather than an error, so what the
   install printed is the evidence that the layout took; a package the core
   requires itself is not required again, because its own version line has
   nothing to do with the TYPO3 version and stating one makes the resolver fail;
   and a package that is its own Composer root is installed from the repository
   root rather than into the extension directory below the document root, so an
   empty directory there is not a broken installation.
2. **Declare the container.** Its project type and its document root follow from
   the layout decided above, not the other way round. Its interpreter is
   declared here too, and it is the number nothing later re-asks:
   `typo3_hint_lookup` with `id=php-versions` for what the target version
   requires, what it resolves dependencies against and what the core runs its
   own suites on, and the choice is made against that answer rather than against
   what the machine already has. Two things then have to be verified rather than
   assumed: that the environment fails its start when a provisioning task fails,
   because an install that failed behind a green start is the expensive failure
   of this step; and that a command which rewrites the environment configuration
   has not dropped what was set by hand, which is what reading the file back
   after such a command is for.
3. **Install non-interactively.** The console's setup command answers its own
   questions from a fixed set of environment variables, and `typo3_hint_lookup`
   with `id=environment-runtime-readers` names them. Ask
   `typo3_documentation_lookup` for the options at the version installed, and
   check two things in what it answers: the value a connection option accepts is
   not necessarily the value written into the settings afterwards, and the
   command refuses a database that already holds tables. The second is what
   makes an install script re-runnable or not — it needs its own guard on what a
   previous run left behind, and forcing the settings does not remove a schema.
4. **Seed the content the package is to be developed against**, where the task
   needs one. `typo3_hint_lookup` owns this: `id=sitepackage-initial-content`
   for which of the two setup commands imports it and what makes a package count
   as a distribution, `id=initial-content-import-once` for why a changed file
   does not arrive a second time, `id=initial-content-references` for what the
   import remaps and what it leaves pointing at a stranger. What this workflow
   adds is where to look when it lands: a seeded installation answering
   not-found at the project root is a site configuration whose base is not this
   installation's URL, which is the importer's doing and not the package's. Read
   what actually landed with `typo3_configuration_lookup`, correct it in the
   installation's own site configuration, and verify it again there.
5. **Decide what the install wrote into the repository.** The installation's
   configuration, its writable state and its document root land in the Composer
   root, which is the versioned repository itself. `typo3_hint_lookup` owns
   this: `id=project-configuration-files` for which of those files the project
   owns and which the environment generates, `id=project-build-and-scripts` for
   what surrounds the site rather than sitting in it — where the tooling and the
   one-off scripts belong, how a colleague runs any of it, and what is never
   committed. The ignore rules follow from both answers and are written before
   the first commit, not after the first accidental one.

## The environment's settings against the installation's own

Where the local environment generates settings into the installation, the
generated file and the installation's own are one boundary with two owners, and
`typo3_hint_lookup` with `id=project-configuration-files` is what owns it. This
workflow adds the case that breaks it: such a generator knows only the services
it provides itself, so an installation deliberately put on something else — a
database the environment does not run, or none — has the generated file merged
over what the install wrote, and it can no longer connect. Taking the file over
is the documented way out, and it is a step of the install rather than a repair
afterwards. Establish what the merged result actually is with
`typo3_configuration_lookup` rather than from the files, because the merge is
what the installation runs on.

## Prove it, and how far depends on who wrote the sequence

A repository that was booted from what it already declares is proved by the site
answering: the backend, and the frontend on the URL the installation is
configured for. Nothing here is torn down to establish that. Booting is not
authoring, there is no new sequence to test, and an installation that was asked
for and then destroyed is a change nobody asked for.

Where the sequence was written in this session, it is proved from the state it
will be started in again, and every step below is part of the work:

1. Start from the state a colleague's clone is in — no installed dependencies,
   no installation, no container — and let the declared sequence run unattended.
   Anything that needed a hand is not part of the setup yet.
2. Prove that the site answers on both sides: the backend, and the frontend on
   the URL the installation is configured for.
3. Start it a second time without cleaning up. A setup that is not idempotent is
   a setup somebody will run twice.
4. Report the exact commands run, what each one printed, and what the
   installation now is: the document root, the console that reaches it, the URL
   that answered, and the database it is on. `typo3_task_guide` carries what a
   finished setup owes its user beyond that, credentials included; report what
   it names rather than a second version of it.
5. Draft the message for what the setup added to the repository with
   `typo3_commit_message_guide` and `workflow="project"`. The manifest, the
   container declaration and the ignore rules are that repository's own files,
   which is the workflow that argument names.

## Where this stops

This skill owns the installation a package is developed in: the Composer root
package that installs TYPO3 beneath it, the container the repository declares,
the non-interactive install, the content it is seeded with, and what the install
writes into the repository. It does not own hosting, deployment or backups, and
it does not own what runs against the installation once it answers.

The installation a suite boots is not this one, and the difference is what each
is for rather than how it is laid out. This workflow produces a site somebody
opens in a browser and clicks through, which is why the package's own manifest
becomes the Composer root. A package whose TYPO3 is installed below a build
directory, with the package linked in and no site to visit, is a test fixture
and belongs to `typo3-extension-testing` — a repository can have both, and
asking which one the task needs is the first thing that decides the layout.

Tests and static checks are `typo3-extension-testing`, and the crossing is
explicit in both directions. Going out: state the verified point — the document
root, the console command, the URL that answered, the database — stop before
editing that owner's files, and activate it. Coming in: a suite that needs a
served site and has none is this workflow first, up to that same verified point,
and then back.
