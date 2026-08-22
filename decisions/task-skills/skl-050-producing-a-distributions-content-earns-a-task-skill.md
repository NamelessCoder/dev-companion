---
id: D-SKL-050
title: Producing a distribution's content earns a task skill, and the project repository is owned
date: 2026-08-18
status: confirmed
---

# D-SKL-050 — Producing a distribution's content earns a task skill, and the project repository is owned

**Producing the content artifact a distribution ships earns a task skill of its
own.** It owns the seed, the export, and where the artifact and its files are
placed in the package. The site configuration ships beside the export rather
than inside it, and a clean install is what proves the whole. The project
repository half of the same report needs nothing built:
`typo3-development-installation` sequences it in step 5 and did not arrive.

The feedback asks for two skills and names the second one's territory
accurately. The first one's is already written, in the skill the session
activated by hand.

## Evidence

- **The re-run reproduces the routing.** `typo3_task_guide` was called through
  `bin/typo3-dev-companion` from this worktree on 2026-08-18, with the
  feedback's own query and `changeType: feature`. The answer opens
  `Recognized as: Adding or changing a content element` and names one skill,
  `typo3-content-element-development`. `installation-setup` is not named at all,
  weakly or otherwise, though the brief's first unit is a development
  installation.
- **No skill names the producing side.** A search of `skills/` for
  `impexp-artifact`, `datahandler-seeding`, `impexp` and `export` returns
  nothing. The five hints the feedback assembled its sequence from are
  `sitepackage-initial-content`, `initial-content-import-once`,
  `initial-content-references` and `impexp-artifact` in
  `knowledge/hints/distribution.json`, and `datahandler-seeding` in
  `knowledge/hints/datahandler.json`.
- **The installation skill owns the consuming side and stops there.** Its step 4
  seeds the content a package is developed against and routes to three of those
  hints: which command imports the file, why a changed one does not arrive a
  second time, and what the import remaps. Neither of the two hints that write
  the file in the first place is named.
- **The project half is owned by name.** Step 5 of the same skill is "Decide
  what the install wrote into the repository". It routes to
  `project-configuration-files` and `project-build-and-scripts`, and it closes:
  "The ignore rules follow from both answers and are written before the first
  commit, not after the first accidental one." That is the feedback's first gap,
  sequenced, in a published file.
- **The rest of that gap has owners too.** `typo3-extension-testing`'s
  description names Playwright, PHPUnit, PHPStan and php-cs-fixer.
  `typo3-extension-upgrade` owns "proving every version it claims", which is the
  untested PHP floor. The container declaration is named in
  `typo3-development-installation`'s own **Where this stops**.
- **The session's next report contradicts this one's count.**
  `feedback/2026-08-17-213027`, filed three minutes later by the same session,
  says seven of the user's ten findings sit inside
  `typo3-extension-conformance`'s stated scope, and names that scope: TCA,
  content elements, site sets, TypoScript, Fluid, labels, icons. This feedback's
  seven are the ignore file, Playwright, PHPUnit, php-cs-fixer, editorconfig, a
  composer script, a seeding script, the PHP floor and the container PHP
  version. The two lists share nothing, so at most one of them is seven of ten.
  Neither can be checked here, because the ten findings are the user's and sit
  in no file this repository holds.
- **The producing side was reached independently three weeks earlier.**
  `feedback/archive/2026-07-29-180809`, from `/home/benji/projects/site-new`: "I
  regenerated Initialisation/data.xml three times in one session and never once
  imported it ... I verified the artifact by reading the XML and checking that a
  softref pointed where I expected. That is reasoning, not verification." That
  is the terminal proof this feedback says nothing owns, missed from another
  project on another task.
- **The order exists only as neighbour sentences.**
  `sitepackage-initial-content` closes by naming the other three hints and what
  each one adds. `feedback/2026-08-17-211306`, archived, is the same session
  reporting that such a closing sentence is read where the appetite for another
  lookup is lowest.
- **The corpus is one session on this half.** `bin/cli feedback:list` on
  2026-08-18 reports 13 open, all in `/home/benji/projects/site-demo`, all
  `claude-opus-5`, all recorded between 20:59 and 21:30 on 2026-08-17. The
  second arrival is the archived report above rather than a second open card.

## Decided

- **Step 1b for the distribution artifact, and taken on.** The answers are here
  and nothing says in which order to ask for them, which is the half of that
  rung a skill fills.
- **Step 2 for the project repository, and it belongs to another card.** The
  rule is here, in the skill the session activated by hand, and it did not
  arrive. What failed to deliver it is `2026-08-17-213027`'s subject — a
  crossing named in a closing sentence — and that card is already in the queue.
- **One skill rather than the two the feedback proposes.** A description is paid
  for by the other skills and not by its own (`D-SKL-026`), so a thirteenth
  costs the twelve, and the second one would buy a workflow step 5 already
  carries.
