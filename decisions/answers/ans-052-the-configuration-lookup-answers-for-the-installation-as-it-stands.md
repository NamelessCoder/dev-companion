---
id: D-ANS-052
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
