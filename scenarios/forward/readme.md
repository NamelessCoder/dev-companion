# Open forward reviews

These are deliberately broad repository reviews. The prompt names the working
context and the user's intent, but no subsystem, skill, tool, expected defect,
or implementation shape. What the agent chooses to inspect and prioritize is
the evidence.

See [the scenario readme](../readme.md) for the audiences and the environments,
and [contracts/](../contracts/readme.md) for the targeted cases these are not.

| Review | Working context |
| --- | --- |
| [`REVIEW-01`](review-01-site-project.md) | A TYPO3 site project and its site package |
| [`REVIEW-02`](review-02-reusable-extension.md) | A reusable TYPO3 extension |
| [`REVIEW-03`](review-03-core-patch.md) | A TYPO3 core patch |

## Status of a forward review

| Mark | Meaning | What a run is for |
| --- | --- | --- |
| `unrun` | No session has established a result yet. | Run it without predicting what the repository will reveal. |
| `covered` | The server should answer this well today. | A bad answer is a regression; fix it. |
| `boundary` | Deliberately outside scope. | The right answer is a clean decline plus where to go instead. A confident invented answer is the failure. |
| `partial` | Answered, but not for every audience or not to the depth the task needs. | Record which half was missing. |
| `gap` | An accepted requirement that is not met yet, or knowledge that does not exist. | Expected to fall short. Record what the task actually needed, not that it fell short. |

`boundary` and `gap` are not the same thing. A boundary is an answer — "this
server does not cover it, the documentation does" — and it can be given
perfectly. A gap is an answer that ought to exist and does not.

## Running a forward review

1. Start the MCP client in the environment the review names — a fresh session,
   with the skills published there as they are in this checkout right now. It
   need not be a person typing: a client driven non-interactively is the same
   evidence, as long as it is given the prompt and nothing else, and a session
   id it was started with is what makes its transcript findable afterwards.
   [todo.md](../../todo.md) says which checkout plays which environment on this
   machine, and how the client is reached there.
2. `bin/cli scenarios record <id> <client>` writes the empty run, and
   `bin/cli scenarios show <id>` prints the prompt and the numbered criteria.
3. Paste the prompt verbatim. Add nothing: no tool names, no hints that a
   TYPO3 knowledge server is attached, no correction when the agent goes the
   wrong way. What the agent does with an under-specified request is part of
   what is being measured.
4. Let the review reach its own stopping point. Do not steer it toward a known
   subsystem or finding.
5. Grade against **What has to come out of it** and **How it fails**, and write
   the judgment and its evidence into the recorded run, together with the skills
   that activated and the tools the session actually called. `bin/cli scenarios
   check` — and `composer test` — then hold that run to this file.

## What a forward run produces

The run itself, as one file below [runs/](../runs/). That file is what the
**Status today** line of a review rests on: the environment, the server it ran
against, the skills the session activated, the tools it reached for, and one
judgment with evidence per criterion. The verdict is not written into it — it
follows from the judgments, and a review whose mark disagrees with what its run
establishes is a failing check rather than a sentence nobody rereads.

Everything else a run produces, it produces on top of that — and a run that went
well produces it too. What a run teaches is rarely the verdict: three
`REVIEW-02` runs in two repositories were all judged `covered` or better against
the same criteria, and the thing worth keeping from them is that not one of the
three executed a single project-owned command and not one of the answers said
so. That is neither a criterion nor a property of the repository under review,
and left in the run's evidence field it is read once, by whoever judged that run.

So after judging, ask what the run taught that is **not** specific to the one
repository, and file each answer where the recurring work already walks:

- `typo3_feedback_record` for what was missing, wrong, or unhelpful — with the
  review id in the observation, so the note can be traced back to the task that
  exposed it. Give it the run's own prompt as its query, so the note can be
  re-run against a later server the way every other note is.
- A new contract case, when the session exposes a repeatable task or failure
  shape worth holding directly.
  That is the more valuable outcome of the two.

A defect the same session fixes is the exception: that is a requirement and the
commit that closed it, not a note that would be deleted on creation. Otherwise
the usual route applies — the note is worked off in a commit that deletes it,
and what has to keep holding afterwards goes into
[requirements/](../../requirements/readme.md). See [AGENTS.md](../../AGENTS.md).

For a `gap` review, do not re-file the part that is already written down —
its **Status today** line names the requirement. File what the task needed
beyond it.

## Judging one

The run is graded from the transcript, not from what the session felt like. The
client stores it as JSONL, one file per session, so which skills activated and
which tools were called are evidence rather than recollection — and a handful of
the answer's findings are worth re-checking against the checkout before the
judgment is written. Grading an answer and grading a claim are two different
things.

- **Judge against the criteria as written**, not against how much better this
  run was than the last. Three `REVIEW-01` runs in a row scored four of five
  while missing a defect none of the five asked about; the criterion that now
  catches it exists because the gap was written down instead of excused.
- **Judge the verdict a finding puts on top of its evidence.** A run can get
  every path, count and line right and still call a design decision a defect.
  `REVIEW-02` was `partial` on its first run and `covered` on its second with
  the same reading underneath.
- **One change, one run.** Editing the criteria resets the recorded run to
  `unrun` by design — the digest check catches a judgment answering criteria
  that have since been rewritten — so the superseded run survives only in its
  commit. Name that commit in the new run's notes.
- **Reset the environment between runs.** Restoring the working tree is not
  enough if the session wrote to the database; the next run finds what the last
  one left and behaves differently.

## When a run stops without an error

Treat it as a defect in this server until something else is proven. A client
waiting on a tool call that will never return looks exactly like a client
thinking hard, and neither side reports anything.

Measure before theorising. Constant CPU time and an unmoving `rchar` in
`/proc/<pid>/io` mean idle rather than busy, and no TCP socket in
`ls -l /proc/<pid>/fd` rules out waiting on the model. Client debug output names
the tool that never came back, which is the whole diagnosis — two `REVIEW-02`
attempts died 24 minutes apart on the first pair of tool calls a client
dispatched concurrently, and the cause was this server handing its own stdin to
a console command ([`R-DIS-18`](../../requirements/discovery/dis-18-a-console-command-never-inherits-the-clients-stdin.md)).
