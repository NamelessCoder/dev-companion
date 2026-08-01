# Decide how much of the corpus is judged at a time

**Serves:** decisions/, documentation/
**Waiting on:** whether the portion goes from five feedback to one, and whether
    the journal is worth a file of its own or the commit is enough.

`bin/cli feedback:next` hands over five and `D-FBK-5` set that number.
[documentation/feedback/judging.md](../../documentation/feedback/judging.md) was
drafted for one per run, in a loop that ends when nothing is unjudged. The draft
is right that `D-FBK-5` named this outcome as its own **Wrong if**: it was taken
against 56 open feedback and 38 queued items, and later the same day there were
69 and 33. The queue moved by five, the pile by thirteen. One day is a direction
rather than a proof, and the reversal follows the decision's own reasoning
rather than overruling it.

Three things the draft proposed and this repository does not have. `bin/cli
feedback next` hands over **one**, the oldest with no answer, and exits nonzero
while any remain. A **journal** carries one line per feedback — date, ladder
step, evidence, outcome. And a **skip** lasts for one pass, is written down
nowhere, and is kept apart from a state.

The journal is what a portion of one costs: no run ever sees two feedback, so
the two cases a single one cannot state — one that corrects three earlier ones,
the same gap reported by several sessions — are visible only in a list of
answers.

The step is the question, not the build, which is why this waits rather than
being queued: no reading here settles it, and the answer decides whether
`D-FBK-5` is corrected in place. `R-FBK-7` holds the current shape, and the
draft header of `judging.md` says out loud that the page and the command
disagree until this is answered.
