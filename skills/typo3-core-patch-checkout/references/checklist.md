# Resolve or stop

One rule decides every conflict, and it is about what you are allowed to know:

**A conflict is resolvable only where the change itself decides it.** The patch
says what it wants, the branch says what is there now, and where those two
together leave exactly one way to write the hunk, writing it is transcription.
Where they leave a choice, the choice is the author's and taking it produces a
patch nobody wrote — which then gets read, run and reported on as if it were
theirs.

Apply it per conflict, not per file. A change can be transcription in one hunk
and somebody else's decision in the next, and the second one ends the work
whatever happened in the first.

## Resolve

- **Context moved.** The lines the patch touches are unchanged; something above
  or below them shifted. Nothing to decide.
- **A rename the branch made and the patch predates.** The change's intent is
  unchanged under the new name, and the new name is what the branch uses
  everywhere. Verify the second before believing the first.
- **Both sides made the same edit.** Keeping one copy is the only reading.
- **Import, use-statement and namespace ordering.** The tool the checkout
  declares decides these, not you — run it and take what it writes.

## Stop

- **Both sides changed the same lines with different intent.** This is the case
  the rule exists for. Any resolution is an authoring decision.
- **The branch already fixed what the patch fixes, differently.** The patch may
  now be unnecessary, partly unnecessary, or a better approach that should
  replace the other. All three are the author's and a reviewer's call.
- **The API the patch calls is gone or changed shape.** Rewriting a call site to
  the new API is writing the patch, and it is also the moment the change might
  no longer be needed at all.
- **The conflict is in a test's expectations.** What the expectation should be
  is the substance of the change, never a merge artefact.
- **The conflict is in a changelog entry, a fixture or generated output.** Which
  version it belongs to and what it says are decisions upstream of the diff.
- **Resolving would need the issue to be re-read to decide it.** That is the
  signal that the change no longer carries its own answer.
- **More than a handful of hunks conflict.** Individually resolvable or not, the
  patch is stale enough that its author rebasing it is the honest outcome; say
  how many and where.

## What a stop reports

- The change, its patch set, its target branch, and the commit the checkout was
  put back on.
- Which files and which hunks conflicted, and for each one which stopping rule
  it hit.
- What the two sides wanted, in one sentence each. That is what makes the report
  usable by whoever rebases it properly, and it is the part a diff does not say.
- Whether anything was resolved before the stop, and what. A partially resolved
  carry that was then aborted still tells the next person which hunks are free.

## After any resolution

- The build and the suites that cover the touched paths are what says the
  resolution holds. A carry that produced a checkout nobody ran is not a carry
  that worked.
- Say that the checkout holds the patch on other code and is no longer the
  revision under review. Every result from it is about the commit you made and
  not about the patch set the reviewers see, and reporting one as the other is
  the failure this whole file guards.
- The carried state is local. It is not pushed, and pushing it would be opening
  a patch set in somebody else's name — that belongs to the workflow that owns
  amending a change, and only where the change is yours to amend.
