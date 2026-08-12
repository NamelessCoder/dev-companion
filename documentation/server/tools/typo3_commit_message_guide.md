# `typo3_commit_message_guide`

Draft and check a TYPO3 commit message. The message is read by a person who
wants to know what the commit did, so write it in plain English and only as long
as that answer needs: the diff carries the detail. Either assemble one from
parts (changeType plus summary) or pass an existing message to check and correct
it. The returned draft is ready to commit: the body is wrapped at 72 characters,
and the checks name every run of lines the wrapping joined and every line it
could not bring under the width. Defaults to a repository of your own, where the
subject and body conventions apply but the Forge issue, the Releases: trailer
and the changelog do not; pass workflow="core" for a patch against the TYPO3
core, where the Releases: trailer is also held against the branches that take a
patch today. Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# A complete existing commit message to check, subject and trailers included.
# Unknown trailers such as Change-Id are kept, so an amended patch set stays
# valid.
message: string  # optional
# One of: core, project. Which rules to apply. "project", the default: any
# repository of your own — the keyword, the 52/72 character limits and the
# wrapping are checked, no trailer is added or demanded, and [SECURITY] is
# allowed. "core": a patch against the TYPO3 core, with the Forge issue and the
# Releases: trailer required.
workflow: string  # optional
# One of: BUGFIX, FEATURE, TASK, DOCS, SECURITY. TYPO3 commit message keyword.
# [SECURITY] is reserved for the TYPO3 Security Team and is only accepted with
# workflow="project".
changeType: string  # optional
# Summary text without the TYPO3 keyword prefix. Say what the commit did, in
# words a reader understands from the log alone.
summary: string  # optional
# Forge issue number, with or without leading #.
issue: string  # optional
# Optional related Forge issue numbers.
relatedIssues: [string]  # optional
# Target releases, for example main or 13.4. Left out, the draft carries a
# RELEASE_TARGET placeholder and the checks name the lines taking a patch today
# — the branches a change is released on are not guessed. Each one passed is
# held against those lines: a branch out of regular support is an error, since
# ELTS releases come from the ELTS partners rather than from a patch to that
# branch.
releases: [string]  # optional
# Optional commit body, for what the diff does not say: why the change was made,
# what it rests on. It is wrapped at 72 characters in the draft: indent a block
# to keep the line breaks you wrote, and keep those lines under the width
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

Derived by `bin/cli tools:index`, and `bin/cli tools:check` holds it — the
same as everything above this heading. This tool reads nothing an installation
contains: what reaches its answer is the bundled knowledge and which TYPO3
major the caller is on, so what comes back is written down rather than recorded
from one machine's checkout. Answered against the core checkout this repository
writes below .fixtures/, declaring TYPO3 14.3.0.

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
```

Checks:
- INFO: No commit message readiness issues found by the local checks.

Checked without the core workflow: keyword, 52/72 limits and wrapping apply, the Forge issue and the Releases: trailer do not. workflow="core" for a patch against the TYPO3 core.
````

Data:

```json
{
    "message": "[BUGFIX] Show hidden records in the import preview\n\nResolves: #106123",
    "checks": [
        {
            "level": "info",
            "code": "no-issues-found",
            "message": "No commit message readiness issues found by the local checks."
        }
    ],
    "workflow": "project"
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

Checked without the core workflow: keyword, 52/72 limits and wrapping apply, the Forge issue and the Releases: trailer do not. workflow="core" for a patch against the TYPO3 core.
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
    "workflow": "project"
}
```
