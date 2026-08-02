# What `typo3_documentation_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## documentation: unsupported version

Called with:

```json
{
    "queries": [
        "page title event"
    ],
    "targetVersion": "999"
}
```

Text:

```
Official TYPO3 documentation for 999.
Source: https://docs.typo3.org
Could not answer: TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main.
```

Data:

```json
{
    "mode": "search",
    "status": "unavailable",
    "targetVersion": "999",
    "source": "https://docs.typo3.org",
    "queries": [
        "page title event"
    ],
    "results": [],
    "unavailable": {
        "cause": "version-not-covered",
        "reason": "TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main."
    }
}
```
