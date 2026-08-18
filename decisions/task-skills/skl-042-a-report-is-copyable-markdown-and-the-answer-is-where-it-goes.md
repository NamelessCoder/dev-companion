---
id: D-SKL-042
date: 2026-08-14
status: open
---

# D-SKL-042 — A report is copyable markdown, and the answer is where it goes

**A skill whose product is a report says that the report is markdown the reader
can copy, and the answer is where it goes.**

A file is one way to be copyable and not the property being asked for. What the
report has to survive is being moved somewhere else — a Gerrit comment, an
issue, a chat — and rich rendering is what it does not survive.

## Evidence

- The maintainer, asked on 2026-08-14 in the session that merged `D-SKL-040`:
  the report may be written in the chat and needs no path, "but the user must be
  able to copy it from there, the problem with formatted html is that is cannot
  be easily transferred".
- The correction that produced the feedback says the same thing and was read one
  step too far. "reviews sollten immer im markdown format ausgegeben werden",
  then "damit es kopierbar ist" — the second names the reason, and markdown is
  the fix it asks for. `D-SKL-040` read the file the session wrote afterwards as
  the requirement rather than as one way of meeting it.
- What that entry established holds unchanged: the three `## Report` sections
  specify five bands, what each finding owes and the surfaces it closes on, and
  none of them names a form.
- The skill is what makes the report long, and length is why the form matters. A
  hundred lines rendered as HTML is the case where copying breaks; four lines is
  not.

## Decided

- The property is copyable markdown, stated in the report section of
  `typo3-core-patch-review`, `typo3-extension-conformance` and
  `typo3-core-issue-triage`.
- The answer is where the report goes by default. Nothing is written to a path
  unless the caller asks for one, so nothing has to decide a name or a directory
  and the assessed checkout cannot be dirtied by accident.
- A file stays available and is the caller's call. Where one is written, it goes
  outside the checkout under review, because that skill's own checklist reports
  what is untracked beside the patch as a finding.
- `D-SKL-040` is revoked rather than corrected. Its statement named the fix as
  the file, and its **Wrong if** were a list about paths and naming, which is
  not what can go wrong here.

## Assumed

- A client that renders markdown still hands the source over when the reader
  copies it. Where one does not, the form is not what fixes this and a path is
  back on the table.
- The three skills are the ones whose product is a report. Read off the bodies
  rather than off a field, as `D-SKL-040` assumed before it.

## Wrong if

- A review under the rewritten skill is still handed over in a form the reader
  cannot lift out — then the sentence names a form the session does not control,
  and where the report goes is the lever after all.
- A user asks for the report at a path often enough that "the answer, unless you
  ask" is the wrong default.
- A report turns out to be long enough that nobody copies it at all, which would
  make the deliverable the thing to shorten rather than the form to fix.
