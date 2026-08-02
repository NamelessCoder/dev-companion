# Let a second E-SITE be made where the first one's checkout is gone

**Serves:** src/Upkeep/
**Priority:** normal
**Branch:** todo/let-a-second-e-site-be-made-where-the-first-ones-checkout-is-gone
**Claimed:** 2026-08-02

`bin/cli environment:create E-SITE` refuses where DDEV already registers
`typo3-mcp-e-site` at another checkout, which is right while that checkout is
there and wrong once it is not: a worktree that made an environment and was
then removed leaves the registration behind, pointing at a directory DDEV
itself reports as `project directory missing`. The next session reads "that
checkout's environment would be taken over by making this one" and is named a
path that does not exist, with nothing saying what to do — measured on
2026-08-02, on the registration the worktree behind `D-EVI-004` left. The way
out was `ddev stop --unlist typo3-mcp-e-site`, which is what the guard could
say, or could do on its own where the approot is gone: an environment nothing
can reach is not one being taken from anybody. `Environments::projects()`
already reads the approot the check compares, so what is missing is that it
also asks whether it is still there.

The second half is the volume. `ddev stop --unlist` leaves the database behind,
so making the environment again under the same project name runs the build into
`The selected database contains already 42 tables. Please delete all tables or
select another database.` at the setup step — past `--force`, which
`Environments::build` documents as what makes a half-built environment
finishable and which TYPO3's setup does not extend to a populated database.
`ddev delete --omit-snapshot -y typo3-mcp-e-site` is what cleared it. Settle
whether the build detects that and says so, whether `--force` is the wrong
promise to have written down, or whether a create that finds a database it did
not put there stops rather than guesses — the last is a repository this
repository does not own. This is `D-EVI-004`'s second **Wrong if** arriving on
the second machine to run the build, which was this one.
