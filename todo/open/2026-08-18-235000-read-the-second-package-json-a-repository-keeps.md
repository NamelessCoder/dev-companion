# Read the second package.json a repository keeps its build in

**Serves:** D-SCO-013
**Priority:** low

Decide whether `Project::commands()` and `Node::describe()` read a
`Build/package.json` beside the root one, and build it or write down why not.
Both read `<root>/package.json` and nothing else, so a repository that keeps its
frontend build one directory down has no npm command in the list and no Node
number under it — the answer is silent rather than wrong, and nothing in it says
which of the two it is.

What makes it worth deciding rather than assuming: `bootstrap_package`, the
repository `feedback/2026-08-18-113501` was recorded in, is one of them — its
Gruntfile and its manifest are in `Build/`, and the session that lost five
sixths of itself to that build got neither answer. The TYPO3 core checkout is
another. Against that stands what a second location costs: two manifests
declaring a `build` script are two commands with one name, and which one a
caller is meant to run is a question the files may not settle.

`low` because one repository shape is one reading, and because the same decision
has to be made for the command list first — the Node numbers follow whatever it
settles rather than being answered on their own.
