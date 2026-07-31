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

1. Start the MCP client in the environment the review names.
2. `bin/scenarios record <id> <client>` writes the empty run, and
   `bin/scenarios show <id>` prints the prompt and the numbered criteria.
3. Paste the prompt verbatim. Add nothing: no tool names, no hints that a
   TYPO3 knowledge server is attached, no correction when the agent goes the
   wrong way. What the agent does with an under-specified request is part of
   what is being measured.
4. Let the review reach its own stopping point. Do not steer it toward a known
   subsystem or finding.
5. Grade against **What has to come out of it** and **How it fails**, and write
   the judgment and its evidence into the recorded run, together with the skills
   that activated and the tools the session actually called. `bin/scenarios
   check` — and `composer test` — then hold that run to this file.

## What a forward run produces

The run itself, as one file below [runs/](../runs/). That file is what the
**Status today** line of a review rests on: the environment, the server it ran
against, the skills the session activated, the tools it reached for, and one
judgment with evidence per criterion. The verdict is not written into it — it
follows from the judgments, and a review whose mark disagrees with what its run
establishes is a failing check rather than a sentence nobody rereads.

Everything else a run produces, it produces on top of that. A run that went well
produces nothing more. A run that did not produces one of two things:

- `typo3_feedback_record` for what was missing, wrong, or unhelpful — with the
  review id in the observation, so the note can be traced back to the task
  that exposed it. This is the normal outcome for `gap` and `partial`.
- A new contract case, when the session exposes a repeatable task or failure
  shape worth holding directly.
  That is the more valuable outcome of the two.

From there the usual route applies: the note is worked off in a commit that
deletes it, and what has to keep holding afterwards goes into
[requirements/](../../requirements/readme.md). See [AGENTS.md](../../AGENTS.md).

For a `gap` review, do not re-file the part that is already written down —
its **Status today** line names the requirement. File what the task needed
beyond it.
