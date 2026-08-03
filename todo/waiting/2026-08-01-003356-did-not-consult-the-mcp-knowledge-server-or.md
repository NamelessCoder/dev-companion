# Debrief of the TYPO3 14 testimonials session: the assistant did not consult the MCP knowledge ser...

**Serves:** feedback/2026-08-01-003356-did-not-consult-the-mcp-knowledge-server-or.md
**Priority:** low
**Waiting on:** does `typo3_task_guide` name the skill that owns the task, which
    is `src/`? It is the question `todo/waiting/2026-07-31-192945` already
    carries, asked from the other side. This feedback answers half of it: the
    two options that card weighs are not the same size. Saying in
    `skills/base.md` what the call is worth to a caller that arrived through a
    skill leaves a caller that arrived without one exactly where this session
    was. Judging.md keeps a **Wrong if** out of reach of an autonomous answer,
    so neither is made from here.

The judgement is written into
[`D-SKL-001`](../../decisions/task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)
and the feedback stays open behind that question. It is ladder step 3: the skill
exists, the payload the session needed is in the guide's own answer, and nothing
joins them. The other half of the feedback — that no skill activated — already
has the `D-AUD-003` description rewrite of 2026-08-02 against it, made for this
same task shape and not yet confirmed by a run.

The whole `site-new` corpus was read sideways on 2026-08-03 and the reading is
[`D-SKL-006`](../../decisions/task-skills/skl-006-the-site-new-cluster-earns-the-route-into-the-skill-that-owns-the-task.md).
It settles what this session's fourteen feedback are evidence about: the skill
that owns the task exists and was never loaded, so the cluster earns no new one.
The question above is what it earns, and `todo/waiting/2026-07-31-192945` asks
it from inside a skill.

What the reading established, so the answer does not need it again. `site-new`
runs `bin/typo3-cms-mcp` out of the main checkout, and `18a371a` put the
hand-off sentence into the `instructions` at 18:33 CEST on 2026-07-31, six hours
before this session filed. Re-run on 2026-08-02 from `site-new`,
`typo3_task_guide` for this task matches the content-element and test intents
and returns the `record` hint the session guessed at. Its "Next lookups" name
seven tools, no skill among them and no `typo3_documentation_lookup`, and
`src/Tool/TaskGuide.php` contains no skill name at all. What is unmeasured is
whether that client passes `instructions` to the model at all. It does not gate
the answer: a caller that reads the sentence and makes the call still reaches no
skill.
