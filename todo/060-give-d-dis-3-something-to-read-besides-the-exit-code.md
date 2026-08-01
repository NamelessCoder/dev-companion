# Give `D-DIS-3` something to read besides the exit code

**Serves:** decisions/

A label query asks the console with a regex and reads the exit code alone, so a
command that exits 0 and prints nothing usable for any other reason becomes a
confident "none" where nothing was established. Take one console command that
can do that and see what the answer looks like. What would hold it is a
`Typo3CliTest` case with a fixture command that exits 0 on unusable output, and
an answer that says "nothing established" rather than "none" — the two are
different results and the tool currently has one shape for both.
