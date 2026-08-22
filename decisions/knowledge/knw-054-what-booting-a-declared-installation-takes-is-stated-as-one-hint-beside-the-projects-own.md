---
id: D-KNW-054
date: 2026-08-03
status: open
---

# D-KNW-054 — What booting a declared installation takes is stated as one hint beside the project's own

**`installation-boot` in `knowledge/hints/project.json` states what a fresh
clone of a project repository needs before it answers, and the brief for a boot
task selects it first.** The four cards `D-SKL-012` put before the skill landed
the installation that has to be created; this is the one that already exists.

The re-run measured here says the corpus was reached by none of them, and the
hint half of `feedback/2026-08-03-154508` was open behind that.

## Evidence

- The reported call, re-run on 2026-08-03 through this branch's
  `bin/typo3-dev-companion`, at `changeType: unknown` and with no path. The
  intent is `installation-operations` at strong confidence and the checklist is
  the one `D-GUI-008` landed, and the hints were `extension-asset-build`,
  `datahandler-basics`, `fal-basics` and `public-assets` — the four the feedback
  reported, unchanged.
- What had landed by then and was still not reached. `bin/cli hints:probe` with
  the feedback's own query selected none of `installation-setup`,
  `extension-repository-installation`, `project-configuration-files`,
  `sitepackage-initial-content` or `impexp-artifact`. Their vocabulary is the
  install — `typo3 setup`, `--driver`, `app-dir`, `#ddev-generated`,
  `Initialisation/` — and the query's is the boot.
- The statements were read in `.checkouts/` on 12.4, 13.4, 14.3 and main, and
  every one of them holds on all four, so none carries a range.
  `extension:setup` migrates the schema of all active packages before the
  per-package setup and applies `add`, `change`, `create_table` and
  `change_table` only — `InstallUtility::updateDatabase()` on 12.4,
  `PackageActivationService::updateDatabase()` on 13.4, `PackageSetup` on 14.3
  and main, the same four actions in each. `hash`, `pages` and `rootline`
  default to `Typo3DatabaseBackend`, so a dump carries them, and `cache:flush`
  takes `--group system|pages|di` with `all` as the default.
  `backend:user:create` reads the six `TYPO3_BE_USER_*` variables where the
  option is absent, forces the password question even under `--no-interaction`,
  and refuses a username that exists with exception 1670797516, deleted rows
  counted. `SiteMatcher::getRouteCollectionForAllSites()` puts the base's host,
  scheme and port on the route, so a request on another host resolves to a
  `NullSite`; `VerifyHostHeader` throws 1396795884 above it. `LocalDriver`
  resolves `sys_file_storage`'s `basePath` against `pathType` and throws
  1704807715 outside the project root, 1299233097 for a directory that is not
  there.
- `database:updateschema` is in none of the four checkouts. It is
  `helhum/typo3-console`'s, and it is what the DDEV `post-import-db` hook of the
  reporting project runs, which `D-ANS-044` quotes as the boot the session read
  by hand.
- The corpus after the entry: 127 hints, mean body 212 words, longest 702, 88
  words of headroom under the `MAX_MEAN_BODY_WORDS` ceiling. No hint is
  unreachable by its own title.

## Decided

- **One hint rather than a statement added to each of the five.** The subject is
  a sequence: what the schema owes the code decides whether the cache flush is
  worth anything, and the user step is what the site is then opened with. Spread
  over the install hints, a boot query would have to reach all five to get an
  order, and it reaches none of them.
- **Filed in `project.json`, with `scope: project`.** The repository that holds
  an installation is that file's subject, and `project-configuration-files` and
  `project-build-and-scripts` are the two neighbours the boot crosses. The
  installation created below an extension is `extension-repository-installation`
  and stays there.
- **The patterns are the query's vocabulary and not the install's.**
  `ddev start` is deliberately left out — it is `installation-setup`'s already,
  and the boot is named by `ddev import-db`, `ddev pull`, `fresh clone`,
  `start the local environment` and `create a backend user`.
- **What the second run costs is stated beside what the first one does**, which
  is the half a procedure written from one successful boot leaves out: the taken
  username, the settings file the setup command will not overwrite, the
  processed-file records with nothing behind them.
- **The two off-topic hints are left standing.** `datahandler-basics` and
  `fal-basics` still come back for this query, on `import` and on the files, and
  the brief now leads with the boot. Removing them is matcher work on a term
  that is genuinely in both.

## Assumed

- That a boot is asked in this vocabulary. The patterns are one session's
  wording plus the `installation-operations` match list, and no second report of
  this task shape has been read against them.
- That the DDEV half belongs to `typo3_project_describe` rather than here. The hint
  names the hooks as the procedure and points at that tool for them, on
  `R-PRJ-009`, so nothing about a stage or a provider is stated twice.
- That `scope: project` costs nothing where the installation is an extension's.
  A hint's scope is a notice rather than a filter, so the statements still
  answer, labelled as the project's.

## Wrong if

- A session boots a clone with this hint in front of it and still reads
  `.ddev/config.yaml` by hand for the order. The sequence would then be the
  environment's rather than TYPO3's, and what this states belongs in the answer
  `R-PRJ-009` already gives.
- The next boot report names a step this hint has no statement for — the asset
  build, the mail catcher, a search index that has to be rebuilt. The subject
  would be wider than the six statements, and the split `D-KNW-030` asks for is
  what comes next rather than a seventh clause.
- `installation-boot` starts answering an install query, or displaces
  `installation-setup` on one. The two are one subject read from two ends, and
  the patterns are the only thing keeping them apart.
- The skill in `skills/typo3-development-installation/` is published and its
  boot section still routes to no hint of its own. Then the corpus gained a
  statement nothing sends a caller to.

## Covered by

- `HintsTest::bootingADeclaredInstallationIsAnsweredBeforeThePhpFallback`

## Since then

The fourth **Wrong if** fired, and the second report the first **Assumed** was
waiting for arrived with it. `feedback/2026-08-17-212702` is a session on the
create branch of the published skill, which names five hints and none of them
this one, while the boot section it would otherwise have crossed into names no
hint at all. It ruled the entry out by its title, hit exception 1396795884 on
the rebuild that had turned its task into the boot case, and diagnosed that from
24,000 characters of rendered HTML. So the patterns are not what failed here:
the query was never made. `D-KNW-092` carries that judgement, and the hint it
queues is reached from the symptom rather than from the task.

The fourth **Wrong if** is repaired rather than standing. The boot section of
`skills/typo3-development-installation/SKILL.md` names `id=installation-boot` as
its first bullet, before the declared steps are run, and
`SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`
holds it there. What that does not answer is the first **Assumed**: the routing
sends a boot task to the hint whatever it was worded as, so the patterns are
still one session's vocabulary and still unmeasured against a second report.
