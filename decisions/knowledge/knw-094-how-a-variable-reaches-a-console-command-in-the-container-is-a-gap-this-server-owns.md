---
id: D-KNW-094
title: How a variable reaches a console command in the container is a gap this server owns
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::theLineThatCarriesAVariableIntoTheContainerIsAnswered
---

# D-KNW-094 — How a variable reaches a console command in the container is a gap this server owns

**The corpus names the variables `typo3 setup` reads and never says how one
reaches the command inside the container, so the feedback is queued.**

[`D-KNW-046`](knw-046-the-non-interactive-install-path-is-a-gap-this-server-owns.md)
is the same subject one question shallower: what the command takes and what it
refuses. The session it was written for had that answer, read it as correct and
useful, and then spent two failed round trips on the line that carries those
variables into the web container.

## Evidence

- The miss reproduces on the server as it is now. `bin/cli hints:probe` with the
  feedback's own task returns `environment-variables`, `installation-setup`,
  `environment-runtime-readers`, `project-configuration-files` and
  `installation-boot`; the first three name the `TYPO3_DB_`, `TYPO3_SETUP_` and
  `TYPO3_BE_USER_` families, and none of the five names an invocation. `bash -c`
  and `web_environment` appear nowhere below `knowledge/` or `skills/`.
- The server hands the caller the form the variables do not survive.
  `ProjectDescribe::whereTheyRun()` answers `"ddev exec <command>"` for
  everything that is not a composer script, and the `operations` checklist says
  commands typed beside the lifecycle hooks are what boots the installation. A
  session following both types `ddev exec TYPO3_DB_DRIVER=… typo3 setup` and
  gets DDEV's error rather than TYPO3's.
- The shell on that transport is measured in this repository and kept where no
  caller reads it. `Typo3Cli::pastTheShell()` records against DDEV v1.25.1 on
  2026-08-02 that `ddev exec` joins its arguments and hands the line to bash
  inside the container, which is why every argument on that transport is escaped
  and why `typo3_label_lookup` answered from the package files there for weeks.
  It is a comment in `src/`.
- Two sessions name the missing line and a third pays on the same surface.
  `feedback/2026-08-18-070423` reports three attempts —
  `ddev exec -e VAR=… typo3 setup` refused for an unknown shorthand flag,
  `ddev exec --raw=false "VAR=… typo3 setup"` stat'ing the whole string as one
  binary name, and `ddev exec bash -c "VAR=… typo3 setup --no-interaction"`
  succeeding. `feedback/2026-08-18-070538` asks for a boot document and writes
  that exact `bash -c` line into the list of what it would carry.
  `feedback/2026-08-18-080743` reports a `--filter A|B` whose pipe the shell ate
  inside `ddev exec`.
- The two variable families are both written and never contrasted where a caller
  meets them. `knowledge/hints/testing.json` names `typo3DatabaseDriver` and its
  four siblings for a functional run; `environment-runtime-readers` names the
  `TYPO3_DB_` family for the setup command. That the first family is what a
  `.ddev/config.yaml` commonly carries under `web_environment`, and that it
  configures nothing for setup, is stated nowhere.

## Decided

- Step 1a for both halves. The invocation is the one the session paid for, and
  the family a `.ddev/config.yaml` carries is what makes the caller believe the
  container is already configured — a caller reads that file and the hint in one
  breath, so they are one entry and one card.
- Queued rather than closed on the spot. What holds about `ddev exec` is outside
  this repository, and this run read this repository and started no container.
- The reading settles a contradiction rather than confirming a suggestion. This
  repository records that `ddev exec` hands its line to bash, and the feedback
  records a string that was stat'ed as one binary name — so what `--raw`
  actually switches, and whether an assignment prefix needs `bash -c` at all, is
  the first thing to measure. The feedback's suggested line is not copied into
  `knowledge/` on its author's word.
