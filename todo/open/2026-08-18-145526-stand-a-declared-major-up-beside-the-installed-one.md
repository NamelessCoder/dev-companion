# Write the page that stands a declared major up beside the installed one

**Serves:** feedback/2026-08-18-081129-nothing-says-how-to-execute-the-other-typo3.md
**Priority:** normal

`D-VER-008` is the judgement: step 1a, taken on, as a `knowledge/documents/`
page in the extension scope beside
`extension/compatibility/a-declared-major-that-is-not-installed`, which ends
where this one starts. Establish the procedure by running it rather than by
recalling it — `bin/cli environment:create` makes the working directory, and the
other declared major is stood up beside that installation — and write down what
the run actually found, in the order the questions arise: whether the
repository's own CI covers the cell, which is asked before anything is
installed; the invocation that installs the other major in a directory of its
own; what it writes and what it leaves the working installation; whether the
database survives; and which checks are worth re-running there. Route it from
`knowledge/server-scope.json` and from `typo3-extension-upgrade`'s **Prove it on
every version it claims**, which names the cell and not how it comes to exist.
