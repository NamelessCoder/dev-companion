# What was decided, and on what evidence

A feedback note is deleted by the commit that closes it, and the commit message
says what changed and why. What a commit message cannot carry is the part that
may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

That is what this file is for. One entry per decision worth revisiting, newest
first. An entry is not a changelog line — a change nobody would need to
reconsider does not belong here. When an assumption is later disproved, the
entry stays and gains a **Corrected** line: the wrong assumption is the useful
part, because it names the place where the next one is likely to sit.

## 2026-07-29 — The frontend is recognised by name, and only the two UI sections go

The backend CSS hints were answered for a Bootstrap 5 theme extension, where
every one of them is inverted: treat Bootstrap as legacy, prefer `--typo3-*`
properties, work in both backend color schemes. The paths gave nothing away —
`Resources/Public/Scss/bootstrap.scss` and `Build/Sources/Sass/_variables.scss`
are shaped exactly like core paths, and the second one is one.

- **Decided:** the task text decides. A frontend marker with no backend marker
  withholds the `Backend CSS` and `Backend TypeScript` categories, and the
  answer says which and why. `Scope::isOutsideCore` was the obvious lever and
  is the wrong one: an extension's backend module has backend CSS, and the core
  renders a frontend.
- **Assumed:** words are enough here although R-SCO-1 says they are not for
  `outsideCore`. The difference is the cost of being wrong: withholding leaves
  a caller with a pointer to docs.typo3.org, while the wrong direction hands
  over four confident instructions to rewrite a working theme.
- **Assumed:** naming the categories `Backend CSS` and `Backend TypeScript` is
  worth the churn in every rendered answer. A category label is read on every
  hit, a boundary note only when it fires.
- **Would falsify it:** a core contributor working on the frontend rendering of
  `fluid_styled_content` who loses the CSS hints they wanted. `styleguide` and
  `backend` are the escape, and they are named in the notice.

## 2026-07-29 — The commit workflow is asked for, not inferred

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::isOutsideCore` reads the
paths and the task text. This one does not.

- **Decided:** an argument, defaulting to `core`. A commit message carries no
  paths, and the one thing it does carry — the subject text — describes the
  change, not the repository it lands in. Inferring from it would be guessing
  from prose, which is exactly what R-SCO-1 exists to stop.
- **Assumed:** the caller knows which repository they are committing in, and
  the pointer at the end of every answer is enough for them to find the other
  mode. The default is `core` because dropping rules must be something the
  caller asked for, never something a typo achieves.
- **Would falsify it:** agents committing in a project repository and never
  passing the argument, so the hard `missing-issue` error stays the normal
  answer there. The next step would then be for `typo3_task_guide`, which does
  compute `outsideCore`, to hand the workflow to the commit guide by naming it
  in the follow-up tool call it suggests.
- **Assumed:** `[SECURITY]` stays refused for core work. The keyword exists in
  the core's history — the Security Team writes those commits — so its absence
  from the enum was a gap, but accepting it for a contributor would be a wrong
  answer with worse consequences than a missing one.

## 2026-07-29 — A missing release target becomes a placeholder, not `main`

`typo3_commit_message_guide` appended `Releases: main` when the caller named no
release, and warned `missing-releases` in the same answer. Two ways out: stop
warning, or stop filling in. It now fills in `RELEASE_TARGET` and keeps the
warning, the way a missing issue has always produced `Resolves: #ISSUE_NUMBER`.

- **Decided:** the draft states what it does not know, in the place where the
  answer belongs. A placeholder is visible in a `git commit` editor and in a
  diff; a plausible default is not, and `main` is the wrong answer for every
  backport.
- **Assumed:** the checks are read. A caller that copies the draft without
  reading them now commits `Releases: RELEASE_TARGET`, which is a worse commit
  message than `Releases: main` would have been — but a visibly broken one
  rather than a quietly wrong one, and Gerrit rejects it.
- **Would falsify it:** the placeholder showing up in a pushed commit. Then the
  guide would have to refuse the draft outright instead of marking it.

## 2026-07-29 — What is core-only is decided per line, by what it names

