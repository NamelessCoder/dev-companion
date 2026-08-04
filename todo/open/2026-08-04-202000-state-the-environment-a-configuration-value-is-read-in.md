# State the environment a configuration value is read in

**Serves:** feedback/2026-08-04-180112-task-implement-and-then-prove-a-documented.md, src/Tool/
**Priority:** normal

`D-ANS-052` decided the description carries the boundary. Read what
`typo3_configuration_lookup`'s description and its `path` argument say today,
then add the one sentence: the value is the installation's in the environment it
is in, and a path that has to be shown resolving under another environment —
with a variable set, with `IS_DDEV_PROJECT` unset — is `configuration:show` on
the project's own console. Say it where the call is composed rather than in the
answer, and check `documentation/tools/typo3_configuration_lookup.md` is
regenerated with `bin/cli tools:index`. The `env` map the feedback proposes is
answered and is not built: the maintainer settled on 2026-08-04 that this tool
reports the running instance and what is in it, so say that as what the tool is
for rather than as what it cannot do.
