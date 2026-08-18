---
id: D-SKL-056
date: 2026-08-18
status: open
---

# D-SKL-056 — The installation workflow branches on the declared procedure and proves what the run wrote

**The installation skill branches on whether the repository declares a boot
procedure, and its closing obligations follow what the run wrote into the
repository.**

Both discriminants name something else today — the traces an installation leaves
in a repository, and who authored the sequence — and a repository that declares
an environment and nothing else falls on the wrong side of each.

## Evidence

- The feedback, read whole. `t3g/blog` declares `.ddev/config.yaml` and a
  document root, and its hooks are an empty array, its manifest has no install
  script, and its lock file and `config/` are ignored. The session took the boot
  branch, ran out of declared steps after starting the environment, and composed
  everything from the install onward out of the create branch.
- The fork as it stands is a disjunction of traces: "an environment
  configuration, a document root, a site configuration, a lock file". Every
  bullet under it presupposes a sequence — "Run the declared steps in the
  declared order", "the finding is which declared step failed". One trace is
  enough to enter a branch that then has nothing to run.
- The closing section splits on authorship and asks a session that wrote the
  sequence for three more things: a re-run from a clone with "no installed
  dependencies, no installation, no container", a second start, and a commit
  message drafted with `typo3_commit_message_guide`. The reporting session wrote
  the sequence and committed nothing — `git status --short` was empty, because
  `.build/`, `config/` and `var/` are ignored — so the message had no subject
  and the re-run would have destroyed the installation that had just been asked
  for. The boot branch states that case two paragraphs above: "an installation
  that was asked for and then destroyed is a change nobody asked for".
- The rung is not the corpus. `bin/cli hints:probe` on 2026-08-18, asked in the
  reported repository's own terms, returns `installation-setup`,
  `project-build-and-scripts`, `project-configuration-files`,
  `installation-boot` and `environment-variables`. The skill was reached on its
  description alone and every hint id it names inline was fetchable and correct,
  which the feedback states first. Delivery and routing worked; the wording is
  what is left.
- `bin/cli feedback:list` on 2026-08-18 reports 35 open in two directories, 32
  of them in `/home/benji/projects/blog`. `074606` is a second task shape out of
  that directory reaching the same edge from the other end — an installation
  that is up and answers wrong, whose nearest section is this same closing one.
- `D-SKL-012`'s third **Assumed** reads booting and creating as sharing the
  install sequence and differing "in the first step". This is the first report
  of one repository standing in both.
- `SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`
  asserts both branch headings and
  `## Prove it, and how far depends on who wrote the sequence` verbatim, so the
  wording and its guard move together.

## Decided

- **Step 4 of the ladder, and queued rather than closed on the spot.** The
  change is the structure of a published skill and the assertions that hold it,
  which is reviewed rather than improvised.
- **The fork asks what procedure is declared.** A repository declaring one is
  booted from it. One declaring an environment and no procedure runs what it
  declares and takes the rest from the create branch, changing nothing that is
  declared.
- **The closing obligations follow what the run wrote into the repository.**
  Where every path the install wrote is ignored, the unattended re-run and the
  commit message have no subject; the site answering is the whole proof, and the
  report says why the other two were not owed.
- **No third section.** A third branch restates the create branch's steps, and
  what a skill costs is paid by every session that loads it — `D-SKL-052`.
- **Priority `normal`, set by two task shapes in one directory reaching the same
  fork** and by the file being a copy no release of this server corrects. Not
  `high`: one session series, one repository, and the task was finished.
- **`074606` keeps its own card.** It asks whether an installation that is up
  and answering wrong has an owner at all, which is rung 1b, and folding it in
  would hide that question behind a rewrite.

## Assumed

- That the repository is as reported. Nothing here reads that checkout, so its
  shape rests on the account and only the skill's own wording was read.
- That declaring an environment and no procedure is a shape rather than one
  repository's peculiarity. One report says so.

## Wrong if

- A session in a repository declaring only an environment reads the re-cut fork
  and still reconciles two branches. The pair would be wrong rather than the
  discriminant, and the third shape is a branch of its own after all.
- A session that committed its setup skips the second start because this run
  wrote nothing. The discriminant would be read as what happened today rather
  than as what the repository now carries.
- A repository with a full boot procedure is sent through the create branch's
  steps. The condition would be catching more than it was written for.

## Covered by

- `SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`

## Since then

The third shape carries a second cost, found by reading the re-cut fork against
`feedback/2026-08-18-071435` — the same reporting series, on the repository this
entry was written from. What sends such a session into the create branch is also
what skips its first two steps: they are the steps the repository already
declares, and "change nothing that is declared" is the clause that passes over
them. Step 1 is where `extension-repository-installation` is named, and the
layout it answers for is what the session probed by hand. `D-SKL-058` routes the
boot branch to that hint on what the repository is, which leaves the fork as
this entry cut it.

The last **Decided** was reversed on 2026-08-18. `074606` no longer keeps its
own card: `D-SKL-059` decides the 1b question it asks — an installation that is
up is `typo3-development-installation`'s own — and folds the card into the one
that carries that work. The reason this entry gave still holds and is what
changed: the card was kept so a rewrite would not hide the question, and it is
folded into the card that answers it rather than into a rewrite.
