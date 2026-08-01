# Let the JSON decoder find a payload a notice got in front of

**Serves:** decisions/, R-ANS-8

`Typo3Cli::decode()` tries the whole output, then the substring from the first
`{` or `[`, and stops. `D-DIS-3` was corrected on 2026-08-01 with the fixture
runs that show what that costs: an Xdebug `[Step Debug]` line or a deprecation
naming `{closure}` ahead of the payload puts the slice at the wrong character,
the decode fails, and a console that answered in full is read as one that
answered nothing. `typo3_label_lookup` now degrades to the packages' XLF files
there rather than to a false "none", and `typo3_configuration_lookup` and
`typo3_fluid_namespace_list` report the question unanswered — none of them is
wrong any more, and all three throw away an answer the console gave them. Make
`decode()` try each `{` and `[` offset in turn rather than the first alone, and
hold it with a `Typo3CliTest` case whose output carries a brace ahead of the
payload. Settle before changing it what the cost is on output that carries no
payload at all — a long stack trace has many braces, and every one of them is a
`json_decode` on the rest of the string.
