# Check the twelve descriptions for workflow the body already owns

**Serves:** R-SKL-010
**Priority:** normal

A description is the only part of a skill read before it is chosen, and this
repository already says what it is for: the words a user brings — the request,
the symptom, the files being touched. What it does not yet say is what happens
when a description also summarises the workflow, and that failure is measured.
obra/superpowers found that an agent follows the summary **instead of reading
the body**: a description saying "code review between tasks" produced one review
where the skill's own flow specified two. Their reading is that a description
which summarises workflow creates a shortcut the agent will take, and the body
becomes documentation it skips.

That is a different failure from the one `D-AUD-003` records. There the
description was too narrow and the skill never activated. Here the skill
activates, the session believes it is following it, and the steps that decide
the outcome are never read — which no assertion over the directory can see,
because the wording is present in both cases.

Read the twelve descriptions against that one question: which clauses are
triggers a user would type, and which are the skill telling the reader what it
will do. `typo3-core-patch-checkout` at 951 characters is where to start, and
the four core skills are the ones whose bodies carry the most ordered steps and
therefore have the most to lose. Cut the summary clauses, keep every side the
skill owns, since `R-SKL-010` and
`SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` still hold.

This is the same twelve files as the listing-budget card and the same edit, so
whichever runs second is reading a moved target. Take them together or take this
one first — the cut this card asks for is the cut that card wants anyway, and
doing it for the reason stated here means the characters that go are the ones
that were costing accuracy rather than the ones that were cheapest to lose.
