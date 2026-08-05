# `typo3_forge_lookup`

Read the TYPO3 issue tracker at forge.typo3.org before writing a patch. Pass
issue with a number to read that one: subject, tracker, status, target version,
the TYPO3 and PHP versions it was reported against, related issues, and the
comments — where a maintainer who closed or reassigned it said why, which the
description never says. Or pass query with words to find out which other issues
describe the same thing, which the relations of one issue only answer for what
somebody linked by hand. Or pass open to enumerate the core project's unresolved
issues without holding a number or a wording — oldest filed or longest
untouched, narrowed by tracker and by date, which is where a triage of the
backlog starts; the count of everything that matched comes back with the page,
so a limited answer says whether it is the whole set. Each entry carries its
number, subject, tracker, status and URL. A call carries issue, query or open,
never two of them. An issue that does not exist is answered as such, and so is a
tracker that could not be reached. Reading only, and no credential: commenting,
assigning and closing stay yours. Answers from: network.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: true`

Answers from [`network`](answer-sources.md#network).

## Takes

```yaml
# Forge issue number, with or without the leading #, for example "110348". Reads
# that one issue whole, comments included. A call carries issue, query or open,
# never two of them.
issue: string  # optional
# Words to search the tracker for, for example "image cache busting". Answers
# the issues whose text matches them — which is how a duplicate nobody has
# linked is found at all, since the relations of an issue only carry what
# somebody linked by hand. Nothing is ranked and one wording does not settle it:
# ask again in the reporter's words as well as your own, because an issue worded
# differently is invisible to this. A call carries issue, query or open, never
# two of them.
query: string  # optional
# One of: oldest, stale. Enumerate the core project's unresolved issues instead
# of reading one or matching words: "oldest" orders them by when they were
# filed, "stale" by how long nobody has touched them. The two answer different
# questions about one backlog — filed long ago is about the report, untouched
# for years is about the attention it got — and an issue that is both is the
# candidate a triage is looking for. Unresolved is the tracker's own set of open
# statuses, so New, Accepted, Under Review, Needs Feedback, On Hold and
# Postponed are all in it. Narrow with tracker, createdBefore and updatedBefore.
# A call carries issue, query or open, never two of them.
open: string  # optional
# One of: Bug, Feature, Major Feature, Support, Task, Story, Suggestion,
# Impediment, Epic, Work Package, Topic. Only issues filed under this tracker,
# for example "Bug". Worth setting before reading a set: an old Bug and an old
# Feature are two different findings, since one claims something is broken today
# and the other that something was wanted once. Narrows open and is ignored by
# issue and query.
tracker: string  # optional
# Only issues the core files under this area, in your own words: "rte", "backend
# ui", "workspaces", "fluid". Matched against the project's own category names
# one word at a time, so a half-remembered name reaches the right area and a
# word naming several — "backend" — selects all of them and says which. That
# is the way in for "are there known bugs in the RTE" and "the oldest issues in
# the backend UI", which no wording of the report itself reaches. The categories
# that exist come back with every answer, so a word matching none is corrected
# without a second call. Narrows open and is ignored by issue and query.
category: string  # optional
# Only issues filed before this day, as YYYY-MM-DD. Narrows open and is ignored
# by issue and query.
createdBefore: string  # optional
# Only issues nobody has touched since this day, as YYYY-MM-DD. This is the one
# that finds a report everybody has walked past, which age alone does not: an
# issue filed in 2009 and commented on last month is being worked. Narrows open
# and is ignored by issue and query.
updatedBefore: string  # optional
# How many entries come back. A search answers with at most 25 whatever is asked
# for, because a set that has to be paged through is answered by other words
# rather than by more of these.
limit: integer  # optional
```

The call carries exactly one of these sets of arguments: `issue` — or `query` —
or `open`.

## Answers with

```yaml
# One of: answered, empty, unavailable.
status: string
# The tracker the answer came from.
source: string
# What was read, so the same question can be asked again by hand.
url: string
# The words the tracker was searched for, so a set that looks too narrow can be
# asked again in other words. Empty where an issue was read by number and where
# the open issues were enumerated.
query: string
# How many issues matched in total, of which results carries at most limit.
# Where the two differ the answer is a page and not the set, and asking for more
# of it is a narrower filter rather than a bigger limit. Zero where an issue was
# read by number.
total: integer
# Every area the core files its issues under, read from the project itself.
# Answered on every enumeration, so a category word that matched none is
# corrected from the answer rather than from a second call. Empty where an issue
# was read by number and where words were searched for.
categories: [string]
# The categories the category word resolved to, in the tracker's own spelling.
# Empty where none was asked for — and empty where the word matched none,
# which is answered as no issues and is a statement about the word rather than
# about the backlog.
categoriesUsed: [string]
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
  # Who the tracker says holds this, empty where nobody does. An assignee is not
  # a promise that somebody is working on it — on an issue nothing has moved
  # on for years it is usually who last did.
  assignedTo: string
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
# The issues the query matched or the enumeration selected, in the tracker's own
# order — nothing here ranks them, and what an entry is worth is the caller's
# to judge. Empty where an issue was read by number.
results:
  - # The issue number, which is what this tool reads whole.
    issue: integer
    subject: string
    # Bug, Feature, Task, Epic.
    tracker: string
    # Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.
    status: string
    # The area the core files it under, empty where none is set or where the
    # entry did not carry it. Empty on a query, whose hits are titles rather
    # than issues.
    category: string
    # Who the tracker says holds this, empty where nobody does or where the
    # entry did not carry it. What it decides for a triage is whether the issue
    # is free to take, and on an old one it is usually who last touched it
    # rather than who is on it.
    assignedTo: string
    # When it was filed. Empty on a query, whose hits are titles rather than
    # issues.
    createdOn: string
    # When anything last moved on it, which is the measure of neglect rather
    # than of age. Empty on a query.
    updatedOn: string
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

