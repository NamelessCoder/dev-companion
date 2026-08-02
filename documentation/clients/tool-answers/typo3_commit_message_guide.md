# What `typo3_commit_message_guide` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## commit: from parts

Called with:

```json
{
    "changeType": "BUGFIX",
    "summary": "Show hidden records in the import preview",
    "issue": "106123"
}
```

Text:

````
Commit message draft:
```text
[BUGFIX] Show hidden records in the import preview

Resolves: #106123
Releases: RELEASE_TARGET
```

Checks:
- WARNING: The draft carries "Releases: RELEASE_TARGET". Replace it with the target versions, for example "Releases: main, 13.4".

Checked against the core contribution rules, trailers included. workflow="project" applies the same subject and body rules without the Forge issue and the Releases: trailer.
````

Data:

```json
{
    "message": "[BUGFIX] Show hidden records in the import preview\n\nResolves: #106123\nReleases: RELEASE_TARGET",
    "checks": [
        {
            "level": "warning",
            "code": "missing-releases",
            "message": "The draft carries \"Releases: RELEASE_TARGET\". Replace it with the target versions, for example \"Releases: main, 13.4\"."
        }
    ],
    "workflow": "core"
}
```

## commit: from a message

Called with:

```json
{
    "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main"
}
```

Text:

````
Commit message, corrected:
```text
[TASK] Do a thing

Body.

Resolves: #1
Releases: main
```

Checks:
- INFO: No commit message readiness issues found by the local checks.

Checked against the core contribution rules, trailers included. workflow="project" applies the same subject and body rules without the Forge issue and the Releases: trailer.
````

Data:

```json
{
    "message": "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
    "checks": [
        {
            "level": "info",
            "code": "no-issues-found",
            "message": "No commit message readiness issues found by the local checks."
        }
    ],
    "workflow": "core"
}
```
