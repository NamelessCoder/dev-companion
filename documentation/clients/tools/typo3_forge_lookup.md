# `typo3_forge_lookup`

Read a TYPO3 issue from the tracker at forge.typo3.org before writing a patch
for it: subject, tracker, status, target version, the TYPO3 and PHP versions it
was reported against, related issues, and the comments — where a maintainer who
closed or reassigned it said why, which the description never says. An issue
that does not exist is answered as such, and so is a tracker that could not be
reached. Reading only, and no credential: commenting, assigning and closing stay
yours.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: true`

## Takes

```yaml
# Forge issue number, with or without the leading #, for example "110348".
issue: string
```

## Answers with

```yaml
# One of: answered, empty, unavailable.
status: string
# The tracker the answer came from.
source: string
# What was read, so the same question can be asked again by hand.
url: string
# The issue, where status says answered. Null otherwise.
issue:
  id: integer
  subject: string
  # New, Accepted, Resolved, Closed, Rejected — the tracker's own word.
  status: string
  # Bug, Feature, Task, Epic.
  tracker: string
  priority: string
  # The release it is scheduled for, empty where none is set.
  targetVersion: string
  # The TYPO3 version it was reported against, which is not the version it still
  # reproduces on.
  typo3Version: string
  phpVersion: string
  createdOn: string
  updatedOn: string
  # Where a person reads it.
  url: string
  # The report as it was written, which is what the reporter saw and not what
  # was decided.
  description: string
  # Issues this one is filed against, which is where a duplicate or a blocker is
  # named.
  relations:
    - # The other issue.
      issue: integer
      # duplicates, relates, blocked, precedes.
      relation: string
  # How many comments the issue carries in total.
  noteCount: integer
  # The most recent comments, oldest first. A closure, a reassignment and a "we
  # will not do this" are here rather than in the description.
  notes:
    - author: string
      on: string
      note: string
# Why nothing was answered, where status says unavailable. Null otherwise.
unavailable:
  # One of: source-not-answering, source-not-parseable. source-not-answering:
  # the tracker did not answer this time. source-not-parseable: something
  # answered with a page rather than with the API, which is what the bot
  # protection in front of it looks like from here.
  cause: string
  reason: string
```

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/, whose
console could not be reached: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
`bin/cli tools:check` holds it.

### forge: what an issue says and what was decided

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

### forge: no such issue

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
