# `typo3_project_describe`

Describe the project around the TYPO3 installation this server was started in:
its TYPO3 and PHP constraints, including the PHP floor the installed core
requires and not only the one this project declares, the extensions that are its
own rather than TYPO3's, the sites it configures with the site sets each depends
on, and the commands it declares in composer.json and package.json — each marked
a check that hands the code back as it was, a change that rewrites something, or
unknown where the declared body does not say. Read from files only, no console
and no database, so it answers on a fresh clone. It also names the environment
the repository configures to run itself in: a DDEV project states the PHP its
container runs, which is a different interpreter from the caller's shell and
where the commands below are run, plus what that environment runs without being
asked — each hook as the stage it fires at and the command it runs, and the pull
recipes its database and files come from. Call it before booting such a project
or before recommending or running a check — these are the commands that exist in
this repository, and the ones marked check are what a task told not to change
files may run. Answers from: packages.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`packages`](answer-sources.md#packages).

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
# What the installed typo3/cms-core requires of PHP, out of that package's own
# composer.json — the lowest a package here may declare it supports. Neither
# of the other two PHP numbers: not what this project declares, and not what
# environment.php runs. Not derivable from the TYPO3 major either — v13.4 and
# v14.3 both require ^8.2, v12.4 requires ^8.1. Null where no core package was
# found to read.
corePhpConstraint: string or null  # optional
# The environment this repository configures to run itself in, read from that
# environment's own files. Null means nothing here configures one that this
# server reads — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what it
# reads — so the commands below run wherever the caller runs them.
environment:  # optional
  # One of: ddev, override. ddev: the repository carries a .ddev/config.yaml.
  # override: nothing in the files says so, and TYPO3_DEV_COMPANION_CONSOLE
  # names a command that reaches this installation somewhere other than the
  # caller's own shell.
  via: string
  # The PHP that environment runs, where its files state it. Null is not "none":
  # a DDEV project that states no php_version gets the default of the installed
  # DDEV, and an environment named by TYPO3_DEV_COMPANION_CONSOLE states its
  # version nowhere this server can read. typo3_server_scope reports the version
  # the console actually answers on.
  php: string or null
  # Where this was read: the .ddev config file that states the version last, or
  # TYPO3_DEV_COMPANION_CONSOLE.
  source: string
  # True when this server is already running inside that environment, so its
  # shell is that environment and a declared command needs nothing in front of
  # it.
  entered: boolean
  # What this environment runs without being asked, from .ddev/config.yaml and
  # every .ddev/config.*.yaml beside it. The commands list is what a caller may
  # run; these fire on their own at the stage each names, so an environment that
  # installs dependencies on start and updates the schema on import says so
  # here. Empty means those files declare no hooks. Unmarked, unlike the
  # commands: runs says whether a caller may run something, and a hook is not
  # the caller's to run.
  hooks:
    - # The DDEV stage it fires at: post-start, post-import-db, pre-pull and the
      # rest.
      stage: string
      # What that stage runs, as the file states it. A block of several lines is
      # joined with ";", which is what the shell does with it.
      command: string
      # The container it runs in, "web" where the task names none. Null means it
      # runs on the host instead, which is what an exec-host task is.
      service: string or null
  # The pull and push recipes below .ddev/providers/ that this repository wrote,
  # which is where its database and files come from. DDEV writes its own recipes
  # into every project and marks them #ddev-generated; those are left out,
  # because they say what DDEV puts everywhere rather than what this project
  # decided.
  providers:
    - # What to pass: "ddev pull <name>".
      name: string
      # The recipe file, relative to the project root.
      source: string
      # pull, push, or both — which of the two the recipe declares commands
      # for. A recipe with no push commands is one you cannot push upstream
      # with.
      operations: [string]
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
# The whole procedures this server carries, named here because this is the call
# every task starts with. They are also served as typo3://guides resources, and
# a client that lists no resources renders none of them — four sessions in one
# week finished without learning they exist. Each is one typo3_rule_lookup call
# by documentId, which needs no resource list; a search over sections answers a
# question and never hands one of these over whole.
guides:  # optional
  - # What typo3_rule_lookup takes as documentId to return the whole document.
    id: string
    title: string
# One of: packages. packages: read from the files the installed packages ship,
# because the console could not be asked — overrides applied at runtime are
# not reflected.
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
`extensions`, `sites`, `commands`, `patches`, `guides`, `answeredBy` — or
`unsupported`.

## Answered

Recorded on 2026-08-08 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### project

Called with:

```json
{}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
<installation> — core-checkout, TYPO3 14.3.6-dev, PHP ^8.2, and the installed core requires ^8.2 — the lowest a package here may declare

Extensions: none beyond TYPO3's own.

Sites: none configured below config/sites/.

Commands this repository declares — these exist here, the core's testing suites do not. The core's suites are run by Build/Scripts/runTests.sh, which no manifest here declares. typo3_test_run_guide names the ones a change needs, with the invocation. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
Nothing in this repository configures an environment of its own — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what this reads — so these run wherever you run them.
- composer gerrit:setup (composer.json) — unknown: @gerrit:setup:commitMessageHook:enable && @gerrit:setup:preCommitHook:enable
- composer gerrit:setup:commitMessageHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enableCommitMessageHook
- composer gerrit:setup:preCommitHook:enable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::enablePreCommitHook
- composer gerrit:setup:preCommitHook:disable (composer.json) — unknown: TYPO3\CMS\Composer\Scripts\InstallerScripts::disablePreCommitHook

Whole procedures this server carries, each one typo3_rule_lookup with that documentId — no resource list needed, and none of them is answered by a search over sections:
- any/security/reporting-a-vulnerability — Reporting a TYPO3 Vulnerability
- core/contribution/commit-messages — TYPO3 Core Commit Message Rules
- core/contribution/gerrit-workflow — TYPO3 Gerrit Workflow
- core/contribution/rules — TYPO3 Core Contribution Rules
- core/contribution/sources — TYPO3 Contribution Sources
- core/testing/scripts — TYPO3 Core Script Help
- extension/documentation/manual — Setting Up an Extension Manual
- extension/testing/phpunit — Setting Up PHPUnit in a TYPO3 Extension
- project/testing/playwright — Setting Up Playwright in a TYPO3 Project
```

Data:

```json
{
    "root": "<installation>",
    "kind": "core-checkout",
    "typo3Version": "14.3.6-dev",
    "phpConstraint": "^8.2",
    "coreConstraint": null,
    "corePhpConstraint": "^8.2",
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
    "guides": [
        {
            "id": "any/security/reporting-a-vulnerability",
            "title": "Reporting a TYPO3 Vulnerability"
        },
        {
            "id": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules"
        },
        {
            "id": "core/contribution/gerrit-workflow",
            "title": "TYPO3 Gerrit Workflow"
        },
        {
            "id": "core/contribution/rules",
            "title": "TYPO3 Core Contribution Rules"
        },
        {
            "id": "core/contribution/sources",
            "title": "TYPO3 Contribution Sources"
        },
        {
            "id": "core/testing/scripts",
            "title": "TYPO3 Core Script Help"
        },
        {
            "id": "extension/documentation/manual",
            "title": "Setting Up an Extension Manual"
        },
        {
            "id": "extension/testing/phpunit",
            "title": "Setting Up PHPUnit in a TYPO3 Extension"
        },
        {
            "id": "project/testing/playwright",
            "title": "Setting Up Playwright in a TYPO3 Project"
        }
    ],
    "answeredBy": "packages"
}
```

#### From the installation this repository writes below .fixtures/, whose console answers

Text:

```
<installation> — composer-project, TYPO3 14.3.0, PHP ^8.2, and the installed core requires ^8.2 — the lowest a package here may declare

Extensions that are not TYPO3's own:
- acme_events (project) — packages/acme_events

Sites, with the sets each one depends on:
- fixture at https://fixture.example.org/, root page 1, sets: typo3/fluid-styled-content, acme/acme-events

Commands this repository declares — these exist here, the core's testing suites do not. What each one does to the sources is read off its body, never by running it: a check reports and leaves them as they are, a change rewrites something, and unknown is a body that does not say — a test suite runs the project's own code, and no declaration covers that. A task told not to change files can run the checks and nothing else. A check may still write a cache of its own; what it does not do is hand the code back different.
Nothing in this repository configures an environment of its own — .ddev/config.yaml and TYPO3_DEV_COMPANION_CONSOLE are what this reads — so these run wherever you run them.
- composer cgl (composer.json) — change: php-cs-fixer fix
- composer cgl:ci (composer.json) — check: php-cs-fixer fix --dry-run --diff
- composer test (composer.json) — unknown: phpunit -c Build/phpunit.xml

Whole procedures this server carries, each one typo3_rule_lookup with that documentId — no resource list needed, and none of them is answered by a search over sections:
- any/security/reporting-a-vulnerability — Reporting a TYPO3 Vulnerability
- core/contribution/commit-messages — TYPO3 Core Commit Message Rules
- core/contribution/gerrit-workflow — TYPO3 Gerrit Workflow
- core/contribution/rules — TYPO3 Core Contribution Rules
- core/contribution/sources — TYPO3 Contribution Sources
- core/testing/scripts — TYPO3 Core Script Help
- extension/documentation/manual — Setting Up an Extension Manual
- extension/testing/phpunit — Setting Up PHPUnit in a TYPO3 Extension
- project/testing/playwright — Setting Up Playwright in a TYPO3 Project
```

Data:

```json
{
    "root": "<installation>",
    "kind": "composer-project",
    "typo3Version": "14.3.0",
    "phpConstraint": "^8.2",
    "coreConstraint": "^14.3",
    "corePhpConstraint": "^8.2",
    "environment": null,
    "extensions": [
        {
            "key": "acme_events",
            "path": "packages/acme_events",
            "origin": "project"
        }
    ],
    "sites": [
        {
            "identifier": "fixture",
            "base": "https://fixture.example.org/",
            "rootPageId": 1,
            "sets": [
                "typo3/fluid-styled-content",
                "acme/acme-events"
            ],
            "languages": [
                "English"
            ]
        }
    ],
    "commands": [
        {
            "command": "composer cgl",
            "source": "composer.json",
            "declares": "php-cs-fixer fix",
            "runs": "change"
        },
        {
            "command": "composer cgl:ci",
            "source": "composer.json",
            "declares": "php-cs-fixer fix --dry-run --diff",
            "runs": "check"
        },
        {
            "command": "composer test",
            "source": "composer.json",
            "declares": "phpunit -c Build/phpunit.xml",
            "runs": "unknown"
        }
    ],
    "patches": [],
    "guides": [
        {
            "id": "any/security/reporting-a-vulnerability",
            "title": "Reporting a TYPO3 Vulnerability"
        },
        {
            "id": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules"
        },
        {
            "id": "core/contribution/gerrit-workflow",
            "title": "TYPO3 Gerrit Workflow"
        },
        {
            "id": "core/contribution/rules",
            "title": "TYPO3 Core Contribution Rules"
        },
        {
            "id": "core/contribution/sources",
            "title": "TYPO3 Contribution Sources"
        },
        {
            "id": "core/testing/scripts",
            "title": "TYPO3 Core Script Help"
        },
        {
            "id": "extension/documentation/manual",
            "title": "Setting Up an Extension Manual"
        },
        {
            "id": "extension/testing/phpunit",
            "title": "Setting Up PHPUnit in a TYPO3 Extension"
        },
        {
            "id": "project/testing/playwright",
            "title": "Setting Up Playwright in a TYPO3 Project"
        }
    ],
    "answeredBy": "packages"
}
```
