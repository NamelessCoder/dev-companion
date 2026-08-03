# What `typo3_forge_lookup` answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## forge: what an issue says and what was decided

Called with:

```json
{
    "issue": "110348"
}
```

Text:

```
#110348 Rework AdminPanel "imagesOnPage" feature
Task · Resolved · priority Should have · https://forge.typo3.org/issues/110348
Target version: 15.0
Reported against TYPO3 15 — which is what the reporter had, not what it still reproduces on.

## Reported
The "imagesOnPage" feature is older than git. It needs to be revised to be integrated into FAL.

## Comments (3 of 3, oldest first)
What was decided is here rather than above.

**Gerrit Code Review**, 2026-07-31T19:23:24Z
Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

**Gerrit Code Review**, 2026-08-01T06:13:04Z
Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

**Benni Mack**, 2026-08-02T20:45:10Z
Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66.
```

Data:

```json
{
    "status": "answered",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/issues/110348.json?include=journals,relations",
    "issue": {
        "id": 110348,
        "subject": "Rework AdminPanel \"imagesOnPage\" feature",
        "status": "Resolved",
        "tracker": "Task",
        "priority": "Should have",
        "targetVersion": "15.0",
        "typo3Version": "15",
        "phpVersion": "",
        "createdOn": "2026-07-31T18:06:11Z",
        "updatedOn": "2026-08-02T20:45:10Z",
        "url": "https://forge.typo3.org/issues/110348",
        "description": "The \"imagesOnPage\" feature is older than git. It needs to be revised to be integrated into FAL.",
        "relations": [],
        "noteCount": 3,
        "notes": [
            {
                "author": "Gerrit Code Review",
                "on": "2026-07-31T19:23:24Z",
                "note": "Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
            },
            {
                "author": "Gerrit Code Review",
                "on": "2026-08-01T06:13:04Z",
                "note": "Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
            },
            {
                "author": "Benni Mack",
                "on": "2026-08-02T20:45:10Z",
                "note": "Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66."
            }
        ]
    },
    "unavailable": null
}
```

## forge: no such issue

Called with:

```json
{
    "issue": "99999999"
}
```

Text:

```
TYPO3 issue tracker: no issue 99999999 at https://forge.typo3.org.
```

Data:

```json
{
    "status": "empty",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/issues/99999999.json?include=journals,relations",
    "issue": null,
    "unavailable": null
}
```
