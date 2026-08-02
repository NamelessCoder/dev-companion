# Try `D-SCO-005` from a checkout that is not the one being worked on

**Serves:** decisions/

The `TYPO3_MCP_ROOT` half is done: the variable named the installation to read
and moved the boundary with it, and `Instance::startedIn()` now separates the
two. What the entry says under **Since then** is the reading.

What is left is the back half of the first **Wrong if** — a core contributor
passing paths relative to the system extension directory they are standing in.
`Classes/Controller/EditDocumentController.php` is read as extension work by its
shape, and the shape is consulted before the checkout is, so a core checkout
never gets to say otherwise. `R-SCO-001` orders it that way on purpose, which is
what makes this a decision rather than a fix: either the layout prefixes stop
being evidence where the session is standing in a core checkout, or the entry
says this is the cost and names the same escape it names for the front half.
Settle which, and pin it in `ScopeTest` next to the two cases the other half
left.