Recorded on 2026-08-05 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 15.0.0-dev, an installation outside this repository, whose
console could not be reached: the DDEV project is paused — start it with
"ddev start" in <installation> to answer from the installation. Nothing checks
what is below this heading; everything above it is derived from the class that
answers the call, and `bin/cli tools:check` holds it.

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
Assigned to Benni Mack — which says who holds it and not that somebody is working on it.
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
    "total": 0,
    "categories": [],
    "categoriesUsed": [],
    "issue": {
        "id": 110348,
        "subject": "Rework AdminPanel \"imagesOnPage\" feature",
        "status": "Resolved",
        "tracker": "Task",
        "priority": "Should have",
        "assignedTo": "Benni Mack",
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
    "total": 0,
    "categories": [],
    "categoriesUsed": [],
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
    "total": 15,
    "categories": [],
    "categoriesUsed": [],
    "issue": null,
    "results": [
        {
            "issue": 107904,
            "subject": "Cache-busting applied to folder paths",
            "tracker": "Bug",
            "status": "Closed",
            "category": "",
            "assignedTo": "",
            "createdOn": "",
            "updatedOn": "",
            "url": "https://forge.typo3.org/issues/107904"
        },
        {
            "issue": 107869,
            "subject": "Add option to not add cache busting to generated URIs",
            "tracker": "Bug",
            "status": "Closed",
            "category": "",
            "assignedTo": "",
            "createdOn": "",
            "updatedOn": "",
            "url": "https://forge.typo3.org/issues/107869"
        },
        {
            "issue": 105953,
            "subject": "f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled",
            "tracker": "Bug",
            "status": "Closed",
            "category": "",
            "assignedTo": "",
            "createdOn": "",
            "updatedOn": "",
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
    "total": 0,
    "categories": [],
    "categoriesUsed": [],
    "issue": null,
    "results": [],
    "unavailable": null
}
```

### forge: the oldest issues nobody has resolved

Called with:

```json
{
    "open": "oldest",
    "limit": 3
}
```

Text:

```
TYPO3 issue tracker: 3 of 2487 open issues of the TYPO3 Core project, oldest filed first
This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end.
Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.

## #14277 Start/Stop time for pages is ignored in standard menu objects
Feature · Accepted · Frontend · unassigned · filed 2004-08-20 · last touched 2025-04-04 · https://forge.typo3.org/issues/14277

## #14858 extended clipboard: setCopyMode can`t be set to copy by default
Bug · New · Backend User Interface · unassigned · filed 2005-07-11 · last touched 2026-01-23 · https://forge.typo3.org/issues/14858

## #15984 menu.showAccessRestrictedPages doesn't replace link for  "include subpages"
Bug · Accepted · Frontend · unassigned · filed 2006-04-05 · last touched 2026-04-15 · https://forge.typo3.org/issues/15984
```

Data:

```json
{
    "status": "answered",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=created_on%3Aasc&limit=3",
    "query": "",
    "total": 2487,
    "categories": [
        "AdminPanel",
        "Authentication",
        "Backend API",
        "Backend JavaScript",
        "Backend User Interface",
        "Caching",
        "Categorization API",
        "CLI",
        "Code Cleanup",
        "composer",
        "Content Rendering",
        "Content Security Policy",
        "Dashboard",
        "Database API (Doctrine DBAL)",
        "DataHandler aka TCEmain",
        "Documentation",
        "Extbase",
        "Extbase + l10n",
        "Extension Manager",
        "felogin",
        "File Abstraction Layer (FAL)",
        "Fluid",
        "Fluid Styled Content",
        "Form Framework",
        "FormEngine aka TCEforms",
        "Frontend",
        "Image Cropping",
        "Image Generation / GIFBUILDER",
        "Import/Export (T3D)",
        "Indexed Search",
        "Install Tool",
        "Language Manager (backend)",
        "Link Handling & Redirect Handling",
        "Linkvalidator",
        "Localization",
        "Locking / Session Handling",
        "Logging",
        "Mailer API",
        "Miscellaneous",
        "Pagetree",
        "Performance",
        "Recycler",
        "Reports",
        "RTE (rtehtmlarea + ckeditor)",
        "scheduler",
        "Security",
        "SEO",
        "Site Handling, Site Sets & Routing",
        "System/Bootstrap/Configuration",
        "t3editor",
        "Tests",
        "Themes",
        "TypoScript",
        "WebHooks - Incoming = Reactions + Outgoing",
        "Workspaces"
    ],
    "categoriesUsed": [],
    "issue": null,
    "results": [
        {
            "issue": 14277,
            "subject": "Start/Stop time for pages is ignored in standard menu objects",
            "tracker": "Feature",
            "status": "Accepted",
            "category": "Frontend",
            "assignedTo": "",
            "createdOn": "2004-08-20T08:45:13Z",
            "updatedOn": "2025-04-04T06:59:33Z",
            "url": "https://forge.typo3.org/issues/14277"
        },
        {
            "issue": 14858,
            "subject": "extended clipboard: setCopyMode can`t be set to copy by default",
            "tracker": "Bug",
            "status": "New",
            "category": "Backend User Interface",
            "assignedTo": "",
            "createdOn": "2005-07-11T23:31:03Z",
            "updatedOn": "2026-01-23T08:30:36Z",
            "url": "https://forge.typo3.org/issues/14858"
        },
        {
            "issue": 15984,
            "subject": "menu.showAccessRestrictedPages doesn't replace link for  \"include subpages\"",
            "tracker": "Bug",
            "status": "Accepted",
            "category": "Frontend",
            "assignedTo": "",
            "createdOn": "2006-04-05T03:07:50Z",
            "updatedOn": "2026-04-15T09:44:14Z",
            "url": "https://forge.typo3.org/issues/15984"
        }
    ],
    "unavailable": null
}
```

### forge: what is known about one area

Called with:

```json
{
    "open": "stale",
    "category": "rte",
    "tracker": "Bug",
    "limit": 3
}
```

Text:

```
TYPO3 issue tracker: 3 of 23 open issues of the TYPO3 Core project, tracker Bug, in RTE (rtehtmlarea + ckeditor), longest untouched first
This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end.
Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.

## #87400 CKEditor: assign correct CSS class to tags with entryHTMLparser_db
Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2019-01-11 · last touched 2019-01-11 · https://forge.typo3.org/issues/87400

## #97817 RTE removes line with empty, allowed tags when saving
Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2022-06-28 · last touched 2022-06-28 · https://forge.typo3.org/issues/97817

## #88690 Translated content elements are not available in linkbrowser of the ckeditor in free mode
Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2019-07-05 · last touched 2023-03-05 · https://forge.typo3.org/issues/88690
```

Data:

```json
{
    "status": "answered",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=updated_on%3Aasc&limit=3&tracker_id=1&category_id=1001",
    "query": "",
    "total": 23,
    "categories": [
        "AdminPanel",
        "Authentication",
        "Backend API",
        "Backend JavaScript",
        "Backend User Interface",
        "Caching",
        "Categorization API",
        "CLI",
        "Code Cleanup",
        "composer",
        "Content Rendering",
        "Content Security Policy",
        "Dashboard",
        "Database API (Doctrine DBAL)",
        "DataHandler aka TCEmain",
        "Documentation",
        "Extbase",
        "Extbase + l10n",
        "Extension Manager",
        "felogin",
        "File Abstraction Layer (FAL)",
        "Fluid",
        "Fluid Styled Content",
        "Form Framework",
        "FormEngine aka TCEforms",
        "Frontend",
        "Image Cropping",
        "Image Generation / GIFBUILDER",
        "Import/Export (T3D)",
        "Indexed Search",
        "Install Tool",
        "Language Manager (backend)",
        "Link Handling & Redirect Handling",
        "Linkvalidator",
        "Localization",
        "Locking / Session Handling",
        "Logging",
        "Mailer API",
        "Miscellaneous",
        "Pagetree",
        "Performance",
        "Recycler",
        "Reports",
        "RTE (rtehtmlarea + ckeditor)",
        "scheduler",
        "Security",
        "SEO",
        "Site Handling, Site Sets & Routing",
        "System/Bootstrap/Configuration",
        "t3editor",
        "Tests",
        "Themes",
        "TypoScript",
        "WebHooks - Incoming = Reactions + Outgoing",
        "Workspaces"
    ],
    "categoriesUsed": [
        "RTE (rtehtmlarea + ckeditor)"
    ],
    "issue": null,
    "results": [
        {
            "issue": 87400,
            "subject": "CKEditor: assign correct CSS class to tags with entryHTMLparser_db",
            "tracker": "Bug",
            "status": "New",
            "category": "RTE (rtehtmlarea + ckeditor)",
            "assignedTo": "",
            "createdOn": "2019-01-11T11:07:13Z",
            "updatedOn": "2019-01-11T11:07:13Z",
            "url": "https://forge.typo3.org/issues/87400"
        },
        {
            "issue": 97817,
            "subject": "RTE removes line with empty, allowed tags when saving",
            "tracker": "Bug",
            "status": "New",
            "category": "RTE (rtehtmlarea + ckeditor)",
            "assignedTo": "",
            "createdOn": "2022-06-28T07:46:04Z",
            "updatedOn": "2022-06-28T07:46:04Z",
            "url": "https://forge.typo3.org/issues/97817"
        },
        {
            "issue": 88690,
            "subject": "Translated content elements are not available in linkbrowser of the ckeditor in free mode",
            "tracker": "Bug",
            "status": "New",
            "category": "RTE (rtehtmlarea + ckeditor)",
            "assignedTo": "",
            "createdOn": "2019-07-05T11:01:00Z",
            "updatedOn": "2023-03-05T17:47:02Z",
            "url": "https://forge.typo3.org/issues/88690"
        }
    ],
    "unavailable": null
}
```

### forge: a word that names no area

Called with:

```json
{
    "open": "oldest",
    "category": "quantumflux"
}
```

Text:

```
TYPO3 issue tracker: "quantumflux" names no area the core files issues under, so nothing was read. That is about the word and not about the backlog.
The areas are: AdminPanel, Authentication, Backend API, Backend JavaScript, Backend User Interface, Caching, Categorization API, CLI, Code Cleanup, composer, Content Rendering, Content Security Policy, Dashboard, Database API (Doctrine DBAL), DataHandler aka TCEmain, Documentation, Extbase, Extbase + l10n, Extension Manager, felogin, File Abstraction Layer (FAL), Fluid, Fluid Styled Content, Form Framework, FormEngine aka TCEforms, Frontend, Image Cropping, Image Generation / GIFBUILDER, Import/Export (T3D), Indexed Search, Install Tool, Language Manager (backend), Link Handling & Redirect Handling, Linkvalidator, Localization, Locking / Session Handling, Logging, Mailer API, Miscellaneous, Pagetree, Performance, Recycler, Reports, RTE (rtehtmlarea + ckeditor), scheduler, Security, SEO, Site Handling, Site Sets & Routing, System/Bootstrap/Configuration, t3editor, Tests, Themes, TypoScript, WebHooks - Incoming = Reactions + Outgoing, Workspaces
```

Data:

```json
{
    "status": "empty",
    "source": "https://forge.typo3.org",
    "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=created_on%3Aasc&limit=15",
    "query": "",
    "total": 0,
    "categories": [
        "AdminPanel",
        "Authentication",
        "Backend API",
        "Backend JavaScript",
        "Backend User Interface",
        "Caching",
        "Categorization API",
        "CLI",
        "Code Cleanup",
        "composer",
        "Content Rendering",
        "Content Security Policy",
        "Dashboard",
        "Database API (Doctrine DBAL)",
        "DataHandler aka TCEmain",
        "Documentation",
        "Extbase",
        "Extbase + l10n",
        "Extension Manager",
        "felogin",
        "File Abstraction Layer (FAL)",
        "Fluid",
        "Fluid Styled Content",
        "Form Framework",
        "FormEngine aka TCEforms",
        "Frontend",
        "Image Cropping",
        "Image Generation / GIFBUILDER",
        "Import/Export (T3D)",
        "Indexed Search",
        "Install Tool",
        "Language Manager (backend)",
        "Link Handling & Redirect Handling",
        "Linkvalidator",
        "Localization",
        "Locking / Session Handling",
        "Logging",
        "Mailer API",
        "Miscellaneous",
        "Pagetree",
        "Performance",
        "Recycler",
        "Reports",
        "RTE (rtehtmlarea + ckeditor)",
        "scheduler",
        "Security",
        "SEO",
        "Site Handling, Site Sets & Routing",
        "System/Bootstrap/Configuration",
        "t3editor",
        "Tests",
        "Themes",
        "TypoScript",
        "WebHooks - Incoming = Reactions + Outgoing",
        "Workspaces"
    ],
    "categoriesUsed": [],
    "issue": null,
    "results": [],
    "unavailable": null
}
```
