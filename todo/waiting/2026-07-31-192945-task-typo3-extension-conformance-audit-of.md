# Task: TYPO3 extension conformance audit of printworks_sitepackage. The typo3-extension-conformanc...

**Serves:** feedback/2026-07-31-192945-task-typo3-extension-conformance-audit-of.md
**Priority:** low
**Waiting on:** what is step 3 of `skills/base.md` for, once a task skill is
    already loaded? Two answers are open and they run opposite ways. Either the
    base says what `typo3_task_guide` is worth to a caller that arrived through
    a skill, which changes a contract installed in somebody else's project. Or
    `typo3_task_guide` names the workflow that step 3 says it returns, which is
    `src/`. Judging.md puts a **Wrong if** on the fifth rung and out of reach of
    an autonomous answer, so neither is made from here.

The judgement is written and the feedback stays open behind that question. It is
step 5: the strength lands in the `D-SKL-001` **Wrong if** rather than reporting
no gap, which is also the first `D-FBK-018` **Wrong if** firing. Both entries
carry the paragraph.

What the reading established, so the answer does not need it again. The strength
recites the order without `typo3_task_guide`, and the same session's tool log at
`feedback/2026-07-31-193005` shows thirteen round trips with no such call. That
log also has the checkout read at positions 5 and 6, ahead of the first
conventions lookup at 7. The copy it read is not the reason: the installed
`references/base.md` in `site-new` carries step 3, and step 3 is older than the
sweep that same strength recites. Re-run on 2026-08-02 from `site-new` through
`bin/typo3-cms-mcp`, `typo3_task_guide` answers that task in 1,937 words, points
back at steps 4 and 5, and names no workflow — `src/Tool/TaskGuide.php` has no
skill in it. `feedback/2026-07-31-194826` is the same call from another model in
the same project, reporting that it restated the skill's own checklist.

Once answered, the feedback is archived by the commit that implements whichever
side was chosen. `feedback/2026-07-31-194826` was the other half of the same
property and was judged on 2026-08-02. It does not wait here: what it reports
survives either answer to the question above, so it is queued as `R-GUI-006` and
the `D-SKL-001` **Since then** says why.
