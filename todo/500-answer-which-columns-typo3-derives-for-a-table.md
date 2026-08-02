# Answer which columns TYPO3 derives for a table

**Serves:** decisions/

`D-DIS-008` settled that `DefaultTcaSchema::enrich()` can be asked without a
populated schema — it needs the container and a database server that answers,
and it uses the platform in one branch only. What is left is the tool. It is
`typo3_schema_lookup`, bounded to the derived side: for one table, the columns
TYPO3 adds by itself, which is what an `ext_tables.sql` may leave out and what
`REVIEW-02` run 5 could not check a column against.

The step is a topic in `probe.php` beside the icons and the TCA tables: build an
empty Doctrine `Table` for every TCA table — `enrich()` throws where one is
missing — call it, and hand back the added column names per table with their
type. Both branches have to be served from one probe: 13.4 reads
`$GLOBALS['TCA']` itself and takes no constructor argument, 14.3 takes a
`TcaSchemaFactory` and defaults it through `makeInstance`, so the call is
`GeneralUtility::makeInstance(DefaultTcaSchema::class)` on both and the
difference is invisible from here. A failure to reach the database is the
`unsupported` shape the installation-backed tools already carry, with the reason
`Typo3Cli` reports for a container that is down — not an empty answer, which
would read as "TYPO3 derives nothing".

What to settle while writing it: whether one table or every table is the answer.
The rule is asked per `ext_tables.sql`, which is per extension and therefore
several tables, and a probe reading is kept for the session anyway — so the cost
of all of them is one boot either way, and the shape of the answer is the
question rather than the cost.
