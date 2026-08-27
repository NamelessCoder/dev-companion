---
description: >-
  What a new issue on the TYPO3 Core project carries: how to establish that nobody has filed it yet, the trackers it is filed under, the fields of a Bug and the one it does not go without, where the area comes from, who sets the target version, and the Textile the description renders as.
whenToUse: >-
  When writing the title and the description of a core bug report, or filling in the new-issue form on forge.typo3.org — the report a patch's Resolves: trailer will point at included.
hints: []
---

# Filing a TYPO3 Core Bug Report

Source: https://forge.typo3.org/projects/typo3cms-core

Core issues are filed on the Redmine at forge.typo3.org, in the TYPO3 Core
project. Filing one needs an account and the form is behind the login, so what a
report carries is read off the issues that were filed rather than off the form:
`https://forge.typo3.org/issues/<number>.json` answers one whole, and
`https://forge.typo3.org/projects/typo3cms-core.json?include=trackers,issue_categories`
answers the project's own lists.

## Whether It Is Already Reported

Two calls, and the first of them does not settle it on its own.

```
typo3_forge_lookup with query="<two or three words of the symptom>"
typo3_forge_lookup with open="newest", createdSince="<YYYY-MM-DD>", limit=50
```

A hit on the search is conclusive and an empty answer is not: it matches text,
unranked, and every word has to be in the same issue, so a report worded
differently is invisible to it. What an empty answer is read against is the
second call — everything filed since that day, newest first, read as subjects.
The day is the earliest the defect could have been reported: when the code that
carries it shipped, or when you first saw it.

`total` is what says whether that was the whole set or a page of it. Where it is
larger than the rows, move `createdSince` later until the two agree, because
what a page of that end left out is older than its last row.

`category` narrows it further where the area is certain. An issue filed under no
Category is in no area at all, and thousands of the open bugs carry none, so the
narrowing that reaches those is the day rather than the area.

## What a Report Carries

- **Tracker** — `Bug` for something broken. The project offers `Bug`, `Feature`,
  `Task`, `Story` and `Epic` and nothing else, so a tracker Redmine has
  elsewhere is not one this project takes.
- **Subject** — the title, and the line a triage reads instead of the report.
  Name the subsystem and what it does wrong.
- **Description** — the report itself, rendered as Textile. The section below is
  the syntax, and the one after it the shape.
- **TYPO3 Version** — the major the report is against, as the major on its own
  rather than as a full release number. **This is the one field a Bug does not
  go without.**
- **Category** — the area the core files it under. Optional, and the section
  below is where the value comes from.
- **PHP Version** — the major and minor the report was produced on. Optional,
  and most reports leave it empty.
- **Priority** — `Should have` unless there is a reason, which is what nearly
  every report carries. `Must have`, `Could have`, `Won't have this time` and
  `-- undefined --` are the others.
- **Target version** — left empty by the reporter. The section below is why.
- **Complexity** — `trivial`, `easy`, `medium` or `hard`, and a guess about
  somebody else's work. Optional.
- **Is Regression** — a checkbox for a defect an earlier release did not have.
  Optional.
- **Sprint Focus** — set at a sprint by whoever runs it, not by the reporter.
- **Tags** — free text, comma separated. Optional.
- **Assignee** — left empty. Taking a report on is what assigns it.

## The Area

The categories are administered on the project and the core adds to them, so
this page does not copy them. One call answers which one a subject belongs to:

```
typo3_forge_lookup with open="oldest", category="<your own words>", limit=1
```

`categoriesUsed` in that answer is the tracker's own spelling of the areas those
words named, and the spelling is what the field takes. A word naming several
areas is answered with all of them, which is how two candidates are told apart
rather than picked between. `category="*"` asks for the whole list without
naming a subject at all.

A word that names no area is answered as such, and that is a statement about the
word rather than about the project.

## The Target Version

A report leaves it empty, and most core bugs carry none at any point. It says
which release a fix is scheduled for, which is decided when somebody takes the
fix on rather than when the defect is described.

The values it is later given are the project's own open versions:
`next-patchlevel` and `Candidate for patchlevel` carry mostly bugs,
`Candidate for Major Version` mostly features, and the open majors carry what is
scheduled for a release by name. Which of the two patchlevel versions a given
bugfix takes is the scheduler's call and is not derivable from the tracker.

## The Markup

**The description field renders Textile, not Markdown.**

- Heading: `h2. Steps to reproduce`, and the level is the digit.
- Ordered list: `#` and a space, one per line. Unordered: `*` and a space.
- Inline code: `@Foo::bar()@`. A backtick renders as a backtick.
- Code block: `<pre><code class="php">` … `</code></pre>`, syntax-highlighted by
  the class. `class="diff"` colours a diff. `<pre>` on its own is preformatted
  and unhighlighted.
- Bold: `*word*`. Quote: `bq.` or `>` at the start of the line.

Nothing inside `<pre>` needs escaping. A placeholder written as `<type>` arrives
as `<type>`, so escaping the angle brackets by hand puts the entities in the
output.

Three things are rewritten outside it, silently and in a way that survives into
the filed report:

- **A Markdown fence is not a fence.** Three backticks render as three
  backticks, in a paragraph that has lost the block's indentation with it.
- **A diff pasted as text is read as markup.** Leading `+` and `-` around the
  lines between them become an insertion span, so the removed lines disappear
  into it. Wrap a diff in `<pre><code class="diff">`.
- **A placeholder inside a URL is eaten by the auto-linker.**
  `https://<domain>/route` becomes a link whose text is doubly escaped. Put the
  URL in `@…@` where it carries a placeholder.

Consecutive lines starting `1.`, `2.`, `3.` are not a list either. They render
as one paragraph with a line break between them, which reads as intended and is
not what `#` produces.

## What the Description Says

A core report is read by somebody deciding whether to reproduce it, so it is
written in the order that decision is made. The headings the core's own reports
use, as `h2.` or `h3.`:

- **Problem** — the symptom, in the words it showed in. An exception message, a
  wrong value, a rendering that differs from the expected one.
- **Steps to reproduce** — an ordered list starting from a state somebody else
  can reach. A step naming a fixture only you have is where a report stalls.
- **Cause** — the class and method the defect is in, and what it does wrong.
  Optional, and what turns a report into a patch somebody can write.
- **Suggested fix** — a diff, where the cause section named one.

The description is what the reporter saw. What was decided about the report goes
into its comments afterwards and never back into this field.
