---
id: D-ANS-126
title: The runnable form of a declared command is a field
date: 2026-08-28
status: open
coveredBy:
  - ProjectTest::theAnswerSaysWhatRunsTheProject
---

# D-ANS-126 — The runnable form of a declared command is a field

**`typo3_project_describe` says how each declared command is run from where the
caller stands as data, rather than only in the prose above the list.**

The prose has said it since 2026-08-04 and a session read past it, because the
half it was reading was the payload. What the payload carries is the command as
the manifest declares it, and the environment as a separate object.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001333`](../../feedback/archive/2026-08-28-001333-declared-commands-and-the-declared-environment.md).
  It ran `composer cgl:ci` as reported and got Composer's platform check: "Your
  Composer dependencies require a PHP version >= 8.4.1. You are running 8.3.23."
- **The text already answers it.** `ProjectDescribe::whereTheyRun()` prints,
  directly above the command list, "They are run in the DDEV project this
  repository configures, not in the shell you have: `ddev composer <name>` for a
  composer script, `ddev exec <command>` for the rest", and
  `ProjectDescribe::startable()` warns where the caller's PHP is below the bound
  — which is `D-ANS-086`, written for this same failure. Both are asserted by
  `ProjectTest::theAnswerSaysWhatRunsTheProject`, and both landed before this
  session ran.
- **The report reads the payload.** It enumerates `commands`, `environment`,
  `installedPhpBound` and `phpRelation` as fields and says the answer "never
  said" what the prose says three lines above the list it quotes.
- **In the payload the rule is in a description.** `commands[].command` declares
  "Where environment is not null, it is run inside that environment rather than
  in the caller's shell" — which tells a reader of the schema what the value
  means and leaves the value itself unrunnable.
- **`AGENTS.md` decided this shape generally**: what is read as data lives where
  data lives, and what is parsed out of prose is a regex somebody has to write
  again.

## Decided

- **One field per command**, carrying the command as it is run from where the
  caller stands. `environment.entered` decides whether it is prefixed at all,
  and the declaring manifest decides the prefix — `ddev composer <name>` for a
  composer script, `ddev exec` for the rest, which are the two forms the prose
  already names.
- **Queued.** It is a tool's declared schema, which the ladder keeps off the
  spot whatever the judging run holds.
- **The prose stays.** It carries why, and the field carries what to run; a
  field that replaced the sentence would leave the caller who runs it on the
  host with no reason for the failure.
- **The report's second half is a separate step**, and it is a reading rather
  than a shape: whether `ddev composer <script>` passes the stdout of a tool the
  script wraps. Nothing here can establish it — it needs a DDEV project — and
  writing it from the report would be a knowledge entry with a guess's
  substance.

## Assumed

- That every declared command is meant to run in the environment where there is
  one. The prose already says so without qualification, so the field states what
  the sentence states.

## Wrong if

- A repository declares a command that has to run on the host — one that calls
  `ddev` itself, or a git hook — and the field prefixes it into something that
  cannot work. Then the composition needs a condition, and the sentence was
  carrying that ambiguity by being a sentence.
- A client is reported acting on the prose, which would say the field pays for
  what was already delivered.

## Since then

Built the same day as `commands[].invocation`, composed in
`Project::invocation()` from the two things the answer already held: the
environment, and whether the command is a composer script. It is the declared
command unchanged where nothing can be put in front of it — no environment, a
shell already inside one, and the `TYPO3_DEV_COMPANION_CONSOLE` override, whose
command reaches this installation's console rather than an arbitrary script.

Both sides of `entered` are asserted in
`ProjectTest::theAnswerSaysWhatRunsTheProject`, where the prose half was already
held.

The report's second half is still open, and so is its feedback:
`ddev composer <script>` and the stdout of a tool the script wraps needs a DDEV
project to establish.

The report's second half was measured rather than left to the card, in
`.environments/e-site-14.3` against DDEV v1.25.3, with a composer script writing
one line to each stream and exiting 8:

| form                           | exit code the caller sees | stdout         | stderr                   |
| ------------------------------ | ------------------------- | -------------- | ------------------------ |
| `ddev composer <name>`, exit 0 | 0                         | passed through | passed through           |
| `ddev composer <name>`, exit 8 | 1                         | dropped        | folded into ddev's error |
| `ddev exec composer <name>`    | 8                         | passed through | passed through           |

So the wrapper loses exactly the run whose output is the finding, which is what
the session hit: a formatter's dry run exits non-zero and writes the diff to
stdout.

**`invocation` still composes `ddev composer <name>`.** It is DDEV's own command
for a composer script and what a caller running an installing script needs;
swapping the default to `ddev exec` for every command would trade a lost diff
for a host tree the wrapper exists to keep in step, which is not measured here.
The caveat is a statement in `project-build-and-scripts` instead, naming both
forms and what each does with a failing script.
