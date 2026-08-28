---
id: D-DIS-003
title: 'A label query is words and the console is asked with a regex'
date: 2026-07-29
status: revoked
---

# D-DIS-003 — A label query is words and the console is asked with a regex

**The words of a label query go to the console as one case-insensitive regex
union, and the intersection is taken here.**

`language:domain:search --search=` matches one literal string, so the words of a
query had to be recomposed into something the console can answer at once.

## Decided

- The words go over as `--regex=/(one|two)/i` — the union — and the intersection
  is taken here. One call per query rather than one per word, because a console
  call boots TYPO3, and the union is also what makes "save alone matches 65
  labels" answerable without asking a second time.

## Assumed

- `--regex` is available wherever the command itself is. It was added in the
  same commit as `--search`, `--json` and the command ([TASK] Add CLI command
  and service to search labels, on 14.0 and later), so an installation that
  answers the one answers the other.
- Matching is a plain case-insensitive substring on both sides, not a word
  boundary as elsewhere in this server. A trans-unit id is
  `labels.save_document` and an underscore is a word character, so anchoring
  would drop exactly the ids a caller searches by.
- A console that exits 0 without a JSON payload found nothing. That is what this
  command does — it prints `[WARNING] No language resource files found.` and
  returns SUCCESS — and no other command this server calls answers `--json` with
  anything but JSON.

## Wrong if

- A command exits 0 and prints nothing usable for a reason other than an empty
  result; the answer would then be a confident "none" where nothing was
  established. The exit code is the only signal being read.

## Revoked on 2026-08-01

Not because the "Wrong if" was caught happening. It was not. What was measured
is the half that does not depend on catching it: put to `typo3_label_lookup`
through a fixture console, four different stdouts — the real
`[WARNING] No language resource files found.`, an empty one, and two carrying a
bracket ahead of an intact payload — came back as one answer,
`answeredBy: installation`, `matchCount: 0`, "No label in … carries all of". In
the two with a payload the installation held the very label that was asked for.
The tool had one shape for four inputs and no way to tell them apart, which is
enough to correct without knowing who produces the input.

## Revoked on 2026-08-01

The mechanism first written here was wrong. `--json` does share stdout with the
title the command prints ahead of it, but neither carrier named for getting
something onto that stream survived checking: Xdebug's connection message goes
to stderr, which is read on its own pipe, and a deprecation reaches stdout only
with `display_errors=On` and not even then inside a booted command. **No
producer of stdout noise ahead of the payload is established.**

## Since then

The exit code is not the signal, on the narrower ground that it never certified
anything: only the warning is read as "none", and every other exit-0 without a
payload takes the route an unreachable console takes. Reading the warning fails
in the safe direction — a moved wording costs a fallback rather than a wrong
answer. The transport does not fold the two streams either, read from DDEV's own
source rather than from a run: `ddev exec` appends `-T` where stdin is not a
terminal, so no pseudo-TTY is allocated and Docker keeps the streams apart. What
a run would still add is what a misbehaving container puts on them.
