# What `typo3_schema_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## schema: one table

Called with:

```json
{
    "table": "tt_content"
}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": "tt_content",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

## schema: every table

Called with:

```json
{}
```

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "table": null,
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```
