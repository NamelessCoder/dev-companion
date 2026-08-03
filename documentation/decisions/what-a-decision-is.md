# What a decision is

A commit message says what changed and why. What it cannot carry is the part
that may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

One entry is one decision worth revisiting, and a change nobody would need to
reconsider does not get one. An entry is written by the commit that implements
it — nothing here is a proposal stage, and by the time a decision exists the
change it describes is in the code.

An entry backs what stands on it. What must be true from now on is a
requirement, and it names the decisions it rests on in its own `restsOn:`, so a
revoked one is readable from the requirement written on top of it —
[what a requirement is](../requirements/what-a-requirement-is.md). Where an
entry goes and how it is written is
[writing-a-decision.md](writing-a-decision.md).

A feedback is one occasion on which a decision gets made. It is not what this
directory is for: the entry is read long after that question was answered, and
what gets decided as readily arrives from a review, a recorded run, or a
question somebody had to be asked.

## What the state means

`status` is one of `open`, `confirmed` and `revoked` — the `DecisionStatus`
enum — and it names the **last** dated section rather than the only one. A
decision has a history: `D-KNW-003` was confirmed by a run on the morning of
2026-08-02 and revoked by the evidence that arrived the same day, and both are
in the file. What a reader relies on is the latest.

The status is not a workflow. `open` does not mean unbuilt — it means nobody has
been back to the **Wrong if** yet. Most decisions are open and stay that way,
which is what makes the state easy to stop seeing, and
[`bin/cli backlog:list`](../feedback/readme.md) is what reads them out.

`revokedBy` is what a revoked entry owes its reader: where to go instead. It
names one decision, only a revoked entry may carry it, and the generated listing
shows it, so nobody has to open a dead entry to find the live one.
