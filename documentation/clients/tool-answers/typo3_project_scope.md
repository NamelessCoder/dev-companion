# What `typo3_project_scope` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## project

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
