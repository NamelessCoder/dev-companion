---
id: D-KNW-113
title: Reporting a core issue is a subject this server owns
date: 2026-08-24
status: open
coveredBy:
  - ForgeTest::areasThatCouldNotBeReadAreSaidToBeUnreachable
  - ForgeTest::theAreasAreEnumeratedWithoutAWordThatHasToFail
  - KnowledgeTest::aReportBeingWrittenIsToldWhichMarkupTheDescriptionRenders
---

# D-KNW-113 — Reporting a core issue is a subject this server owns

**A `core/contribution/reporting-an-issue` document states what a new core issue
carries: its fields, the areas the project files under, and the markup its
description renders as.**

A session wrote the title and the description for a Forge issue out of its own
recollection of the form, and hedged the markup with `<pre>` because nothing
here says which one the field renders.

## Evidence

- [`feedback/2026-08-24-133626`](../../feedback/archive/2026-08-24-133626-no-route-for-authoring-a-new-forge-issue-fields.md)
  delivered a metadata table — Tracker, Category, Target version, TYPO3 Version,
  PHP Version, Complexity, Is Regression, Sprint Focus — with every field name
  recalled and none verified. It names three guesses: the Category value, the
  `next-patchlevel` target version, and whether the description renders Textile
  or Markdown.
- Nothing here answers the question it says it would have asked.
  `bin/cli hints:probe` on the feedback's own wording matched no hint;
  `typo3_rule_lookup` for "report a bug on forge new issue fields category
  target version" matched no section; `typo3_task_guide` for "write the title
  and description for a new Forge issue reporting a core bug" answered the
  patch-development brief, down to the `runTests.sh` suites. Run on 2026-08-24.
- The markup is here once, and not as a property of the field.
  `**The comment is pasted into Forge, which renders Textile rather than Markdown.**`
  sits in `skills/typo3-core-issue-triage/references/checklist.md`, where it is
  about the comment that closes a bugfix. The session was writing a patch, so
  neither the skill nor the sentence was in front of it.
- The areas are already reachable, by accident. `typo3_forge_lookup` with
  `open: "oldest"` and a `category` word naming no area answers the project's
  own 55 names, read live — measured 2026-08-24. Both values the feedback was
  torn between are real and separate: `TypoScript`, and `Site Handling` beside
  `Site Sets & Routing`. So the enumeration it could not check was one call
  away, behind a word that has to be wrong for it to arrive.
- The `categories` field of `ForgeLookup::outputSchema()` says
  "typo3_server_scope carries the vocabulary for a caller that wants it without
  a question". It does not: `typo3_server_scope` was called on 2026-08-24 and
  carries none of `Workspaces`, `Link Handling`, `Pagetree` or `Linkvalidator`.
- One session, not a domain. `bin/cli feedback:list` on 2026-08-24 read 37 open
  feedback across four checkouts, ten of them naming Forge, and this is the only
  one about writing an issue rather than reading one.

## Decided

- **Step 1a for the fields, the target-version convention and the markup; step 2
  for the areas.** Taken on, and the answer is a document rather than a rule:
  what the session could not get was the shape of a report and the order its
  fields are filled in, which a sentence cannot carry.
