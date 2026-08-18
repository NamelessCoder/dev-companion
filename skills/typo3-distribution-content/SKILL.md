---
name: typo3-distribution-content
description: Ship the content of a TYPO3 site inside an extension, as a distribution — the initial content a package carries, the export artifact and the files beside it, and the site configuration that travels with them. Use for initial content, data.xml, Initialisation, seeding a page tree, and a distribution that has to come up on a fresh installation.
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
metadata:
  typo3-dev-companion-status: draft
---

# TYPO3 Distribution Content

Produce the content a distribution ships, in an order where each step decides
what the next one can be. Keep this skill as routing and workflow; never retain
command options, table names, package names or version numbers — each of those
belongs to a tool that releases on its own cycle and none of them can be
re-asked from here.

## Where this starts

Work through [references/base.md](references/base.md) first. Two of its answers
are this workflow's entry condition rather than findings: the installation the
content is produced in, and the extension that is to carry it. Nothing here can
be exported before both exist, and where the installation is the one that is
missing, say so and bring it into existence in the workflow that owns that
before coming back.

Then read what an extension ships content as, once, before anything is written:
`typo3_hint_lookup` with `id=sitepackage-initial-content`. It names the three
other ids this workflow turns on, and each of them is fetched at the step below
that needs it rather than now.

## Seed what exists nowhere yet

A distribution's first content has nothing to be exported from, so the artifact
begins as records written into the installation by a script. `typo3_hint_lookup`
with `id=datahandler-seeding` owns that: what has to be booted, which user the
write runs as, and what one call can and cannot resolve for itself.

What this workflow adds is that the seed is **read back out of the installation
before it is exported**. A record takes the defaults its configuration gives it,
which are not the defaults its columns carry, and one of those decides whether a
page is delivered to a visitor at all. So query the installation for what
actually landed — the tree, the elements on it, the relations hanging off them —
and where it is wrong, correct the script rather than the records: the script is
what the next version of the artifact comes out of, and a record fixed by hand
is a correction the next export loses.

## Export it

`typo3_hint_lookup` with `id=impexp-artifact` owns the export: which command
writes it, what the package has to require for the import to exist at all, what
the command leaves out without saying so, and where it puts the file, which is
not where the argument said. `typo3_documentation_lookup` at the version being
shipped is the other half, and the page to ask for is the one on creating a
distribution: which records belong in the artifact and which are the receiving
installation's own is a decision that page has already made.

What this workflow adds is that **the command reporting success is not evidence
that the export worked**. It reports the same success for an artifact that left
every image out and for one that left a whole table out, and both failures are
invisible until somebody installs the package. So the artifact is what is
checked, not the message: every table the tree holds is a part of it, and the
part that carries files is there with one file per referenced file beside it.

## Place it in the package

The artifact and the directory of files that belongs to it both move into the
extension under the names the import looks for, which
`id=sitepackage-initial-content` states. Neither arrives there by being asked
for: the export keeps only the last segment of the name it was given, so putting
both in place is a step of this workflow and not an argument to the command.

## Ship the site configuration beside the export, not inside it

A site configuration can travel two ways and they are not equivalent — one of
them loses the address the site answers on, because the importing installation
cannot know it. `typo3_hint_lookup` with `id=initial-content-references` owns
which route does what, and what survives an import at all.

What this workflow adds is that the route which keeps the configuration intact
copies **the whole directory the site keeps its configuration in**, not the one
file in it that is obviously configuration. Whatever else the installation put
beside it — the rendering the site is defined by, among it — is part of the site
and has to travel with it. Left behind, the receiving installation resolves the
site, finds every page, and renders nothing, which reads as a broken import and
is not one.

Only the root page is rewritten to the record that was actually imported.
Anything else in that configuration naming a record by number points at a
stranger on the second installation, so a configuration written for a
distribution names as few of them as it can, and where one is unavoidable the
package says so where somebody will read it.

## Prove it on an installation that has never seen this package

The artifact is write-only on the installation it came from: the import runs
once and is remembered, so re-importing it there proves nothing, and reading the
document back is reasoning rather than verification — `typo3_hint_lookup` with
`id=initial-content-import-once` owns why, and names the cheap form of the same
proof.

The proof is an installation that has never had this package, with the package
active before the install runs. Three things are checked there, and the first
one alone is what a session usually stops at:

1. The records arrived — the tree, the content on it, and the files it
   references as files rather than as rows.
2. The site answers on the address that installation is configured for, and it
   renders. A site that resolves and renders nothing passes every check made
   from the console.
3. Every page the artifact carries answers, not only the one at the root. A page
   shipped invisible and a reference pointing at a stranger both survive an
   import that reports nothing wrong.

Read what the receiving installation actually got with
`typo3_configuration_lookup` rather than off the files it was given, because the
merged result is what it runs on.

Then report what the package is: which records it ships, which files, which site
it configures, and the installation the three checks above were run on. Draft
the message for it with `typo3_commit_message_guide` and `workflow="project"` —
the artifact, its files and the site configuration are that repository's own
files, which is the workflow that argument names.

## Where this stops

This skill owns the content a distribution ships and the package that carries
it: seeding what exists nowhere yet, the export, where the artifact and its
files sit in the extension, the site configuration that travels beside them, and
the installation that proves the result. It owns both directions of that one
thing — writing the content and shipping it — and neither half is finished
without the other.

It does not own what the content is made of. The templates, the elements an
editor fills in, and the extension that renders them are the sitepackage's, and
the crossing is explicit in both directions: going out, state what the artifact
verifiably contains and stop before editing that owner's files; coming in, a
package whose content has to be shipped is this workflow from the seed onwards.

It does not own an installation either. Bringing one into existence, and
repairing one that came up wrong, belong to the installation workflow — this one
uses two of them and creates neither. Where the second installation cannot be
had, say that the proof was not run rather than substituting a reading of the
artifact for it.