`typo3_task_guide` now drops core-only material outside the core. What counts
as core-only is not a flag on each entry but a check on its text: does it name
something that exists in the core repository and nowhere else —
`typo3/sysext/`, `Build/Scripts/`, Gerrit, a Change-Id, the core branch policy.

- **Decided:** a mechanical check over the rendered line, in `Scope`, applied
  to the checklist, the checkout discovery steps and the follow-up tools. The
  alternative — marking every checklist item, every intent item and every scope
  entry in the knowledge files — is a flag on a hundred strings that has to be
  set correctly each time one is added, and forgetting it fails silently.
- **Assumed:** naming a core artefact is a reliable proxy for being unusable
  outside the core, and the cost of the two error directions is asymmetric: a
  transferable line dropped because it mentioned a core path as an example is a
  smaller loss than an unrunnable command handed over as a step.
- **Would falsify it:** a checklist item that has to survive although it names
  a core path — advice about reading the core as a reference rather than
  changing it would be exactly that. It would then need the flag after all.

## 2026-07-29 — A core-only intent asks for evidence, not for silence

`outsideCore` was to be the gate for the patch submission intent. It is not
enough: the reported task — "Maintain and extend the third-party TYPO3
extension bk2k/bootstrap-package … review TCA …" — does not trip a single
outside-core marker, because "third-party TYPO3 extension" is not the phrase
the list carries. Gating on the flag alone would have left the note's own case
answered exactly as before.

- **Decided:** a core-only intent needs positive evidence — a `typo3/sysext/`
  path, or Gerrit, Forge, "TYPO3 core" named outright. Outside the core it is
  dropped, and where nothing says either way it is demoted to the conditional
  match the catalog already models, so the answer offers it rather than states
  it.
- **Assumed:** `coreOnly` is a property of the intent, not of the task, and
  patch submission is currently the only one. Deprecation, breaking change and
  changelog were considered and left alone: their subject is real work outside
  the core too, and it is their `checks` that are core-only, which R-SCO-2
  handles.
- **Would falsify it:** a core contributor whose task text names neither a
  sysext path nor Gerrit and who now gets the submission rules as conditional
  rather than as fact. That is the cost of not guessing, and the condition line
  is what keeps it cheap.

## 2026-07-29 — Outside the core the test guide declines rather than adapts

`typo3_test_run_guide` recognises work outside the core and now returns no
suite at all, while `typo3_architecture_lookup` keeps its hints and drops only
their check commands.

- **Decided:** the difference between the two is what the payload is made of. A
  hint is a convention and travels; a suite is a command against a script that
  lives in the core repository and does not.
- **Assumed:** nothing in `knowledge/` describes how an extension runs its
  tests, so anything the guide offered instead would be invented on the spot —
  and an invented command is the failure the note reported, one level further
  along. A pointer to https://docs.typo3.org/ is the honest answer until that
  knowledge exists.
- **Would falsify it:** extension testing conventions in `knowledge/`. The
  guard then becomes a branch to the other corpus rather than a decline, and
  this entry is what says why it was ever a decline.

## 2026-07-29 — A label query is words, and the console is asked with a regex

`language:domain:search --search=` matches one literal string, so the words of a
query had to be recomposed into something the console can answer at once.

- **Decided:** the words go over as `--regex=/(one|two)/i` — the union — and the
  intersection is taken here. One call per query rather than one per word,
  because a console call boots TYPO3, and the union is also what makes "save
  alone matches 65 labels" answerable without asking a second time.
- **Assumed:** `--regex` is available wherever the command itself is. It was
  added in the same commit as `--search`, `--json` and the command
  ([TASK] Add CLI command and service to search labels, on 14.0 and later), so
  an installation that answers the one answers the other.
- **Assumed:** matching is a plain case-insensitive substring on both sides, not
  a word boundary as elsewhere in this server. A trans-unit id is
  `labels.save_document` and an underscore is a word character, so anchoring
  would drop exactly the ids a caller searches by.
- **Assumed:** a console that exits 0 without a JSON payload found nothing.
  That is what this command does — it prints `[WARNING] No language resource
  files found.` and returns SUCCESS — and no other command this server calls
  answers `--json` with anything but JSON.
