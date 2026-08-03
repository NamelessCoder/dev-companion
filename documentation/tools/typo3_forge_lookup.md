# `typo3_forge_lookup`

Read the TYPO3 issue tracker at forge.typo3.org before writing a patch. Pass
issue with a number to read that one: subject, tracker, status, target version,
the TYPO3 and PHP versions it was reported against, related issues, and the
comments — where a maintainer who closed or reassigned it said why, which the
description never says. Or pass query with words to find out which other issues
describe the same thing, which the relations of one issue only answer for what
somebody linked by hand; each hit comes back with its number, subject, tracker,
status and URL. A call carries issue or query, never both. An issue that does
not exist is answered as such, and so is a tracker that could not be reached.
Reading only, and no credential: commenting, assigning and closing stay yours.
Answers from: network.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: true`

Answers from [`network`](answer-sources.md#network).

## Takes

```yaml
# Forge issue number, with or without the leading #, for example "110348". Reads
# that one issue whole, comments included. A call carries issue or query, never
# both.
issue: string  # optional
# Words to search the tracker for, for example "image cache busting". Answers
# the issues whose text matches them — which is how a duplicate nobody has
# linked is found at all, since the relations of an issue only carry what
# somebody linked by hand. Nothing is ranked and one wording does not settle it:
# ask again in the reporter's words as well as your own, because an issue worded
# differently is invisible to this. A call carries issue or query, never both.
query: string  # optional
limit: integer  # optional
```

The call carries exactly one of these sets of arguments: `issue` — or `query`.

## Answers with

```yaml
# One of: answered, empty, unavailable.
status: string
# The tracker the answer came from.
source: string
# What was read, so the same question can be asked again by hand.
url: string
# The words the tracker was searched for, so a set that looks too narrow can be
# asked again in other words. Empty where an issue was read by number.
query: string
# The issue, where status says answered and a number was asked for. Null
# otherwise.
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
# The issues whose text matches the query, in the tracker's own order —
# nothing here ranks them, and what a hit is worth is the caller's to judge.
# Empty where an issue was read by number.
results:
  - # The issue number, which is what this tool reads whole.
    issue: integer
    subject: string
    # Bug, Feature, Task, Epic.
    tracker: string
    # Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.
    status: string
    # Where a person reads it.
    url: string
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
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

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
    "query": "",
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
    "results": [],
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
    "query": "",
    "issue": null,
    "results": [],
    "unavailable": null
}
```

### forge: which other issues describe this

Called with:

```json
{
    "query": "cache busting",
    "limit": 3
}
```

Text:

```
TYPO3 issue tracker: 3 issues match "cache busting"
These words, in the tracker's own order and unranked. Another wording finds another set, so this is which issues mention it rather than which one it duplicates. Read one whole by passing its number as issue.

## #107904 Cache-busting applied to folder paths
Bug · Closed · https://forge.typo3.org/issues/107904

## #107869 Add option to not add cache busting to generated URIs
Bug · Closed · https://forge.typo3.org/issues/107869

## #105953 f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled
Bug · Closed · https://forge.typo3.org/issues/105953
```

Data:

```json
{
    "status": "answered",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/search.json?q=cache%20busting&issues=1&limit=3",
    "query": "cache busting",
    "issue": null,
    "results": [
        {
            "issue": 107904,
            "subject": "Cache-busting applied to folder paths",
            "tracker": "Bug",
            "status": "Closed",
            "url": "https://forge.typo3.org/issues/107904"
        },
        {
            "issue": 107869,
            "subject": "Add option to not add cache busting to generated URIs",
            "tracker": "Bug",
            "status": "Closed",
            "url": "https://forge.typo3.org/issues/107869"
        },
        {
            "issue": 105953,
            "subject": "f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled",
            "tracker": "Bug",
            "status": "Closed",
            "url": "https://forge.typo3.org/issues/105953"
        }
    ],
    "unavailable": null
}
```

### forge: nothing matches these words

Called with:

```json
{
    "query": "quantumflux transponder"
}
```

Text:

```
TYPO3 issue tracker: no issue matches "quantumflux transponder" at https://forge.typo3.org.
These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a word search. Ask again in the words a reporter would have used.
```

Data:

```json
{
    "status": "empty",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/search.json?q=quantumflux%20transponder&issues=1&limit=15",
    "query": "quantumflux transponder",
    "issue": null,
    "results": [],
    "unavailable": null
}
```
