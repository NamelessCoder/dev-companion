# Wrap a line that begins with a number and a full stop as prose

**Serves:** src/Upkeep/
**Priority:** low
**Run:** bin/cli prose:format decisions/knowledge/knw-049-what-ddev-writes-into-the-settings-is-named-in-full-and-so-is-what-it-cannot-configure.md

`Wrap::document()` reads every line matching `^(\s*)(?:[-*+]|\d+\.)\s` as a list
item, wherever it stands. A wrapped paragraph whose second or later line happens
to begin with a number — `D-KNW-049` has one starting `5432. Nothing on that
path` — is then flushed as an item: the number becomes the marker in
`Wrap::flush()`, and every line under it is indented to hang from it. No word
moves, so `ProseTest::rewrappingChangesNothingButTheLineBreaks` passes and the
paragraph is silently reformatted in a command that is run over the whole corpus.
Markdown itself does not read it that way — a list may interrupt a paragraph only
where the marker is `1.` — so the step is to make `$item` at
[src/Upkeep/Wrap.php](../../src/Upkeep/Wrap.php) line 88 depend on a paragraph
being open, with the case above as the test.
