# What `typo3_gerrit_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## gerrit: has this issue a patch already

Called with:

```json
{
    "issue": "110348",
    "limit": 3
}
```

Text:

```
TYPO3 core review server: https://review.typo3.org
Query: message:110348

## [TASK] Deprecate AssetCollector media handling (MERGED)
Change 95040 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040
Last moved: 2026-08-02 20:40:50.000000000
```

Data:

```json
{
    "status": "answered",
    "source": "https://review.typo3.org",
    "query": "message:110348",
    "changes": [
        {
            "number": 95040,
            "subject": "[TASK] Deprecate AssetCollector media handling",
            "status": "MERGED",
            "branch": "main",
            "project": "Packages/TYPO3.CMS",
            "updated": "2026-08-02 20:40:50.000000000",
            "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
        }
    ],
    "unavailable": null
}
```

## gerrit: one change by number

Called with:

```json
{
    "change": "89011"
}
```

Text:

```
TYPO3 core review server: https://review.typo3.org
Query: change:89011

## [TASK] Raise --dev phpunit/phpunit:^11.5.17 (MERGED)
Change 89011 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/89011
Last moved: 2025-04-09 19:01:42.000000000
```

Data:

```json
{
    "status": "answered",
    "source": "https://review.typo3.org",
    "query": "change:89011",
    "changes": [
        {
            "number": 89011,
            "subject": "[TASK] Raise --dev phpunit/phpunit:^11.5.17",
            "status": "MERGED",
            "branch": "main",
            "project": "Packages/TYPO3.CMS",
            "updated": "2025-04-09 19:01:42.000000000",
            "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/89011"
        }
    ],
    "unavailable": null
}
```
