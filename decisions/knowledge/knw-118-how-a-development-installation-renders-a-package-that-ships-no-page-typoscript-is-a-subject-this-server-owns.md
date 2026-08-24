---
id: D-KNW-118
title: How a development installation renders a package that ships no page TypoScript is a subject this server owns
date: 2026-08-25
status: open
coveredBy:
  - HintsTest::whereADevelopmentInstallationGetsItsPageObjectIsStated
---

# D-KNW-118 — How a development installation renders a package that ships no page TypoScript is a subject this server owns

**A development installation of a package that ships no page TypoScript is given
a page object deliberately, and where that object lives is what this server
states.**

The corpus names three ways a page comes to render and each of them answers a
different question. A session developing a consent extension read four hints,
found none of them under the question it had, and invented a shape across three
architecture reversals.

## Evidence

- **The probe reaches no statement about it.** `bin/cli hints:probe` on
  2026-08-25 answers "development installation page does not render" with
  `extension-repository-installation`, `extension-repository-tests` and
  `browser-tests`, and "extension ships no page TypoScript" with `site-sets` and
  `site-set-labels-and-layouts`. Every hit is on the body text alone; no
  `appliesTo` entry in the corpus is about a page object that has to be
  supplied.
- **The three mechanisms are here, spread over three hints.** `site-sets` states
  that a site naming a set, or holding a `setup.typoscript` beside its
  `config.yaml`, renders without a `sys_template` row and that a site with
  neither errors out. `installation-setup` states what `--create-site` writes on
  each major. `fresh-instance-seeding` states that a set fills nothing until a
  site names it under `dependencies`. Each answers its own question and none is
  reached from this one.
- **Where the demo page object lives is nowhere.** `export-ignore` occurs below
  `knowledge/` twice: in `extension-ter-release`, about the artefact, and in
  `extension-test-site`, about the core's own `Tests/`. Nothing says where a
  page object that is only for development belongs.
- **The corpus contradicts the feedback's own suggestion.** It says `Build/`
  being export-ignored is what keeps the demo extension unreleased.
  `extension-ter-release` states that what the artefact contains is decided by
  Tailor's `conf/ExcludeFromPackaging.php` and never by `.gitattributes`, so one
  commit hands the TER and a git archive different file sets. Which packaging
  path honours the attribute is a reading rather than a copy.
- **The corpus moved after the feedback was written.**
  [`D-KNW-116`](knw-116-the-page-object-typo3-setup-leaves-behind-is-a-subject-this-server-owns.md)
  landed on `installation-setup` at 23:08 on 2026-08-24, nine hours after this
  report: `14.3` and `main` write `config/sites/<identifier>/setup.typoscript`
  with a welcome `page = PAGE`, `12.4` and `13.4` insert a `sys_template` row
  with `clear = 3` carrying the same object. The installation the feedback
  describes as having nothing that renders a page has a page object already.
- **That turns the shape the session ended at into a collision.** A
  development-only extension shipping a site set with `page = PAGE` is a set,
  and the same entry establishes that on `14.3` the site's own file replaces
  every path it assigns while on `13.4` the clearing row discards what the sets
  built. The feedback names `13.4` and `14.3` as its targets, so the shape it
  recommends fails on both — silently, at HTTP 200 with an empty log.
- **The skill was active and its seeding step is about content.** Step 4 of
  `typo3-development-installation` is "Seed the content the package is to be
  developed against" and routes to `fresh-instance-seeding`,
  `sitepackage-initial-content`, `initial-content-import-once` and
  `initial-content-references`. A page object is none of those, and the session
  says it did not call the first because its description read as being about
  distributions.
- **The neighbouring statement stops one step short.**
  `extension-repository-tests` states that the development setup serving the
  site is part of the extension repository, and `browser-tests-outside-core`
  that the specs run against that served site. Neither says what makes it
  render.
- **One session on this subject.** `bin/cli feedback:list` on 2026-08-25 reads
  34 open across four checkouts, six of them from
  `/home/benji/projects/ext-usercentrics`, and this is the only one asking where
  a page object comes from. Its sibling `2026-08-24-140130` asked what was
  already rendering and is archived.

## Decided

- **Step 1a, and queued.** The pieces are here and the question is not: what is
  missing is the statement that puts them under it, and the half about where a
  development-only page object lives, which is a reading against `.checkouts/`
  this run did not make.
- **Not step 2.** Delivery is the answer where one hint carries the statement
  and the session never reached it. Here three hints carry three answers to
  three other questions, so moving any one of them is not what was needed.
- **The statement starts from `D-KNW-116` rather than beside it.** That entry
  says what is rendering the page after the install; this one says what renders
  the package instead, and the first sentence of it is that the installation
  already has an object to replace.
