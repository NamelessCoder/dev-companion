---
id: D-SKL-073
title: A verdict that ends an issue carries the comment that closes it
date: 2026-08-24
status: open
---

# D-SKL-073 — A verdict that ends an issue carries the comment that closes it

**A triage whose verdict ends the issue hands over the comment that closes it,
and the tracker boundary stops the act rather than the wording.**

The skill stops one step earlier than that today. It says what a verdict owes as
evidence and what form the answer takes, and the sentence closest to the moment
tells the session that closing is somebody else's — which reads as a stop before
the text as well as before the act.

## Evidence

- `feedback/2026-08-24-170208-triage-that-proves-an-issue-is-already-fixed.md`
  reports a triage that established an issue was already fixed and handed over
  no reason the reporter could close it with.
- The skill as it stands says it: the **Gone** verdict in
  `skills/typo3-core-issue-triage/references/checklist.md` owes "what changed,
  named" and nothing that goes to the reporter, and the
  `Say what the triage found` section of `SKILL.md` names the form — markdown
  the reader can copy, `D-SKL-042` — and never the content for this case.
  Nothing in either file has changed since `4c1fe8fc` on 2026-08-19, so the
  feedback describes the file that is there now.
- The checklist's last bullet is what a session reads at that moment: "Whether
  anything here is a recommendation to close, reassign or reopen. That is the
  maintainer's act; the triage supplies what it rests on and stops." Two
  readings of it are defensible, and the one that costs the deliverable is the
  one the reporting session took.
- A second session reported the same missing deliverable from another task
  shape.
  `feedback/2026-08-24-173131-which-releases-contain-a-given-fix-took-four.md`
  was asked mid-task for "the list of already-fixed issues with justification so
  they could close them on Forge", assembled the release fact by hand four
  times, and got one of the four wrong until it read the `Releases:` trailer.
- `bin/cli hints:probe "a triage session on an open forge.typo3.org issue that turns out to be fixed already"`
  matched nothing, and `knowledge/` names Forge in the Gerrit and sources
  documents alone. What a closing comment on the tracker owes is not written
  anywhere here.

## Decided

- The diagnosis is step 4 of the ladder, wording, in the triage skill's own two
  files. The verdict, the evidence it owes and the copyable form were all
  delivered; what stopped the session was a sentence about the act being read as
  a sentence about the text.
- The deliverable is owed by the verdicts that end the issue — **Gone**,
  **Superseded** and **Not a defect** — and not by the two that ask the reporter
  something instead. **A security defect** is untouched: it owes the tracker
  nothing, and a closing comment is the public step that verdict exists to
  prevent.
- The boundary does not move. This server holds no credential, comments on
  nothing and closes nothing, and the person who files the comment is still the
  one who acts.
- The work is queued rather than made in the judging run, on both of the tests
  `documentation/records/judging.rst` sets: it changes a skill's contract, which
  lands in somebody else's project, and it needs a fact about TYPO3 that is not
  in this repository.
- That fact is the todo's first step and is a step 1a of its own: what a closing
  comment on forge.typo3.org owes — which status and resolution a fixed issue is
  closed with, how the fixing change is named, and which markup the tracker
  renders. Read from the core's own contribution documentation, not recalled.
- The card stays its own rather than folding into the one serving
  `feedback/2026-08-24-173131-which-releases-contain-a-given-fix-took-four.md`.
  That one is a tool gap about a fact nothing answers in one call, this one is
  the skill's deliverable, and the wording will route to whatever answers the
  fact by the time it is written.
- The priority is `normal`, set by two sessions reporting the same gap from two
  task shapes on one day and by the maintainer filing this one to be worked now.

## Assumed

- The reporter pastes the wording as it stands. Where the tracker renders
  something other than markdown, the form is part of what the reading has to
  settle rather than a detail of it.
- A maintainer wants the closing text supplied. The feedback says the reporter
  was left to compose it, which is evidence that it was wanted once.

## Wrong if

- A triage under the rewritten skill hands over a closing a maintainer rewrites
  before pasting it, which would make the deliverable the evidence and not the
  text.
- A maintainer reports a supplied closing as noise, on the ground that how an
  issue is closed is the tracker's own voice.
- A session writes a closing for a verdict that should have asked the reporter a
  question. Writing "not reproducible as written" up as "gone" is the trap the
  checklist already names, and a deliverable that only exists on the closing
  side is a reason to reach for it.
