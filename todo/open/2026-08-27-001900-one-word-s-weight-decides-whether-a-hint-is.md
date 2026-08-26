# One word's weight decides whether a hint is reachable

**Serves:** requirements/
**Priority:** normal

`R-KNW-053` is held by a margin of one word. Writing an unrelated hint into
`knowledge/hints/php.json` on 2026-08-26 used the word "test" twice, which
diluted that term's IDF weight enough to drop `project-extension-tests` below
the coverage floor on the query "live database versus test database", and
`HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun` failed. Rewording
the new hint restored it. Nothing was wrong with either statement, and the
failure named the innocent one.

Establish how much room the requirement actually has: what the margin is on each
query the test holds, and whether the corpus can be written in without reading
the matcher. Then decide what carries it — a floor with room in it, a query that
does not rest on a common word, or a check that says which statement moved the
weight rather than which assertion failed.

Found while working
`todo/open/2026-08-24-224958-a-releases-line-naming-an-older-branch-sets-a.md`,
whose session reported the diagnosis but wrote no card for it.
