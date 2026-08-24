# Write the workflow that orders a package's asset build

**Serves:** feedback/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md
**Priority:** normal

Ask the maintainer whether the workflow is published on the review alone, the
way `typo3-extension-patch-review` and `typo3-distribution-content` were
(`D-SKL-064`), or whether it waits for a run. The review is worked in whole as
of 2026-08-24 and so is the frontend half's route, so nothing else is owed
before the publishing commit — and that commit owes the listing budget, which is
step 3 below.

Judged 1b on 2026-08-21 — the shape is missing. `D-SKL-067` holds the evidence,
the boundary the workflow owns and what it stops at.

## What the review said, 2026-08-24

The review `writing-a-skill.rst` makes a condition of publishing was given by
the maintainer on 2026-08-24, against the draft as it stands. Three findings
came from them and two from the reading beside it:

- **Committed artefacts are rebuilt with the source they came from.** Confirmed
  as the draft has it, and it is what makes the question of step 2 the one that
  decides step 6.
- **A package's build is read rather than prescribed.** The draft says this as a
  prohibition on what it may retain; the maintainer said it as the principle the
  workflow runs on. What follows is that where the output lands is a property of
  the package, and from v14 it is declared in `Configuration/Resources.php`.
- **Frontend and backend differ, and the draft has only the backend half.** Its
  verification section is titled for the backend and every check in it is one:
  the import map, a borrowed backend class, an icon. A sitepackage that builds
  Sass into a stylesheet and includes it through TypoScript has no check at all
  after the rebuild, and that is the commonest case this workflow will meet.
- **The maintainer would skip step 3**, the build of the untouched tree before
  anything is changed. It is the most expensive instruction in the workflow and
  it is not what somebody who knows the repository does, so it goes — see below
  for what takes its place.
- **The borrowed-class step asks the wrong question.** The draft has the query
  name the class rather than the component around it, and the maintainer read
  that as useless: the class is not helpful without the rest of the markup.
  `table-fit` is the wrapper `div` around a `.table`, so a caller told the name
  exists still does not know where it goes — which is what the `EXT:blog`
  session got wrong. It also stands after the change rather than before it, and
  that ordering is the smaller half of the same finding.
- Step 6 of the order and step 1 of **Closing the change** say the same thing.

## What replaces the baseline build

Step 3 came from the `bootstrap_package` session, where an unreproducible build
was the whole defect: Node 24.19 raised `Buffer.poolSize`, fantasticon's EOT
generator wrote 65536 bytes instead of 5828, and the padding was recycled
process heap that differed on every run. Deleting the step without replacing it
takes that class of defect out of the workflow, so two things take it over:

- **The repository's own gate, where it has one.** That session's defect was
  caught by a working-tree-clean check going red, not by anybody building twice
  — and the draft already establishes in step 2 whether such a check exists and
  calls its absence a finding.
- **The untouched tree as a diagnostic rather than a step.** Where the rebuild
  produces a diff that the change does not account for, building the unchanged
  checkout is what separates the two causes. It costs nothing in the ordinary
  case and it is there in the one case it answers.

What this trades away is stated rather than hidden. A maintainer skips the
up-front build because they know what their repository does on a clean run; an
agent in a repository it has never opened does not, and it learns the answer
later and with a change already on top of it.

## What the borrowed-class finding costs

The instruction inverts. A borrowed class is a position in a markup structure
rather than a token, so the query names the **component** and the class is
located inside the answer. Where the entry is withheld on a declared major, the
class answer says the name is written there and nothing about where it goes, and
that is a pointer to the core Sass rather than the end of the step.

It reaches further than the skill, and that part is not settled here:

- `D-CAT-006`'s first **Assumed** is what this refutes — that a caller naming a
  class is asking whether the class exists. The `table-fit` caller was asking
  where it goes, and the class-shaped answer cannot say. A **Since then** on
  that entry records the reading.
- The catalog's own shape carries the same conflation. `table-fit` sits in
  `modifiers` beside `table-striped`, and the entry's markup puts one on the
  wrapper and the other on the table. Nothing in the data tells them apart, so a
  caller reading the modifier list is invited to make the mistake. Whether that
  is repaired, and how far it reaches across the entries, is a catalog question
  rather than a skill one.

## What the review's third finding costs

The two halves are checked differently, which is why one section cannot carry
both. Backend is static: the import map is a file, the build writes files, and
whether they agree is a comparison. Frontend is not: whether a stylesheet
reaches the page is decided by the TypoScript that resolves for a site, so no
file says it.

- The publish step is missing from the order and `public-assets` already holds
  it per major. Below v14 an asset added after the packages were installed is
  not resolvable until they are installed again, and nothing throws — the
  symptom is a 404 in the browser. From v14 a build output directory outside the
  three default paths resolves only once `Configuration/Resources.php` names it,
  and the failure has a name.
