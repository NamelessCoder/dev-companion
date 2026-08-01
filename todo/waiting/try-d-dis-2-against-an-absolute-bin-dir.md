# Try `D-DIS-2` against a console invoked from outside the root

**Serves:** decisions/
**Waiting on:** the DDEV half needs a project whose container working directory
    is not the project root, and there is none here. Which one do you want built?
    (a) One of the environments reconfigured for it — `syntax` is the smaller
    one — `working_dir: web: /var/www/html/.build/public`, `ddev start`, ask the
    server, put it back: one container start and a shared checkout altered for
    an hour. (b) A throwaway DDEV project of its own: nothing shared is touched,
    at the price of a new entry in `ddev list` and an image pull. (c) Neither,
    and the shape is closed on paper — `TYPO3_MCP_CONSOLE` is already the repair
    for it, and the decision says the invocation holds for DDEV's default
    working directory and names the setting for a project that moves it. I would
    take (a): the question is whether the invocation actually breaks there, and
    only a run answers that.

The file keeps its name because the branch carries the half that is done. The
absolute `bin-dir` shape is settled (2026-08-01): Composer 2.9.5 does install
the console into an absolute `bin-dir`, this server dropped the declaration and
reported no console at all, and one below the root is now expressed relative to
it — `Typo3Cli::binDirectory()`, held by two `Typo3CliTest` cases and written
into `R-DIS-3` and the `Tested on` line of `D-DIS-2`. It was a `Typo3Cli` case
rather than the `InstanceTest` case this todo expected: `Instance` resolves the
packages, the console is `Typo3Cli`'s.

What is left is the other shape the **Wrong if** names, and it is one run rather
than a fixture. DDEV 1.25.1 defaults `working_dir.web` to `/var/www/html`, the
project root, and its own generated `config.yaml` calls that "the directory in
which commands passed into ddev exec are run" — so `ddev exec -- .build/bin/typo3`
holds by default, and only a project that overrides it to its docroot would
break. Whether it then breaks, and what the reason says when it does, is what
the run has to show.
