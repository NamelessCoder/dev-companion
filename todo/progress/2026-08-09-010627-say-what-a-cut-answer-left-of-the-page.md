# Say what a cut answer left of the page

**Serves:** feedback/2026-08-08-224406-the-guides-are-reachable-only-through-project.md
**Priority:** normal
**Branch:** todo/say-what-a-cut-answer-left-of-the-page
**Claimed:** 2026-08-08

Judged as wording rather than a gap — `D-ANS-070`. `Result\Prose::readWhole()`
already names the pages the excerpts were cut from and the call that reads one
whole, and the session that reported this had that line and searched anyway: the
same sentence stands under an answer that matched most of a page and under the
one it got, which was two of the nine `##` sections of
`core/contribution/commit-messages`.

Settle first what the line says. A share — *2 of the 9 sections of this page* —
and the headings the search did not return are both candidates, and the second
is what a session picks its next query out of. A number owes `D-ANS-008` what it
counted, so it is stated as sections and reproducible by reading the page.

Then the change is `Result\Prose`, which is one place for all three tools that
render this corpus — `typo3_rule_lookup`, `typo3_script_lookup` and
`typo3_task_guide`. `Documents::sections()` is private and this needs a reader
beside it. The assertion sits next to
`KnowledgeTest::aCutScriptSectionSaysHowToReadThePageWhole`.

**Run:** `bin/cli todo:next`

## What this does not touch

The two skills that tell a session to read a page whole and hand it an address
rather than the call. That half is
`todo/open/2026-08-09-010626-name-a-whole-page-as-the-call-that-reads-it.md`.
