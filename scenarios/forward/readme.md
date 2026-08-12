# Open forward reviews

These are deliberately broad repository reviews. The prompt names the working
context and the user's intent, but no subsystem, skill, tool, expected defect,
or implementation shape. What the agent chooses to inspect and prioritize is the
evidence.

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

Only these are run and recorded. One run is one file below [runs/](../runs/),
and it is what the **Status today** line of a review rests on: the environment,
the server it ran against, the skills that activated, the tools the session
called, and one judgment with evidence per criterion. The verdict is not written
into it — it follows from the judgments, and a review whose mark disagrees with
what its run establishes is a failing check rather than a sentence nobody
rereads.

How one is run, judged, and read when it stops without an error:
[documentation/records/forward-runs.rst](../../documentation/records/forward-runs.rst).