- **Where the boundary runs.** Inside: seeding the content with DataHandler
  because nothing exists to export yet, the export and the table and relation
  flags it takes, where the artifact and its files directory are placed in the
  package, the site configuration shipped through `Initialisation/Site/`, and
  the clean install that proves the result. Outside, unchanged: what the import
  does on the receiving installation, which is the installation skill's step 4.
- **The compound brief is its own card.** The guide named one skill for a brief
  naming three units, and `installation-setup` matched none of it. That is step
  3 on an intent and a skill that both exist, it is checkable without reading
  anything about TYPO3, and the new skill landing does not improve it.
- **Priority `normal` on both cards, and what sets it is arrival rather than
  weight.** Two sessions in two projects reached the distribution gap, which is
  not `low`. It is not `high` either: `D-SKL-035` buys a new skill a baseline
  run, and `impexp-artifact` carried two wrong claims into this very session,
  corrected the same day by `D-KNW-080` — so the skill would route to answers
  that were being repaired last night.
- **The feedback is trimmed rather than archived.** Its project half is answered
  above and goes; the distribution half stays open behind the card that builds
  the skill.

## Assumed

- **That the project half is delivery rather than ownership.** Step 5 sequences
  the two project hints, and whether a session that reaches it writes the ignore
  rules from those answers instead of from memory is not established here — this
  session never reached the skill through routing at all.
- **That producing and consuming a distribution are two workflows.** Read as
  one, the work is four steps added to the installation skill's step 4 and no
  new description is spent.
- **That the later of the two counts is the correcting one.** Both come from one
  session thirty minutes apart, and nothing here can read the ten findings they
  divide.

## Wrong if

- The reading finds the producing side is four hints in an order and one verify
  command. Then it belongs on the installation skill's step 4, and this entry
  built a skill for a checklist.
- `2026-08-17-213027`'s card lands, a session reaches
  `typo3-development-installation` from the guide, and the ignore rules are
  still written before the install has revealed what it generates. Then the
  project half was 1b after all and step 5 is not enough.
- The baseline run `D-SKL-035` buys shows a session without the skill seeding,
  exporting, placing and proving in that order anyway. Then the order was never
  what was missing.
- The thirteenth description pushes a skill out of the listing every client
  reads under one budget, and the one dropped is
  `typo3-development-installation`. Then this entry bought one workflow by
  quietly removing the one that owns the other half of the same task.

## Confirmed on 2026-08-18

The producing side was run end to end and the first **Wrong if** did not fire:
it is not four hints in an order and one verify command. Two installations on
14.3.6 below this worktree, one on mariadb to produce the artifact and one on
mysql to receive it, both built by `bin/cli environment:create`. A page tree of
three pages under the root, two content elements, three images and one internal
link, seeded through DataHandler; exported; placed in a package; installed on
the second one from empty.

Four of the steps carry a failure that reports success, and three of them were
paid for in this session rather than read:

- A page takes the default its TCA declares, which is 1 for `pages.hidden`
  against 0 in the schema — `Configuration/TCA/Overrides/pages.php` on 13.4,
  14.3 and `main`, and the column itself on 12.4. The first seed wrote three
  hidden pages, the export shipped them, and the receiving installation answered
  404 on every page below its root while every command involved reported
  success.
- `processRemapStack()` splits a NEW id on its last underscore and reads what is
  in front of it as a table name, on all four covered lines. `NEW_ref1` in a
  relation field therefore substituted nothing: three `sys_file_reference` rows
  were written with `uid_foreign` 0, the parent's field counted 0, and nothing
  was logged. Renamed to `NEWref1` the same single datamap attached all three.
- The site configuration route that keeps the base intact copies the whole
  directory: `ImportSiteConfigurationsOnPackageInitialization` calls
  `GeneralUtility::copyDirectory()`. Shipping only `config.yaml` produced a
  receiving installation that resolved its site, found all four pages, and
  answered 500 with "no TypoScript object of type PAGE" — because `typo3 setup`
  had written `setup.typoscript` beside the yaml on the exporting installation
  and it had not travelled. With that one file shipped as well, all four pages
  answered 200, the images rendered, and the internal link resolved to the page
  it was exported pointing at.
- `--include-related=sys_file` is what admits the bytes, as `D-KNW-080`
  recorded. `--table=_ALL --include-related=_ALL`, which the official page on
  creating a distribution prescribes, added `sys_file_storage` to this tree and
  nothing else.

Two claims of the corpus held as written. `typo3 setup` on the receiving
installation answered "The --distribution and --create-site commandline options
have no effect, when distributions are already active" and let the shipped site
configuration through, with `rootPageId` rewritten to the imported page and
`base` untouched. The import was remembered as
`site_distribution:Initialisation/dataImported`.

What the reading could not settle here is what publication owes. The twelve
published descriptions stand at 3597 characters against a ceiling of 3600, which
is this entry's fourth **Wrong if** arriving as a wall rather than as a
displaced skill — so the skill is published as a draft, the budget is counted
over what a client reads (`D-SKL-054`), and the baseline run `D-SKL-035` buys
and the review `writing-a-skill.rst` requires are the publishing card's.
