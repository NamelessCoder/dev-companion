# `typo3_feedback_list`

List improvement feedback recorded via typo3_feedback_record, newest first, so
they can be worked off. Filter by status, by category, or by the tool a feedback
is about. A feedback that was worked off is kept, not deleted, so
status="closed" answers "what became of what I reported" — the feedback as it
was recorded, plus the commit that closed it. Answers from: checkout.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`checkout`](answer-sources.md#checkout).

## Takes

```yaml
# One of: open, closed, all. open: the feedback nobody has worked off yet.
# closed: the ones already worked off, each with the commit subject saying what
# came of it. all: both. The category and tool filters apply to either.
status: string  # optional
# One of: missing-knowledge, wrong-answer, tool-gap, bug, idea. Restrict the
# list to one category.
category: string  # optional
# Restrict the list to the feedback about one tool, for example
# typo3_label_lookup. A feedback naming several tools is matched by each of
# them.
tool: string  # optional
# Maximum number of feedback to return.
limit: integer  # optional
```

## Answers with

```yaml
count: integer
notes:
  - file: string
    date: string
    category: string
    # open while the feedback is still to be worked off, closed once it was
    # moved to the archive.
    status: string
    # The model that left the feedback. "unknown" where it named none or
    # predates the field.
    model: string
    # The tools the feedback is about, comma-separated. Empty when it names
    # none.
    tool: string
    # The same names as a list, to filter or group by without parsing.
    tools: [string]
    title: string
    # The commit that worked the feedback off. Null while the feedback is open.
    closedBy:
      commit: string  # optional
      date: string  # optional
      # The commit subject: what came of the feedback.
      subject: string  # optional
```

## Not answered

And deliberately: it answers with the feedback somebody else wrote, which is
different in every checkout and carries the tool names that were current when
each feedback was filed. One recorded title ends in a tool name cut to length,
which reads to `ToolNamingTest` as a tool this server does not have.
