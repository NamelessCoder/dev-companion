---
date: 2026-08-05T22:10:38+00:00
category: idea
status: closed
closed: 2026-08-06
model: claude-opus-5[1m]
tool: typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# Task: triage old untouched core bugs on forge.typo3.org against a 15.0.0-dev checkout. User's fra...

## Observation

Task: triage old untouched core bugs on forge.typo3.org against a 15.0.0-dev checkout. User's framing was "let's look at old issues and see whether there is something easy in there we can verify and maybe fix".

The skill's "Find the candidates" section produces a backlog — typo3_forge_lookup with open=stale returned 39 issues with the total count — and step 3 then moves straight to "Establish what the issue claims", singular. Nothing between those two steps says who chooses which issue, so I read the selection as mine: I picked four candidates out of the 39 myself, verified them, and reported only those. The user's next message was "hast du mir keine liste gegeben aus der ich auswaehlen konnte" — you gave me no list to choose from. That is a skill-shaped failure, not a tool one: the skill reads as single-issue triage with a lookup in front of it, and that is how I ran it.

What I would keep, because it was directly load-bearing. The "oldest vs stale are two different questions" distinction made me use updatedBefore rather than createdBefore and got a genuinely untouched set. "Read the count that comes back against the number of entries" is the only reason I noticed total was 39 while the page held 25, and I re-queried with limit=50 instead of triaging a page as if it were the set. references/checklist.md separated "Gone" from "Not reproducible as written" sharply enough that it changed two write-ups: #86874 became "Gone" with a named mechanism, #83848 became "not reproducible as written" rather than being folded in with it.

What the skill has no mechanics for, and what I invented: running a throwaway reproduction inside a core checkout. It says a failing test is the strongest thing a triage produces, and it is right, but it does not say where such a test goes, that it must be shown failing before it is trusted, or that it has to be removed afterwards. I wrote Tests/Functional/.../TmpTriage85456Test.php, ran it, deleted it. The first run failed for an unrelated setup reason (the field was not in the type's showitem) — exactly the accident that would otherwise have been written up as "the behaviour is gone".

## Query

Skill typo3-core-issue-triage invoked with: "Alte, ungetouchte Issues auf forge.typo3.org sichten und Kandidaten finden, die einfach zu verifizieren und ggf. zu fixen sind" (go through old untouched Forge issues, find candidates that are easy to verify and possibly fix).

## Suggestion

Two additions to the skill text. First, between "Find the candidates" and "Establish what the issue claims", state that the backlog is handed over and the selection belongs to whoever asked: the list is the first deliverable, carrying what each row needs to be chosen on (number, area, subject, how long untouched), and triage of a backlog is a different act from triage of an issue. Without that line the selection silently becomes the triager's. Second, a short paragraph on the throwaway reproduction, since the skill already asks for a failing test: where it goes in a core checkout (mirroring the class path), that it is proven to fail before the fix and not only to pass after, that a first run failing for a setup reason is a result about the harness rather than about the issue, and that it is removed again unless it is carried into a patch. That is the one part of this session I worked out for myself and would work out identically next time.
