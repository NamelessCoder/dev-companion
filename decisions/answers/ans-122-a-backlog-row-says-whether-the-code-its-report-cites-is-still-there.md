---
id: D-ANS-122
title: 'A backlog row says whether the code its report cites is still there'
date: 2026-08-27
status: open
---

# D-ANS-122 — A backlog row says whether the code its report cites is still there

**`typo3_forge_lookup` names the classes, methods and files an issue's own text
cites and says which of them the installed packages still ship.**

A stale issue's status is untouched by definition, so the tracker cannot say
that a 2015 report is about code that is gone. Three of the five candidates one
session read in full were dead in exactly that way, and it found out by reading
the checkout.

## Evidence

- `feedback/2026-08-26-223257` counted the cost. Roughly 13 round trips of code
  reading rejected three candidates against roughly 5 that confirmed the one it
  patched, and of the session's ~85 calls 12 were this server and ~72 were Bash
  reading the checkout.
- The three it rejected are three different verdicts, and none of them is on the
  tracker: #71566 names `is_callable()` in an `ObjectAccess` that has since been
  rewritten onto Symfony PropertyAccess, #78546 asks for something
  `ClassSchema::reflectMethods()` cannot carry, and #78607 is already fixed
  because `Result::forProperty()` is typed `?string` today.
- Re-run on 2026-08-27 through `ForgeLookup::answer()`: issues 78607 and 71566
  both answer `answered` and their payload carries `id`, `subject`, `status`,
  `tracker`, `priority`, `assignedTo`, `targetVersion`, `typo3Version`,
  `phpVersion`, `createdOn`, `updatedOn`, `url`, `description`, `relations`,
  `attachments`, `reviews`, `noteCount`, `botNoteCount` and `notes`. Not one of
  those is about the code the report is about, so the feedback is not answered
  by the tool as it stands.
- `typo3Version` is the nearest field and says the opposite of what a triage
  needs. #78607 answers "6.2" and #71566 "7", which the answer itself calls what
  the reporter had rather than what it still reproduces on.
- The advice was already there and the reading was still by hand.
  `ForgeLookup::workflow()` has printed five readings under a page of `stale`
  and `oldest` since `e12ec2c9` on 2026-08-25 13:37, a day before this session,
  and two of them — where the symptom appears, how far the mechanism reaches —
  are the reading those 13 round trips were. So what is missing is performing it
  and not prescribing it.
- `bin/cli hints:probe "does the class this issue names still exist in the installed core"`
  matches nothing out of 109 candidate hints. This is not step 1a: no statement
  in the corpus would answer it and none should, because the answer is about one
  caller's own tree.
- The same session named the shape, as a strength rather than as a request.
  `feedback/2026-08-26-223414` credits the inline `reviews` field with deciding
  four candidates without a Gerrit call, and asks for this verdict in that shape
  — a small field on the row a sweep reads without a second call.
- The source is precedented. `Source::Packages` is the files the installed
  packages ship, read rather than executed, and `typo3_changelog_lookup` already
  declares `[Packages, Network]`, so a tracker answer that also reads the tree
  is not a new kind of tool.

## Decided

- It is built. The measure is `D-FBK-027`'s: the question costs its caller a
  round trip per cited symbol, it is asked once per candidate on a page of
  candidates, and both counts are in the report.
- It is a field on `typo3_forge_lookup` and not a tool of its own. The question
  is never asked apart from an issue, a second tool would be a second call on
  every row, and the six verbs have nothing this would be the seventh of.
- The field sits on a row of an enumeration as well as on an issue read whole.
  The sweep is where it pays, which is what `reviews` already demonstrates.
- It reads `Source::Packages` and nothing else — no boot, no console, no
  `Typo3Cli`. `.checkouts/` is this repository's own evidence and never a
  caller's tree, so it is not read here either.
- What it says is where a symbol stands, not whether the defect reproduces.
  Ranking candidates is what the report asked for, and it is what an extraction
  out of ten-year-old prose can carry.
- A name it cannot place is reported as unplaced and never as gone. A wrong
  "gone" discards a valid candidate unread, which costs more than the hand
  reading it replaces, and it is the one failure this may not have.

## Assumed

- One session, which reported the cost twice — once as the gap and once as the
  shape to build it in. The corpus holds no second triage and a session files
  nothing unprompted, so that is one report rather than a rate.
- That an old report cites its code in forms something can read. Both issues
  re-read today name classes in prose and in `<pre>` blocks; whether that holds
  across a page of stale Bugs is the todo's first reading.
- That a caller triaging the core backlog is standing in a tree that holds the
  core — as a checkout for a contributor, under `vendor/` for everybody else.
  Both are `Source::Packages`.

## Wrong if

- A session reports discarding a candidate on a "gone" that turns out wrong,
  which would say the extraction claims more than it can place.
- The citations come back empty across a whole page of stale Bugs, which would
  say the reports of that era name their code in a way this does not read.
- A session reads the field and goes to the checkout for the same candidates
  anyway, which would say what decides a candidate is not where its symbols are.
