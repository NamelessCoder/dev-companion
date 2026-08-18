# the core checkouts a recording answers from have dependencies nothing here installs

**Serves:** documentation/server/tools/
**Priority:** normal
**Branch:** todo/the-core-checkouts-a-recording-answers-from-have
**Claimed:** 2026-08-18

`bin/cli tools:record` defaults to the newest released core checkout because
`bin/cli checkouts:update` makes it, which is what makes the recording
repeatable — and `ToolRecord` states that this root has no reachable console.
All four checkouts below `.checkouts/` on this machine have had `composer
install` run in them since 2026-08-14, which nothing in this repository does, so
the console answers there now and the installation-backed tools record a
Doctrine exception about a missing database instead of the "no console" shape.
Decide which of the two the command should stand on: either `checkouts:update`
takes such a checkout back to what it makes, or `tools:record` says the root it
was handed is not one it can record from. Until then a recording is only
reproducible by rebuilding the tree from the checkout's index, which is what the
commit removing the orientation pointer did.
