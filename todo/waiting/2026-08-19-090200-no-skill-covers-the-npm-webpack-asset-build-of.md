# Write the workflow that orders a package's asset build

**Serves:** feedback/2026-08-19-090200-no-skill-covers-the-npm-webpack-asset-build-of.md
**Priority:** normal
**Waiting on:** the review `writing-a-skill.rst` makes a condition of
    publishing. The draft is `skills/typo3-extension-asset-build/`, and what it
    needs is asked by name rather than as "does this look good": is this the
    order the work actually goes in, which step is missing, which one is wrong,
    what does it claim that is not true in these repositories. Nothing here can
    answer it — both sessions the workflow is written from ran elsewhere, and no
    session in this checkout has maintained one of these builds. Reading the
    draft where it loads is
    `bin/typo3-dev-companion install --agent=claude --drafts` in the project,
    which `documentation/usage/installing.rst` describes.

Judged 1b on 2026-08-21 — the shape is missing. `D-SKL-067` holds the evidence,
the boundary the workflow owns and what it stops at.

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

## What is left, in the order it goes

1. The review above, worked in before anything else.
2. The baseline run `D-SKL-035` buys: the case prompt in an `E-EXT` checkout the
   installer was not run in. It needs a session started by hand in a current
   client, the way `todo/waiting/2026-08-05-183000` does — nothing here can
   drive one. Two skills were published on the review alone on 2026-08-19
   (`D-SKL-064`), so whether this one waits for a run is the maintainer's call
   and worth asking together with the review.
3. The publishing commit, and all of it in that one commit: delete the
   `metadata` declaration, write the intent that routes to it in
   `knowledge/task-intents.json`, and name the workflow in
   `knowledge/server-scope.json`, or `ScopeTest` fails. That commit also
   archives `feedback/2026-08-19-090231`, whose entry-point half this workflow
   is the answer to and whose card waits beside this one.
4. The listing budget, which is not there. Measured on 2026-08-21: the thirteen
   published descriptions cost 3966 characters against the 3970 ceiling in
   `SkillTest`, and this description costs 462 more. So publishing means either
   moving the ceiling the way `D-SKL-064` did or shortening descriptions — the
   question `D-SKL-054` puts at publication, about which fourteen descriptions
   are worth their room.
