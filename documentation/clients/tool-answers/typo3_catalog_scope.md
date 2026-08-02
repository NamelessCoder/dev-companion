# What `typo3_catalog_scope` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## catalog scope

Called with:

```json
{}
```

Text:

```
Installed component contract
For TYPO3 v14, 25 of the 25 curated component entries were found in the installed backend CSS or JavaScript. Their class and custom-property contracts were read from those packages.
The bundled catalog remains the curated search index and markup fallback; it does not override installed classes.

Bundled fallback source checkout
- Source: https://github.com/TYPO3/typo3
- Checkout branch: main (TYPO3 15.0)
- Commit: 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0
- Verified: 2026-07-28
- Re-check with: `bin/cli catalog:paths /path/to/typo3-core-checkout`

Scope
- components: The bundled fallback and curated search index for backend UI components, with markup, Sass source paths, and the TYPO3 majors each entry was verified on. When the target is the active installation, its backend CSS and JavaScript replace the class and custom-property contract, and an installed styleguide example replaces fallback markup where available. The index remains a subset, not every CSS class in the core.
- systemExtensions: Every system extension of every covered TYPO3 line, read off one checkout per version: the extension key, the Composer package name to require it by, what it is for, and the majors that ship it. Complete rather than curated — `bin/cli catalog:check` re-derives it, so a release that adds or drops one is reported.

Counts
- components: 25
- systemExtensions: 38

A lookup miss means the component is not in the curated search index. The installed backend CSS may still contain an uncatalogued class, so inspect it before concluding the class does not exist.
```

Data:

```json
{
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    },
    "verifyCommand": "bin/cli catalog:paths /path/to/typo3-core-checkout",
    "scope": {
        "components": "The bundled fallback and curated search index for backend UI components, with markup, Sass source paths, and the TYPO3 majors each entry was verified on. When the target is the active installation, its backend CSS and JavaScript replace the class and custom-property contract, and an installed styleguide example replaces fallback markup where available. The index remains a subset, not every CSS class in the core.",
        "systemExtensions": "Every system extension of every covered TYPO3 line, read off one checkout per version: the extension key, the Composer package name to require it by, what it is for, and the majors that ship it. Complete rather than curated — `bin/cli catalog:check` re-derives it, so a release that adds or drops one is reported."
    },
    "counts": {
        "components": 25,
        "systemExtensions": 38
    },
    "targetVersion": 14,
    "verifiedCount": 25,
    "componentSource": "installation",
    "withheld": []
}
```
