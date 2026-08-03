# `typo3_commit_message_guide`

Draft and check a TYPO3 commit message. Either assemble one from parts
(changeType plus summary) or pass an existing message to check and correct it.
The returned draft is ready to commit: the body is wrapped at 72 characters, and
the checks name every run of lines the wrapping joined and every line it could
not bring under the width. Defaults to the core contribution rules; pass
workflow="project" in a project or extension repository of your own, where the
subject and body conventions apply but the Forge issue, the Releases: trailer
and the changelog do not. Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# A complete existing commit message to check, subject and trailers included.
# Unknown trailers such as Change-Id are kept, so an amended patch set stays
# valid.
message: string  # optional
# One of: core, project. Which rules to apply. "core": a patch against the TYPO3
# core, with the Forge issue and the Releases: trailer required. "project": any
# other repository — the keyword, the 52/72 character limits and the wrapping
# are checked, no trailer is added or demanded, and [SECURITY] is allowed.
workflow: string  # optional
# One of: BUGFIX, FEATURE, TASK, DOCS, SECURITY. TYPO3 commit message keyword.
# [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with
# workflow="project".
changeType: string  # optional
# Summary text without the TYPO3 keyword prefix.
summary: string  # optional
# Forge issue number, with or without leading #.
issue: string  # optional
# Optional related Forge issue numbers.
relatedIssues: [string]  # optional
# Target releases, for example main or 13.4. Left out, the draft carries a
# RELEASE_TARGET placeholder and the checks ask for it — the branches a change
# is released on are not guessed.
releases: [string]  # optional
# Optional commit body. It is wrapped at 72 characters in the draft: indent a
# block to keep the line breaks you wrote, and keep those lines under the width
# yourself.
body: string  # optional
# Whether this is a breaking change requiring [!!!]. Left out, the checks say
# the classification was assumed: it is a property of the diff, which this tool
# never sees.
isBreaking: boolean  # optional
# Whether this is a deprecation. Left out, it is assumed the same way and the
# checks say so.
isDeprecation: boolean  # optional
```

## Answers with

```yaml
# The commit message, ready to use.
message: string
checks:
  - # One of: error, warning, info.
    level: string
    # Stable identifier of the check, for example summary-too-long.
    code: string
    message: string
# One of: core, project. Which rules the draft was written and checked against.
# "core" adds the Forge issue and the Releases: trailer and demands them;
# "project" applies the subject and body rules alone.
workflow: string
```

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### commit: from parts

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
- INFO: The subject carries no [!!!] and the call passed no isBreaking, so the classification was assumed rather than checked. It is a property of the diff, which this tool never sees: a removed or narrowed public or protected member makes the change breaking. A breaking change owes [!!!], a Breaking changelog entry and an extension scanner matcher. isDeprecation is assumed the same way. Confirm both against the diff and call again with what you found; typo3_rule_lookup(query "breaking change") has the rules.

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
        },
        {
            "level": "info",
            "code": "breaking-not-assessed",
            "message": "The subject carries no [!!!] and the call passed no isBreaking, so the classification was assumed rather than checked. It is a property of the diff, which this tool never sees: a removed or narrowed public or protected member makes the change breaking. A breaking change owes [!!!], a Breaking changelog entry and an extension scanner matcher. isDeprecation is assumed the same way. Confirm both against the diff and call again with what you found; typo3_rule_lookup(query \"breaking change\") has the rules."
        }
    ],
    "workflow": "core"
}
```

### commit: from a message

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
- INFO: The subject carries no [!!!] and the call passed no isBreaking, so the classification was assumed rather than checked. It is a property of the diff, which this tool never sees: a removed or narrowed public or protected member makes the change breaking. A breaking change owes [!!!], a Breaking changelog entry and an extension scanner matcher. isDeprecation is assumed the same way. Confirm both against the diff and call again with what you found; typo3_rule_lookup(query "breaking change") has the rules.

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
        },
        {
            "level": "info",
            "code": "breaking-not-assessed",
            "message": "The subject carries no [!!!] and the call passed no isBreaking, so the classification was assumed rather than checked. It is a property of the diff, which this tool never sees: a removed or narrowed public or protected member makes the change breaking. A breaking change owes [!!!], a Breaking changelog entry and an extension scanner matcher. isDeprecation is assumed the same way. Confirm both against the diff and call again with what you found; typo3_rule_lookup(query \"breaking change\") has the rules."
        }
    ],
    "workflow": "core"
}
```
