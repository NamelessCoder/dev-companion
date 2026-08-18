---
description: >-
  How it is settled whether a class, a member or a signature is there on a TYPO3 major the package declares and the installation does not have.
whenToUse: >-
  When the code has to run on more than one declared major and one of them is installed — before writing against an API the installed copy happens to have. It names the reading that settles the question; the shape itself is read from the branch, and no per-version list of identifiers is bundled anywhere here.
hints:
  - extension-repository-layout
---

# Settling an API Question on a Declared Major That Is Not Installed

A package states the majors it runs on, and the installation around it supplies
one of them. Whether the API being written against is there on another one is a
question about that branch, and it is settled by reading it. No signature is
bundled here: one written down today is wrong at the next release, and a wrong
signature is believed exactly as readily as a right one.

## Which Majors the Question Is About

`typo3_project_describe` reports `coreConstraint`, which is what the package
requires of `typo3/cms-core`. That range is what the code has to hold on, and
the installed version is one point in it. A compatibility judgement made against
the installed version alone is a judgement about one of the declared majors.

The knowledge answers already cover all of them. `typo3_hint_lookup` and
`typo3_task_guide` keep a statement that holds on any declared major and name
which those are, and `targetVersion` narrows to one. What none of them covers is
the core's own source, which is where the shape of a class is written.

The question is per symbol rather than per package. A class that stands on both
branches can have gained a method on the newer one, a return type can be
nullable on one side, and a constant can hold a different set of entries.

## What the Changelog Settles and What It Does Not

`typo3_changelog_lookup` answers with change events: what a version added,
deprecated, changed or removed. The entries of the older lines are on disk,
because a core package ships the changelog of every line it has, so the
direction below the installed major is covered.

An entry saying an API arrived before the older declared major settles that the
API is there, and nothing past that. Nothing writes an entry for what did not
change, so a member added later to a class that was already there leaves no
trace, and neither does a signature that was widened. The changelog narrows the
question, and the branch closes it.

## Reading the Branch

The core repository keeps every maintained line as a branch, so a checkout of it
answers for a major that is installed nowhere. It is one call per symbol:

```bash
git cat-file -e <branch>:typo3/sysext/<key>/Classes/<Path>.php
git grep -n "function <name>" <branch> -- typo3/sysext/<key>/Classes
git show <branch>:typo3/sysext/<key>/Classes/<Path>.php
```

Where a whole subsystem is being written against, the diff between the two
branches is that reading in one call:
`git diff <installed> <declared> -- typo3/sysext/<key>/Classes/<Subtree>/`.

Those paths are the repository's, and the installed copy spells them the other
way. `typo3/sysext/<key>/` is `vendor/typo3/cms-<key>/` in an installation, with
the underscores of the extension key written as dashes, so
`typo3/sysext/indexed_search/` is `vendor/typo3/cms-indexed-search/`. Everything
below `Classes/` is the same path on both sides.

A branch is the tip of its line and not the release this project resolved. The
tip is what the next update brings, so it is what a constraint on that major is
read against. Where one exact release is the question, its tag is what reads it.

Where no checkout of the branch is at hand, the same file is read out of the
released package for that major, required into a directory of its own. Where two
or three symbols are the whole question, reading those files alone is the
smaller step: assembling the branch to answer them costs more than they do.

## What Reading Proves, and What It Does Not

Reading settles the shape and not the behaviour. What a member does with the
arguments it is passed is the run, and the package's own suite resolved against
the other major is what says so. That costs the download of that major and is
what a change worth proving is proved on;
`typo3://guides/extension/compatibility/running-on-a-declared-major-that-is-not-installed`
is how it is stood up beside the installation.

Where nothing can be run, the compatibility claim is an argument and is written
as one. It owes what was read — the symbols, the branch, and the revision that
branch was at — and what it left uncovered, and it says outright that it is
unproven. An argument naming none of those cannot be rechecked by the next
session, and the next session is who pays for it.

What to do with a difference once it is established is not this page. The
`extension-repository-layout` hint carries it, and one policy in two places is
the pair that disagrees.
