# `typo3_gerrit_lookup`

Find out whether a TYPO3 core patch already exists, from the review server at
review.typo3.org. Pass issue with a Forge issue number to search the commit
messages of every change for it — the question "has somebody already fixed this"
— or change with a change number to read one. Answers with the change number,
subject, status, target branch and review URL. A call carries issue or change,
never both. This reaches the network, and it reads: reviewing, voting and
uploading stay yours.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: true`

## Takes

```yaml
# Forge issue number, with or without the leading #, for example "105403".
# Searches every change whose commit message names it, which is where Resolves:
# and Related: put it. A call carries issue or change, never both.
issue: string  # optional
# Gerrit change number, the digits a review URL ends with, for example "89011".
# A call carries issue or change, never both.
change: string  # optional
limit: integer  # optional
```

The call carries exactly one of these sets of arguments: `issue` — or `change`.

## Answers with

```yaml
# One of: answered, empty, unavailable.
status: string
# The review server the answer came from.
source: string
# The Gerrit query this was answered with, so the same question can be asked
# again by hand.
query: string
# The changes that matched, newest activity first.
changes:
  - # Change number, the digits its review URL ends with.
    number: integer  # optional
    # The commit subject.
    subject: string  # optional
    # NEW while it is open, MERGED once it landed, ABANDONED when it was given
    # up.
    status: string  # optional
    # The branch the change targets.
    branch: string  # optional
    # The Gerrit project it was pushed to.
    project: string  # optional
    # When the change last moved.
    updated: string  # optional
    # Where a person reads the review.
    url: string  # optional
# Why nothing was answered, where status says unavailable. Null otherwise.
unavailable:
  # One of: source-not-answering, source-not-parseable. source-not-answering:
  # review.typo3.org did not answer this time, and the same call may answer the
  # next. source-not-parseable: something answered and it was not the review
  # API, which is what a proxy or a captive portal looks like from here.
  cause: string
  reason: string
```

## Answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/, whose
console could not be reached: <installation> has no TYPO3 console — none of
bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this heading;
everything above it is derived from the class that answers the call, and
`bin/cli tools:check` holds it.

### gerrit: has this issue a patch already

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

### gerrit: one change by number

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
