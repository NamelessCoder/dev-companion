# Make the environments a run validates in creatable here

**Serves:** scenarios/
**Priority:** normal

Settle whether this repository creates the environments it validates in or only
declares how one is recreated, then create the first one the answer allows: a
DDEV instance with a TYPO3 site in it, which is the one that was asked for.
Today all four environments sit on one machine and are named in
`todo/reference/which-checkout-plays-which-environment.md` — a DDEV site with a
site package, two DDEV extension checkouts, one host-PHP extension a major
behind. Nothing here creates any of them, so a forward run is reproducible only
for whoever owns that machine, and the console half is exercised by no test:
`Typo3Cli` reaches an installation through `ddev exec`, and both `D-DIS-007` and
`R-DIS-018` were found by a real run rather than by the suite. `bin/cli
checkouts:update` is the precedent the answer is weighed against — gitignored
below `.checkouts/`, created by one command, re-fetchable at any time. A DDEV
instance is not cheap in the same way: containers, a database, minutes per
create, and a docker daemon a CI job may not have. The extension that is a major
behind is a real third-party history no scaffold produces, so the answer will not
be the same for all four. Write what it comes out as as a decision, including
which environments stay declared, because the cost is what a later session would
otherwise reopen.
