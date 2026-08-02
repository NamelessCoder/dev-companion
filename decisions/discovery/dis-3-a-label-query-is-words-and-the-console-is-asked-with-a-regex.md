---
id: D-DIS-3
date: 2026-07-29
status: revoked
---

# D-DIS-3 — A label query is words, and the console is asked with a regex

**The words of a label query go to the console as one case-insensitive regex
union, and the intersection is taken here.**

`language:domain:search --search=` matches one literal string, so the words of a
query had to be recomposed into something the console can answer at once.

## Decided

- The words go over as `--regex=/(one|two)/i` — the union — and the
  intersection is taken here. One call per query rather than one per word,
  because a console call boots TYPO3, and the union is also what makes "save
  alone matches 65 labels" answerable without asking a second time.

## Assumed

- `--regex` is available wherever the command itself is. It was added in the
  same commit as `--search`, `--json` and the command ([TASK] Add CLI command
  and service to search labels, on 14.0 and later), so an installation that
  answers the one answers the other.
- Matching is a plain case-insensitive substring on both sides, not a word
  boundary as elsewhere in this server. A trans-unit id is
  `labels.save_document` and an underscore is a word character, so anchoring
  would drop exactly the ids a caller searches by.
- A console that exits 0 without a JSON payload found nothing. That is what
  this command does — it prints `[WARNING] No language resource files found.`
  and returns SUCCESS — and no other command this server calls answers `--json`
  with anything but JSON.

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

And the mechanism first written here was wrong, so it is written out. `--json`
does share stdout with the `$io->title()` the same command prints ahead of it,
and `Typo3Cli::decode()` does start at the first `{` or `[`. But neither
carrier named for getting something onto that stream survived checking.
Xdebug's "Could not connect to debugging client" goes through `xdebug_log_ex` →
`xdebug_php_log` → `php_log_err()`, which on CLI with `error_log` unset writes
to **stderr** — measured — and `Typo3Cli::execute()` reads stderr on its own
pipe. A PHP deprecation reaches stdout only with `display_errors=On` (measured;
off in this machine's CLI ini), and inside a booted command not even then:
`Bootstrap::initializeErrorHandling()` registers `ErrorHandler`, whose
`handleError()` returns `ERROR_HANDLED = true` on every path that does not
throw, so PHP prints nothing. `CommandApplication::run()` additionally discards
what `ext_localconf.php` buffered. **No producer of stdout noise ahead of the
payload is established.** What would settle it is whether `ddev exec` folds the
container's stderr into its own stdout — DDEV is how this server reaches most
consoles, and no project on this machine was running to ask.

## Since then

The exit code is not the signal, on the narrower ground that it never certified
anything. `Typo3Cli::json()` hands back what was printed, only the warning
above is read as "none", and every other exit-0-without-payload settles nothing
and takes the route an unreachable console takes: the packages' XLF files, and
`answeredBy: nothing` where they hold none either. Reading the warning rather
than the exit code fails in the safe direction — were its wording to move, an
empty result would read as nothing established, which costs a fallback rather
than an answer that is wrong.
