# `typo3_project_scope`

Describe the project around the TYPO3 installation this server was started in:
its TYPO3 and PHP constraints, the extensions that are its own rather than
TYPO3's, the sites it configures with the site sets each depends on, and the
commands it declares in composer.json and package.json — each marked a check
that hands the code back as it was, a change that rewrites something, or unknown
where the declared body does not say. Read from files only, no console and no
database, so it answers on a fresh clone. It also names the environment the
repository configures to run itself in: a DDEV project states the PHP its
container runs, which is a different interpreter from the caller's shell and
where the commands below are run. Call it before recommending or running a check
— these are the commands that exist in this repository, and the ones marked
check are what a task told not to change files may run.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

Nothing.

## Answers with

```yaml
# Absolute path of the project. Null when there is no installation to describe.
root: string or null  # optional
# core-checkout or composer-project.
kind: string  # optional
# The TYPO3 version installed here, read from the core package.
typo3Version: string or null  # optional
# What composer.json requires of PHP. What the project declares, not what runs
# it — see environment.
phpConstraint: string or null  # optional
# What it requires of typo3/cms-core.
coreConstraint: string or null  # optional
# The environment this repository configures to run itself in, read from that
# environment's own files. Null means nothing here configures one that this
# server reads — .ddev/config.yaml and TYPO3_MCP_CONSOLE are what it reads —
# so the commands below run wherever the caller runs them.
environment:  # optional
  # One of: ddev, override. ddev: the repository carries a .ddev/config.yaml.
  # override: nothing in the files says so, and TYPO3_MCP_CONSOLE names a
  # command that reaches this installation somewhere other than the caller's own
  # shell.
  via: string
  # The PHP that environment runs, where its files state it. Null is not "none":
  # a DDEV project that states no php_version gets the default of the installed
  # DDEV, and an environment named by TYPO3_MCP_CONSOLE states its version
  # nowhere this server can read. typo3_server_scope reports the version the
  # console actually answers on.
  php: string or null
  # Where this was read: the .ddev config file that states the version last, or
  # TYPO3_MCP_CONSOLE.
  source: string
  # True when this server is already running inside that environment, so its
  # shell is that environment and a declared command needs nothing in front of
  # it.
  entered: boolean
# Extensions that are not TYPO3 system extensions.
extensions:  # optional
  - key: string
    # Relative to the project root.
    path: string
    # One of: project, third-party, fixture. project: inside the repository, so
    # what it is working on. third-party: installed as a dependency. fixture:
    # shipped by the repository's test setup, below a Tests/ directory, so it
    # exists to be loaded by a suite rather than developed.
    origin: string
sites:  # optional
  - identifier: string
    base: string
    rootPageId: integer or null
    # The site sets this site depends on, by their composer-style name.
    sets: [string]
    languages: [string]
# What this repository declares. A check that is not here does not exist here.
commands:  # optional
  - # As this repository declares it. Where environment is not null, it is run
    # inside that environment rather than in the caller's shell.
    command: string
    # composer.json or package.json.
    source: string
    # The body the manifest declares for it, lines joined with &&.
    declares: string
    # One of: check, change, unknown. What running it does to the sources, read
    # off the body rather than by running it. check: it reports and hands the
    # code back as it was, so a task told not to change files can run it — it
    # may still write a cache of its own. change: it rewrites something.
    # unknown: the body does not say, which is what a test suite is, because it
    # runs the project's own code.
    runs: string
# Patches from extra.patches. A patched package does not behave as its version
# says.
patches:  # optional
  - # The dependency being patched.
    package: string
    # What the patch is for, where composer.json says.
    description: string
    # The patch file, relative to the project root.
    file: string
# One of: installation, packages. installation: its assembled runtime state
# answered. packages: read from the files the installed packages ship, because
# the console could not be asked — overrides applied at runtime are not
# reflected.
answeredBy: string  # optional
unsupported:  # optional
  # One of: no-installation, misconfigured, installation-not-answering.
  # no-installation: nothing to ask from here, and searched says where it
  # looked. misconfigured: an installation was named and could not be used, so
  # nothing was searched for. installation-not-answering: one was found and its
  # console did not answer — a stopped container or a database with no schema,
  # which is a state that ends without reinstalling anything.
  cause: string
  # What stopped it, in the words the attempt produced.
  reason: string
  # What the reason means where the message alone does not say it — a console
  # that starts and then fails on a missing table has a database without a
  # schema, not a broken installation. Empty where nothing beyond the reason is
  # known.
  diagnosis: string  # optional
  # Every directory the discovery walked, in order. "Nothing was found" and "the
  # server was started somewhere else" wear one sentence, and only this tells
  # them apart. Empty where discovery never ran.
  searched: [string]
  # What was set and could not be used. Null where nothing was set.
  misconfiguration: string or null  # optional
  settings:
    # Environment variable that names the installation root.
    root: string
    # Environment variable that names the console command.
    console: string
```

The answer carries exactly one of these sets of fields: `root`, `environment`,
`extensions`, `sites`, `commands`, `patches`, `answeredBy` — or `unsupported`.

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### project

Called with:

```json
{}
```

Text:

```
<installation> — core-checkout, TYPO3 14.3.6-dev, PHP ^8.2

Extensions: none beyond TYPO3's own.

Sites: none configured below config/sites/.

Commands this repository declares — these exist here, the core's testing suites do not. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
Nothing in this repository configures an environment of its own — .ddev/config.yaml and TYPO3_MCP_CONSOLE are what this reads — so these run wherever you run them.
- composer gerrit:setup (composer.json) — unknown: @gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable
- composer gerrit:setup:commitMessageHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enableCommitMessageHook
- composer gerrit:setup:preCommitHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enablePreCommitHook
- composer gerrit:setup:preCommitHook:disable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::disablePreCommitHook
```

Data:

```json
{
    "root": "<installation>",
    "kind": "core-checkout",
    "typo3Version": "14.3.6-dev",
    "phpConstraint": "^8.2",
    "coreConstraint": null,
    "environment": null,
    "extensions": [],
    "sites": [],
    "commands": [
        {
            "command": "composer gerrit:setup",
            "source": "composer.json",
            "declares": "@gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable",
            "runs": "unknown"
        },
        {
            "command": "composer gerrit:setup:commitMessageHook:enable",
            "source": "composer.json",
            "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::enableCommitMessageHook",
            "runs": "unknown"
        },
        {
            "command": "composer gerrit:setup:preCommitHook:enable",
            "source": "composer.json",
            "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::enablePreCommitHook",
            "runs": "unknown"
        },
        {
            "command": "composer gerrit:setup:preCommitHook:disable",
            "source": "composer.json",
            "declares": "TYPO3\\CMS\\Composer\\Scripts\\InstallerScripts::disablePreCommitHook",
            "runs": "unknown"
        }
    ],
    "patches": [],
    "answeredBy": "packages"
}
```
