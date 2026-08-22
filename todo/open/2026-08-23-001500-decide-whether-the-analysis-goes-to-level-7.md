# Decide whether the analysis goes to level 7

**Serves:** decisions/code/
**Priority:** low
**Run:** sed 's/level: 6/level: 7/' phpstan.neon > phpstan.level7.neon; vendor/bin/phpstan analyse -c phpstan.level7.neon --no-progress; rm -f phpstan.level7.neon

`phpstan.neon` says level 6 and has since it was written, with nothing recording
why that level rather than another. Level 7 reports 57 on 2026-08-22, 27 of them
in `src/` and 30 in `tests/`, and the reading below is what they are — the six
that were a real invariant nothing stated were fixed the same day and are not in
that count.

What is left falls in four kinds, and only one of them is about this code being
wrong:

- **A library hands back a union this code narrows by knowing better.**
  `DOMNode` against `DOMElement` in `Manual\Documentation` seven times,
  `curl_setopt_array` in `Http\Fetch`, `proc_open` in `Process\SystemRunner`,
  and the two schema shapes `mcp/sdk` declares in `Server\Factory`. Fixing these
  means writing down the narrowing the code already performs in its head.
- **`json_decode()` at a network boundary.** `Forge::api()`, three in `Gerrit`
  and two in `Knowledge\Hints` declare a shape and return what a decode gave.
  This is the kind worth the most: it is where this server reads something it
  did not write.
- **A capture group PHPStan cannot see is always there.** Four in `Wrap`, one in
  `Server\Installer`, one in `Tool\ChangelogLookup`.
- **`max()` over a list that is not empty in context.** `HintCoverage` and two
  in `Prose`.

What is asked is whether the level moves. It is not a free change: 30 of the 57
are in `tests/`, where the answer is mostly an annotation, and a level is a
promise about every file written after it. `D-COD-004` is the neighbouring entry
— what a unit test may do — and a decision recording why this level would sit
beside it.

The reading a session should not skip: none of the 57 is a failing test today,
and the six that could have crashed something were the ones taken out first.
