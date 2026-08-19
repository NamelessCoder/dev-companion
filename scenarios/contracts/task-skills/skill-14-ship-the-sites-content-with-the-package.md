# SKILL-14 — Ship the site's content with the package

**Environment:** `E-SITE`, with the site package in the repository beside it and
a second installation of the same version that has never had the package ·
**Contract:** `held` — `D-SKL-050`, `D-SKL-035`, `D-SKL-064`
**Held by:** `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`
and `SkillTest::everySkillStatesWhatItOwns`, which read back the order the
workflow asks its three lookups in and that it says where it stops, and
`SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange`, because the
artifact and the site configuration are that repository's own files. That the
task reaches the workflow at all is **not guarded** and cannot be: no assertion
can hold that a request in somebody's own words reaches a workflow, and only a
run measures it. The skill was published on 2026-08-19 — the draft declaration
is gone, `distribution-content` in `knowledge/task-intents.json` routes to it,
and `SkillTest::everyPublishedSkillIsNamedByAnIntent` holds that route.

> The pages and the content on them are built in my development installation,
> and the site package sits in the repository beside it. I want that content to
> ship with the package, so a colleague installing it on an empty TYPO3 gets the
> same pages, the same content on them and the same images, and the site comes
> up rendering.

**What has to come out of it**

- Content that exists nowhere yet is written by a script rather than clicked
  together, and where what landed is wrong the script is corrected rather than
  the records — the script is what the next version of the artifact comes out
  of.
- What actually landed is read back out of the installation before anything is
  exported. A record takes the default its configuration gives it and not the
  one its column carries, and a page that arrives hidden is delivered to nobody
  while every command involved reports success.
- The export is judged on the artifact and not on the command's message: every
  table the tree holds is part of it, and the files it references are in it as
  files rather than as rows.
- The artifact and its files directory are placed in the package under the names
  the import looks for. Neither arrives there by being asked for.
- The whole directory the site keeps its configuration in travels with the
  package, and only the root page is rewritten to the record that was imported.
- The result is proved on an installation that has never had this package, with
  three things checked there: the records arrived, the site answers on that
  installation's address and renders, and every page the artifact carries
  answers rather than only the one at the root.
- Where the second installation cannot be had, the answer says the proof was not
  run.

**How it fails**

- The content is built by hand in the backend, so the artifact has nothing to be
  regenerated from and the correction of a wrong record is lost by the next
  export.
- The export command reporting success is reported as the export having worked,
  with an artifact that shipped no images or left a table out.
- The artifact is verified by reading it back, or by re-importing it on the
  installation it came from, which remembers the import and does nothing.
- The site configuration is shipped inside the export, or as the one file in its
  directory that is obviously configuration — the receiving installation then
  resolves the site, finds every page, and renders nothing.
- A relation is written pointing at nothing and nothing is logged, so the
  package ships content whose images are attached to no record.
- The proof is reported as done after the root page was opened, or after a
  console check that a page tree exists.
