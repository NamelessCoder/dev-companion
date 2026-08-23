---
id: D-SKL-020
title: A re-check runs what the finding was about
date: 2026-08-04
status: open
---

# D-SKL-020 — A re-check runs what the finding was about

**The re-check that closes a cleanup finding re-runs the thing the finding was
about; re-reading the changed file is what let a reverting fix be reported as
closed.**

The step that hands the worked list back was reported as the half that worked,
and the one line it was missing is what separated a closed finding from a
shipped regression.

## Evidence

- `feedback/2026-08-04-180010`. The finding was that
  `config/system/additional.php` was owned by DDEV, which decides ownership by
  searching the whole file for its generated-file signature. The fix removed the
  marker and quoted the literal signature in the comment explaining why — so
  DDEV still owned the file. The suite was green, the commit was made, the
  finding was reported closed.
- Reading the file showed a correct 131-line file. What showed the defect was
  `ddev restart` and a checksum comparison: the file came back as the 45-line
  stock template with the environment contract gone, no error and no prompt.
- The same pass caught a second regression by running rather than reading: a
  Playwright project whose dependency skipped left backend specs failing instead
  of skipping, so `npm run test:e2e` was red on a fresh clone for something that
  is not a defect.
- The rest of the step was reported as paying for itself and is untouched. "What
  a dropped candidate owes" made the session write down 13 candidates it had
  raised and let go, several of which would have been false findings — the
  `<f:layout name="Default"/>` its own `settings.yaml` documents as deliberate
  among them.

## Decided

- The strength is evidence about a **boundary** rather than about a decision
  ([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)):
  what a re-read establishes is that the file says what the fix wrote, and what
  a re-run establishes is that it still says it. Only the second closes a
  finding about ownership, generation or an environment.
- One sentence, in step 12 where the re-check is prescribed, and nothing else.
  The step is otherwise reported as working and a rewrite would be a second
  answer to a question that has one.
- **Closed on the spot.** It is the wording of a rule that is already there,
  with nothing about TYPO3 looked up and no contract moved.

## Assumed

- That a session which re-runs the environment sees the difference. Here it took
  a checksum comparison; a session that restarts and re-reads without comparing
  would read the stock template as the file it wrote.

## Wrong if

- A re-check re-runs everything and the cost is a cleanup nobody finishes. Then
  what a finding was "about" has to be narrowed to the environment that owns the
  file rather than left to the reader.
- A finding of this shape is missed again with the sentence in place. Then the
  lever is the audit's evidence rule rather than the re-check's wording.

## Since then

The sentence survived the merge and moved. `8a67338a` on 2026-08-19 made the
audit and its fixes one skill, and what was step 12 is step 14 of
`typo3-extension-health`: "Re-run the audit above on the worked list rather than
re-reading the files it changed: a file that reads correctly can still be
rewritten by the environment that owns it, and the difference only shows once
that environment runs again."

That wording answers half of the first **Wrong if** before it can fire. The
re-run is bound to the worked list rather than to everything, and the
environment that owns the file is named in the sentence rather than left to the
reader — which is what the bullet said the narrowing would have to be. What it
does not bound is what re-running the audit on that list costs, and no session
has reported the cost either way.

Nothing reports the second one. No feedback after 2026-08-04 describes a
reverting fix reported as closed, and none of the five that mention a re-check
at all is about this step.
