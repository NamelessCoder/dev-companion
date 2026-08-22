---
id: R-PRJ-005
title: 'What an extension registers is answered from the installation and its files'
status: held
---

# R-PRJ-005 — What an extension registers is answered from the installation and its files

**What an extension registers is answered per extension: from the booted
installation for the registries that have no file behind them, and from its own
files for the rest.**

What is answered is the tables its TCA defines and the ones it extends, the
content elements it adds, its backend modules and routes, its icons, its site
sets, its service tags, its middlewares, and its Fluid roots and namespaces.

The three the installation owns are its tables, its content elements and its
icons: TCA and the icon registry are assembled at runtime and belong to no
package, so an entry is attributed back to this extension by the `EXT:<key>/`
reference it carries — a `LLL:EXT:` label or ctrl title, an icon the registry
resolves to a file below that extension. Where nothing attributes an entry, it
is the installation's rather than a package's, and where the installation could
not be booted the parsed list stands and the answer says what it leaves out.

The table an override file extends is read from what the file does, never from
its name — extensions number those files to fix their load order, and write one
file per element — and the content elements are the identifiers of the items it
adds to `tt_content.CType`, in both the positional and the keyed item shape and
through either call that adds one — `addTcaSelectItem()`, which names the table
first, and `addRecordType()`, whose table is its fifth argument and defaults to
`tt_content` — rather than the pointer at `tt_content` that says where they are
registered. Each carries the template it renders through, read from its own
TypoScript and left unknown where that says nothing, because a template name
derived from the identifier sends the caller to a file that is not there. A file
that returns an array is read for *that* array and for no other literal in it,
and where its list is only knowable by running the file — built in a `foreach`,
assembled into a variable — nothing is returned rather than the keys of the
literal lying beside it, and the file that hit that floor is named rather than
left as an omitted section; that is the parser's floor, and the booted
installation is what raises it. What is declared is answered; what an extension
does at runtime is named as not covered rather than guessed. The project's
Composer patches are part of what the project is.

## From

An evaluation for a site with a sitepackage and its own extension, where the
scope named the extension and nothing inside it (2026-07-29); a session whose
CType, registered with `addRecordType()` in a file of its own, came back as no
content element at all (2026-07-30); and a `REVIEW-02` run against
`georgringer/news` (2026-07-31) that was told the extension registers the icons
`provider` and `source` — the keys of the literal its `foreach` builds each icon
from.

## Held by

- `ProjectTest::whatAnExtensionRegistersIsReadFromItsOwnFiles`
- `ProjectTest::whatTheInstallationHasBeatsWhatTheFilesCouldBeReadFor`
- `ProjectTest::aRegistrationFileBuiltInALoopIsNotDeterminableRatherThanWrong`
- `ProjectTest::theContentElementsAnExtensionAddsAreNamedRatherThanPointedAt`
- `ProjectTest::aContentElementRegisteredWithAddRecordTypeIsFoundAsWell`
- `ProjectTest::anExtensionTheInstallationDoesNotHaveIsAMissWithTheKeysItDoes`
- `ProjectTest::aPatchedDependencyIsPartOfWhatThisProjectIs`
