# What `typo3_translation_domain_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## domain: EXT reference

Called with:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf"
}
```

Text:

```
EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf resolves to the translation domain:

  backend.alt_doc

Reference a label in it as "backend.alt_doc:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
Composed for the installation here, TYPO3 14.3.6-dev. State targetVersion where the label is being written for another branch.
Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.
```

Data:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": 14,
    "domain": "backend.alt_doc",
    "domainOnNewerVersions": null
}
```

## domain: checkout path

Called with:

```json
{
    "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf"
}
```

Text:

```
typo3/sysext/core/Resources/Private/Language/locallang.xlf resolves to the translation domain:

  core.messages

Reference a label in it as "core.messages:<trans-unit id>" — in TCA, in LanguageService::sL(), and in f:translate as separate domain and key attributes.
Composed for the installation here, TYPO3 14.3.6-dev. State targetVersion where the label is being written for another branch.
Which trans-units the file actually holds is a property of your checkout: read the file, and remember that an installation can override it through LANG/resourceOverrides.
```

Data:

```json
{
    "path": "typo3/sysext/core/Resources/Private/Language/locallang.xlf",
    "targetVersion": 14,
    "domain": "core.messages",
    "domainOnNewerVersions": null
}
```

## domain: on an older target

Called with:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": "13.4"
}
```

Text:

```
TYPO3 13, which you asked about, has no translation domains: the API that resolves them arrived after it. Reference the file itself instead:

  LLL:EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf:<trans-unit id>

For the record, the domain this path would resolve to on a version that has them is "backend.alt_doc". Writing it into a label there renders nothing, and fails at runtime rather than at build time.
```

Data:

```json
{
    "path": "EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf",
    "targetVersion": 13,
    "domain": null,
    "domainOnNewerVersions": "backend.alt_doc"
}
```

## domain: miss

Called with:

```json
{
    "path": "somewhere/else.xlf"
}
```

Text:

```
"somewhere/else.xlf" names no extension, so no translation domain follows from it.
Pass either an EXT: reference ("EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf") or a checkout path ("typo3/sysext/backend/Resources/Private/Language/locallang_alt_doc.xlf").
```

Data:

```json
{
    "path": "somewhere/else.xlf",
    "targetVersion": 14,
    "domain": null
}
```
