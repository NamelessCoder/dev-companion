---
id: R-PRJ-013
status: held
restsOn: [D-SCO-013, D-SCO-014]
---

# R-PRJ-013 — The project answer states the Node its declared commands run on

**Where a repository declares npm commands, the answer states the Node each file
names — `engines.node`, an `.nvmrc`, an `actions/setup-node` step, a DDEV
`nodejs_version` — and how they relate.**

The composer half of that command list has carried its interpreter since
`R-PRJ-008` and the relation between the numbers since `R-PRJ-010`. The npm half
beside it carried none, while the difference between the Node on the machine and
the Node in CI is what a build breaks on.

Read from the files as they stand, so `R-PRJ-001` still holds and the answer
arrives on a fresh clone. Nothing is executed, which is the whole of what it may
not claim: it says what the repository declares, never that any of it was run —
and the Node the caller's own shell has is in none of these files.

A version is read where a file names one outright and stated back where it does
not. A `node-version` that is a matrix entry, an expression, an `lts` alias or a
range comes back as the workflow writes it, because the workflow is one file for
the caller to open and a resolved wrong number would carry this answer's
authority.

Only the segments both sides spell are compared. An `.nvmrc` naming a major and
a workflow naming a patch level agree wherever the major does, and the release
difference inside one major is a thing no file here states.

Said even where nothing declares one, because that silence is the finding: the
npm commands are in the list above and what runs them is whatever node is on the
path. Withheld only where the repository has no npm surface at all.

## From

`feedback/2026-08-18-113501` (2026-08-18), a sitepackage maintenance session in
`bootstrap_package`. Five sixths of it were Node, npm and GitHub Actions work,
and its defect was fantasticon writing 65536 bytes instead of 5828 because Node
24.19 raised `Buffer.poolSize` — reproduced only in a container, because the
machine ran 24.16 and CI ran 24.19.

## Held by

- `ProjectTest::theNodeThoseNpmCommandsRunOnIsStatedBesideThem`
- `ProjectTest::aNodeAWorkflowDecidesElsewhereIsStatedBackRatherThanResolved`
- `ProjectTest::aRepositoryThatDeclaresNoNodeIsSaidToDeclareNone`
- `ProjectTest::aRepositoryWithNoNpmSurfaceIsToldNothingAboutNode`
- `ProjectTest::theManifestBelowBuildIsReadWhereTheRepositoryKeepsItThere`

What is read is every `package.json` `Project::commands()` reads its npm scripts
from: the root one, and the `Build/package.json` beside it that a core checkout
has instead of a root one. Each number is answered with the file it came from,
because the same field means the root's manifest in one repository and the one
below it in the next — `D-SCO-014`.
