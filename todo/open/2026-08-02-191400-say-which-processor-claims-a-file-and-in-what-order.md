# Say which processor claims a file, and in what order

**Serves:** feedback/2026-08-01-114526-session-debrief-i-reviewed-a-patch-replacing-gd.md
**Priority:** low

Ladder step 1a, judged as `D-KNW-028`: nothing below `knowledge/` says how a
file becomes a processed one, so a patch review read seven core classes by
hand. Establish the chain
against `.checkouts/` before writing anything. `SYS/fal/processors` and
`SYS/fal/processingTaskTypes` in
`typo3/sysext/core/Configuration/DefaultConfiguration.php` are identical on
`12.4`, `13.4`, `14.3` and `main`, so the list is not the part to read — what
decides the answer is `canProcessTask()` on each of `SvgImageProcessor`,
`DeferredBackendImageProcessor`, the OnlineMedia `PreviewProcessing` and
`LocalImageProcessor`, plus the `before` and `after` keys that order them, and
`ProcessedFileRepository` for where a processed file is looked up before one is
made. Then place it: `file-abstraction-layer` in
`knowledge/architecture-hints/php.json` is the candidate, and it stops at
storages, drivers and metadata today. Whichever hint takes it, its `appliesTo`
has to reach `Classes/Resource/Processing/` and the word "thumbnail", because
neither reaches anything now, and the corpus's only "processor" is
`frontend-dataprocessors` in a different subsystem. Say in it that the effective
list is `typo3_configuration_lookup` with `SYS/fal/processors`, which answers
only where the installation does.
