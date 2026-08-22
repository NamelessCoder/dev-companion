# Route the three testing cells nothing names

**Serves:** D-KNW-008
**Priority:** normal
**Run:** bin/cli hints:probe "which extensions does my functional test load"

`extension-test-extensions`, `extension-test-site` and
`extension-repository-tests` are named by nothing outside their own hint file.
`D-KNW-008` decided that the row of testing cells is crossed by routing rather
than by structure — a cell that owns one names the ids of the others — and these
three were added without it. A probe reaches each of them third, on text alone.

All three are `extension` scope and all three sit beside
`project-extension-tests`, which is named from seven places. So the reading is
which of them that hint should send a caller to and at which sentence: what a
functional test loads and what site it builds are questions somebody setting a
suite up arrives with, and the instance an extension suite builds itself is the
one a repository test needs.

What decides it is what each cell answers rather than what it is called, so read
the four bodies before writing a sentence. `bin/cli hints:probe` is what says
whether the route is needed at all — a cell already reached first by the words a
caller writes does not need to be pointed at.