- **Where the boundary runs.** Inside: which ways a development installation can
  be given a page object that renders the package, which of them survives what
  `--create-site` left on each covered major, and which packaging path drops the
  files that carry it. Outside: what the demo page's template contains, and how
  a repository arranges its own `Build/` — the feedback records two rejections
  of that arrangement, and both are the user's taste rather than TYPO3's.
- **The feedback's suggestion is evidence and not copy.** Its packaging claim is
  contradicted by `extension-ter-release`, and its claim about the directory
  name is a correction the session took in its own project.
- **`normal` rather than the `low` the card arrived at.** What the session
  lacked it would today invent again, and the shape it invents fails on both
  majors it targets without saying so — the silent cost
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  measures, and the same reading that put the sibling at `normal`.
- **Not `high`.** One session reported it, and what is missing is a statement
  rather than a capability.
- **Neither archived nor trimmed.** The statement that landed since answers what
  was rendering, which this feedback did not ask, and nothing below `knowledge/`
  says where a replacement comes from.
- **Nothing holds this yet.** The test declares the id in the commit that writes
  the statement.

## Assumed

- That the caller arrives inside the workflow rather than on a query. The
  session had `typo3-development-installation` active and reached no hint from
  it, which is what puts the lever at step 4 of the skill and makes the hint
  what that step routes to.
- That the three ways the corpus names are the whole list. Whether core lets a
  development installation be given a page object some fourth way is what the
  reading settles, and `D-KNW-096` is the entry whose list grew that way once
  already.
- That the shape is worth stating at all. One session arrived at a
  development-only extension under `Build/`, and its user's rejections shaped
  that as much as TYPO3 did.

## Wrong if

- The reading finds a set can never supply the page object on `14.3` while the
  site's own `setup.typoscript` stands. Then the statement is about removing
  that file first, and this is a paragraph of `D-KNW-116` rather than a subject
  of its own.
- A session given the statement at step 4 still writes the demo page object into
  the released package. Then what was missing is where it lives rather than that
  it is needed, and the packaging half was the whole lever.
- The reading finds nothing to state about the directory an extension is
  developed in, because a development-only package is loaded through a Composer
  path repository that names the package and not the folder. Then that half of
  the feedback is one project's correction and no statement follows from it.
- A second session hits this and reaches for a `sys_template` row by hand
  anyway. Then what was missing is which of the three ways to take rather than
  that there are three.

## Since then

The statement landed on 2026-08-25 as `development-installation-page-object`,
and the reading settled the first **Wrong if** as a step rather than as a
subject: a set does supply the page object, and it supplies it only once what
`--create-site` left is gone, which is `D-KNW-116`'s half and the first sentence
of this one. The list of ways stayed at three, and the reading added what each
of them costs rather than a fourth.

`SetupService::createSite()` writes the site's own `dependencies` on `14.3` and
`main` — `typo3/fluid-styled-content` and `typo3/fluid-styled-content-css` where
`fluid_styled_content` is active — so the development set is appended to a key
that exists. `13.4` writes no such key and puts the same static includes on the
`sys_template` row, so a site there gains the key as well as the entry.

The route that looked like it would spare that edit does not exist.
`ImportSiteConfigurationsOnPackageInitialization` returns before it reads
`Initialisation/Site/` unless `ImportContentOnPackageInitialization` left an
`Import` in the event storage, on `13.4`, `14.3` and `main` alike — so a package
shipping the directory and no data file has it copied nowhere and logs nothing.
`12.4` is the other side of that boundary:
`InstallUtility::importSiteConfiguration()` takes the import as an optional
argument and copies the directory either way.

The third **Wrong if** came out affirmative rather than empty. The directory
does not decide the extension key: `extra.typo3/cms.extension-key` names it,
`Bootstrap::createPackageCache()` reads a Composer installation's packages from
`ComposerPackageArtifact` and scans no directory on any covered major, and
`PackageManager::getPackageKeyFromManifest()` falls back to the directory's own
name up to `13.4` and throws 1348146451 from `14.3` on. So the correction the
feedback's session took in its own project is a convention, and what the
statement says is where the key actually comes from.

The packaging half is stated as two lists. `git archive` honours an
`export-ignore` attribute, measured on a throwaway repository on 2026-08-25: a
`Build/` directory named by the attribute is absent from the archive while
`.gitattributes` itself is in it. The step from there to the dist a Composer
install downloads rests on the source archive a git host builds being that
command, which was not measured — Tailor is in no checkout here either, and the
TER half is `extension-ter-release`'s statement rather than a second reading.
The statement also assumes the demo set assigns `page`; one assigning only paths
the welcome object leaves alone renders beside it, which is `D-KNW-116`'s
reading and is not repeated here.
