# Decide what the comment report counts now the tail is read

**Serves:** D-DOC-035
**Priority:** low
**Run:** bin/cli prose:check

Both sweeps that entry queued are done, and its **Since then** of 2026-08-18
carries what the second one found: of the 141 comments at 11 to 13 lines, 113
are cross-references and were left as they are. So the 158 the report now names
are, above that band, comments the first sweep cut, and inside it comments this
one read. That is the second **Wrong if** met — a report nobody acts on is noise
the command adds to — arriving by the number falling rather than by it sitting
still.

What has to be decided is what `Prose::RETOLD` is counted against, and three
answers are priced:

- **Raise the line past the docblock floor**, to 13 or 14. One constant, and
  what is left named is the comments long enough that length is still a signal
  on its own. What it gives up is every retelling written inside an annotated
  docblock, which is where seven of the ones cut in this sweep were.
- **Count a comment's prose lines rather than its lines** — without the
  delimiters, the summary, the blank lines and the `@param` and `@return`. Ten
  then means ten lines of prose, which is what the rule in AGENTS.md is about,
  and the floor goes rather than being worked around. It is a function in
  `Prose` and a rewritten `ProseTest` case, and it costs the comparability of
  the 240, 166 and 158 the entry records.
- **Leave it.** Nothing is broken and the share is still measured; what it costs
  is a list of 158 naming work nobody has to do.

The recommendation is the second. It removes the floor rather than moving the
line, and what it costs is a sentence in `D-DOC-035` saying which measure each
number was taken with. Whichever is chosen goes into that entry, because its
second **Wrong if** is what the choice answers.
