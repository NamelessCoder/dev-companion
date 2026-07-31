---
id: R-PRJ-5
status: held
---

# R-PRJ-5 — What an extension registers is read from its own files

**What an extension registers is answered from its own files: the tables its
TCA defines and the ones it extends, the content elements it adds, its backend
modules and routes, its icons, its site sets, its service tags, its
middlewares, its Fluid roots and namespaces.**

The table an override file extends is read from what the file does, never from
its name — extensions number those files to fix their load order, and write
one file per element — and the content elements are the identifiers of the
items it adds to `tt_content.CType`, in both the positional and the keyed item
shape and through either call that adds one — `addTcaSelectItem()`, which
names the table first, and `addRecordType()`, whose table is its fifth argument
and defaults to `tt_content` — rather than the pointer at `tt_content` that
says where they are registered. Each carries the template it renders through,
read from its own TypoScript and left unknown where that says nothing, because
a template name derived from the identifier sends the caller to a file that is
not there. What is declared is answered; what an extension does at runtime is
named as not covered rather than guessed. The project's Composer patches are
part of what the project is.

**From:** an evaluation for a site with a sitepackage and its own extension,
where the scope named the extension and nothing inside it (2026-07-29); and a
session whose CType, registered with `addRecordType()` in a file of its own,
came back as no content element at all (2026-07-30).

**Held by:** `ProjectTest::whatAnExtensionRegistersIsReadFromItsOwnFiles`,
`ProjectTest::theContentElementsAnExtensionAddsAreNamedRatherThanPointedAt`,
`ProjectTest::aContentElementRegisteredWithAddRecordTypeIsFoundAsWell`,
`ProjectTest::anExtensionTheInstallationDoesNotHaveIsAMissWithTheKeysItDoes`,
`ProjectTest::aPatchedDependencyIsPartOfWhatThisProjectIs`
