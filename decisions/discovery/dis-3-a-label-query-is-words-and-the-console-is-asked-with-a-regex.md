---
id: D-DIS-3
date: 2026-07-29
status: standing
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