- **Nothing here says how a package's CSS or JavaScript reaches a rendered
  page.** Read on 2026-08-24: no hint and no document carries the set, and the
  only thing on it anywhere is the `<f:asset.css>` trap in
  `fluid-layouts-sections`. That is the document `todo/open/2026-08-24-000156`
  writes, and until it exists the frontend half has nothing to route to.
- Backend JavaScript is not bundled. It reaches the browser as ES modules
  through the import map, one specifier per file, so a pipeline written for the
  frontend produces the wrong shape when it is pointed at backend JavaScript —
  and nothing fails in PHP. That belongs in the skill rather than in the
  document, because it is a statement about the shape of the output and not one
  that binds to a version.

## What the research established, 2026-08-21

Read against the two recorded sessions rather than from recall, and each of
these decided a step of the draft:

- `typo3_component_lookup` answers the `table-fit` question the `EXT:blog`
  session shipped unverified, and answers it per major: at `targetVersion` 12
  and 13 the `table` entry comes back **withheld**, saying it was never verified
  there and naming the core Sass partial to check it against; at 14 it answers
  with the markup and the modifier that wraps a table for horizontal overflow.
  So the borrowed-class step is one call per declared major, and a withholding
  is the answer rather than a miss.
- `typo3_icon_lookup` reports `not answerable here` without an installation, and
  answers from the installed one where there is a project. It settles one major
  and no others, which is what the draft says of it.
- `typo3_project_describe` already carries the whole npm surface — both
  manifests, the command list with the manifest each came from and its
  check/change marking, and the Node the manifest, the pin, the CI workflow and
  the container each state with the disagreements named (`D-SCO-014`,
  `ProjectDescribe`). That is the Node difference the `bootstrap_package`
  session's defect turned on, so the call is discharged by the base's first step
  rather than routed to again.
- `typo3_documentation_lookup` reaches the backend JavaScript module pages by
  their titles; a sentence-shaped query covers 41% of itself and says so.
- `typo3_task_guide` with the `EXT:blog` session's own paths returns **no skill
  and no intent**, and six hints — `extension-asset-build`,
  `project-build-and-scripts`, `public-assets`, `backend-ui`,
  `backend-typescript`, `extension-declarative-files`. The surface knowledge is
  therefore already written and the draft restates none of it; what was missing
  is the order and the routing.
- `bin/cli hints:probe "update the npm dependencies and close the dependabot pull requests"`
  now reaches `extension-asset-build`, which is the repair `D-SKL-067` asked for
  in its own commit.
- No document below `knowledge/documents/` carries an asset build as a whole
  procedure, so no step of the draft hands one over.

## What is done

- `skills/typo3-extension-asset-build/SKILL.md`, declaring itself a draft.
- `SkillTest`: its routings in order, `typo3_project_describe` recorded as the
  call it discharges, its commit step, its three crossings, and the note that it
  is construction rather than judgment and keeps no checklist. `composer ci` is
  green.
- The review, given on 2026-08-24 and written above.

## What is left, in the order it goes

1. The frontend half's route, which waits on the document. The rest of the
   rework landed on 2026-08-24, and `D-CAT-008` is what made the borrowed-class
   step answerable at all: `typo3_component_lookup` returns where each class
   sits now, so "verified against every declared major" has something to return.
2. The baseline run `D-SKL-035` buys was read again on 2026-08-24 and is
   arguably already paid: that decision asks for the same task in an environment
   where the skill is not installed, and `feedback/2026-08-19-090200` and
   `feedback/archive/2026-08-18-113501` are two such runs, recorded, one of them
   with two unverified assumptions shipped to `master`. What no run has shown is
   the other side — whether the workflow changes that outcome — which is
   `D-SKL-067`'s first **Wrong if** and needs a session started by hand in a
   current client. Whether to buy it is the maintainer's call, as it was for the
   two skills published on the review alone (`D-SKL-064`).
3. The publishing commit, and all of it in that one commit: delete the
   `metadata` declaration, write the intent that routes to it in
   `knowledge/task-intents.json`, and name the workflow in
   `knowledge/server-scope.json`, or `ScopeTest` fails. That commit also
   archives `feedback/2026-08-19-090231`, whose entry-point half this workflow
   is the answer to and whose card waits beside this one.
4. The listing budget, which is not there. Measured on 2026-08-24: the thirteen
   published descriptions cost 3944 characters against the 3970 ceiling in
   `SkillTest`, and this entry costs 461 more. So publishing means moving the
   ceiling the way `D-SKL-064` did, or shortening descriptions — the question
   `D-SKL-054` puts at publication, about which fourteen are worth their room.
   `D-SKL-064`'s third **Wrong if** is the one this satisfies: the next skill is
   blocked by the ratchet again, so what `D-SKL-026` left open is what has to be
   answered rather than deferred a third time.
