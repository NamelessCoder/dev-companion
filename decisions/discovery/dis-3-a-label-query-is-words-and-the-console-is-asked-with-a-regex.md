---
id: D-DIS-3
date: 2026-07-29
status: corrected
---

# D-DIS-3 — A label query is words, and the console is asked with a regex

**The words of a label query go to the console as one case-insensitive regex
union, and the intersection is taken here.**

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
- **Wrong if:** a command exits 0 and prints nothing usable for a reason other
  than an empty result; the answer would then be a confident "none" where
  nothing was established. The exit code is the only signal being read.

- **Corrected on 2026-08-01:** the assumption held for the command and failed
  for the stream it writes to. `TranslationDomainSearchCommand` in
  `.checkouts/14.3` and `.checkouts/main`, and the other three commands this
  server calls, answer every outcome but the empty result with
  `Command::FAILURE`, so no core command takes a strange exit-0 path. But
  `--json` shares stdout with the `$io->title()` the same command prints ahead
  of it, and `Typo3Cli::decode()` starts at the first `{` or `[`: whatever else
  reaches that stream before the payload defeats the decoder while the exit
  code stays 0. Put to `typo3_label_lookup` through a fixture console, an
  Xdebug `[Step Debug]` line, a deprecation naming `{closure}`, the real
  `[WARNING] No language resource files found.` and an empty stdout all came
  back as one answer — `answeredBy: installation`, `matchCount: 0`, "No label
  in … carries all of" — and in the first two the installation held the label
  that was asked for.
- **Since then:** the exit code is not the signal. `Typo3Cli::json()` hands back
  what was printed, only the warning above is read as "none", and every other
  exit-0-without-payload settles nothing and takes the route an unreachable
  console takes: the packages' XLF files, and `answeredBy: nothing` where they
  hold none either. Reading the warning rather than the exit code fails in the
  safe direction — were its wording to move, an empty result would read as
  nothing established, which costs a fallback rather than an answer that is
  wrong.
