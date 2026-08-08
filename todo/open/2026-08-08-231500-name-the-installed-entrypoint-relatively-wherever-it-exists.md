# Name the installed entrypoint relatively wherever it exists

**Serves:** feedback/2026-08-08-184226-install-writes-a-machine-specific-absolute.md
**Priority:** high

Judged as a repair rather than a gap — `D-DIS-015`. `jsonServer()` uses the
relative path `installedEntrypoint()` returns only where `.ddev/config.yaml`
exists, so a project that has this server as a Composer dependency and no DDEV
gets the absolute host path even though `vendor/bin/typo3-dev-companion` is
sitting there. Measured 2026-08-08 in a fixture declaring the package.

High because it is every project that installed this server the ordinary way,
and because the entry it writes is wrong on every machine but one.

**First, and before the change:** establish that a client spawns the command
with the project root as its working directory. A relative `args` entry resolves
only there, and a wrong answer is worse than the host path — it fails on the
machine that ran the install too. The DDEV branch already rests on this for one
client; what is needed is the other ten, from their own documentation, the way
`documentation/clients/installing.md` records the restart-and-approval answers.
Where a client does not say, that is recorded as unestablished rather than
guessed.

Then the change is one branch in `jsonServer()`: take `$installed` where it is
not null, and keep `ddev exec` as the wrapper it already is rather than as the
condition. `InstallerTest` gains the case the DDEV one is missing its pair of.

**Run:** `bin/cli todo:next`

## What this does not touch

The standalone checkout, where `installedEntrypoint()` returns null and the
absolute path is the only one that exists. That half is
`todo/waiting/2026-08-08-231600-decide-what-a-standalone-install-writes-into-a-shared-file.md`
and it is a question rather than a defect.
