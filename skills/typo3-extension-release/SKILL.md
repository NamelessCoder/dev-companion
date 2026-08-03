---
name: typo3-extension-release
description: Prepare a TYPO3 extension, sitepackage or project package for a release and verify the exact archive that would be published. Use for releasing, shipping, publishing or distributing a package, cutting or bumping a version, tagging, building or inspecting a release artifact or archive, publishing to TER, Packagist or another package registry, release notes and upgrade instructions for a version, and any request to say whether a package is ready to ship or what still blocks it. Preparation is local and reversible; tagging, pushing and publishing stay a separate step that happens only when it is explicitly asked for.
---

# TYPO3 Extension Release

Verify what would actually be published, not the checkout it was built from.
Keep this skill as routing and verification method; do not embed registry rules,
packaging tools or version facts.

## Establish scope, target and evidence

1. Work through [references/base.md](references/base.md) — it fixes the order
   every task here starts in. A release is where the order pays twice: the
   commands the repository declares are what a candidate has to survive, and
   they are established before anything is built rather than invented after an
   archive exists.
2. Read [references/checklist.md](references/checklist.md) for the release
   surfaces, the two exclusion mechanisms, and what separates a blocker from a
   recommendation.
3. Settle the target before building anything: which registries this package
   publishes to, which version is being prepared, and from which ref. The
   checkout answers some of it — the declared registries, the last tag, the
   distance to it — and where it does not, ask. A release prepared for the wrong
   target is verified against the wrong rules, and every later step inherits
   that.
4. `typo3_documentation_lookup` with several short English queries for the
   requirements of each registry in scope and for the metadata a published
   extension has to carry. Registry rules change on their own schedule and this
   file cannot; what is written here is how to check, never what the rule
   currently is.

## The artifact is not the checkout

This is what the skill exists for. A green checkout says nothing about the
archive, because the archive is a different set of files chosen by a different
mechanism, and more than one mechanism is usually in play:

- The version-control export honours the exclusion attributes committed beside
  the files.
- A registry's own packaging tool applies its own exclusion list and does not
  read those attributes.

So two registries can receive two different file sets from one commit, and
nothing in a repository reports the difference. Establish, for each registry in
scope, which mechanism builds its archive and which exclusion list that
mechanism actually reads — then compare the file lists against each other, not
against the working tree. The comparison is the finding: a file that ships to
one registry and not to the other is a defect in whichever of the two is wrong,
and it is invisible from either side alone.

Build through the repository's own release command where it declares one, and
say so where it does not — a hand-assembled archive verifies a procedure nobody
will repeat.

## Verify the candidate

Against the built artifact, in this order, because each answer decides what the
next is worth:

1. Its file list: what it contains, and what it omits. Development
   configuration, editor and CI directories, tests where the package excludes
   them, build sources, local environment files and anything carrying a
   credential are the ones to name explicitly — present or absent, both are a
   result.
2. Its metadata: the version, the declared dependency and platform constraints,
   the licence and the package identity, consistent across every file that
   states them and consistent with the tag being prepared.
3. Its installability: resolve it from its declared dependencies in a clean
   place, not in the working tree that already has them.
4. The commands `typo3_project_scope` reported as checks, run against the
   candidate. They hand the code back as it was, so a preparation told to change
   nothing runs them. A check that passes in the checkout and fails on the
   artifact is the highest-value finding this workflow produces.

What the deprecation sweep the base fixes returned belongs in the release notes
of the version being prepared, and a sweep that came back empty is reported as
having run.

## Report, and stop before publishing

Preparation ends with a report and nothing else changed outside the working
tree. It carries the artifact path and its checksum, what the archive includes
and excludes per registry, the verification results with what was run and what
it printed, the blockers that remain, and the publication steps deliberately not
taken — written concretely enough to be carried out, and not carried out.

Tagging, pushing and publishing to a registry change state other people depend
on and cannot be undone by this workflow. They happen in a separate step, on an
explicit request, with the repository, the version and the credentials confirmed
first. An unclear target is a question, never a guess: this is the one place
where continuing on an assumption publishes it.

This skill owns the release gate — the target, the artifact, its verification
and the boundary at publication. It does not own being right about the code:
what a package is worth in general goes to `typo3-extension-conformance`, a
missing or broken check goes to `typo3-extension-testing`, release notes and
upgrade instructions go to `typo3-extension-documentation`, and crossing a
package to another supported range goes to `typo3-extension-upgrade`. Name the
owner in the report, hand over the verified stopping point, and keep the
re-verification of the artifact here — a fix made elsewhere is not released
until the archive has been built and read again.
