# Give the task guide the change type a deprecation patch is

**Serves:** feedback/2026-08-01-122113-typo3-task-guide-has-no-deprecation-changetype.md
**Priority:** normal

`typo3_task_guide` offers bugfix, feature, cleanup, test, documentation and
unknown — `src/Tool/TaskGuide.php:87` — so the one core patch shape with a fixed
rule set has no checklist, and the reviewing session verified every rule by
grepping the `setCorrelationId` precedent instead. Establish against
`.checkouts/main` what a deprecation actually owes before the enum is touched:
the `@deprecated` docblock wording, the `trigger_error(…, E_USER_DEPRECATED)`
message shape, the changelog file named `Deprecation-<issue>-<slug>` with its
anchor and its `Index.rst` inclusion, whether a scanner matcher is owed or an
explicit `NotScanned` tag is, that removal is staged for the next major, and
that no core caller of the deprecated API is left behind —
`typo3/sysext/core/Documentation/Changelog/Howto.rst` is the authority on the
tag. Then add the type beside the six in the enum and its block in the two
tables above it, and route it in `knowledge/task-intents.json`. It changes a
declared schema and lands beside the two core skills published on 2026-08-03, so
it is the same contract read twice: what the skill orders and what the guide
answers have to name the same rules.
