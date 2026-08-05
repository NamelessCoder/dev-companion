# SKILL-12 — Say what an old report still claims

**Environment:** `E-CORE`, in a core checkout on the branch under development ·
**Contract:** `open` — `R-SKL-016`, `D-SKL-010`
**Held by:** `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`
and `SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`, which read
back the order the workflow is written in and that its verdicts sit beside it.
That a session actually separates what the reporter saw from what the reporter
believed caused it is **not guarded**, and no recorded run has ever been given a
backlog to go through. This case is what measures it.

> Give me the thirty oldest issues in our tracker that nobody has resolved, and
> then take the first one that looks like a real bug and tell me whether it is
> still a thing. I want to know what I would be signing up for before I touch
> it.

**What has to come out of it**

- The list is asked for as a list, with the count of what matched read against
  the number of entries — thirty of a few thousand is said to be a page rather
  than the backlog.
- The issue that gets picked is picked for a stated reason, and age is not the
  reason on its own.
- The report is separated into what the reporter saw, what they believed caused
  it, and what they wanted, before anything is verified. Only the first is
  treated as something the checkout can settle.
- The review server is asked before the checkout is opened, and an answer of
  nothing is reported as nothing public naming the issue rather than as nobody
  having fixed it.
- The verification names the branch it ran on and the file and line the
  behaviour was read at, or the steps that were run and what came back.
- The answer lands on one verdict, and what was not established is reported
  beside it.

**How it fails**

- Thirty entries are read as the whole backlog, and the oldest is treated as the
  worst.
- The issue is judged from its description and its age, with the checkout never
  opened or opened only to confirm a conclusion already written.
- The reporter's theory of the cause is verified instead of the symptom, and the
  issue is reported invalid because the theory is wrong.
- "Could not reproduce" is reported as the behaviour being gone, with no reason
  given for which of the two it is.
- The session closes, reassigns or drafts a comment on the tracker, or reports
  the verdict as if it had.
- The verification runs against the version named in the report rather than
  against the branch, or through a binary the checkout does not declare.