- The page is `knowledge/documents/core/contribution/reporting-an-issue.md`,
  declaring what it is and when to reach for it as
  [`D-KNW-057`](knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  requires. `commit-messages` and `changelog` are the siblings it sits beside,
  and `D-KNW-111` is the same shape one step earlier in the same workflow.
- **It names the call that reads the areas and does not copy their names.** The
  list is administered per project and a tool already answers it live, so 55
  values in prose is a copy that goes stale beside a source that cannot.
- `typo3_forge_lookup` stays a read. Writing an issue needs a credential and
  stays the caller's, which is the fourth **Decided** bullet of
  [`D-ANS-038`](../answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md),
  and a name promising the authoring direction would be the lie the feedback
  reports in reverse. What the tool owes is the areas without a wrong word: the
  route to them is the todo's, and the `categories` description's claim about
  `typo3_server_scope` is false today and is corrected with it.
- `knowledge/task-intents.json` and the `routing` of
  `knowledge/server-scope.json` gain the authoring direction. The session's own
  account is that it never called the tool because the name reads as retrieval,
  so a document nothing routes to is the same miss again.
- Whether the triage checklist's Textile sentence becomes a reference to the
  page is the todo's, under the skill rules. A skill lands in somebody else's
  project and cannot dereference what it does not carry.
- The priority is `normal`, set by one session and by how small the reading is:
  the areas are answered already, and what is left is the form and the markup.
- Nothing holds this yet. The test declares the id in the commit that writes the
  page.

## Assumed

- That the new-issue form can be read without an account. The area enumeration
  is anonymous, and the required fields and their defaults may not be.
- That the field set is stable enough to be prose. Redmine's fields are
  administered per project, so a page naming them goes stale without anything
  failing.

## Wrong if

- The form cannot be read without a credential. Then the page states what one
  person saw once, nothing here can check it again, and it belongs in the skill
  as a caveat rather than in the corpus as fact.
- A session with `core/contribution/reporting-an-issue` in its guides list still
  recalls the fields. Then the name was not what kept it out, and what the
  feedback reports is the guides list not being pulled at the point of need —
  `D-KNW-111`'s first **Wrong if** is the same shape.
- The page carries the area names after all and they drift from what the tracker
  answers. That is the copy this entry refused, and the drift is silent.

## Since then

**The first assumption was wrong and the first Wrong if did not follow from
it.** `https://forge.typo3.org/projects/typo3cms-core/issues/new` redirects to
the login, so the form cannot be read without an account — but what the page
states was read off the filed issues instead, which anybody can read again with
a URL.

- The field set is `custom_fields` on any issue: `/issues/<number>.json` answers
  every field visible for that tracker, empty ones included. The trackers and
  the areas are
  `/projects/typo3cms-core.json?include=trackers,issue_categories`.
- Mandatory was settled by the filter rather than by the form. `TYPO3 Version`
  is unset on 2156 bugs and the newest of them was filed on 2013-05-15, so
  nothing in over a decade got past without it. Every other custom field is
  unset on the majority: `PHP Version` on 21082 bugs, `Complexity` on 32133,
  `Is Regression` on 24283, and `Category` on 14042, one of them filed the same
  day. Measured 2026-08-24 with `cf_<id>=!*` on `/issues.json`.
- The markup was settled by rendering rather than by a setting. The project's
  own description is stored as `h3. Current Development` and served as `<h3>`,
  which is Textile. On issue 110433 the reporter's Markdown fences came back as
  literal backticks in a paragraph, and their pasted diff was read as markup —
  `++` and `-` around the lines between them became an `<ins>` span, taking the
  removed lines into it. Inside `<pre>` nothing needs escaping: `<type>` on
  issue 110527 arrived as `<type>`.
- The target version is not the reporter's. It is unset on 21443 of 36602 bugs,
  and both `next-patchlevel` and `Candidate for patchlevel` are in current use
  with no anonymous source saying which a given bugfix takes. So the page says
  to leave it empty, which is the answer the feedback guessed at.

**The route to the areas is `category="*"` and not a wider default.**
`D-ANS-054` answers them only where a word named none or several, which
`feedback/2026-08-19-134717` is why. The wildcard is the tracker's own idiom,
already passed here as `status_id`, no area is named for it, and it reads no
issue at all — so the enumeration is asked for rather than arrived at, and every
other call is unchanged.

**The triage skill keeps its own Textile sentence.** It is about the comment
that closes a bugfix and tells the triager to keep it plain, which holds whether
or not they know Textile's syntax; routing it to this page would spend a call to
reach a page about writing a report instead.
