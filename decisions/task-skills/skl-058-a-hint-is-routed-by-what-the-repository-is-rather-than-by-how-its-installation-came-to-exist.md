---
id: D-SKL-058
date: 2026-08-18
status: open
---

# D-SKL-058 — A hint is routed by what the repository is rather than by how its installation came to exist

**The boot branch of `typo3-development-installation` routes to
`typo3_hint_lookup` with `id=extension-repository-installation`, because that
hint's subject is an extension repository with TYPO3 beneath it and not the act
that put it there.**

The id is named in the create branch alone, so a session that correctly took the
boot branch reads which question it answers and rules it out by where it is
filed.

## Evidence

- The feedback, read against the skill as it is now. Step 1 of
  `## Create one where none is declared` still names the id with the description
  the report quotes, closing on "why the extension directory below the document
  root is empty rather than broken" — which is the question the session hit, on
  the branch it was not in.
- The session ran `ls .build/public/typo3conf`, got no such directory, and
  settled it from what `typo3 extension:setup` had printed. The hint's fourth
  statement is the answer: the root package is loaded from the Composer root,
  its `Resources/Public/` is published as a symlink up into it, and nothing is
  installed into `typo3conf/ext/` in a Composer installation, which is not a
  broken install.
- **The re-cut fork does not close it.** `D-SKL-056` landed after the feedback
  was filed and put the reported shape — an environment declared and no
  procedure — into both branches: "run what it declares, take every step after
  that from the create branch, and change nothing that is declared". Steps 1 and
  2 are what the repository already declares, so the sentence that sends the
  session into the create branch is also the one that skips the step naming the
  id.
- **The document the boot branch reads first does not carry it either.**
  `knowledge/documents/project/installation/booting-a-clone.md` is `D-KNW-095`'s
  and is the branch's opening bullet. Its eight steps are the environment, the
  data, the schema, the login and the two requests; `typo3conf`, `.build` and
  the root package appear nowhere in it, and its `hints:` front matter names
  `installation-boot` and `installation-setup`.
- **The corpus answers the symptom and the boot query misses it.** On
  2026-08-18,
  `bin/cli hints:probe "extension repository typo3conf/ext is empty below the document root after composer install, is the layout right"`
  returns `extension-repository-installation` first at
  `appliesTo(22) + text(566)`. Asked in the task's own words —
  `bin/cli hints:probe "boot the local DDEV development installation of an extension repository from a clean checkout"`
  — it does not come back at all: `extension-repository-layout`,
  `extbase-domain-mapping`, `project-configuration-files`,
  `extension-boot-files` and `installation-boot` do. So the matcher closes it
  from the symptom and not from the task, and the session made neither call.
- `extension-repository-layout`, which that boot query reaches first, already
  points onward to `extension-repository-installation` — for the Composer keys
  and the two that fail quietly, and not for the layout a booted repository is
  being looked at through.
- **One id rather than a class of them.** The other create-branch ids are about
  the act: `php-versions` is the interpreter a boot finds declared,
  `environment-runtime-readers` and the seeding ids are the install and the
  import, which the document covers from the boot side, and
  `project-configuration-files` is named again in the shared section on the
  environment's generated settings. `extension-repository-installation` is the
  only one whose subject is the repository.
- The statements are settled. `D-KNW-053` read all four off a built root package
  and `R-KNW-064` is what keeps them answered, so nothing about TYPO3 is open
  here.
- **Convergence is not what carries this.** `bin/cli feedback:list` reports 29
  open on 2026-08-18, 26 of them from `/home/benji/projects/blog` and one
  debrief, so the evidence is one session series. `D-SKL-056` is the first
  finding against this fork and this is the second.

## Decided

- **Step 2 of the ladder, delivery.** The statement is here, verified, reachable
  and named in the file the session had loaded — in the half its task did not
  pass through. What is missing is placement, not a statement and not a route
  that does not exist.
- **Queued rather than closed on the spot.** The change is a published skill's
  contract, which `documentation/records/judging.rst` puts on the todo side of
  that line, and `SkillTest` asserts the branch headings the bullet lands under.
- **The condition is what the repository is.** The bullet reads "where the
  repository is an extension with TYPO3 installed beneath it", so it fires on
  the layout the caller is looking at rather than on who created it.
- **Rejected: stating it in `booting-a-clone` instead.** That document is the
  order a project clone is brought up in, and where the extension's own manifest
  is the Composer root is not one of its steps. The session also did not fail to
  find the id — it found it and was told where it belongs, and only the file
  that told it can say otherwise.
- **Rejected: splitting the hint.** Three of its four statements are about
  writing the manifest and the fourth is about reading the result, but they are
  one subject: `D-KNW-047` put them here rather than into
  `project-build-and-scripts`, and `D-KNW-053` verified them together. A hint is
  fetched whole, so a boot caller reads three statements about a manifest it is
  not writing — cheaper than a second entry to keep true.
- **Rejected: a line above the fork.** What a skill costs is paid by every
  session that loads it — `D-SKL-052` — and the create branch already carries
  the id. Only the boot branch is missing it.
- **Priority `normal`.** The file is a copy no release of this server corrects,
  and this is the second finding against the same fork; not `high`, because one
  session series reported it and its task finished.

## Assumed

- That the fourth statement is what the session needed. That is read off the
  hint's text and the report's, not from a run that fetched the hint at the
  moment the `ls` failed.
- That a repository which is itself the extension and declares its own boot is a
  shape rather than one project's peculiarity. One report describes it.
- That a boot caller sent to the whole hint is not slowed by the three create
  statements. Nothing has measured one reading it.

## Wrong if

- A boot session with the bullet in front of it still probes the layout by hand.
  The id would then be delivered and unread, which is wording rather than
  placement, and what the branch owes is the statement instead of the route.
- A repository that declares a full procedure and is not an extension is sent to
  the hint anyway. The condition would be catching more than it was written for.
- The next report says the three create statements got in the way of the one
  that answered. The split declined here is what comes next.
- `extension-repository-layout` turns out to be what such a session reaches,
  with its closing statement widened to the layout. The route would belong in
  the corpus rather than in the skill.

## Covered by

- `SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`
