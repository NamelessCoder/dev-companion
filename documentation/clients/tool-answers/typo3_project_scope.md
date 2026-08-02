# What `typo3_project_scope` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks this page; [tools.md](../tools.md) is where the
current shape of an answer is, and [readme.md](readme.md) is what the recording
as a whole is of.

The core-checkout answer below was taken again on 2026-08-02 for `R-PRJ-008`,
which put the environment a repository configures for itself into this answer.
The E-SITE one was not: no `.environments/E-SITE` existed on the machine that
worked that requirement, and its site base names a `.ddev.site` host, so that
block is the older shape and its opening line is short one number.
`bin/cli environment:create E-SITE` followed by `bin/cli tools:record` is what
replaces it.

## project

Called with:

```json
{}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

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

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
<installation> — composer-project, TYPO3 14.3.5, PHP unconstrained

Extensions: none beyond TYPO3's own.

Sites, with the sets each one depends on:
- main at https://typo3-mcp-e-site.ddev.site/, root page 1, sets: typo3/fluid-styled-content, typo3/fluid-styled-content-css

This repository declares no commands of its own in composer.json or package.json. What to run is then whatever its CI configuration does.
```

Data:

```json
{
    "root": "<installation>",
    "kind": "composer-project",
    "typo3Version": "14.3.5",
    "phpConstraint": null,
    "coreConstraint": "^14.3",
    "extensions": [],
    "sites": [
        {
            "identifier": "main",
            "base": "https://typo3-mcp-e-site.ddev.site/",
            "rootPageId": 1,
            "sets": [
                "typo3/fluid-styled-content",
                "typo3/fluid-styled-content-css"
            ],
            "languages": [
                "English"
            ]
        }
    ],
    "commands": [],
    "patches": [],
    "answeredBy": "packages"
}
```
