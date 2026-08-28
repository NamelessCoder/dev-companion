---
id: D-ANS-044
title: 'The environment answer carries the lifecycle it declares'
date: 2026-08-03
status: open
coveredBy:
  - ProjectTest::theAnswerStatesWhatTheEnvironmentRunsWithoutBeingAsked
---

# D-ANS-044 — The environment answer carries the lifecycle it declares

**`typo3_project_describe` says what its declared environment runs commands on
and nothing of what that environment runs by itself, so
`feedback/2026-08-03-154501` is queued as
[`R-PRJ-009`](../../requirements/project/prj-009-the-project-answer-states-the-lifecycle-its-environment-declares.md).**

What is missing is the shape rather than the knowledge.
[`D-ANS-013`](ans-013-what-runs-a-project-is-a-placement-not-a-missing-answer.md)
put the interpreter into that field and said nothing about the rest of the file
it was read from. The rest of that file is how the project boots.

## Evidence

- The feedback re-run on 2026-08-03 through this branch's
  `bin/typo3-dev-companion`, from `/home/benji/projects/site-demo-typo3-org`,
  the directory it was written in. `typo3_project_describe` opens with
  "composer-project, TYPO3 14.3.5, PHP ^8.3 declared and 8.3 in DDEV", its
  `environment` is
  `{"via":"ddev","php":"8.3","source":".ddev/config.yaml","entered":false}`, and
  its `commands` hold one entry — `composer frontend-builds`. The four fields
  and the one command are what the feedback quotes.
- What the same file holds and the answer does not. `.ddev/config.yaml` states
  `hooks` at four stages: `post-start` runs `composer install`; `post-import-db`
  runs `bin/typo3 database:updateschema`, then `extension:setup` and
  `cache:flush`, then `backend:user:create` with three `TYPO3_BE_USER_*`
  variables in front of it; `pre-pull` fetches and unzips the demo data;
  `post-pull` removes it and restarts, one of the two `exec-host` rather than
  `exec`. Those are the steps the task needed, and every one of them was read by
  hand.
- The pull provider is the other half of the import. Ten files sit under
  `.ddev/providers/`, nine of them marked `#ddev-generated`, and the tenth —
  `dump.yaml` — is the project's own: its `db_pull_command` copies the unzipped
  dump into `.ddev/.downloads/db.sql.gz`, which is what makes `ddev pull dump`
  reproducible.
- This server already opens that file. `Project::ddev()` reads
  `.ddev/config.yaml` and every `.ddev/config.*.yaml` beside it, and takes
  `php_version` out of them and nothing else.
- Nothing else here answers it. A search of `src/` for hooks and providers finds
  one line, and it is about a git `commit-msg` hook.
  `bin/cli hints:probe "DDEV lifecycle hooks post-start post-import-db pull provider bootstrap a project"`
  reaches three PHP hints — `events-extension-points`,
  `project-configuration-files`, `extension-boot-files` — and none of them is
  about a local environment.
- The corpus, read before the card. `bin/cli feedback:list` reports 29 open in
  four directories, two of them from this one. The sibling `2026-08-03-154508`
  is the same session on `typo3_task_guide` and a different gap, so it stays its
  own card. Two more from `/home/benji/projects/ext-guidedtour` —
  `2026-08-03-162858` on `fail_on_hook_fail`, `2026-08-03-162745` on a
  `post-start` install hook — write a DDEV lifecycle rather than read one, which
  makes the subject recurrent and this reading of it single.

## Decided

- Queued rather than closed on the spot. It changes `Project::describe()` and
  the declared `outputSchema` of `typo3_project_describe`, and
  [judging.md](../../documentation/records/judging.rst) puts a schema beyond a
  run that has read only this repository.
- The rung is 1b in kind and neither of the two instances that page names. Not
  1a, because nothing about TYPO3 is missing and the file is the caller's own;
  not 2, because no other tool here delivers it; not 4, because the data is
  absent rather than misworded. What is missing is the shape — and no tool is
  missing either, since the one that owns the shape exists, is called first by
  the `instructions`, and already opens the file. That is repair, which is the
  queued rung rather than the one above it.
- `normal` rather than `low`, on one session and what it counted. A boot read by
  hand end to end is the cost `D-FBK-027` weighs, the field it belongs in
  already exists, and the answer read as complete while the executable half was
  absent — which is the trap that page names beside the round trips.
- The feedback's suggestion is taken in shape, not in wording. It asks for the
  hooks "marked check or change the way the composer commands already are", and
  whether `Project::runs()` can read a hook body at all is part of the reading
  the todo owns: the bodies here run `bin/typo3`, which that rule already
  answers
  `unknown`.
- [`D-ANS-011`](ans-011-a-scope-answer-states-what-a-manifest-declares.md) is
  not crossed. The environment field reads a file that is no manifest already,
  which is what `D-ANS-013` settled, and this adds to that field rather than to
  the manifest ones.
- What the answer says is not settled here. How DDEV merges `hooks` across
  `.ddev/config.*.yaml` and what `override_config` does to them, whether a
  provider is reported at all, how a project's own is told from a
  `#ddev-generated` one, and whether `.ddev/commands/` belongs beside them are
  read from DDEV's own documentation at the version in play.

## Assumed

- That `.ddev/config.yaml` states the hooks the container runs, which is the
  assumption `D-ANS-013` already makes for `php_version`. A `config.*.yaml`
  carrying `override_config: true` replaces rather than merges, and nothing here
  has measured what that does to `hooks`.
- That the session read what it says it read. Its account of the boot — schema
  update, extension setup, backend user, a pull that brings the data — is the
  file, in the file's order.

## Wrong if

- A project answer that names the hooks is followed by a session that reads
  `.ddev/config.yaml` by hand to boot anyway. The lifecycle would then be
  delivered and not taken, which is step 4 and a rewrite rather than a field.
- A project is read whose hooks are not in these files — an `override_config`
  that drops them, or a `.ddev/commands/` carrying what the hooks carry here —
  so an answer built from `config.yaml` states a lifecycle the container does
  not run. `R-PRJ-009` would then be demanding it from the wrong place.
- A second boot reports the same cost in a project whose environment declares no
  lifecycle, the procedure living in its README. The gap would then be the
  README rather than the environment, and this field would be built and unused.

## Since then

The four readings this entry left open were taken from the DDEV on the machine,
since the lock file names none, and the field was built on them.

The hooks of one stage concatenate across every configuration file beside the
first in filename order, and a stage no later file mentions keeps what it had.
The override flag replaces per stage and only for the stages the file carrying
it names, so an empty list under it erased one stage and left the others
standing — which settles the **Assumed**, narrower than it read: the flag never
replaces the whole map.

The providers are reported and DDEV's own are not: only one file extension is
offered, so a recipe is that exact shape, and the generated marker is DDEV's own
signature for a file it replaces while the marker is there.
