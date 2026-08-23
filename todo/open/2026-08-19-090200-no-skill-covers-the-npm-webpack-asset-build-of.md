# Write the workflow that orders a package's asset build

**Serves:** feedback/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md
**Priority:** normal

Work the review of 2026-08-24 into
`skills/typo3-extension-asset-build/SKILL.md`: the one verification section
becomes a backend half and a frontend half, because the two are checked
differently and only the backend one is written. What each half says is below,
and the frontend one routes to the document `todo/open/2026-08-24-000156` writes
rather than restating it.

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
- The borrowed-class verification stands after the change rather than before it,
  so the class is already written when the answer arrives. Whether that is the
  order the work goes in is the one review question still open.
- Step 6 of the order and step 1 of **Closing the change** say the same thing.

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

1. The rework above. Its frontend half waits on the document, and the rest of it
   does not.
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
