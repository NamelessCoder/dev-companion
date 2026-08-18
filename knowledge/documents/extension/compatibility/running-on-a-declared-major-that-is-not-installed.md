---
description: >-
  How a run against a TYPO3 major the package declares and the installation does not have is stood up beside that installation, and what a green run there proves.
whenToUse: >-
  When a change has to hold on more than one declared major and the installation supplies one of them — before the claim about the other one is written down. It says what CI already covers, where the second Composer root goes, what it costs the installation, and how to tell a cell that could have failed from one that could not.
hints: []
---

# Running a Package on a Declared Major That Is Not Installed

A claim about a major is worth what it was run on. What runs it is the package's
own suite resolved against that major, in a Composer root of its own, beside the
installation rather than in place of it. The installation the developer works in
is never repinned: it is the one thing in the room that cannot be rebuilt from
the repository.

Whether a symbol is there on that major is the neighbouring question and is
settled by reading the branch. This page begins where that one stops, at the
run.

Where the installation runs in a container, every command below runs inside it —
the interpreter, the extensions and the database service are the container's,
and a run on the host proves a cell nobody has.

## Ask What CI Already Covers, Before Installing Anything

The matrix a repository declares is one file, and reading it decides whether the
rest of this page is needed at all: `.github/workflows/`, `.gitlab-ci.yml`, or
whatever the repository's own configuration is. Read which majors the matrix
resolves and which commands it runs per cell, because a matrix that installs a
major and then runs one linter has not run the suite there.

A covered cell is proved by pushing the branch, and that is the cheaper answer.
It proves the commit rather than the working tree, so a claim made about
uncommitted changes still needs the local run.

Nothing here reads that file for you. `typo3_project_describe` reports what the
repository declares as its own commands, which is what a cell should run, and
the workflow files are read from the checkout.

## A Composer Root of Its Own

The second root is a directory with its own `composer.json`, placed inside the
project so a container reaches it and outside the document root so nothing
serves it. `var/` is both, and TYPO3's own base distribution already ignores
everything below it — a project whose ignore list differs is worth one look,
because a vendor tree staged by accident is a large commit.

```json
{
    "repositories": [
        { "type": "path", "url": "../../packages/<key>", "options": { "symlink": true } }
    ],
    "require": {
        "typo3/cms-core": "^<the other major>",
        "<vendor>/<package>": "@dev"
    },
    "require-dev": {
        "typo3/testing-framework": "^<the release that major pairs with>"
    },
    "config": {
        "allow-plugins": {
            "typo3/class-alias-loader": true,
            "typo3/cms-composer-installers": true
        }
    },
    "extra": { "typo3/cms": { "web-dir": "public" } }
}
```

```bash
composer update --no-interaction    # in that directory
```

The path repository is what keeps one source tree: the package is symlinked into
the second root's `vendor/`, so a fix is made once and both cells run it. A
package that is its own repository root is the same arrangement with the copy
taken out — the second root requires the package from its own checkout.

The root requires the other major, so the solver resolves the package's declared
range to that side. A range that will not resolve is the result rather than a
setback: it is what says the declaration is a claim nobody has installed.

The price is one core download and the disk that core takes. It buys a tree that
is thrown away with the directory, which is why it is cheaper than the pin the
question usually starts as.

## What It Writes, and What the Installation Keeps

Nothing of the installation moves. On the run this page was written from,
`composer.json` and `composer.lock` were byte-identical afterwards, the
installed core answered the same version to its own console, and the frontend
answered as before. There is nothing to restore, because nothing was taken.

What the two roots share is the container and the database service, and neither
is written by resolving.

## What the Second Root Resolves Differently

The platform pin is not inherited. An installation may pin the interpreter it
resolves against in `config.platform.php`, and a fresh root has no such pin, so
it resolves against whatever PHP the container actually runs. Carry the pin over
where the cell is meant to be about that PHP; the pin is otherwise a fact of the
first root alone.

The development tooling floats. The second root's solver takes the newest
release its constraints admit, so the test runner there can be a major above the
one the installation has. The same configuration file then goes through a
stricter reader in one cell than in the other, and warnings that only one cell
prints are about the runner rather than about the core.

## Whether the Database Survives

It does, and it is not the expensive half. A functional run never uses the
configured database itself: each test class gets one derived from that name, and
the live one is left as it was. The credentials it reads, and what a finished
run leaves behind, belong to `typo3://guides/extension/testing/phpunit` and are
not repeated here.

So the second root needs no database of its own. Pointing it at the same service
as the installation is the whole of the setup.

What both roots do share is that derivation. The name is derived from the test
class, so one suite run in two roots claims one database name — the runs are
serial, and two at once are one run tearing down the other's. The instance
directory is per root and collides with nothing.

## Which Checks Are Worth Re-running There

Whatever reads the core: the unit suite, the functional suite, and static
analysis, which resolves the symbols it judges out of the core it is pointed at.
What reads only the package's own files — the formatter, the syntax check — says
the same thing in both roots and is run once.

A cell that cannot fail proves nothing, and a fresh root has several ways of not
running what you think it runs. Make it red before believing it green: assert
something only one of the majors has, and watch the cell that should fail, fail.
That is one throwaway test, and it is the difference between a proof and a
directory that produced the word `OK`.

## What the Second Root Does Not Give

It is a resolved dependency tree, not an installation. It has no settings file,
no site configuration and no content, so nothing there serves a page. Its
console lists its commands and states the version it is, and a command that
reaches the database stops on the connection named `Default` not being
configured — which reads like a broken setup and is the second root working as
intended. A claim about what renders on the other major is therefore not proved
here: that needs an installation of that major, which is a project of its own
and a different workflow.

## What Is Left Behind

Removing the directory is the whole of the teardown, and the resolved tree, the
published assets and the test instances go with it. The per-class databases the
functional runs created stay until they are dropped, which is the same set the
installed cell leaves and is dealt with at
`typo3://guides/extension/testing/phpunit`.

Name the second root in the report by what it resolved rather than by where it
sat. The directory is gone by then, and what stands is which major and which
release the suite actually ran against.
