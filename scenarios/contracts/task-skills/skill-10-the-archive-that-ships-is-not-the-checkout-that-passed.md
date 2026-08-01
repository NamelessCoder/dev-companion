# SKILL-10 — The archive that ships is not the checkout that passed

**Environment:** `E-EXT`, in an extension that publishes to two registries, is
green on every check it declares, and carries tracked editor configuration
excluded by one packaging mechanism and not by the other · **Contract:** `held`
**Held by:** `SkillTest::aReleaseVerifiesTheArtifactAgainstEachRegistrysOwnExclusions`,
`SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`,
`SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand`

> Prepare this TYPO3 extension for its next release to its package registries.
> Build and verify the exact artifact, tell me what is still blocking the
> release, and do not tag or publish anything until I explicitly ask.

**What has to come out of it**

- The release target is settled before anything is built: which registries,
  which version, from which ref — asked where the checkout cannot answer it.
- An archive is built for each registry in scope, through the mechanism that
  registry actually uses, and through the repository's own release command
  where it declares one.
- The file lists of those archives are compared against **each other**, and the
  editor configuration that ships to one and not to the other is reported as a
  finding with the exclusion list each mechanism read and where that list
  lives.
- Development configuration, credentials, local environment files and build
  sources are each named as present or absent, rather than left unmentioned.
- Version, constraints, licence and package identity are checked across every
  file that states them, against the version being prepared.
- The checks the project declares are run against the candidate, and a check
  that passes in the checkout while failing on the artifact is reported as a
  blocker.
- The answer ends with the artifact path and checksum, the includes and
  excludes per registry, the blockers, and the publication steps written out
  and not taken.

**How it fails**

- The green checkout is taken as the answer: the checks are run in the working
  tree, and no archive is built or read at all.
- One archive is built and verified, so the registry that receives the other
  file set is never examined and the difference cannot be seen.
- The file list is compared against the working tree rather than against the
  other archive, which makes the two mechanisms agree by construction.
- A tag is created, pushed, or a package published, without an explicit request
  and a confirmed target.
- The report is a summary of what looked fine, without the artifact path, the
  file sets, or the steps deliberately not taken.
- Blockers and recommendations are mixed into one list, so the maintainer has
  to re-read it to find what actually stops the release.
