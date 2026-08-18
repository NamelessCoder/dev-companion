---
description: >-
  The run from a cloned project repository to a site that answers on both sides, in the order the steps have to go in, with what decides each one and what says it worked.
whenToUse: >-
  When a repository that declares its own environment has to be brought up locally and nothing is installed below it yet — a fresh clone, or one whose installation was torn down. A package that declares no procedure has an installation created for it instead, which starts a step earlier.
hints:
  - installation-boot
  - installation-setup
---

# Booting a Clone Into a Running Installation

The repository carries the code and the configuration. The installation is
everything else, and booting one is supplying what the clone does not carry, in
an order where each step decides what the next one can be.

## What the Clone Does Not Carry

The database, the files the storages point at, and everything below `var/`. None
of the three is in version control, so a clone answers nothing until they
arrive, and the repository is where it is read what supplies them.
`typo3_project_describe` reports the environment, the hooks bound to each of its
stages with the command each runs, and the pull recipes the data comes from. The
environment configuration file carries the lifecycle whole, and starting the
environment runs it whether or not it was read.

Where that lifecycle exists it is the procedure rather than a description of
one, and commands typed beside it boot something the repository does not
describe. Read the repository's own instructions next to it: which data an
installation here is meant to be filled with is not derivable from the code.

## The Order the Steps Go In

1. Start the environment the repository declares. It carries the interpreter and
   the database every later command runs against, so nothing before it is a
   step.
2. Install the dependencies inside that environment.
3. Start it a second time, because the first start ran before there was an
   installation for it to find.
4. Bring the data in — the import the repository declares, or the unattended
   install where it declares none.
5. Make the installation agree with the code in front of it: the schema first,
   then the caches.
6. Create a backend login, because the users arrive without their passwords.
7. Check the site's base against the host it is being served under.
8. Request the frontend and the backend, and read what the installation wrote
   down where either does not answer.

Steps 1 to 3 are the environment, 4 to 6 are console commands inside it, and 7
and 8 are what says the boot worked. Changing anything that already works is
none of them: booting is not repairing, and where a declared step fails, the
finding is which one failed and on what.

## Why the Environment Is Started Twice

A DDEV project decides where the settings files go by detection rather than by
the start, and it looks for an installed TYPO3 on every one of them —
`vendor/typo3/cms-core/Classes/Information/Typo3Version.php` in a Composer
installation. Finding none it points both paths at the project root and writes
nothing, so the first start in a clone leaves no `config/system/additional.php`,
and every request then fails with exception 1396795884 for the trusted hosts
pattern that file supplies. The detection also runs before the post-start hooks,
so a hook that installs the dependencies gets the file at the next start rather
than in that one.

The second start is therefore a step of the boot and not a repair. A
project-owned `additional.php` is the other way out and is there from the first
request; who owns that file, and what taking it over costs, is
`typo3_hint_lookup` with the id `project-configuration-files`.

## Where the Data Comes From

The repository decides which of two cases this is, and the answer is in what it
declares rather than in what a boot usually looks like.

Where it declares an import — a dump, a pull recipe, a hook that fetches one —
that import is the step, and it is two imports rather than one. The database and
the files are separate, so a clone that brought the database and not the files
keeps every `sys_file` row with nothing behind it, and
`typo3 cleanup:localprocessedfiles` is what deletes the processed-file records
whose files are gone.

Where it declares none, the install is how the installation comes into
existence, and it is run once and unattended. `typo3 setup` refuses an existing
`config/system/settings.php` with exception 1669747685 and a database that
already holds tables with exception 1669747200, so the guard a script needs is
that file rather than an exit code. Under DDEV the variables ride in on the
command line, and
`ddev exec bash -c '<assignments> vendor/bin/typo3 setup --no-interaction'` is
the form that carries every one of them. What the command takes, what each value
becomes in the file it writes, and which of its options are inert on which major
is `typo3_hint_lookup` with the id `installation-setup`.

## Making the Installation Agree With the Code

An imported database was dumped from a different point in the history than the
code in front of it, and two commands close that gap in this order.

`typo3 extension:setup` is the first. It migrates the schema of every active
package before it runs each package's own setup, and it applies the additive
suggestions only: nothing is dropped and nothing is renamed, so a column the
code no longer declares survives the run and a table the code needs and the dump
has not got is created. `database:updateschema` is not the core's command, and a
repository whose hooks call it has `typo3-console` required beside the core.

`typo3 cache:flush` is the second, and it is owed because the dump carries
caches: the hash, pages and rootline caches are configured to the database
backend by default, so the imported tables hold another installation's cached
pages, hashes and rootlines. The command empties those and the file-backed ones
below `var/` with them.

## The Login the Dump Did Not Bring

Backend users arrive with the dump and their passwords do not, so a boot creates
a user of its own with `typo3 backend:user:create`. Two of its answers only show
up in a script. The password question is asked even under `--no-interaction`
where neither the option nor the environment variable supplied one, so an
unattended hook waits on standard input instead of failing. And a username the
imported database already holds is refused with exception 1670797516, which is
how a second boot fails on the step that worked the first time —
`backend:resetpassword` is the way into a user that exists.

Past those two checks the command reports nothing of its own, so the `be_users`
row, or a login, is what says the user was created. Which environment variables
it reads where the matching option is absent is `typo3_hint_lookup` with the id
`installation-boot`.

## The Host the Site Is Served Under

The site configuration is in the repository, so a clone is served under whatever
host its base names, and that is rarely the host this machine reaches it on. One
route is built per site and per language out of that base, with its host, scheme
and port on the route as requirements, so a request arriving on the local host
matches no site and the installation answers its own root with a page-not-found
rather than with the page tree. A base that is a bare path carries no host
requirement and matches every host, and `%env()` in the base is the other way
one configuration serves two environments.

Above that sits the host check: an `HTTP_HOST` that does not match
`SYS/trustedHostsPattern` is refused with exception 1396795884, and an empty
pattern denies every host there is. Read what the installation actually resolved
with `typo3_configuration_lookup` rather than off the files, because a generated
`additional.php` is merged over what the site configuration and the install
wrote.

## What Says the Boot Worked

The site answering is the proof, and it is two requests: the frontend on the URL
the installation is configured for, and the backend. A green start says the
container came up, and a command that exits 0 says it ran — neither says the
installation serves anything.

Where a side does not answer, the failure is read from what the installation
wrote down rather than out of the page it returned, which is `typo3_hint_lookup`
with the id `installation-exception-output`. The two failures a boot is
likeliest to meet are in this document: a trusted hosts pattern that refuses the
local host answers HTTP 500 and is never written to the log at all, and a site
base that matches no host answers the project root with a page-not-found and
throws nothing anywhere. Nothing is torn down to establish any of it — an
installation that was asked for and then destroyed is a change nobody asked for.
