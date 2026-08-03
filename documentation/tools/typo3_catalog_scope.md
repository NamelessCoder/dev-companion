# `typo3_catalog_scope`

Report whether component contracts come from the active installation or the
bundled fallback, which TYPO3 core revision the fallback catalogs were taken
from, what they cover, and how to re-check them. Call this to judge whether a
lookup miss is authoritative: even with installed sources, component names
remain a curated index rather than every backend class. Answers from: packages,
knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`packages`](answer-sources.md#packages),
[`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# The TYPO3 version to report the catalog's coverage for, for example "13.4" or
# "14". Defaults to the version of the installation this server was started in.
targetVersion: string  # optional
```

## Answers with

```yaml
# The core revision behind catalog answers, and how it relates to the
# installation being read. A miss means "not in this snapshot".
catalog:
  repository: string  # optional
  branch: string
  # TYPO3 version of the snapshot.
  version: string
  # Core revision the catalogs were taken from.
  commit: string
  verifiedAt: string
  # TYPO3 version of the installation this server was started in, where there is
  # one. Null means there was nothing to compare the snapshot with.
  installedVersion: string or null  # optional
  # Set when that installation and the snapshot are different TYPO3 majors, and
  # what to do about it. Null when they agree or nothing is known.
  skew: string or null  # optional
verifyCommand: string
# One entry per catalog describing what it contains.
scope: object
# One entry per catalog with its number of entries.
counts: object
# The TYPO3 major the coverage was reported for — stated by the caller, or
# read from the installation. Null means the whole catalog answers.
targetVersion: integer or null  # optional
# How many components were verified on that version.
verifiedCount: integer
# One of: installation, catalog.
componentSource: string
# Components this catalog has but was never verified on the target version. Left
# out of components rather than handed over — an empty answer here means "not
# verified where you are", not "does not exist".
withheld:
  - name: string
    title: string
    # What to verify the entry against on the target version.
    sassPaths: [string]  # optional
    demoPath: string or null  # optional
    # The TYPO3 major this entry starts holding at, or null when it holds on
    # every covered version.
    since: integer or null  # optional
    # The TYPO3 major it stops holding after, or null when nothing has replaced
    # it.
    until: integer or null  # optional
    # The same range as a sentence, empty when the entry holds on every covered
    # version.
    verifiedOn: string
```

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### catalog scope

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