- The environment made here is where it is measured, not the reporting session's
  project. `bin/cli environment:create E-SITE` is that environment and
  `D-EVI-004` is why.
- Where the statement lands is decided with the reading. `installation-setup`
  names the variables and is where a setup question arrives; `installation-boot`
  is where a clone is brought up. The boot document `feedback/2026-08-18-070538`
  asks for may take it instead, and that card is unjudged and stays where it is.
- `normal` rather than `low`. One session's suggestion is a suggestion; this
  line is named by two and the surface is paid for by a third.
- Not step 5. `doesNotCover` excludes operating an installation, and this is the
  transport the corpus already commits to in the `operations` checklist, in
  `project-configuration-files` and in what `typo3_project_describe` answers.

## Assumed

- That DDEV is the environment the caller is in. The corpus assumes it
  throughout, and `D-KNW-045` carries the same assumption for the document root.
- That a DDEV statement is written undated, as every DDEV statement in
  `knowledge/` is today. No hint names a DDEV version, and binding by TYPO3
  major says nothing about a tool with its own release cycle.
- That `web_environment` carrying the functional-testing family is a common
  habit rather than one repository's. The feedback saw it in the `t3g/blog`
  repository, and this run read no second `.ddev/config.yaml`.

## Wrong if

- The reading finds that an assignment prefix survives a plain `ddev exec`. Then
  the statement is about the two flags that do not do what their names suggest,
  not about a form the caller is missing.
- A boot session still types `ddev exec VAR=… typo3 setup` after the statement
  lands. Then it was never the hint but the answer `typo3_project_describe`
  gives, and `whereTheyRun()` is where the fix goes.
- No second repository puts the `typo3Database` family into `web_environment`.
  Then that half is one project's habit, and what is left is the invocation.
- DDEV changes what `ddev exec` does with its arguments. An undated statement
  about somebody else's tool goes stale with nothing failing, which is the cost
  of the second assumption above.

## Confirmed on `2026-08-18`

The gap was real and the statement is now in `installation-setup`. What the
reading changed is what the statement says: the first **Wrong if** fired.
Measured in an `E-SITE` on TYPO3 14.3 made here, against DDEV v1.25.1, an
assignment prefix survives a plain `ddev exec` —
`ddev exec TYPO3_CONTEXT=Development vendor/bin/typo3 --version` answers with
the Development context, and `ddev exec MEASURE=one printenv MEASURE` prints
`one`. So the caller was never missing a form. `ddev exec bash -c` earns its
line for one case only, a value the join would quote, which is why it is in the
second statement rather than the first.

What the session paid for is `--raw`, and the flag is stranger than the feedback
could tell from one attempt. `--raw`, `--raw=true` and `--raw=false` all take
the same path: the arguments are handed to the container as they stand, and
`MEASURE=one` is looked for as a program. Typing the flag is what switches, not
the value given to it — and its own `--help` says it defaults to true while the
default interprets with bash. That is why the feedback's
`--raw=false "VAR=… typo3 setup"` was stat'ed as one binary name.

The reading also confirms what `Typo3Cli::pastTheShell()` recorded rather than
contradicting it. `ddev exec echo '/(save)/i'` still comes back with bash's
syntax error, quoting the line it built — `set -eu && ( … )` — so there is a
shell on that transport, and every argument is still spent through it.

The third **Wrong if** does not hold: `bootstrap_package` carries the same four
`typo3Database` variables against `t3func` under `web_environment` as the
repository the feedback read, so that half is a habit rather than one project's.
Two other DDEV projects on the same machine carry the `TYPO3_DB_` and
`TYPO3_SETUP_` families there instead, which is why the statement says what
those lines mean rather than what a `.ddev/config.yaml` always holds.

The second **Wrong if** is untouched and stays the thing to watch:
`ProjectDescribe::whereTheyRun()` still answers `ddev exec <command>`, which is
now the right answer, and a boot session that still types the variables the
wrong way after this would be a fault in that answer rather than in the hint.
