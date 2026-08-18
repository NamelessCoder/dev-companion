# Name what carries a variable into a console command in the container

**Serves:** feedback/2026-08-18-070423-the-hints-name-the-typo3-setup-variables-but.md
**Priority:** normal
**Branch:** todo/name-what-carries-a-variable-into-a-command-in-the-container
**Claimed:** 2026-08-18

Judged as step 1a on 2026-08-18 —
[`D-KNW-094`](../../decisions/knowledge/knw-094-how-a-variable-reaches-a-console-command-in-the-container-is-a-gap-this-server-owns.md)
carries the evidence, and the reading is what is left. Measure in an environment
made here, `bin/cli environment:create E-SITE`, what `ddev exec` does with an
assignment prefix: whether `ddev exec TYPO3_DB_DRIVER=mysqli typo3 --version`
carries the variable, what `--raw` switches given that
`Typo3Cli::pastTheShell()` records the line being handed to bash, and which
flags the version measured against has — the feedback reports no `-e` and a
`--raw=false` string stat'ed as one binary name. Read a second
`.ddev/config.yaml` before writing the other half, that `web_environment`
commonly carries the `typo3Database` functional testing family which
`typo3 setup` does not read. Both go into `installation-setup` beside the
variables it already names, unless the boot document
`feedback/2026-08-18-070538` asks for is judged first and takes them, and a
`HintsTest` case holds the statement.