- **Would falsify it:** a command that exits 0 and prints nothing usable for a
  reason other than an empty result; the answer would then be a confident "none"
  where nothing was established. The exit code is the only signal being read.

## 2026-07-29 — The unanswered result keeps its shape and gains a reason

Two notes asked for the unavailable case to stop looking like an empty one. One
proposed dropping `matchCount`, `icons`, `found` altogether and returning an
error-shaped object instead, or renaming `answeredBy: "nothing"` to something
that cannot be read as "no source had it".

- **Decided:** the shape stays, an `unavailable` object carries the reason, and
  `found` is null rather than false when nothing was consulted. A field a schema
  requires has to be present on every path through the tool, and dropping keys
  in one case would make the declared output schema a shape a client cannot
  rely on — which is the same defect one level up.
- **Assumed:** a caller that reads `unavailable.reason` is better served than
  one that has to interpret an enum value, so renaming `nothing` buys little
  and breaks every client that already matches on it.
- **Would falsify it:** clients ignoring `unavailable` and still reading a miss
  as a registry answer. `isError: true` on the result is then the next lever —
  bluntest, and it would make the answer an error rather than an answer, which
  is why it was not taken first.

## 2026-07-29 — Three audiences, and the positioning that has not caught up

The server is to serve core contributors, extension authors and site developers
(R-AUD-1 to R-AUD-4). What it currently *contains* is core knowledge, and what
it says about itself matches that: `knowledge/server-scope.json` opens with "a
curated knowledge base for contributing to the TYPO3 core".

- **Decided:** the outward description stays core-first until there is
  non-core knowledge to describe. A promise is made when it can be kept; the
  requirement is the record that it is meant to be.
- **Assumed:** the boolean `outsideCore` cannot carry this. An audience has at
  least three values and an honest fourth — unknown — and the flag was written
  when "not core" was the only distinction that existed.
- **Assumed:** the audience is not readable from the checkout alone, because
  extension development happens inside site installations. Any detection that
  keys on the installation kind alone will be wrong for that case, which is a
  common one rather than an edge.
- **Would falsify it:** if a signal turns out to identify the audience reliably
  on its own — the presence of `typo3/sysext/` in the touched paths comes
  closest — the combining logic is unnecessary complexity.

## 2026-07-29 — Discovery honours the declared vendor-dir and bin-dir

Closes the notes about `.build/bin/typo3` being unreachable and about the
extension checkout not being recognised as an installation at all.

- **Assumed:** what the root `composer.json` declares is enough to find both the
  packages and the console. Composer's `config.vendor-dir` and `config.bin-dir`
  are the only two ways either moves in practice, and everything else — DDEV,
  the interpreter choice — was already right and simply never got a binary.
- **Assumed:** invoking the console through a path relative to the installation
  root works inside DDEV as it does on the host.
- **Evidence:** `bootstrap_package` on this machine — 21 packages found,
  console resolved as `ddev exec -- .build/bin/typo3` on PHP 8.5, and the 29
  `content-bootstrappackage-*` icons that were previously reported as
  non-existent.
- **Would falsify it:** an installation whose console is invoked from somewhere
  other than the root — a DDEV project whose container working directory is the
  docroot rather than the project root would need an absolute path or a `cd`.
  Also an absolute `bin-dir`, which is accepted by Composer and ignored here.

## 2026-07-29 — The root package counts as an installed package

- **Assumed:** in an extension development checkout, the extension being edited
  is the root package and is meant to be part of every answer about "this
  installation" — its icons and labels are as registered as any dependency's.
- **Assumed:** a root package alone is not an installation. The root is only
  added when Composer's metadata yielded packages, so an extension repository
  whose dependencies were never installed still reports no installation rather
  than one holding a single package and no console.
- **Would falsify it:** a monorepo whose root declares a TYPO3 package type but
  is not the thing being worked on, or a setup that installs the root into the
  vendor directory as well — the two entries then resolve to the same realpath
  under one key, which is intended, but has not been seen in the wild here.
