# Release surfaces, exclusions, and what blocks a release

Read this once, at the start. It is the work list a release report closes on:
every surface below is answered for the version being prepared, and a surface
this package cannot have is answered by saying so.

## The surfaces

- **Target.** The registries this package publishes to, the version being
  prepared, the ref it is built from, and the distance between that ref and the
  last released tag. A release prepared from an unclear target is verified
  against the wrong rules.
- **Version.** Every file that states one — the Composer manifest, the
  extension's own metadata file, documentation that quotes it, any generated
  asset that carries it — says the same thing, and it is the version being
  prepared rather than the last one.
- **Metadata.** Package identity, extension key, licence, description, author
  and support links, and the state the registry expects them in. Both manifests
  exist in most extensions and neither is generated from the other, so they
  drift silently.
- **Constraints.** The supported TYPO3 and PHP ranges, declared consistently
  across the manifests, and dependencies that resolve within them. A range the
  package claims and cannot resolve is a release blocker, not a warning.
- **Release notes.** What changed since the last tag, what a user has to do on
  upgrade, and what was deprecated or removed. The deprecation sweep the base
  fixes is where the upgrade half comes from.
- **Generated material.** Compiled frontend assets, generated documentation,
  vendored copies — whether they are built, whether they are current, and
  whether they belong in the archive or are rebuilt by whoever installs it.
- **Exclusions.** What the archive must not carry, which is the surface with two
  owners; see below.
- **Checks.** The commands `typo3_project_describe` reports, run against the
  candidate rather than against the working tree.
- **Publication steps.** Named, ordered, and not taken.

## The two exclusion mechanisms

An extension is published through more than one channel, and the channels do not
share an exclusion list:

- The version-control export builds an archive from committed files and honours
  the export-ignore attributes committed beside them.
- A registry's own packaging tool builds its archive from the working tree and
  applies an exclusion list that ships with the tool itself. It does not read
  the export attributes.

A file excluded in one mechanism and not in the other ships to one registry and
not to the other, from the same commit, with nothing reporting it. Measured in
one extension: the two archives differed by two tracked editor-configuration
files, and the repository said nothing about it either way.

So the check is a comparison, and it is the only one that finds this class of
defect:

1. Build the archive each registry in scope would actually receive, through the
   mechanism that registry uses.
2. List the files in each.
3. Compare the lists against **each other**. Every difference is either a file
   that should be excluded everywhere or one that should be included everywhere;
   there is no third answer, and which of the two it is decides which mechanism
   is wrong.
4. Read the exclusion list each mechanism used, and say where it lives. A rule
   the maintainer cannot find is a rule they cannot fix.

Ask `typo3_documentation_lookup` for the current packaging requirements of each
registry rather than trusting a mechanism to be the one it was last time. Which
tools exist, and what each one excludes by default, is exactly the fact a
published file like this one goes stale on.

## What ships that must not

Name each of these explicitly in the report, present or absent — an unmentioned
absence and an unchecked presence read identically:

- Credentials, tokens, keys, and local environment files.
- Editor, IDE and CI configuration.
- Development dependencies and their lock state, where the package excludes
  them.
- Test fixtures and suites, where the package excludes them — and where it does
  not, say that it ships them on purpose.
- Build sources whose output is already in the archive, and build output whose
  source is expected to be rebuilt.
- Anything from another package that was vendored by hand.

## Blocker or recommendation

A **blocker** is anything that makes the published artifact wrong for the people
who install it: a credential in the archive, a version that disagrees with
itself, a constraint that does not resolve, a check that passes in the checkout
and fails on the artifact, a file set that differs between registries without a
reason, or a metadata field the target registry rejects.

A **recommendation** is anything that would make the next release better and
does not make this one wrong. It is reported after the blockers and never mixed
into them: a release report whose blockers cannot be separated from its
suggestions is one the maintainer has to redo by reading.

Where a finding is about whether the code is any good rather than about what
ships, it belongs to `typo3-extension-conformance` — name it and hand it over
rather than judging it here.

## Closing

Close on this list rather than on a summary: every surface above, marked
verified, blocked, or not applicable to this package, with the evidence that a
verification rests on — the command that was run and what it printed, the file
that was read at its path. A summary assembled at the end reports what the
session remembers checking, never what it never reached.
