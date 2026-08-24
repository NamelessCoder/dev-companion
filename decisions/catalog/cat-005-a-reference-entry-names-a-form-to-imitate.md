---
id: D-CAT-005
title: 'A reference entry names a form to imitate'
date: 2026-08-18
status: open
---

# D-CAT-005 — A reference entry names a form to imitate

**A directory earns an entry in `knowledge/catalog/reference/entries.json` where
a caller reads it before writing one of the same shape, not where a fact happens
to be declared in it.**

A session that had read the frontend's middleware registration on two branches
asked for that file to be listed, so the next caller would find it. What the
next caller needs is the ordering, and a path does not carry one.

## Evidence

- `feedback/2026-08-18-081228` asks for
  `typo3/sysext/frontend/Configuration/RequestMiddlewares.php` and
  `typo3/sysext/frontend/Classes/Event/` beside the document it proposes,
  because "neither is discoverable as a reference today". Neither is in the
  seven entries of `knowledge/catalog/reference/entries.json`.
- Both are already named by the corpus, from the hint that also answers the
  question they were wanted for. `routing-request-handling` carries
  `Configuration/RequestMiddlewares.php` in its `appliesTo` and states that
  middleware is registered there per request scope with `before` and `after`
  naming other middleware. `events-extension-points` states that event classes
  live in `Classes/Event/` of the extension that dispatches them, which holds
  for every extension where an entry would name one directory.
- The path is not what the session lacked. It reports reading
  `RequestMiddlewares.php` on both branches along with both request handlers,
  and reports the cross-branch git read as the efficient part of the session —
  one `git show origin/13.4:<path>` against the same path on `main`. What it
  paid for was the ordering across majors.
- That ordering is now in the corpus as an answer rather than as a path.
  `typoscript-conditions`, written the same day in `899dbdbb` and `3b7a0939`,
  states per major which globals are populated when a condition is matched, and
  `bin/cli hints:probe` on the session's own three questions reaches it —
  `appliesTo(20) + text(193)`, `appliesTo(33) + text(240)`,
  `appliesTo(20) + text(133)`.
- `typo3_reference_list` says what an entry is for in its own description: "Read
  one of these before inventing a layout or a test harness — they are the
  version-correct, currently-passing form of what a convention describes." All
  seven are that: a theme, the styleguide, an Extbase fixture extension, the
  content element rendering, a browser suite, and two build setups.

## Decided

- The two entries are not written. A reference answers *what do I read before
  writing one of these*; the feedback asks *where is this declared*, which is a
  lookup's question, and the lookup already names both files.
- What earns an entry is the writing it saves, not that a session opened the
  file. A path in an index is one call spent to be told where to spend the next
  four, which is the trade `D-FBK-027` measures the other way round.
- The subject is not rejected with the entries. What the session wanted from
  those two directories is answered, and it is answered where an answer is.

## Assumed

- That a caller asking about middleware order asks in words
  `routing-request-handling` matches. Probed here on 2026-08-18: "frontend
  middleware order per major, what each stage has assembled" reaches it at
  `appliesTo(10) + text(146)`.
- That an extension author writing their own
  `Configuration/RequestMiddlewares.php` is served by that hint rather than by
  the core's file. Nothing has measured it, and it is the reading under which
  the entry would be owed after all.

## Wrong if

- A session reports reaching the middleware hint and still not knowing what to
  write in its own registration. The core's file is then a worked example under
  the tool's own reading, and the entry is owed.
- A second session pays round trips to find where the frontend declares its
  middleware order while the hint is reachable. That is the ladder's step 2 and
  a placement question, not a catalog one — and this entry would have sent it to
  the wrong lever.
- An entry is written for a directory nobody imitates and a session reports it
  as the answer to a fact question. The line is then drawn in the wrong place.

## Since then

The two entries are still not written and nothing asks for them again.
`knowledge/catalog/reference/entries.json` holds the same seven, and the probe
this entry rests on returns the same numbers on 2026-08-23: "frontend middleware
order per major, what each stage has assembled" reaches
`routing-request-handling` at `appliesTo(10) + text(146)`, alone.

No second session has paid for it. The feedback that asked, `2026-08-18-081228`,
is archived, and no report since names the frontend's middleware declaration as
something it went looking for. So all three **Wrong if** are undisturbed, and
the one that would fire first — a session reaching the hint and still not
knowing what to write — is the reading the **Assumed** says nothing has
measured.
