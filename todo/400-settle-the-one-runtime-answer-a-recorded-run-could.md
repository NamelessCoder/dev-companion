# Settle the one runtime answer a recorded run could not reach

**Serves:** decisions/

`REVIEW-02` run 5 is the session `D-ANS-3` asked to be recorded: it left the v13
delta-only rule for `ext_tables.sql` unraised, "since I did not verify each
column against the schema analyzer's TCA-derived output", and no project file,
effective setting or checkout it had could supply that. The boundary is narrow —
one table's TCA-derived columns, not SQL, not a log, not the live schema — and
the core carries it as `Core\Database\Schema\DefaultTcaSchema`. The step is to
settle whether that class can be asked without a database, because `E-EXT` has
none and it reaches `ConnectionPool` for the platform at `:497`; read it in
`.checkouts/14.3` and `.checkouts/13.4`, and try it through `Typo3Runtime`,
which is how a registry with no console command is already asked (`D-DIS-5`).
The answer is a decision either way: a `typo3_schema_lookup` bounded to the
derived side, or an entry saying the derived columns are not reachable without a
connection and the rule therefore stays unraisable in an extension checkout.
