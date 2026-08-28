---
id: D-ANS-052
title: The configuration lookup answers for the installation as it stands
date: 2026-08-04
status: open
---

# D-ANS-052 — The configuration lookup answers for the installation as it stands

**`typo3_configuration_lookup` reads the installation in the environment it is
in, and its description says so; whether it may be asked under a stated
environment is a question for the maintainer.**

A session auditing a documented environment contract called it zero times and
used the console six, and was right to.

## Evidence

- `feedback/2026-08-04-180112`. What had to be proven was conditional:
  `SYS/displayErrors` falling back to `-1` with `IS_DDEV_PROJECT` unset,
  `SYS/trustedHostsPattern` resolving through `settings.php`, the DDEV block,
  `.env`, `.env.local` and a real environment variable in that order, and
  `DB/Connections/Default` picking up `TYPO3_DB_HOST`. Each run needed its own
  environment.
- None of that is expressible as "read path X from the installation", which is
  what the tool answers. The session reached for
  `vendor/bin/typo3 configuration:show` and reported the boundary rather than a
  defect.
- The session's own reading is what makes this worth an entry: from the call log
  the run looks like one that ignored the tool. Nothing in the description says
  what it cannot be asked, so the next session spends the call to find out.

## Decided

- The description states the boundary: the value is read in the environment the
  installation is in, and a value that has to be shown resolving under another
  one is the console's. **Queued**, because it moves a declared tool surface.
- The `env` map the feedback proposes is **not decided here**. It would make
  "prove this variable reaches this path" one call for a recurring audit shape,
  and it would also make a lookup run the installation under an environment the
  caller composed — the first thing this server does that is neither reading nor
  the caller's own console.
- That half is put to the maintainer rather than built or dropped quietly. One
  session, one task shape: the corpus does not carry the weight to decide it
  either way, and `D-FBK-027`'s measure — what it takes off the caller — is six
  console runs in one session against a capability nothing else here has.

## Assumed

- That the boundary sentence is what the session was missing. It says so itself,
  and it is one session.

## Wrong if

- A session reads the sentence and files the same report anyway. Then what is
  missing is the capability rather than the description, and the question above
  is answered by the corpus.
- The `env` map is built and the six-run shape does not recur. Then this was one
  audit's convenience.

## Since then

On 2026-08-04, the maintainer answered the half this entry put up: no `env` map.
What the tool reports is the running instance and what is in it, and a value
that only exists under another environment is not a configuration this server
has been asked about.

So the description states that positively rather than as a limitation, and the
console keeps the conditional half because it is the project's own. The second
**Wrong if** above is settled with it: the shape does not have to recur for the
map to be wrong, since what it would answer is not what this lookup is for.

## Since then

The source was read against the covered lines and it answered on two of the
four: the command arrived inside the newest major, so both LTS lines were handed
the console's own "command is not defined" as unsupported. That is the answer a
neighbouring entry rules out in as many words, and its reading is the one this
follows — the booted container answers on every covered line, so the tool has
one source rather than a version-bound pair.
